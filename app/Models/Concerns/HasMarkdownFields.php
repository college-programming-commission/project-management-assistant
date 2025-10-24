<?php

namespace Alison\ProjectManagementAssistant\Models\Concerns;

use Alison\ProjectManagementAssistant\Services\MarkdownService;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasMarkdownFields
{
    protected function markdownToHtml(string $field): Attribute
    {
        return Attribute::make(
            get: function () use ($field) {
                if (empty($this->$field)) {
                    return '';
                }

                return app(MarkdownService::class)->toHtml($this->$field);
            }
        );
    }

    protected function markdownPreview(string $field, int $length = 150): Attribute
    {
        return Attribute::make(
            get: function () use ($field) {
                if (empty($this->$field)) {
                    return '';
                }

                return app(MarkdownService::class)->getPreview($this->$field, $length);
            }
        );
    }
}
