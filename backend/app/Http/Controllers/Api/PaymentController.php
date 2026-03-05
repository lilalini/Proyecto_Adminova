<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('viewAny', Payment::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user = $request->user();
        $query = Payment::with(['booking', 'guest', 'user']);

        if ($user->role === 'owner') {
            $query->whereHas('booking.accommodation', fn($q) => $q->where('owner_id', $user->id));
        } elseif ($user->role === 'guest') {
            $query->where('guest_id', $user->id);
        }

        $payments = $query->paginate(15);
        return PaymentResource::collection($payments);
    }

    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();
        $data['payment_reference'] = 'PAY-' . strtoupper(Str::random(10));
        $data['user_id'] = $request->user()->id;

        $payment = Payment::create($data);
        $payment->load(['booking', 'guest', 'user']);

        return new PaymentResource($payment);
    }

    public function show(Payment $payment)
    {
        if (!Gate::allows('view', $payment)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $payment->load(['booking', 'guest', 'user']);
        return new PaymentResource($payment);
    }

    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $payment->update($request->validated());
        $payment->load(['booking', 'guest', 'user']);

        return new PaymentResource($payment);
    }

    public function destroy(Payment $payment)
    {
        if (!Gate::allows('delete', $payment)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $payment->delete();
        return response()->json(null, 204);
    }
}