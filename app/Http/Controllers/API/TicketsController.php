<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Support\Facades\Validator;

class TicketsController extends Controller
{
    /**
     * Create a new ticket for an event
     */
    public function store(Request $request, $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            if ($request->user()->role !== 'admin' && $event->created_by !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'type' => 'required|string|max:255',
                'price' => 'required|numeric',
                'quantity' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ticket = Ticket::create(array_merge(
                $validator->validated(),
                ['event_id' => $eventId]
            ));

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket created successfully',
                'data' => $ticket
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found'
            ], 404);
        }
    }

    /**
     * Update an existing ticket
     */
    public function update(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            if ($request->user()->role !== 'admin' && $ticket->event->created_by !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'type' => 'sometimes|required|string|max:255',
                'price' => 'sometimes|required|numeric',
                'quantity' => 'sometimes|required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ticket->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket updated successfully',
                'data' => $ticket
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found'
            ], 404);
        }
    }

    /**
     * Delete a ticket
     */
    public function destroy(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            if ($request->user()->role !== 'admin' && $ticket->event->created_by !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden'
                ], 403);
            }

            $ticket->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found'
            ], 404);
        }
    }
}
