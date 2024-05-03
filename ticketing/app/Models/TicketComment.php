<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TicketComment extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_number', 'ticket_number');
    }

    public function tagged()
    {
        return $this->hasMany(TaggedUser::class, 'comment_id', 'id');
    }
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'ticket_comments';
    protected $fillable = [
        'ticket_number',
        'parent_comment_id',
        'user_id',
        'content',
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
