<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoryNumber extends Model
{
    use HasFactory;

    
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_number', 'ticket_number');
    }

    protected $fillable = [
        'ticket_number', 
        'rr_no', 
        'ms_no', 
        'rs_no', 
        'sr_no', 
    ];
}
