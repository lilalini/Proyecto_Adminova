<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoyaltySetting;
use Illuminate\Http\Request;

class LoyaltySettingController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', LoyaltySetting::class);
        return response()->json(LoyaltySetting::orderBy('id')->get());
    }

    public function current()
    {
        $setting = LoyaltySetting::current()->first();
        if (!$setting) {
            return response()->json(['message' => 'No hay configuración activa'], 404);
        }
        return response()->json($setting);
    }

    public function store(Request $request)
    {
        $this->authorize('create', LoyaltySetting::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'points_per_currency' => 'required|numeric|min:0',
            'points_to_currency_ratio' => 'required|numeric|min:0',
            'min_redemption' => 'required|integer|min:0',
            'expiry_days' => 'required|integer|min:1',
            'max_discount' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
        ]);

        return response()->json(LoyaltySetting::create($validated), 201);
    }

    public function update(Request $request, LoyaltySetting $loyaltySetting)
    {
        $this->authorize('update', $loyaltySetting);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'points_per_currency' => 'sometimes|numeric|min:0',
            'points_to_currency_ratio' => 'sometimes|numeric|min:0',
            'min_redemption' => 'sometimes|integer|min:0',
            'expiry_days' => 'sometimes|integer|min:1',
            'max_discount' => 'sometimes|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
        ]);

        $loyaltySetting->update($validated);
        return response()->json($loyaltySetting);
    }

    public function destroy(LoyaltySetting $loyaltySetting)
    {
        $this->authorize('delete', $loyaltySetting);
        $loyaltySetting->delete();
        return response()->json(null, 204);
    }
}