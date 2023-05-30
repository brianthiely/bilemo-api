<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;


/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_user_by_id",
 *          parameters = { "userId" = "expr(object.getId())" }
 *      ),
 *     exclusion = @Hateoas\Exclusion(groups = {"users:read"})
 * )
 *
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route(
 *     "delete_user_by_id",
 *     parameters = { "userId" = "expr(object.getId())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups = {"users:read"})
 * )
 *
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['users:read', 'user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['users:read', 'user:read'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['users:read', 'user:read'])]
    private ?string $lastname = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }
}
