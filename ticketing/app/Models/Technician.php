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

    public function tickets() {
        return $this->hasMany(Employee::class, 'technician_id', 'technician');
    }

    protected $fillable = [
        'technician_id',
        'user_id',
        'is_working',
        'tickets_assigned',
        'tickets_resolved',
        'status'
    ];
}
