<?php

namespace App\Service\Product;

use App\Repository\ProductRepository;
use RuntimeException;
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
     * Serialize product list
     *
     * @param array $productList
     * @return string
     */
    public function serializeProductList(array $productList): string
    {
        try {
            return $this->serializer->serialize($productList, 'json');
        } catch (RuntimeException  $e) {
            throw new RuntimeException('An error occurred while serializing the product list: ' . $e->getMessage(), $e->getCode(), $e);
        }

    }

}
