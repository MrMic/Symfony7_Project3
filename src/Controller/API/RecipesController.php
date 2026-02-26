<?php

namespace App\Controller\API;

use App\DTO\PaginationDTO;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class RecipesController extends AbstractController
{
    // INFO: GET ____________________________________________________________
    #[Route("/api/recipes", methods: ["GET"])]
    public function index(
        RecipeRepository $recipeRepository,
        #[MapQueryString]
        PaginationDTO $paginationDTO
    ): JsonResponse {
        $recipes = $recipeRepository->paginateRecipes($paginationDTO->page);

        // INFO: Format csv si on le veut
        // dd($serializer->serialize($recipes, 'csv', [
        //     'groups' => ['recipes.index'],
        // ]));

        return $this->json($recipes, 200, [], [
            'groups' => 'recipes.index',
        ]);
    }

    // INFO: POST ____________________________________________________________
    #[Route("/api/recipes", methods: ["POST"])]
    public function create(
        Request $request,
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['recipes.create'],
            ]
        )]
        Recipe $recipe,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($recipe);
        $entityManager->flush();

        return $this->json($recipe, 200, [], [
            'groups' => [ 'recipes.index', 'recipes.show' ],
        ]);
    }

    // INFO: GET ____________________________________________________________
    #[Route("/api/recipes/{id}", requirements: ['id' => Requirement::DIGITS], name: "api.recipe")]
    public function show(Recipe $recipe): JsonResponse
    {
        return $this->json($recipe, 200, [], [
            'groups' => [ 'recipes.index', 'recipes.show' ],
        ]);
    }
}
