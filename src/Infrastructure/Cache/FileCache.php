<?php

declare(strict_types=1);

namespace App\Infrastructure\Cache;

use App\Domain\Cache\CacheInterface;

class FileCache implements CacheInterface 
{
    public function __construct(
        private readonly string $cacheDir
    ) {
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
    }

    private function getFilePath(string $key): string
    {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }

    public function get(string $key): mixed
    {
        if (!$this->has($key)) {
            return null;
        }

        $data = unserialize(file_get_contents($this->getFilePath($key)));

        return $data['value'];
    }

    public function set(string $key, mixed $value, int $ttl = 3600): void
    {
        $data = [
            'value' => $value,
            'expires_at' => time() + $ttl
        ];
        file_put_contents($this->getFilePath($key), serialize($data));
    }

    public function has(string $key): bool
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return false;
        }

        $data = unserialize(file_get_contents($file));

        if (time() > $data['expires_at']) {
            unlink($file);
            return false;
        }

        return true;
    }
}