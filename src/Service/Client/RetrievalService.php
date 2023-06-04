<?php

namespace App\Service\Client;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Pagination\PaginationService;


class RetrievalService
{
    private UserRepository $userRepository;

    private PaginationService $paginationService;

    public function __construct(UserRepository $userRepository, PaginationService $paginationService)
    {
        $this->userRepository = $userRepository;
        $this->paginationService = $paginationService;

    }

    public function getUserList($client): array
    {
        $clientId = $client->getId();

        $offset = $this->paginationService->getOffset();
        $limit = $this->paginationService->getLimit();
        return $this->userRepository->findUsersByClientIdWithPagination($clientId, $offset, $limit);
    }

    public function getUserById(int $userId): ?User
    {
        return $this->userRepository->find($userId);
    }

}
