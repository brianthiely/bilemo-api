<?php

namespace App\Service\Product;

use Symfony\Contracts\Cache\TagAwareCacheInterface;

class CacheService
{


    private PaginationService $paginationService;

    private TagAwareCacheInterface $cache;


    /**
     * CacheService constructor.
     *
     * @param PaginationService $paginationService
     * @param TagAwareCacheInterface $cache
     */
    public function __construct(PaginationService $paginationService, TagAwareCacheInterface $cache)
    {
        $this->paginationService = $paginationService;
        $this->cache = $cache;

    }


    /**
     * Get cached product list
     *
     * @param string $jsonProductList
     * @return void
     */
    public function cacheProductList(string $jsonProductList): void
    {
        $offset = $this->paginationService->getOffset();
        $limit = $this->paginationService->getLimit();

        $cacheKey = "product_list_{$offset}_{$limit}";
        $item = $this->cache->getItem($cacheKey);
        $item->tag("productsCache");

        $expiresAt = new \DateTimeImmutable('+1 hour');
        $item->set($jsonProductList);
        $item->expiresAt($expiresAt);

        $this->cache->save($item);

    }

    public function cacheProduct(string $jsonProduct): void
    {
        $productData = json_decode($jsonProduct, true);
        $productId = $productData['productId'] ?? null;

        $item = $this->cache->getItem("product_{$productId}");
        $item->tag("productsCache");

        $expiresAt = new \DateTimeImmutable('+1 hour');
        $item->set($jsonProduct);
        $item->expiresAt($expiresAt);

        $this->cache->save($item);
    }


}
