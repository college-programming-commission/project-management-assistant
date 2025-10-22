<?php

namespace Alison\ProjectManagementAssistant\Models;

use Alison\ProjectManagementAssistant\Services\MarkdownService;
use Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    /** @use HasFactory<MessageFactory> */
    use HasFactory, HasUlids;

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function scopeByProject(Builder $query, string|int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeBySender(Builder $query, string|int $senderId): Builder
    {
        return $query->where('sender_id', $senderId);
    }

    public function scopeByIsRead(Builder $query, bool $isRead): Builder
    {
        return $query->where('is_read', $isRead);
    }

    /**
     * Отримати HTML версію повідомлення
     */
    protected function messageHtml(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->message)) {
                    return '';
                }

                $markdownService = app(MarkdownService::class);
                return $markdownService->toHtml($this->message);
            }
        );
    }

    /**
     * Отримати попередній перегляд повідомлення
     */
    protected function messagePreview(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->message)) {
                    return '';
                }

                $markdownService = app(MarkdownService::class);
                return $markdownService->getPreview($this->message, 100);
            }
        );
    }
}
