<?php

namespace App\Service\Serializer;

use Doctrine\ORM\Mapping\Entity;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

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

    public function deserialize(string $data, string $type, string $format = 'json'): object
    {
        return $this->serializer->deserialize($data, $type, $format);
    }


}
