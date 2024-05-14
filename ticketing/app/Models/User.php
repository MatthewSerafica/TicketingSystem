<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    public function technician(): HasOne
    {
        return $this->hasOne(Technician::class, 'user_id');
    }

    public function post(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function comment(): HasMany
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function tagged(): HasMany
    {
        return $this->hasMany(TaggedUser::class, 'user_id');
    }
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin' || $this->user_type === 'super';
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'user_type',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function avatar(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset(Storage::url($value) ?? 'NoImage.png'),
        );
    }
}
