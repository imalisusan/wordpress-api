<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CanCache
{
    /**
     * Cache some data
     * @param $key
     * @param $data
     *
     * @return mixed
     */
    private static function cache($key, $data = null): mixed
    {
        if(null === $data) {
            return Cache::get($key);
        }

        return Cache::remember($key, config('app.system.db_data_cache_length'), function () use ($data) {
            return $data;
        });
    }

    /**
     * Refresh cache
     *
     * @param $key
     * @return mixed
     */
    private static function clearCache($key): bool
    {
        return Cache::has($key) && Cache::forget($key);
    }
}
