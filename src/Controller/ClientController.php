<?php

namespace App\Controller;

use App\Entity\Client;
use App\Service\Cache\CacheService;
use App\Service\Client\RetrievalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;


class ClientController extends AbstractController
{
    private RetrievalService $retrievalService;

    private CacheService $cacheService;

    public function __construct(RetrievalService $retrievalService, CacheService $cacheService)
    {
        $this->retrievalService = $retrievalService;
        $this->cacheService = $cacheService;

    }


    /**
     * Retrieve a list of all users for a client.
     *
     * @OA\Get(
     *     path="/api/users",
     *     summary="Retrieve a list of all users for a client",
     *     tags={"Users"},
     *     @OA\Response(
     *         response="200",
     *         description="List of users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Client::class))
     *         )
     *     ),
     * )
     */
    #[Route('/api/users', name: 'client', methods: ['GET'])]
    public function getAllUsers(): JsonResponse
    {
        /** @var Client $client */
        $client = $this->getUser();
        $clientId = $client->getId();

        $key = "user_list{$clientId}";
        $jsonUserList = $this->cacheService->get($key);

        if ($jsonUserList === null) {
            $client = $this->getUser();

            /** @var Client $client */
            $jsonUserList = $this->retrievalService->getUserList($client);

            $expiresAt = new \DateTimeImmutable('+1 hour');
            $tags = ['usersCache'];
            $this->cacheService->cache($key, $jsonUserList, $expiresAt, $tags);
        }

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }
}
