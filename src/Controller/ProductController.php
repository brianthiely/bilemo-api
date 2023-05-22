<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\Cache\CacheService;
use App\Service\Product\PaginationService;
use App\Service\Product\RetrievalService;
use App\Service\Serializer\SerializerService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{


    private RetrievalService $retrievalService;

    private PaginationService $paginationService;

    private CacheService $cacheService;

    private SerializerService $serializerService;

    /**
     * Inject services into the controller.
     *
     * @param RetrievalService $retrievalService
     * @param PaginationService $paginationService
     * @param CacheService $cacheService
     */
    public function __construct(RetrievalService $retrievalService, PaginationService $paginationService, CacheService $cacheService, SerializerService $serializerService)
    {
        $this->retrievalService = $retrievalService;
        $this->paginationService = $paginationService;
        $this->cacheService = $cacheService;
        $this->serializerService = $serializerService;

    }


    /**
     * Retrieve a paginated list of all products.
     *
     * @OA\Get(
     *     path="/api/products",
     *     summary="Retrieve a paginated list of all products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response="200",
     *         description="List of products",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Product::class))
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Page number to retrieve",
     *         required=false,
     *         @OA\Schema(type="integer", default="1")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of products to retrieve per page",
     *         required=false,
     *         @OA\Schema(type="integer", default="3")
     *     ),
     *     @OA\Response(response="401", description="Unauthorized"),
     * )
     *
     *
     * @return JsonResponse
     * @throws BadRequestHttpException
     */
    #[Route('/api/products', name: 'get_products_paginated', methods: ['GET'])]
    public function getAllProducts(): JsonResponse
    {
        $offset = $this->paginationService->getOffset();
        $limit = $this->paginationService->getLimit();

        $key = "product_list_{$offset}_{$limit}";
        $jsonProductList = $this->cacheService->get($key);

        if ($jsonProductList === null) {
            $productList = $this->retrievalService->getProductList();
            $jsonProductList = $this->serializerService->serialize($productList, ['products:read']);

            $expiresAt = new \DateTimeImmutable('+1 hour');
            $tags = ['productsCache'];
            $this->cacheService->cache($key, $jsonProductList, $expiresAt, $tags);
        }

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }



    /**
     * Retrieve a product by its id.
     *
     * @OA\Get(
     *     path="/api/products/{productId}",
     *     summary="Retrieve a product by its id",
     *     tags={"Products"},
     *     @OA\Response(
     *         response="200",
     *         description="Product",
     *         @OA\JsonContent(ref=@Model(type=Product::class))
     *     ),
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         description="Product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="404", description="Product not found")
     * )
     *
     *
     * @param int $productId
     * @return JsonResponse
     * @throws BadRequestHttpException
     *
     */
    #[Route('/api/products/{productId}', name: 'get_product_by_id', methods: ['GET'])]
    public function getProductById(int $productId): JsonResponse
    {
        $key = "product_{$productId}";
        $jsonProduct = $this->cacheService->get($key);

        if ($jsonProduct === null) {
            $product = $this->retrievalService->getProductById($productId);
            $jsonProduct = $this->serializerService->serialize($product, ['products:read']);

            $expiresAt = new \DateTimeImmutable('+1 hour');
            $tags = ['productsCache'];
            $this->cacheService->cache($key, $jsonProduct, $expiresAt, $tags);
        }

        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }

}
