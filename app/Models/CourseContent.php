<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CourseDescription;

/**
 * @property int $id
 * @property int $course_description_id
 * @property string $course_title
 * @property string $slug
 * @property array<array-key, mixed> $materials
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read CourseDescription $courseDescription
 * @property-read int $materials_count
 * @property-read array $sorted_materials
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseContent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseContent whereCourseDescriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseContent whereCourseTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseContent whereMaterials($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseContent whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseContent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CourseContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_description_id',
        'course_title',
        'slug',
        'materials',
    ];

    protected $casts = [
        'materials' => 'array', // Cast materials to array automatically
    ];

    /**
     * Get the course description that owns the course content.
     */
    public function courseDescription(): BelongsTo
    {
        return $this->belongsTo(CourseDescription::class, 'course_description_id');
    }

    /**
     * Get materials count attribute
     */
    public function getMaterialsCountAttribute(): int
    {
        return is_array($this->materials) ? count($this->materials) : 0;
    }

    /**
     * Get sorted materials by urutan
     */
    public function getSortedMaterialsAttribute(): array
    {
        if (!is_array($this->materials)) {
            return [];
        }

        return collect($this->materials)
            ->sortBy('urutan')
            ->values()
            ->toArray();
    }

    /**
     * Get a specific material by order
     */
    public function getMaterialByOrder(int $order): ?array
    {
        if (!is_array($this->materials)) {
            return null;
        }

        return collect($this->materials)
            ->firstWhere('urutan', $order);
    }

    /**
     * Get material by index
     */
    public function getMaterialByIndex(int $index): ?array
    {
        if (!is_array($this->materials) || !isset($this->materials[$index])) {
            return null;
        }

        return $this->materials[$index];
    }

    /**
     * Search materials by title
     */
    public function searchMaterials(string $query): array
    {
        if (!is_array($this->materials)) {
            return [];
        }

        return collect($this->materials)
            ->filter(function ($material) use ($query) {
                return stripos($material['judul'] ?? '', $query) !== false;
            })
            ->values()
            ->toArray();
    }
}
