<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketsAssigned extends Model
{
    use HasFactory;

    public function tickets() {
        return $this->hasMany(Ticket::class, 'ticket_number', 'ticket_number');
    }
    public function technician() {
        return $this->hasMany(Technician::class, 'technician_id', 'technician');
    }

    protected $fillable = [
        'ticket_number',
        'technician',
    ];
}
