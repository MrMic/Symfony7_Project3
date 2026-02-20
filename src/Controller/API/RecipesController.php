<?php

namespace App\Controller\API;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;

class RecipesController extends AbstractController
{
    #[Route("/api/recipes", name: "api.recipes")]
    public function index(
        RecipeRepository $recipeRepository,
        Request $request,
        SerializerInterface $serializer
    ): JsonResponse {
        $recipes = $recipeRepository->paginateRecipes($request->query->getInt('page', 1));

        // INFO: Format csv si on le veut
        // dd($serializer->serialize($recipes, 'csv', [
        //     'groups' => ['recipes.index'],
        // ]));

        return $this->json($recipes, 200, [], [
            'groups' => 'recipes.index',
        ]);
    }

    #[Route("/api/recipes/{id}", requirements: ['id' => Requirement::DIGITS], name: "api.recipe")]
    public function show(Recipe $recipe): JsonResponse
    {
        return $this->json($recipe, 200, [], [
            'groups' => [ 'recipes.index', 'recipes.show' ],
        ]);
    }
}
