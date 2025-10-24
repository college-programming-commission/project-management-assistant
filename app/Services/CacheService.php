<?php

namespace Alison\ProjectManagementAssistant\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function rememberForUser(string $key, int $userId, $ttl, callable $callback)
    {
        $cacheKey = "{$key}_user_{$userId}";
        return Cache::remember($cacheKey, $ttl, $callback);
    }

    public function rememberWithQuery(string $key, array $query, int $page, int $userId, $ttl, callable $callback)
    {
        $cacheKey = "{$key}_" . md5(json_encode($query) . "_page_{$page}_user_{$userId}");
        return Cache::remember($cacheKey, $ttl, $callback);
    }

    public function forget(string $key, ?int $userId = null): void
    {
        if ($userId) {
            $cacheKey = "{$key}_user_{$userId}";
            Cache::forget($cacheKey);
        } else {
            Cache::forget($key);
        }
    }

    public function forgetPattern(string $pattern): void
    {
        Cache::forget($pattern);
    }
}
