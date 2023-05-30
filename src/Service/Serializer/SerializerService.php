<?php

namespace App\Service\Serializer;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

class SerializerService
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;

    }

    public function serialize($object, array $groups, string $format = 'json'): string
    {
        $context = SerializationContext::create()->setGroups($groups);
        return $this->serializer->serialize($object, $format, $context);
    }


}
