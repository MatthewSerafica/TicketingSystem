<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignedDepartment extends Model
{
    use HasFactory;

    public function departments()
    {
        return $this->hasMany(Department::class, 'id', 'department_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'technician_id', 'technician');
    }

    protected $fillable = [
        'department_id',
        'technician',
    ];
}
