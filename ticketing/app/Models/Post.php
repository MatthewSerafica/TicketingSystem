<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comment(): HasMany 
    {
        return $this->hasMany(Comment::class, 'post_id');
    }
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'tagged_user',
    ];
}
