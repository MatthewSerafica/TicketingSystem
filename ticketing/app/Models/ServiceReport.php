<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceReport extends Model
{
    use HasFactory;



    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'technicians_id', 'technician_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_number', 'ticket_number');
    }

    protected $fillable = [
        'service_id',
        'technicians_id',
        'date_started',
        'time_started',
        'ticket_number',
        'technician_name',
        'requesting_office',
        'equipment_no',
        'issue',
        'action',
        'recommendation',
        'date_done',
        'time_done',
        'remarks',
    ];
    
    protected $primaryKey = 'service_id';
}
