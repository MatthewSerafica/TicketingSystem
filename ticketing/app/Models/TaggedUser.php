<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaggedUser extends Model
{
    use HasFactory;

    public function post(): HasMany
    {
        return $this->hasMany(Post::class, 'post_id');
    }
    public function comment(): HasMany
    {
        return $this->hasMany(Comment::class, 'comment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    protected $table = 'tagged_users';

    protected $fillable = [
        'post_id',
        'comment_id',
        'user_id',
    ];
}
