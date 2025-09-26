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
    #[Route('/recette', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $recipeRepository, EntityManagerInterface $em): Response
    {
        $recipes = $recipeRepository->findAll();
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route(path: '/recette/{slug}-{id}', name: 'recipe.show', requirements: ["id" => "\d+", "slug" => "[a-z0-9-]+"])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $recipeRepository): Response
    {
        $recipe = $recipeRepository->find($id);
        if($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId(), 'slug' => $recipe->getSlug()]);
        }
        return $this->render('recipe/show.html.twig', [
            'slug' => $slug,
            'id' => $id,
            'recipe' => $recipe
        ]);
    }

    #[Route('recette/{id}/edit', 'recipe.edit')]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em) 
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('recipe/edit.html.twig', [
            'form' => $form,
            'recipe' => $recipe
        ]);
    }
}
