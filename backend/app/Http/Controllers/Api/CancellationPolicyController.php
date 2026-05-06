<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CancellationPolicy;
use Illuminate\Http\Request;

class CancellationPolicyController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', CancellationPolicy::class);
        return response()->json(CancellationPolicy::active()->get());
    }

    public function store(Request $request)
    {
        $this->authorize('create', CancellationPolicy::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cancellation_policies,name',
            'description' => 'nullable|string',
            'free_cancellation_days' => 'required|integer|min:0',
            'penalty_percentage' => 'required|numeric|min:0|max:100',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Si se marca como default, quitar default de las demás
        if (!empty($validated['is_default'])) {
            CancellationPolicy::where('is_default', true)->update(['is_default' => false]);
        }

        $policy = CancellationPolicy::create($validated);
        return response()->json($policy, 201);
    }

    public function show(CancellationPolicy $cancellationPolicy)
    {
        $this->authorize('view', $cancellationPolicy);
        return response()->json($cancellationPolicy);
    }

    public function update(Request $request, CancellationPolicy $cancellationPolicy)
    {
        $this->authorize('update', $cancellationPolicy);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:cancellation_policies,name,' . $cancellationPolicy->id,
            'description' => 'nullable|string',
            'free_cancellation_days' => 'sometimes|integer|min:0',
            'penalty_percentage' => 'sometimes|numeric|min:0|max:100',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Si se marca como default, quitar default de las demás
        if (!empty($validated['is_default'])) {
            CancellationPolicy::where('is_default', true)
                ->where('id', '!=', $cancellationPolicy->id)
                ->update(['is_default' => false]);
        }

        $cancellationPolicy->update($validated);
        return response()->json($cancellationPolicy);
    }

    public function destroy(CancellationPolicy $cancellationPolicy)
    {
        $this->authorize('delete', $cancellationPolicy);

        // No permitir borrar si tiene alojamientos asociados
        if ($cancellationPolicy->accommodations()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar una política con alojamientos asociados'
            ], 422);
        }

        $cancellationPolicy->delete();
        return response()->json(null, 204);
    }
}