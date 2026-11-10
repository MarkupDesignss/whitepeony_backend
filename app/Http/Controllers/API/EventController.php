<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\StripeService;
use App\Mail\EventRegistrationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{
    public function index()
    {
        try {
            $events = DB::table('events')->get();

            return response()->json([
                'success' => true,
                'message' => "Events Fetched successfully",
                'events' => $events
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Can't get events: {$th->getMessage()}"
            ]);
        }
    }

    public function show($id)
    {
        try {
            $event = DB::table('events')->where('id', $id)->first();

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => "Event Fetched successfully",
                'event' => $event
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Can't get event: {$th->getMessage()}"
            ]);
        }
    }
    
    public function register(Request $request, $eventId)
    {
        try {
            $user = Auth::user();

            if ($user->status === 'blocked') {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is blocked. You cannot register for events.',
                ], 403);
            }

            $request->validate([
                'emails' => 'required|array|min:1',
                'emails.*' => 'email',
            ]);

            $event = DB::table('events')->where('id', $eventId)->first();
            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found'
                ], 404);
            }

            $customerName = Customer::where('id', $user->id)->value('name');

            // Check if already registered
            $existing = DB::table('event_registrations')
                ->where('event_id', $eventId)
                ->where('customer_id', $user->id)
                ->where('status', 'registered')
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already registered for this event.'
                ], 200);
            }

            // Seat availability
            $currentRegistrations = DB::table('event_registrations')
                ->where('event_id', $eventId)
                ->where('status', 'registered')
                ->sum('booked_seats');

            $requestedSeats = count($request->emails);
            if ($currentRegistrations + $requestedSeats > $event->capacity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough seats available for this event.'
                ], 400);
            }

            DB::beginTransaction();

            // Update remaining seats
            $remainingSeats = $event->remaining_seats - $requestedSeats;
            DB::table('events')->where('id', $eventId)->update(['remaining_seats' => $remainingSeats]);

            // Generate booking ID (unique)
            $bookingId = 'BK-' . strtoupper(Str::random(8));

            // Price calculation
            $pricePerSeat = $event->price_per_seat ?? 0;
            $totalPrice = $pricePerSeat * $requestedSeats;

            // Save registration
            $registrationId = DB::table('event_registrations')->insertGetId([
                'event_id'       => $eventId,
                'customer_id'    => $user->id,
                'full_name'      => $customerName,
                'status'         => 'registered',
                'per_seat_price' => $pricePerSeat,
                'total_price'    => $totalPrice,
                'payment_status' => 'pending',
                'booked_seats'   => $requestedSeats,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Save attendees
            foreach ($request->emails as $email) {
                DB::table('event_attendees')->insert([
                    'registration_id' => $registrationId,
                    'email'           => $email,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            // ✅ Generate QR code for Ticket / Booking
            $ticketData = [
                'Booking ID'      => $bookingId,
                'Event'           => $event->title,
                'Name'            => $customerName,
                'Seats'           => $requestedSeats,
                'Price Per Seat'  => $pricePerSeat,
                'Total Price'     => $totalPrice,
                'Date'            => date('M d, Y h:i A', strtotime($event->event_date)),
                'Venue'           => $event->address,
            ];

            $qrContent = json_encode($ticketData, JSON_PRETTY_PRINT);
            $svg = QrCode::format('svg')->size(300)->generate($qrContent);

            // Save SVG directly into `qr` column
            DB::table('event_registrations')->where('id', $registrationId)->update([
                'qr' => $svg,
                'updated_at' => now(),
            ]);

            // Public URL to fetch the SVG via controller
            $qrUrl = url('/api/events/qr/' . $registrationId);

            // ✅ Create Stripe Checkout Session
            $stripeService = new StripeService();
            $paymentUrl = $stripeService->createPayment(
                (float) $totalPrice,
                'USD',
                "Event Registration: {$event->title}",
                ['registration_id' => $registrationId, 'type' => 'event']
            );

            if (!$paymentUrl) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Unable to create Stripe payment.'], 500);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proceed to Stripe for payment.',
                'redirect_url' => $paymentUrl,
                'registration_id' => $registrationId,
                'booking_id' => $bookingId,
                'ticket_qr' => $qrUrl,
                'ticket_details' => $ticketData,
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Unable to register: {$th->getMessage()}"
            ]);
        }
    }

    public function stripeEventStatus(Request $request)
    {
        $sessionId     = $request->query('session_id');
        $registrationId = $request->query('registration_id');

        if (!$sessionId || !$registrationId) {
            return redirect('/api/event-payment-failed?registration_id=' . $registrationId . '&reason=missing_reference');
        }

        $stripe = new StripeService();
        $result = $stripe->executePayment($sessionId);

        if (!$result['success']) {
            return redirect('/api/event-payment-failed?registration_id=' . $registrationId . '&reason=' . urlencode($result['message'] ?? 'payment_failed'));
        }

        // Fetch registration
        $registration = DB::table('event_registrations')->where('id', $registrationId)->first();

        if (!$registration) {
            return redirect('/api/event-payment-failed?registration_id=' . $registrationId . '&reason=registration_not_found');
        }

        $transactionId = $result['data']['transaction_id'] ?? $sessionId;

        // Update registration
        DB::table('event_registrations')->where('id', $registrationId)->update([
            'payment_status'         => 'success',
            'stripe_order_id'  => $transactionId,
            'updated_at'             => now(),
        ]);

        // Send confirmation email to user
        $user = Auth::user();
        if ($user && $user->email) {
            try {
                Mail::to($user->email)->send(new EventRegistrationMail($registration));
            } catch (\Exception $e) {
                Log::error('Event registration mail failed: ' . $e->getMessage());
            }
        }

        // Notify Admin
        try {
            Mail::to('admin@yourdomain.com')->send(new EventRegistrationMail($registration, true));
        } catch (\Exception $e) {
            Log::error('Admin event registration mail failed: '.$e->getMessage());
        }

        // Redirect → SUCCESS page with updated params
        return redirect('/api/event-payment-success?registration_id=' . $registrationId
            . '&tracking=' . $transactionId
            . '&total_amount=' . urlencode($registration->total_price)
            . '&payment_method=Stripe'
        );
    }

    public function success(Request $request)
    {
        return view('event-payment-success', [
            'registration_id' => $request->query('registration_id'),
            'paypal_id'       => $request->query('tracking'), // or paypal_id
            'amount'          => $request->query('total_amount'),
            'payment_method'  => $request->query('payment_method') ?? 'PayPal',
        ]);
    }

    public function failed(Request $request)
    {
        return view('event-payment-fail', [
            'registration_id' => $request->query('registration_id'),
            'reason'          => $request->query('reason') ?? 'Payment failed or cancelled.',
            'payment_method'  => $request->query('payment_method') ?? 'PayPal',
        ]);
    }
   
    public function cancel($eventId)
    {
        try {
            $customerId = Auth::user()->id;

            $registration = DB::table('event_registrations')
                ->where('event_id', $eventId)
                ->where('customer_id', $customerId)
                ->where('status', 'registered')
                ->first();

            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not registered for this event.'
                ], 404);
            }

            // Delete attendees
            DB::table('event_attendees')
                ->where('registration_id', $registration->id)
                ->delete();

            // Delete registration
            DB::table('event_registrations')
                ->where('id', $registration->id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Registration cancelled successfully.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Unable to cancel event: {$th->getMessage()}"
            ]);
        }
    }

    public function registeredEvents()
    {
        try {
            $customerId = Auth::user()->id;

            $registrations = DB::table('event_registrations')
                ->join('events', 'event_registrations.event_id', '=', 'events.id')
                ->where('event_registrations.customer_id', $customerId)
                ->where('event_registrations.status', 'registered')
                ->select(
                    'events.id as event_id',
                    'events.title',
                    'events.description',
                    'events.address',
                    'events.event_date',
                    'events.price_per_seat',
                    'events.speaker',
                    'events.capacity',
                    'event_registrations.booked_seats',
                    'event_registrations.total_price',
                    'event_registrations.payment_status',
                    'event_registrations.id as registration_id'
                )
                ->orderBy('events.event_date', 'asc')
                ->get();

            // Fetch attendees for each registration
            foreach ($registrations as $reg) {
                $reg->attendees = DB::table('event_attendees')
                    ->where('registration_id', $reg->registration_id)
                    ->pluck('email');
            }

            return response()->json([
                'success' => true,
                'message' => "Registered Events Fetched Successfully",
                'registered_events' => $registrations
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Unable to fetch registered events: {$th->getMessage()}"
            ]);
        }
    }

    public function nearbyEvents(Request $request)
    {
        try {
            $request->validate([
                'lat' => 'required|numeric',
                'lng' => 'required|numeric',
                'radius' => 'nullable|numeric',
            ]);

            $lat = $request->lat;
            $lng = $request->lng;
            $radius = $request->radius ?? 2;

            // Haversine formula using your 'lat' and 'long' columns
            $events = DB::table('events')
                ->select('*', DB::raw("
                    (6371 * acos(
                        cos(radians($lat)) 
                        * cos(radians(lat)) 
                        * cos(radians(`long`) - radians($lng)) 
                        + sin(radians($lat)) 
                        * sin(radians(lat))
                    )) AS distance
                "))
                ->having('distance', '<=', $radius)
                ->orderBy('distance')
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Events Fetched successfully",
                'events' => $events
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "Unable to fetch nearby events: {$th->getMessage()}"
            ]);
        }
    }

    /**
     * Serve the QR SVG stored in the `qr` column for a registration.
     * Public endpoint so the returned URL can be opened directly in browser.
     */
    public function qr($registrationId)
    {
        try {
            $registration = DB::table('event_registrations')->where('id', $registrationId)->first();
            if (!$registration) {
                return response()->json(['success' => false, 'message' => 'Registration not found'], 404);
            }

            $qr = $registration->qr;
            // If qr is a file path in storage, try to load it, otherwise assume it's SVG content
            if ($qr && strpos(trim($qr), '<svg') === false && file_exists(public_path($qr))) {
                $content = file_get_contents(public_path($qr));
                return response($content, 200)->header('Content-Type', 'image/svg+xml');
            }

            if ($qr && strpos(trim($qr), '<svg') !== false) {
                return response($qr, 200)->header('Content-Type', 'image/svg+xml');
            }

            return response()->json(['success' => false, 'message' => 'QR not available'], 404);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Unable to fetch QR: ' . $th->getMessage()], 500);
        }
    }
}