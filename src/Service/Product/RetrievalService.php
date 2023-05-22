<?php

namespace App\Service\Product;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Serializer\SerializerService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;


class RetrievalService
{


    private ProductRepository $productRepository;
    private PaginationService $paginationService;
    private SerializerService $serializerService;


    /**
     * RetrievalService constructor.
     *
     * @param ProductRepository $productRepository
     * @param PaginationService $paginationService
     */
    public function __construct(ProductRepository $productRepository, PaginationService $paginationService, SerializerService $serializerService)
    {
        $this->productRepository = $productRepository;
        $this->paginationService = $paginationService;
        $this->serializerService = $serializerService;

    }


    /**
     * Get product list
     *
     * @return array
     */
    public function getProductList(): string
    {
        $offset = $this->paginationService->getOffset();
        $limit = $this->paginationService->getLimit();
        $productList = $this->productRepository->findAllWithPagination($offset, $limit);

        return $this->serializerService->serialize($productList, ['products:read']);
    }


    /**
     * Get product by ID
     *
     * @param int $productId
     * @return string|null
     */
    public function getProductById(int $productId): ?string
    {
        $product = $this->productRepository->find($productId);
        if (!$product) {
            return null;
        }
        return $this->serializerService->serialize($product, ['products:read']);
    }

}
