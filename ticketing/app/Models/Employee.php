<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tickets() {
        return $this->hasMany(Employee::class, 'employee_id', 'employee');
    }
    
    protected $fillable = [
        'employee_id',
        'user_id',
        'department',
        'made_ticket'
    ];

    protected $primaryKey = 'employee_id';
}
