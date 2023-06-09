<?php

namespace App\Service\User;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function saveUser($form, $client): User
    {
        $user = new User();
        $user->setFirstname($form->getFirstname());
        $user->setLastname($form->getLastname());
        $client->addUser($user);
        $this->em->persist($form);
        $this->em->flush();

        return $user;
    }

    public function deleteUser(?User $user): void
    {
        if ($user) {
            $this->em->remove($user);
            $this->em->flush();
        }
    }

}