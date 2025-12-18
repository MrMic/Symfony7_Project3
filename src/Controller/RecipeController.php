<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RecipeController extends AbstractController
{
    #[Route('/recette', name: 'recipe.index')]
    public function index(Request $request): Response
    {
        return new Response('Recipe index page');
    }

    #[Route('/recette/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]+'])]
    public function show(string $slug, int $id): Response
    {
        return new JsonResponse([
            'id' => $id,
            'slug' => $slug,
        ]);

        // return $this->json([
        //     'id' => $id,
        //     'slug' => $slug,
        // ]);

        // return new Response('Recette : ' . $slug);
    }
}
