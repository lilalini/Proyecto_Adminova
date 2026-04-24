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
use Illuminate\Support\Facades\DB;

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


    public function monthlyRevenue(Request $request)
    {
        $user = $request->user();
            if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        $query = Payment::query();

        // Filtrar según rol
        if ($user->role === 'owner') {
            $query->whereHas('booking.accommodation', fn($q) => $q->where('owner_id', $user->id));
        } elseif ($user->role === 'guest') {
            $query->where('guest_id', $user->id);
        }
        // Admin ve todos (sin filtro)

        $revenue = $query->select(
            DB::raw('EXTRACT(MONTH FROM payment_date) as month'),
            DB::raw('SUM(amount) as total')
        )
        ->whereYear('payment_date', date('Y'))
        ->whereNotNull('payment_date')
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('total', 'month')
        ->toArray();

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = $revenue[$i] ?? 0;
        }

        return response()->json($monthlyData);
    }

    public function totalRevenue(Request $request)
    {
        $user = $request->user();
        
        if (!Gate::allows('viewAny', Payment::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        
        $query = Payment::query();
        
        if ($user->role === 'owner') {
            $query->whereHas('booking.accommodation', fn($q) => $q->where('owner_id', $user->id));
        } elseif ($user->role === 'guest') {
            $query->where('guest_id', $user->id);
        }
        
        $total = $query->sum('amount');
        
        return response()->json(['total' => round($total)]);
    }

        public function revenueComparison(Request $request)
    {
        $user = $request->user();
        
        if (!Gate::allows('viewAny', Payment::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        
        $query = Payment::query();
        
        if ($user->role === 'owner') {
            $query->whereHas('booking.accommodation', fn($q) => $q->where('owner_id', $user->id));
        } elseif ($user->role === 'guest') {
            $query->where('guest_id', $user->id);
        }
        
        // Año actual (YTD)
        $currentYear = (clone $query)->whereYear('payment_date', now()->year)->sum('amount');
        
        // Año anterior
        $previousYear = (clone $query)->whereYear('payment_date', now()->subYear()->year)->sum('amount');
        
        $percentage = $previousYear > 0 
            ? round(($currentYear - $previousYear) / $previousYear * 100) 
            : ($currentYear > 0 ? 100 : 0);
        
        return response()->json([
            'percentage' => abs($percentage),
            'trend' => $percentage >= 0 ? 'up' : 'down',
            'current_year' => round($currentYear),
            'previous_year' => round($previousYear)
        ]);
    }

        public function monthlyComparison(Request $request)
    {
        $user = $request->user();
        
        if (!Gate::allows('viewAny', Payment::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        
        $query = Payment::query();
        
        if ($user->role === 'owner') {
            $query->whereHas('booking.accommodation', fn($q) => $q->where('owner_id', $user->id));
        } elseif ($user->role === 'guest') {
            $query->where('guest_id', $user->id);
        }
        
        // Mes actual
        $currentMonth = (clone $query)->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');
        
        // Mes anterior
        $previousMonth = (clone $query)->whereMonth('payment_date', now()->subMonth()->month)
            ->whereYear('payment_date', now()->subMonth()->year)
            ->sum('amount');
        
        $percentage = $previousMonth > 0 
            ? round(($currentMonth - $previousMonth) / $previousMonth * 100) 
            : ($currentMonth > 0 ? 100 : 0);
        
        return response()->json([
            'percentage' => abs($percentage),
            'trend' => $percentage >= 0 ? 'up' : 'down',
            'current_month' => round($currentMonth),
            'previous_month' => round($previousMonth)
        ]);
    }
}