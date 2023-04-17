<?php

namespace App\Entity;

use App\Repository\TokenEntityRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Model\Token as ModelToken;

#[ORM\Entity(repositoryClass: TokenEntityRepository::class)]
class Token
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column(length: 255)]
    private ?string $expires = null;

    public function __construct(ModelToken $token)
    {
        $this->token = $token->getToken();
        $this->expires = $token->getExpires();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpires(): ?string
    {
        return $this->expires;
    }

    public function setExpires(string $expires): self
    {
        $this->expires = $expires;

        return $this;
    }
}
