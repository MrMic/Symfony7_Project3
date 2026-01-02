<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RecipeController extends AbstractController
{
    // ______________________________________________________________________
    #[Route('/recettes', name: 'recipe.index')]
    public function index(
        Request $request,
        RecipeRepository $repository,
        // EntityManagerInterface $em
    ): Response {
        $recipes = $repository->findWithDurationLowerThan(20);

        // INFO: Accéde au repository via l' EntityManagerInterface
        // $recipes = $em->getRepository(Recipe::class)->findWithDurationLowerThan(20);

        // INFO: trouver le repository d'une entité
        // dd($em->getRepository(Recipe::class));

        // INFO: Ajouter une recette
        // $recipe = new Recipe();
        // $recipe->setTitle('Barbe à papa')
        //     ->setSlug('barbe-a-papa')
        //     ->setContent('La barbe à papa est une confiserie légère et sucrée ...')
        //     ->setDuration(2)
        //     ->setCreatedAt(new \DateTimeImmutable())
        //     ->setUpdatedAt(new \DateTimeImmutable());
        // $em->persist($recipe);

        // INFO: Supprimer une recette
        // $em->remove($recipes[0]);

        // INFO: Sauvegarder les changements - Fais les requêtes SQL
        // $em->flush();

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    // ______________________________________________________________________
    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {
        // $recipe = $recipeRepository->findOneBy(['slug' => $slug]);
        $recipe = $repository->find($id);
        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipe.show', [
                'id' => $recipe->getId(),
                'slug' => $recipe->getSlug()
            ]);
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        ]);

        // return new JsonResponse([
        //     'id' => $id,
        //     'slug' => $slug,
        // ]);

        // return $this->json([
        //     'id' => $id,
        //     'slug' => $slug,
        // ]);

        // return new Response('Recette : ' . $slug);
    }

    // ______________________________________________________________________
    #[Route('recettes/{id}/edit', name: 'recipe.edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'La recette a bien été mise à jour !');
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }

    // ______________________________________________________________________
    #[Route('recettes/create', name: 'recipe.create') ]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été créée !');
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/create.html.twig', [
            'form' => $form
        ]);
    }

    // ______________________________________________________________________
    #[Route('recettes/{id}/remove', name: 'recipe.delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Recipe $recipe, EntityManagerInterface $em): Response
    {
        $em->remove($recipe);
        $em->flush();

        $this->addFlash('success', 'La recette a bien été supprimée !');

        return $this->redirectToRoute('recipe.index');
    }
}
