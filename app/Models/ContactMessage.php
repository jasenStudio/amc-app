<?php

namespace App\Models;

use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $company
 * @property int|null $service_id
 * @property string $message
 * @property string|null $ip
 * @property string|null $user_agent
 * @property Carbon|null $privacy_accepted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'company',
        'service_id',
        'message',
        'ip',
        'user_agent',
        'privacy_accepted_at',
    ];

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
