<?php

namespace Alison\ProjectManagementAssistant\Models;

use Alison\ProjectManagementAssistant\Models\Concerns\HasMarkdownFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperSubevent
 */
class Subevent extends Model
{
    use HasFactory;
    use HasMarkdownFields;
    use HasUlids;

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function dependsOn(): BelongsTo
    {
        return $this->belongsTo(Subevent::class, 'depends_on');
    }

    public function dependentSubevents(): HasMany
    {
        return $this->hasMany(Subevent::class, 'depends_on');
    }

    public function scopeByEvent(Builder $query, string|int $eventId): Builder
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeSearchByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'LIKE', "%{$name}%");
    }

    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    public function scopeSearchByDescription(Builder $query, string $text): Builder
    {
        return $query->where('description', 'LIKE', "%{$text}%");
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
