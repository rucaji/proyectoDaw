<?php

namespace App\Controller;

use App\Repository\EjercicioRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FiltroEjercicioController extends AbstractController
{
    #[Route('/api/ejercicios/filtro', name: 'filtro_ejercicios', methods: ['POST'])]
    public function filtro(Request $request, EjercicioRepository $repository): Response
    {
        $data = json_decode($request->getContent(), true);

        $autor = $data['autor'] ?? '';
        $dificultad = $data['dificultad'] ?? '';
        $tema = $data['tema'] ?? '';

        $ejercicios = $repository->findEjerciciosFiltrados($autor, $dificultad, $tema);

        return $this->json($ejercicios);
    }
}
