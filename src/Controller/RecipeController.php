<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $recipeRepository, EntityManagerInterface $em): Response
    {
        $recipes = $recipeRepository->findAll();
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route(path: '/recettes/{slug}-{id}', name: 'recipe.show', requirements: ["id" => "\d+", "slug" => "[a-z0-9-]+"])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $recipeRepository): Response
    {
        $recipe = $recipeRepository->find($id);
        if($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId(), 'slug' => $recipe->getSlug()]);
        }
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }

    #[Route('recettes/{id}/edit', 'recipe.edit', methods: ['POST', 'GET'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em) 
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'La recette a bien été modifiée');
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('recipe/edit.html.twig', [
            'form' => $form,
            'recipe' => $recipe
        ]);
    }

    #[Route('/recettes/create', 'recipe.create')]
    public function create(Request $request, EntityManagerInterface $em) {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été créee');
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('recipe/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/recettes/{id}', 'recipe.delete', methods: ['DELETE'])]
    public function delete(Recipe $recipe, EntityManagerInterface $em) {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a bien été supprimée');
        return $this->redirectToRoute('recipe.index');
    }
}
