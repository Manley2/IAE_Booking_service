<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::query();

        if ($request->has('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        return response()->json($query->get());
    }

    public function show($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json($booking);
    }

    public function store(Request $request)
    {
        $customer = Http::get('http://127.0.0.1:8001/api/customers/' . $request->customer_id);

        if ($customer->failed()) {
            return response()->json([
                'message' => 'Customer not found'
            ], 404);
        }

        $room = Http::get('http://127.0.0.1:8002/api/rooms/' . $request->room_id);

        if ($room->failed()) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        if ($room->json('status') !== 'available') {
            return response()->json([
                'message' => 'Room is not available'
            ], 400);
        }

        $booking = Booking::create([
            'customer_id' => $request->customer_id,
            'room_id' => $request->room_id,
            'checkin_date' => $request->checkin_date,
            'checkout_date' => $request->checkout_date,
            'total_price' => $request->total_price,
            'status' => $request->status ?? 'confirmed',
        ]);

        Http::put('http://127.0.0.1:8002/api/rooms/' . $request->room_id, [
            'status' => 'booked'
        ]);

        return response()->json([
            'message' => 'Booking created successfully',
            'data' => $booking
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        $booking->update($request->only([
            'customer_id',
            'room_id',
            'checkin_date',
            'checkout_date',
            'total_price',
            'status'
        ]));

        return response()->json($booking);
    }

    public function destroy($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        $booking->delete();

        return response()->json([
            'message' => 'Booking deleted'
        ]);
    }

    public function room($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        $room = Http::get('http://127.0.0.1:8002/api/rooms/' . $booking->room_id);

        if ($room->failed()) {
            return response()->json([
                'message' => 'Room service error'
            ], 500);
        }

        return response()->json([
            'booking' => $booking,
            'room' => $room->json()
        ]);
    }

    public function customer($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        $customer = Http::get('http://127.0.0.1:8001/api/customers/' . $booking->customer_id);

        if ($customer->failed()) {
            return response()->json([
                'message' => 'Customer service error'
            ], 500);
        }

        return response()->json([
            'booking' => $booking,
            'customer' => $customer->json()
        ]);
    }
    public function detail($id)
{
    $booking = Booking::find($id);

    if (!$booking) {
        return response()->json(['message' => 'Booking not found'], 404);
    }

    $customer = Http::get('http://127.0.0.1:8001/api/customers/' . $booking->customer_id);
    $room = Http::get('http://127.0.0.1:8002/api/rooms/' . $booking->room_id);

    return response()->json([
        'booking' => $booking,
        'customer' => $customer->json(),
        'room' => $room->json()
    ]);
}
}