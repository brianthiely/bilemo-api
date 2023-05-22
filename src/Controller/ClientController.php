<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Service\Cache\CacheService;
use App\Service\Client\RetrievalService;
use App\Service\Serializer\SerializerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;


class ClientController extends AbstractController
{
    private RetrievalService $retrievalService;

    private CacheService $cacheService;

    private SerializerService $serializerService;

    public function __construct(RetrievalService $retrievalService, CacheService $cacheService, SerializerService $serializerService)
    {
        $this->retrievalService = $retrievalService;
        $this->cacheService = $cacheService;
        $this->serializerService = $serializerService;

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
            $userList = $this->retrievalService->getUserList($client);
            $jsonUserList = $this->serializerService->serialize($userList, ['user_list']);

            $expiresAt = new \DateTimeImmutable('+1 hour');
            $tags = ['usersCache'];
            $this->cacheService->cache($key, $jsonUserList, $expiresAt, $tags);
        }

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }



    /**
     * Retrieve a user by id.
     *
     * @OA\Get(
     *     path="/api/users/{userId}",
     *     summary="Retrieve a user by id",
     *     tags={"Users"},
     *     @OA\Response(
     *         response="200",
     *         description="User",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Client::class))
     *         )
     *     ),
     * )
     */
    #[Route('/api/users/{userId}', name: 'client_by_id', methods: ['GET'])]
    public function getUserById(int $userId): JsonResponse
    {
        /** @var Client $client */
        $client = $this->getUser();
        $user = $this->retrievalService->getUserById($userId);

        $users = $client->getUsers();

        if (!$users->contains($user)) {
            throw new NotFoundHttpException('User not found');
        }

        $jsonUser = $this->serializerService->serialize($user, ['user:read']);

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }
}
