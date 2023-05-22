<?php

namespace App\Service\Serializer;

use JMS\Serializer\SerializationContext;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerService
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;

    }

    public function serialize($object, array $groups, string $format = 'json'): string
    {
        $context ??= new SerializationContext();
        $context->setGroups($groups);
        return $this->serializer->serialize($object, $format, (array($context)));
    }

}
