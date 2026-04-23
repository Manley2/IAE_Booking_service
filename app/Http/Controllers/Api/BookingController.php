<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookingController extends Controller
{
    public function index(): JsonResponse
    {
        $bookings = Booking::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar booking berhasil diambil',
            'data' => $bookings
        ], 200);
    }

    public function show($id): JsonResponse
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail booking berhasil diambil',
            'data' => $booking
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'room_id' => 'required|integer',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after_or_equal:check_in',
            'status' => 'required|string|in:confirmed,cancelled'
        ]);

        $roomServiceUrl = env('ROOM_SERVICE_URL', 'http://127.0.0.1:8002');

        try {
            $roomResponse = Http::get($roomServiceUrl . '/api/rooms/' . $validated['room_id']);

            if (!$roomResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room tidak ditemukan di room-service'
                ], 404);
            }

            $roomData = $roomResponse->json('data');

            if (($roomData['status'] ?? null) !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamar tidak tersedia untuk dibooking'
                ], 400);
            }

            $booking = Booking::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil disimpan',
                'room' => $roomData,
                'data' => $booking
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Room service tidak dapat diakses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'room_id' => 'required|integer',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after_or_equal:check_in',
            'status' => 'required|string|in:confirmed,cancelled'
        ]);

        $roomServiceUrl = env('ROOM_SERVICE_URL', 'http://127.0.0.1:8002');

        try {
            $roomResponse = Http::get($roomServiceUrl . '/api/rooms/' . $validated['room_id']);

            if (!$roomResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room tidak ditemukan di room-service'
                ], 404);
            }

            $booking->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil diperbarui',
                'data' => $booking
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Room service tidak dapat diakses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dihapus'
        ], 200);
    }

    public function room($id): JsonResponse
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        $roomServiceUrl = env('ROOM_SERVICE_URL', 'http://127.0.0.1:8002');

        try {
            $response = Http::get($roomServiceUrl . '/api/rooms/' . $booking->room_id);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data room dari room-service'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data room dari booking berhasil diambil',
                'booking' => $booking,
                'room' => $response->json('data')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Room service tidak dapat diakses',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
