<?php

namespace App\Service\Client;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;


class RetrievalService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }

    public function getUserList(Client $client): string
    {
        return $client->getUsers();
    }

    public function getUserById(int $userId): ?User
    {
        return $this->userRepository->find($userId);
    }


}
