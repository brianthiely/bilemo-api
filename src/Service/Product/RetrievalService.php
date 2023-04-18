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

    public function __construct(ProductRepository $productRepository, SerializerInterface $serializer, PaginationService $paginationService)
    {
        $this->productRepository = $productRepository;
        $this->serializer = $serializer;
        $this->paginationService = $paginationService;
    }
    public function getProductList(): array
    {
        $offset = $this->paginationService->getOffset();
        $limit = $this->paginationService->getLimit();
        return $this->productRepository->findAllWithPagination($offset, $limit);
    }

    public function serializeProductList(array $productList): string
    {
        try {
            return $this->serializer->serialize($productList, 'json');
        } catch (RuntimeException  $e) {
            throw new RuntimeException ('Unable to serialize product list');
        }
    }
}
