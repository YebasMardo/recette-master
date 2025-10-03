<?php
namespace App\Controller\API;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class RecipesController extends AbstractController {

    #[Route('/api/recettes', methods: ['GET'])]
    public function index(RecipeRepository $recipe, Request $request)
    {
        $recipes = $recipe->paginatedRecipes($request->query->getInt('page', 1));
        return $this->json($recipes, 200, [], [
            'groups' => ['recipes.index']
        ]);
    }

    #[Route('/api/recettes/{id}')]
    public function show(Recipe $recipe)
    {
        return $this->json($recipe, 200, [], [
            'groups' => ['recipes.index', 'recipes.show']
        ]);
    }

    #[Route('/api/recettes', methods: ['POST'])]
    public function create(
        Request $request,
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['recipes.create']
            ]
        )]
        Recipe $recipe,
        EntityManagerInterface $em
    )
    {
        $recipe->setCreatedAt(new DateTimeImmutable());
        $recipe->setUpdatedAt(new DateTimeImmutable());
        $em->persist($recipe);
        $em->flush();
        return $this->json($recipe, 200, [], [
            'groups' => ['recipes.index', 'recipes.show']
        ]);
    }
}