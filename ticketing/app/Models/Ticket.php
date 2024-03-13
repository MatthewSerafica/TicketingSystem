<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee', 'employee_id');
    }
    
    public function technician1(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'technician1', 'technician_id');
    }

    public function technician2(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'technician2', 'technician_id');
    }

    public function technician3(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'technician3', 'technician_id');
    }

    public function assigned()
    {
        return $this->hasMany(AssignedTickets::class, 'ticket_number', 'ticket_number');
    }

    protected $fillable = [
        'employee', 
        'rr_no', 
        'ms_no', 
        'rs_no', 
        'sr_no', 
        'issue',
        'complexity',
        'description',
        'technician1',
        'technician2',
        'technician3',
        'service',
        'status',
        'remarks',
        'resolved_at'
    ];
    
    protected $primaryKey = 'ticket_number';
}
