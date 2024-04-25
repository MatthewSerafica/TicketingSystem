<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Comment extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function tagged()
    {
        return $this->hasMany(TaggedUser::class, 'comment_id', 'id');
    }

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'comments';
    protected $fillable = [
        'post_id',
        'parent_comment_id',
        'user_id',
        'content',
        'tagged_user',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($comment) {
            if (empty($comment->id)) {
                $comment->id = Str::uuid();
            }
        });
    }
}
