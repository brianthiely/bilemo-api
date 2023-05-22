<?php

namespace App\Service\Product;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Serializer\SerializerService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class RetrievalService
{


    private ProductRepository $productRepository;
    private PaginationService $paginationService;

    /**
     * RetrievalService constructor.
     *
     * @param ProductRepository $productRepository
     * @param PaginationService $paginationService
     */
    public function __construct(ProductRepository $productRepository, PaginationService $paginationService)
    {
        $this->productRepository = $productRepository;
        $this->paginationService = $paginationService;

    }


/**
     * Get product list
     *
     * @return array
 */
    public function getProductList(): array
    {
        $offset = $this->paginationService->getOffset();
        $limit = $this->paginationService->getLimit();
        return $this->productRepository->findAllWithPagination($offset, $limit);

    }


    public function getProductById(int $productId): ?Product
    {
       $product = $this->productRepository->find($productId);

       if (!$product) {
            throw new NotFoundHttpException('Product not found');
        }
        return $product;
    }

}
