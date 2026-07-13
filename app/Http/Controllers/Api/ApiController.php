<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\ParkingRate;
use App\Models\ParkingSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controller API publik untuk integrasi eksternal (Landing Page).
 * Menyediakan endpoint untuk mengambil info kapasitas slot kosong, daftar tarif aktif,
 * dan memproses pendaftaran online member baru (langganan).
 */
class ApiController extends Controller
{
    /**
     * GET /api/available-slots
     *
     * Mengembalikan jumlah slot parkir yang kosong (available) dan total slot keseluruhan per jenis kendaraan.
     */
    public function availableSlots(): JsonResponse
    {
        $types = ['motor', 'mobil', 'truk'];
        $result = [];

        // Looping untuk menghitung slot masing-masing tipe kendaraan
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
     * Mengembalikan semua konfigurasi tarif parkir aktif per jenis kendaraan dari database.
     */
    public function rates(): JsonResponse
    {
        return response()->json(ParkingRate::all());
    }

    /**
     * POST /api/members
     *
     * Menerima kiriman pendaftaran member langganan baru dari landing page publik dengan status awal 'pending'.
     *
     * @param  Request  $request  Request HTTP berisi data diri member baru
     */
    public function registerMember(Request $request): JsonResponse
    {
        // Validasi input data member baru
        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'string', 'max:100'],
            'vehicle_plate' => ['required', 'string', 'max:20', 'unique:members,vehicle_plate'],
            'vehicle_type' => ['required', 'in:motor,mobil,truk'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
        ]);

        // Kembalikan response error 422 jika validasi gagal
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Simpan pendaftaran member baru di database dengan status pending
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
