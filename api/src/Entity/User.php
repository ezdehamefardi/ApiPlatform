<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: "/users",
            security: "is_granted('ROLE_USER')",
            securityMessage: "You should login!",
        ),
        new Get(
            uriTemplate: "/users/{id}",
            security: "is_granted('ROLE_USER')",
            securityMessage: "You should login!",
        ),
        new Post(
            uriTemplate: "/users",
            security: "is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_COMPANY_ADMIN')",
            securityMessage: "Accessible only for admins!"
        ),
        new Delete(
            uriTemplate: "/users/{id}",
            security: "is_granted('ROLE_SUPER_ADMIN')",
            securityMessage: "Accessible only for Super admins!"
        )
    ]
)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 100)]
    #[Assert\Regex(pattern: "/^(?=.*[A-Z])[a-zA-Z ]+$/", message: "Only letters and space between 3-100 character length and at least one uppercase letter!")]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: ['ROLE_USER', 'ROLE_COMPANY_ADMIN', 'ROLE_SUPER_ADMIN'], message: "Choose a valid role!")]
    private ?string $role = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Company $company = null;

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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }
}
