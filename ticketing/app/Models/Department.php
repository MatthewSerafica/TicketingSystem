<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Department extends Model
{
    use HasFactory;
    public function assigned()
    {
        return $this->hasMany(AssignedDepartment::class, 'department_id', 'id');
    }

    protected $fillable = [
        'department',
    ];
}
