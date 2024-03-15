<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Technician extends Model
{
    use HasFactory;
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assigned(): BelongsTo
    {
        return $this->belongsTo(AssignedTickets::class, 'technician');
    }

    public function tickets() {
        return $this->hasMany(Ticket::class, 'technician_id', 'technician');
    }

    public function serviceReport() {
        return $this->hasMany(ServiceReport::class, 'technician_id', 'technician');
    }

    protected $fillable = [
        'technician_id',
        'user_id',
        'is_working',
        'tickets_assigned',
        'tickets_resolved',
        'assigned_department',
        'status'
    ];

    protected $primaryKey = 'technician_id';
}
