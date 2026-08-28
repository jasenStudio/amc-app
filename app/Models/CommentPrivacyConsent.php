<?php

namespace App\Models;

use Database\Factories\CommentPrivacyConsentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $commentable_type
 * @property int $commentable_id
 * @property string|null $commenter_name
 * @property string|null $commenter_email
 * @property string|null $ip
 * @property string|null $user_agent
 * @property Carbon $accepted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class CommentPrivacyConsent extends Model
{
    /** @use HasFactory<CommentPrivacyConsentFactory> */
    use HasFactory;

    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'commenter_name',
        'commenter_email',
        'ip',
        'user_agent',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
