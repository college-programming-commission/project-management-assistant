<?php

namespace Alison\ProjectManagementAssistant\Models;

use Alison\ProjectManagementAssistant\Models\Concerns\HasMarkdownFields;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperEvent
 */
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;
    use HasMarkdownFields;
    use HasUlids;

    protected $appends = ['image_url'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subevents(): HasMany
    {
        return $this->hasMany(Subevent::class, 'event_id');
    }

    public function supervisors(): HasMany
    {
        return $this->hasMany(Supervisor::class, 'event_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'event_id');
    }

    public function scopeByCategory(Builder $query, string|int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('end_date', '>=', now());
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeSearchByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'LIKE', "%{$name}%");
    }

    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }


        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }


        $path = ltrim($this->image, '/');


        if (str_starts_with($path, 'storage/')) {
            return '/' . $path;
        }


        return '/storage/' . $path;
    }

    protected function descriptionHtml(): Attribute
    {
        return $this->markdownToHtml('description');
    }

    protected function descriptionPreview(): Attribute
    {
        return $this->markdownPreview('description');
    }
}
