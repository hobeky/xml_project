<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
//    In case of using database
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[SerializedName('xmlId')]
    private string $xmlId;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank]
    #[SerializedName('name')]
    private ?string $name = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    #[SerializedName('sureName')]
    private ?string $sureName = null;

    #[ORM\Column(length: 1)]
    #[Assert\NotBlank]
    #[SerializedName('sex')]
    private ?string $sex = null;

    #[ORM\Column(length: 4)]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(
        value: 1950,
        message: 'The birth year must be greater than or equal to 1950.'
    )]
    #[SerializedName('birth_year')]
    private ?int $dateOfBirth = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSureName(): ?string
    {
        return $this->sureName;
    }

    public function setSureName(string $sureName): static
    {
        $this->sureName = $sureName;

        return $this;
    }

    public function getXmlId(): string
    {
        return $this->xmlId;
    }

    public function setXmlId(string $xmlId): static
    {
        $this->xmlId = $xmlId;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(?string $sex): void
    {
        $this->sex = $sex;
    }

    public function getDateOfBirth(): ?int
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?int $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }
}
