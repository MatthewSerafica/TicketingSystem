<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    use HasFactory;

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee', 'employee_id');
    }
    
    public function technician()
    {
        
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department', 'department_id');
    }

    protected $fillable = [
        'department',
    ];
    
    protected $primaryKey = 'ticket_number';
}
