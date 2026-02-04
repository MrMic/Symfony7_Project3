<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[Route('/admin/recettes', name: 'admin.recipe.')]
#[IsGranted('ROLE_ADMIN')]
final class RecipeController extends AbstractController
{
    // ______________________________________________________________________
    #[Route('/', name: 'index')]
    public function index(
        RecipeRepository $repository,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em
    ): Response {
        // INFO: $platPrincipal = $categoryRepository->findOneBy(['slug' => 'plat-principal']);
        // INFO: $pates = $repository->findOneBy(['slug' => 'pates-bolognaise']);
        // INFO: $pates->setCategory($platPrincipal);
        // INFO: $em->flush();

        // $this->denyAccessUnlessGranted('ROLE_USER');
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

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    // ______________________________________________________________________
    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été créée !');

            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/create.html.twig', [
            'form' => $form,
        ]);
    }

    // ______________________________________________________________________
    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em, UploaderHelper $helper): Response
    {
        // dd($helper->asset($recipe, 'thumbnailFile'));

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'La recette a bien été mise à jour !');

            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    // ______________________________________________________________________
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Recipe $recipe, EntityManagerInterface $em): Response
    {
        $em->remove($recipe);
        $em->flush();

        $this->addFlash('success', 'La recette a bien été supprimée !');

        return $this->redirectToRoute('admin.recipe.index');
    }
}
