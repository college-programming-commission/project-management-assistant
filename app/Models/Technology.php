<?php

namespace Alison\ProjectManagementAssistant\Models;

use Alison\ProjectManagementAssistant\Models\Concerns\HasMarkdownFields;
use Database\Factories\TechnologyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperTechnology
 */
class Technology extends Model
{
    /** @use HasFactory<TechnologyFactory> */
    use HasFactory;
    use HasMarkdownFields;
    use HasUlids;

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_technology');
    }

    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'ILIKE', "%$name%");
    }

    public function scopeByProject(Builder $query, string|int $projectId): Builder
    {
        return $query->whereHas('projects', function ($q) use ($projectId) {
            $q->where('projects.id', $projectId);
        });
    }

    public function scopeWithLink(Builder $query): Builder
    {
        return $query->whereNotNull('link')->where('link', '!=', '');
    }

    public function scopeWithoutLink(Builder $query): Builder
    {
        return $query->whereNull('link')->orWhere('link', '');
    }

    public function scopeSearchByDescription(Builder $query, string $text): Builder
    {
        return $query->where('description', 'ILIKE', "%$text%");
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
