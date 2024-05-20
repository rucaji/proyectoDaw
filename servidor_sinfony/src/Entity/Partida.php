<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\PartidaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartidaRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('EDIT', object)"),  // Cambio para usar el Voter de propiedad
        new Delete(security: "is_granted('DELETE', object)"),  // Cambio para usar el Voter de propiedad
    ],
    security: "is_granted('ROLE_USER')"  // Seguridad básica para acceso a los recursos
)]
class Partida
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $cabecera = null;

    #[ORM\Column(length: 9000)]
    private ?string $jugadas = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $autor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCabecera(): ?string
    {
        return $this->cabecera;
    }

    public function setCabecera(string $cabecera): static
    {
        $this->cabecera = $cabecera;

        return $this;
    }

    public function getJugadas(): ?string
    {
        return $this->jugadas;
    }

    public function setJugadas(string $jugadas): static
    {
        $this->jugadas = $jugadas;

        return $this;
    }

    public function getAutor(): ?string
    {
        return $this->autor;
    }

    public function setAutor(?string $autor): static
    {
        $this->autor = $autor;

        return $this;
    }
}
