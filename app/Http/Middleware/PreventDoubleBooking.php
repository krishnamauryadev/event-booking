<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Booking;

class PreventDoubleBooking {
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('post') && $request->route()->parameter('id')) {
            $ticketId = $request->route()->parameter('id');
            $user = $request->user();
            $existing = Booking::where('user_id', $user->id)->where('ticket_id', $ticketId)->whereIn('status',['pending','confirmed'])->first();
            if ($existing) return response()->json(['message'=>'You already have a booking for this ticket.'], 422);
        }

        return $next($request);
    }
}
