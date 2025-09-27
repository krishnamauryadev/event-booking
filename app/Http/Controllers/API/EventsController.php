<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class EventsController extends Controller
{
    /**
     * List events with optional filters
     */
    public function index(Request $request)
    {
        $cacheKey = "events:list:" . md5(json_encode($request->only(['q','date','location','page'])));

        $events = Cache::remember($cacheKey, 60, function () use ($request) {
            return Event::with('tickets')
                ->searchByTitle($request->get('q'))
                ->filterByDate($request->get('date'))
                ->when($request->get('location'), function ($q, $loc) {
                    $q->where('location', 'like', '%' . $loc . '%');
                })
                ->orderBy('date')
                ->paginate(10);
        });

        return response()->json([
            'status' => 'success',
            'data' => $events
        ]);
    }

    /**
     * Show single event
     */
    public function show($id)
    {
        try {
            $event = Event::with('tickets')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $event
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found'
            ], 404);
        }
    }

    /**
     * Create a new event
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['created_by'] = $request->user()->id;

        $event = Event::create($data);
        Cache::flush();

        return response()->json([
            'status' => 'success',
            'message' => 'Event created successfully',
            'data' => $event
        ], 201);
    }

    /**
     * Update an existing event
     */
    public function update(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);

            if ($request->user()->role !== 'admin' && $event->created_by !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'sometimes|required|date',
                'location' => 'sometimes|required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $event->update($validator->validated());
            Cache::flush();

            return response()->json([
                'status' => 'success',
                'message' => 'Event updated successfully',
                'data' => $event
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found'
            ], 404);
        }
    }

    /**
     * Delete an event
     */
    public function destroy(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);

            if ($request->user()->role !== 'admin' && $event->created_by !== $request->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden'
                ], 403);
            }

            $event->delete();
            Cache::flush();

            return response()->json([
                'status' => 'success',
                'message' => 'Event deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event not found'
            ], 404);
        }
    }
}
