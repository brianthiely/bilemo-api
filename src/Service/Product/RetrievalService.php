<?php

namespace App\Service\Product;

use App\Entity\Product;
use App\Repository\ProductRepository;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class RetrievalService
{


    private ProductRepository $productRepository;

    private SerializerInterface $serializer;

    private PaginationService $paginationService;


    /**
     * RetrievalService constructor.
     *
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @param PaginationService $paginationService
     */
    public function __construct(ProductRepository $productRepository, SerializerInterface $serializer, PaginationService $paginationService)
    {
        $this->productRepository = $productRepository;
        $this->serializer = $serializer;
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


    /**
     * Get product by ID
     *
     * @param int $productId
     * @return object|null
     */
    public function getProductById(int $productId): ?object
    {
        $product = $this->productRepository->find($productId);
        if (!$product) {
            return null;
        }
        return $product;
    }


    /**
     * Serialize product list
     *
     * @param array $productList
     * @return string
     */
    public function serializeProductList(array $productList): string
    {
        return $this->serializer->serialize($productList, 'json');

    }


    /**
     * Serialize product
     *
     * @param Product $product
     * @return string
     */
    public function serializeProduct(Product $product): string
    {
            return $this->serializer->serialize($product, 'json');

    }

}
