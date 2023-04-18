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
}
