<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CleaningTask extends Model
{
    /** @use HasFactory<\Database\Factories\CleaningTaskFactory> */
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'accommodation_id',
        'booking_id',
        'assigned_to_user_id', // personal de limpieza
        'created_by_user_id', // quien creó la tarea
        'task_type', // cleaning, maintenance, inspection, laundry
        'priority', // low, medium, high, urgent
        'title',
        'description',
        'checklist', // JSON: ["cambiar sábanas", "limpiar baño", etc.]
        'scheduled_date',
        'completed_at',
        'duration_minutes', // tiempo estimado
        'photos', // JSON: antes/después
        'notes',
        'status', // pending, in_progress, completed, cancelled, verified
        'verified_by_user_id',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'checklist' => 'array',
            'photos' => 'array',
            'scheduled_date' => 'datetime',
            'completed_at' => 'datetime',
            'duration_minutes' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    // Relaciones
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForToday($query)
    {
        return $query->whereDate('scheduled_date', now());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('assigned_to_user_id', $userId);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Métodos
    public function assignTo($userId)
    {
        $this->update([
            'assigned_to_user_id' => $userId,
            'status' => 'pending',
        ]);
    }

    public function start()
    {
        $this->update([
            'status' => 'in_progress',
        ]);
    }

    public function complete($notes = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'notes' => $notes ? $this->notes . "\n" . $notes : $this->notes,
        ]);
    }

    public function verify($userId)
    {
        $this->update([
            'status' => 'verified',
            'verified_by_user_id' => $userId,
            'verified_at' => now(),
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason ? $this->notes . "\nCancelado: " . $reason : $this->notes,
        ]);
    }

    public function addPhoto($path)
    {
        $photos = $this->photos ?? [];
        $photos[] = $path;
        $this->update(['photos' => $photos]);
    }

    public function updateChecklist($item, $completed)
    {
        $checklist = $this->checklist ?? [];
        $checklist[$item] = $completed;
        $this->update(['checklist' => $checklist]);
    }
}
