<?php

namespace App\Controller;

use App\Repository\PartidaRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FiltroPartidaController extends AbstractController
{
    #[Route('/api/partidas/filtro', name: 'filtro_partidas', methods: ['POST'])]
    public function filtro(Request $request, PartidaRepository $repository): Response
    {
        $data = json_decode($request->getContent(), true);

        $autor = $data['autor'] ?? '';

        $partidas = $repository->findPartidasFiltradas($autor);

        return $this->json($partidas);
    }
}
