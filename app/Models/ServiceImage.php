<?php

namespace App\Models;

use App\Actions\Images\ConvertImageToWebp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $service_id
 * @property string $image_path
 * @property int $order
 * @property bool $is_cover
 */
class ServiceImage extends Model
{
    protected $fillable = [
        'service_id',
        'image_path',
        'order',
        'is_cover',
    ];

    protected $attributes = [
        'order' => 0,
        'is_cover' => false,
    ];

    protected function casts(): array
    {
        return [
            'service_id' => 'integer',
            'order' => 'integer',
            'is_cover' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    protected static function booted(): void
    {
        static::deleted(function (ServiceImage $image): void {
            app(ConvertImageToWebp::class)->deleteSingle($image->image_path);
        });
    }
}
