<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\NotificationHelper;


class NotificationController extends Controller
{
    public function index()
    {
        $customerId = Auth::user()->id;

        $notifications = DB::table('notifications')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => "Notification fetched  successfully",
            'data' => $notifications
        ]);
    }

    // Mark notification as read
    public function markAsRead($id)
    {
        $customerId = Auth::user()->id;

        DB::table('notifications')
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }
}