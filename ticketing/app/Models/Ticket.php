<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee', 'employee_id');
    }
    
    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'technician', 'technician_id');
    }

    protected $fillable = [
        'employee',
        'issue',
        'description',
        'technician',
        'service',
        'status',
        'resolved_at'
    ];
    
    protected $primaryKey = 'ticket_number';
}
