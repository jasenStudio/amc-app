<?php

namespace App\Models;

use App\Actions\Images\ConvertImageToWebp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $project_id
 * @property string $image_path
 * @property int $order
 * @property bool $is_cover
 */
class ProjectImage extends Model
{
    protected $fillable = [
        'project_id',
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
            'project_id' => 'integer',
            'order' => 'integer',
            'is_cover' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    protected static function booted(): void
    {
        static::deleted(function (ProjectImage $image): void {
            app(ConvertImageToWebp::class)->deleteSingle($image->image_path);
        });
    }
}
