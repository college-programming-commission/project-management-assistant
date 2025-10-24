<?php

namespace Alison\ProjectManagementAssistant\Models;

use Alison\ProjectManagementAssistant\Models\Concerns\HasMarkdownFields;
use Database\Factories\SubjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperSubject
 */
class Subject extends Model
{
    /** @use HasFactory<SubjectFactory> */
    use HasFactory;
    use HasMarkdownFields;
    use HasUlids;

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_subject');
    }

    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'ILIKE', "%$name%");
    }

    public function scopeByCourse(Builder $query, int $course): Builder
    {
        return $query->where('course_number', $course);
    }

    public function scopeSearchByDescription(Builder $query, string $text): Builder
    {
        return $query->where('description', 'LIKE', "%{$text}%");
    }

    public function scopeSearchByName(Builder $query, string $text): Builder
    {
        return $query->where('name', 'LIKE', "%{$text}%");
    }

    public function scopeByCategory(Builder $query, string|int $categoryId): Builder
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
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
