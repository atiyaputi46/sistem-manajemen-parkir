<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\ParkingRate;
use App\Models\ParkingSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * GET /api/available-slots
     *
     * Returns the count of available and total slots per vehicle type.
     */
    public function availableSlots(): JsonResponse
    {
        $types = ['motor', 'mobil', 'truk'];
        $result = [];

        foreach ($types as $type) {
            $result[$type] = [
                'available' => ParkingSlot::where('vehicle_type', $type)
                    ->where('status', 'available')
                    ->count(),
                'total' => ParkingSlot::where('vehicle_type', $type)->count(),
            ];
        }

        return response()->json($result);
    }

    /**
     * GET /api/rates
     *
     * Returns all parking rates.
     */
    public function rates(): JsonResponse
    {
        return response()->json(ParkingRate::all());
    }

    /**
     * POST /api/members
     *
     * Registers a new member with pending status.
     */
    public function registerMember(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'string', 'max:100'],
            'vehicle_plate' => ['required', 'string', 'max:20', 'unique:members,vehicle_plate'],
            'vehicle_type' => ['required', 'in:motor,mobil,truk'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Member::create([
            'full_name' => $request->full_name,
            'vehicle_plate' => strtoupper($request->vehicle_plate),
            'vehicle_type' => $request->vehicle_type,
            'phone' => $request->phone,
            'status' => 'pending',
        ]);

        return response()->json(
            ['message' => 'Pendaftaran berhasil. Admin akan menghubungi Anda untuk aktivasi.'],
            201,
        );
    }
}
