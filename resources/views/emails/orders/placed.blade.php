<x-mail::message>
# 🎉 Order Confirmed!

Hi {{ auth()->user()->name ?? 'Customer' }},

Your order has been successfully placed.

**Order ID:** {{ $orderId }}  
**Total Paid:** ₹{{ number_format($finalTotal, 2) }}

We will notify you as soon as your order is shipped 🚚

<!--<x-mail::button :url="url('/orders/'.$orderId)">-->
<!--View Order-->
<!--</x-mail::button>-->

Thanks for shopping with us!  
**{{ config('app.name') }}**
</x-mail::message>
