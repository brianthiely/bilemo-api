<?php

namespace App\Service\Client;

use App\Entity\Client;
use App\Service\Serializer\SerializerService;


class RetrievalService
{
    private SerializerService $serializerService;

    public function __construct(SerializerService $serializerService)
    {
        $this->serializerService = $serializerService;

    }

    public function getUserList(Client $client): string
    {
        $userList = $client->getUsers();
        return $this->serializerService->serialize($userList, ['users:read']);
    }

}
