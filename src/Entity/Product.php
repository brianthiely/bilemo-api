<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "get_product_by_id",
 *          parameters = { "productId" = "expr(object.getProductId())" }
 *      ),
 * )
 *
 */
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['products:read'])]
    private ?int $productId = null;

    #[ORM\Column(length: 42)]
    #[Groups(['products:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 42)]
    #[Groups(['products:read'])]
    private ?string $brand = null;

    #[ORM\Column]
    #[Groups(['products:read'])]
    private ?int $price = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['products:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 512)]
    #[Groups(['products:read'])]
    private ?string $picture = null;

    #[ORM\Column]
    #[Groups(['products:read'])]
    private int $screenSize;

    #[ORM\Column]
    #[Groups(['products:read'])]
    private string $color;

    #[ORM\Column]
    #[Groups(['products:read'])]
    private int $storageCapacity;

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /**
     * @param string|null $picture
     */
    public function setPicture(?string $picture): void
    {
        $this->picture = $picture;
    }

    /**
     * @return Float
     */
    public function getScreenSize(): float
    {
        return $this->screenSize;
    }

    /**
     * @param Float $screenSize
     */
    public function setScreenSize(float $screenSize): void
    {
        $this->screenSize = $screenSize;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return int
     */
    public function getStorageCapacity(): int
    {
        return $this->storageCapacity;
    }

    /**
     * @param int $storageCapacity
     */
    public function setStorageCapacity(int $storageCapacity): void
    {
        $this->storageCapacity = $storageCapacity;
    }

}
