<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Service\Cache\CacheService;
use App\Service\Client\RetrievalService;
use App\Service\Pagination\PaginationService;
use App\Service\Serializer\SerializerService;
use App\Service\User\UserManager;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ClientController extends AbstractController
{
    private RetrievalService $retrievalService;

    private CacheService $cacheService;

    private SerializerService $serializerService;

    private UserManager $userManager;

    private ValidatorInterface $validator;

    private PaginationService $paginationService;

    public function __construct(RetrievalService $retrievalService, CacheService $cacheService, SerializerService $serializerService, UserManager $userManager, ValidatorInterface $validator, PaginationService $paginationService)
    {
        $this->retrievalService = $retrievalService;
        $this->cacheService = $cacheService;
        $this->serializerService = $serializerService;
        $this->userManager = $userManager;
        $this->validator = $validator;
        $this->paginationService = $paginationService;

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
     *             @OA\Items(ref=@Model(type=User::class))
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Page number to retrieve",
     *         required=false,
     *         @OA\Schema(type="integer", default="1")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of users to retrieve per page",
     *         required=false,
     *         @OA\Schema(type="integer", default="3")
     *     ),
     *     @OA\Response(response="401", description="Unauthorized"),
     * )
     *
     */
    #[Route('/api/users', name: 'get_users', methods: ['GET'])]
    public function getAllUsers(): JsonResponse
    {
        $offset = $this->paginationService->getOffset();
        $limit = $this->paginationService->getLimit();

        $key = "user_list_{$offset}_{$limit}";
        $jsonUserList = $this->cacheService->get($key);

        if ($jsonUserList === null) {
            /** @var Client $client */
            $client = $this->getUser();
            $userList = $this->retrievalService->getUserList($client);
            $jsonUserList = $this->serializerService->serialize($userList, ['users:read']);

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
     *             @OA\Items(ref=@Model(type=User::class))
     *         )
     *     ),
     * )
     */
    #[Route('/api/users/{userId}', name: 'get_user_by_id', methods: ['GET'])]
    public function getUserById(int $userId): JsonResponse
    {
        /** @var Client $client */
        $client = $this->getUser();

        if ($client === null) {
            throw new AccessDeniedHttpException("Access denied");
        }

        $users = $client->getUsers();

        $key = "user{$userId}";
        $jsonUser = $this->cacheService->get($key);

        if ($jsonUser === null) {
            $user = $this->retrievalService->getUserById($userId);

            if (!$users->contains($user)) {
                throw new NotFoundHttpException("User not found");
            }

            $jsonUser = $this->serializerService->serialize($user, ['user:read']);

            $expiresAt = new \DateTimeImmutable('+1 hour');
            $tags = ['usersCache'];
            $this->cacheService->cache($key, $jsonUser, $expiresAt, $tags);
        }

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }


    /**
     * Add a user.
     *
     * @OA\Post(
     *     path="/api/users",
     *     summary="Add a user",
     *     tags={"Users"},
     *     @OA\Response(
     *         response="200",
     *         description="User",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=User::class))
     *         )
     *     ),
     *     @OA\RequestBody(
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="firstname", type="string"),
     *     @OA\Property(property="lastname", type="string"),
     *
     *     )
     *    )
     *
     * )
     * @throws InvalidArgumentException
     */
    #[Route('/api/users', name: 'add_user', methods: ['POST'])]
    public function addUser(Request $request): JsonResponse
    {
        /** @var Client $client */
        $client = $this->getUser();

        $form = $this->serializerService->deserialize($request->getContent(), User::class);

        $errors = $this->validator->validate($form);
        if ($errors->count() > 0) {
            return new JsonResponse($this->serializerService->serialize($errors, ['errors:read']), Response::HTTP_BAD_REQUEST, [], true);
        }

        $savedUser = $this->userManager->saveUser($form, $client);

        $client->addUser($savedUser);

        $offset = $this->paginationService->getOffset();
        $limit = $this->paginationService->getLimit();

        $key = "user_list_{$offset}_{$limit}";
        $this->cacheService->delete($key);

        $jsonUser = $this->serializerService->serialize($form, ['users:read']);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true);
    }


    /**
     * Delete a user.
     *
     * @OA\Delete(
     *     path="/api/users/{userId}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     @OA\Response(
     *     response="204",
     *     description="User deleted"
     *    )
     * )
     *
     *
     * @param int $userId
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    #[Route('/api/users/{userId}', name: 'delete_user_by_id', methods: ['DELETE'])]
    public function deleteUser(int $userId): JsonResponse
    {
        /** @var Client $client */
       $client = $this->getUser();
       $users = $client->getUsers();

       $user = $this->cacheService->get("user{$userId}");

         if ($user === null) {
              $user = $this->retrievalService->getUserById($userId);
         }

       if ($users->contains($user)) {
           $this->userManager->deleteUser($user);
       } else {
           throw new NotFoundHttpException("User not found");
       }

         $key = "user{$userId}";
         $this->cacheService->delete($key);

         return new JsonResponse(null, Response::HTTP_NO_CONTENT);

    }

}
