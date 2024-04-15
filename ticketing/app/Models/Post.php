<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'posts';
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'tagged_user',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->id)) {
                $post->id = Str::uuid();
            }
        });
    }
}
