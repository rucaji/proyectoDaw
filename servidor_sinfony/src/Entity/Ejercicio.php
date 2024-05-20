<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\EjercicioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EjercicioRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('EDIT', object)"),  // Usar el Voter para verificar la propiedad del autor
        new Delete(security: "is_granted('DELETE', object)"),  // Usar el Voter para verificar la propiedad del autor
    ],
    paginationEnabled: true,
    paginationItemsPerPage: 200,
    security: "is_granted('ROLE_USER')"  // Seguridad básica para todas las operaciones
)]
class Ejercicio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $dificultad = null;

    #[ORM\Column(length: 255)]
    private ?string $tema = null;

    #[ORM\Column(length: 255)]
    private ?string $autor = null;

    #[ORM\Column(length: 255)]
    private ?string $fen = null;

    #[ORM\Column(length: 255)]
    private ?string $solucion = null;

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\Column(length: 255)]
    private ?string $enunciado = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $texto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDificultad(): ?string
    {
        return $this->dificultad;
    }

    public function setDificultad(string $dificultad): static
    {
        $this->dificultad = $dificultad;

        return $this;
    }

    public function getTema(): ?string
    {
        return $this->tema;
    }

    public function setTema(string $tema): static
    {
        $this->tema = $tema;

        return $this;
    }

    public function getAutor(): ?string
    {
        return $this->autor;
    }

    public function setAutor(string $autor): static
    {
        $this->autor = $autor;

        return $this;
    }

    public function getFen(): ?string
    {
        return $this->fen;
    }

    public function setFen(string $fen): static
    {
        $this->fen = $fen;

        return $this;
    }

    public function getSolucion(): ?string
    {
        return $this->solucion;
    }

    public function setSolucion(string $solucion): static
    {
        $this->solucion = $solucion;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getEnunciado(): ?string
    {
        return $this->enunciado;
    }

    public function setEnunciado(string $enunciado): static
    {
        $this->enunciado = $enunciado;

        return $this;
    }

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(?string $texto): static
    {
        $this->texto = $texto;

        return $this;
    }
}
