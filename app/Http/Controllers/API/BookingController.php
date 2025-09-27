<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Notifications\BookingConfirmed;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Create a new booking
     */
    public function store(Request $request, $ticketId)
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $quantity = $request->quantity;

            if ($ticket->quantity < $quantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not enough tickets'
                ], 422);
            }

            $ticket->quantity -= $quantity;
            $ticket->save();

            $booking = Booking::create([
                'user_id' => $request->user()->id,
                'ticket_id' => $ticketId,
                'quantity' => $quantity,
                'status' => 'pending'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking created successfully',
                'data' => $booking
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found'
            ], 404);
        }
    }

    /**
     * List bookings for authenticated user
     */
    public function index(Request $request)
    {
        $bookings = $request->user()->bookings()->with('ticket.event')->get();

        return response()->json([
            'status' => 'success',
            'data' => $bookings
        ]);
    }

    /**
     * Cancel a booking
     */
    public function cancel(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);

            if ($booking->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden'
                ], 403);
            }

            $booking->status = 'cancelled';
            $booking->save();
            $booking->ticket->increment('quantity', $booking->quantity);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found'
            ], 404);
        }
    }

    /**
     * Pay for a booking
     */
    public function pay(Request $request, $id, PaymentService $ps)
    {
        try {
            $booking = Booking::findOrFail($id);

            if ($booking->user_id !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden'
                ], 403);
            }

            if ($booking->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking not payable'
                ], 422);
            }

            $amount = $booking->ticket->price * $booking->quantity;
            $result = $ps->charge($amount);

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $amount,
                'status' => $result['status']
            ]);

            if ($result['status'] === 'success') {
                $booking->status = 'confirmed';
                $booking->save();
                // $booking->user->notify(new BookingConfirmed($booking));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Payment processed successfully',
                'data' => [
                    'payment' => $payment,
                    'booking' => $booking
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found'
            ], 404);
        }
    }

    /**
     * Show payment details
     */
    public function paymentShow($id)
    {
        try {
            $payment = Payment::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found'
            ], 404);
        }
    }
}
