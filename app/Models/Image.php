<?php

namespace App\Models;

use App\Actions\Images\ConvertImageToWebp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $imageable_type
 * @property int $imageable_id
 * @property string $thumb_path
 * @property string $full_path
 * @property string|null $alt
 * @property int $order
 */
class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'imageable_type',
        'imageable_id',
        'thumb_path',
        'full_path',
        'alt',
        'order',
    ];

    protected $attributes = [
        'order' => 0,
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Boot model hooks: clean up image assets on deletion.
     */
    protected static function booted(): void
    {
        static::deleted(function (Image $image): void {
            app(ConvertImageToWebp::class)->delete($image->thumb_path, $image->full_path);
        });
    }
}
