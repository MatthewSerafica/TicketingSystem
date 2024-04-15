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

    public function assigned()
    {
        return $this->hasMany(AssignedTickets::class, 'ticket_number', 'ticket_number');
    }

    public function history()
    {
        return $this->hasOne(HistoryNumber::class, 'ticket_number', 'ticket_number');
    }

    protected $fillable = [
        'employee', 
        'request_type',
        'rr_no', 
        'ms_no', 
        'rs_no', 
        'sr_no', 
        'issue',
        'complexity',
        'description',
        'service',
        'status',
        'remarks',
        'resolved_at'
    ];
    
    protected $primaryKey = 'ticket_number';
}
