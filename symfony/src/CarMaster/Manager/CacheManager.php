<?php

declare(strict_types=1);

namespace App\CarMaster\Manager;

use Psr\Cache\CacheItemPoolInterface;

class CacheManager
{
    public function __construct(private readonly CacheItemPoolInterface $cache)
    {
    }

    public function saveCache($spareParts, $value)
    {
        $partsItems = $this->cache->getItem($searchKey = 'part.search.' . $value);

        if (!$partsItems->isHit()) {
            $partsItems->set($spareParts);
            $partsItems->expiresAt(new \DateTime('+ 1 hour'));
            $this->cache->save($partsItems);

            $allKeys = $this->cache->getItem('part.search.keys');
            $keys = $allKeys->get() ?? [];
            $keys[] = $searchKey;
            $allKeys->set(array_unique($keys));
            $this->cache->save($allKeys);
        } else {
            return $partsItems->get();
        }
        return $spareParts;
    }
}