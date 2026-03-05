<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Commission extends Model
{
    /** @use HasFactory<\Database\Factories\CommissionFactory> */
    use HasFactory;

     protected $fillable = [
        'booking_id',
        'channel_id',
        'accommodation_id',
        'owner_id',
        'commission_type', // channel, platform, owner
        'rate',
        'amount',
        'currency',
        'status', // pending, calculated, invoiced, paid
        'invoice_number',
        'invoice_date',
        'due_date',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'amount' => 'decimal:2',
            'invoice_date' => 'date',
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    // Relaciones
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function channel()
    {
        return $this->belongsTo(DistributionChannel::class);
    }

    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'calculated']);
    }

    public function scopeInvoiced($query)
    {
        return $query->where('status', 'invoiced');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeForOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeForPeriod($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    // Métodos
    public function markAsInvoiced($invoiceNumber = null)
    {
        $this->update([
            'status' => 'invoiced',
            'invoice_number' => $invoiceNumber ?? $this->generateInvoiceNumber(),
            'invoice_date' => now(),
        ]);
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    protected function generateInvoiceNumber()
    {
        return 'COM-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    // Estadísticas
    public static function totalPendingForOwner($ownerId)
    {
        return self::forOwner($ownerId)
            ->pending()
            ->sum('amount');
    }

    public static function totalPaidForOwner($ownerId)
    {
        return self::forOwner($ownerId)
            ->paid()
            ->sum('amount');
    }
}
