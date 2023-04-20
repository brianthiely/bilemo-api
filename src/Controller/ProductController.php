<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\Product\CacheService;
use App\Service\Product\PaginationService;
use App\Service\Product\RetrievalService;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{


    private RetrievalService $retrievalService;

    private PaginationService $paginationService;

    private CacheService $cacheService;


    /**
     * Inject services into the controller.
     *
     * @param RetrievalService $retrievalService
     * @param PaginationService $paginationService
     * @param CacheService $cacheService
     */
    public function __construct(RetrievalService $retrievalService, PaginationService $paginationService, CacheService $cacheService)
    {
        $this->retrievalService = $retrievalService;
        $this->paginationService = $paginationService;
        $this->cacheService = $cacheService;
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
     *     @OA\Response(response="400", description="Bad request")
     * )
     *
     *
     * @return JsonResponse
     * @throws BadRequestHttpException
     */
    #[Route('/api/products', name: 'get_products_paginated', methods: ['GET'])]
    public function getAllProducts(): JsonResponse
    {
        $this->paginationService->getOffset();
        $this->paginationService->getLimit();
        $productList = $this->retrievalService->getProductList();
        $jsonProductList = $this->retrievalService->serializeProductList($productList);
        $this->cacheService->cacheProductList($jsonProductList);

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
     */
    #[Route('/api/products/{productId}', name: 'get_product_by_id', methods: ['GET'])]
    public function getProductById(int $productId): JsonResponse|NotFoundHttpException
    {
        $product = $this->retrievalService->getProductById($productId);
        if (!$product) {
            return new NotFoundHttpException('Product not found');
        }

        $jsonProduct = $this->retrievalService->serializeProduct($product);
        $this->cacheService->cacheProduct($jsonProduct);

        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }

}
