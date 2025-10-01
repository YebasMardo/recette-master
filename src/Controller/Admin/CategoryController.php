<?php
namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/admin/categories", "admin.category.")]
#[IsGranted('ROLE_ADMIN')]
final class CategoryController extends AbstractController {

    #[Route("/", "index")]
    public function index(CategoryRepository $categoryRepository) {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categoryRepository->findAll()
        ]);
    }

    #[Route("/create", "create")]
    public function create(Request $request, EntityManagerInterface $em) {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'La catégorie a bien été crée');
            return $this->redirectToRoute('admin.category.index');
        }
        return $this->render('admin/category/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route("/{id}", "edit", methods: ['POST', 'GET'], requirements: ["id" => Requirement::DIGITS])]
    public function edit(Category $category, Request $request, EntityManagerInterface $em) {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'La catégorie a bien été modifiée');
            return $this->redirectToRoute('admin.category.index');
        }
        return $this->render('admin/category/edit.html.twig', [
            'form' => $form,
            'category' => $category
        ]);
    }

    #[Route("/{id}", "delete", methods: ['DELETE'], requirements: ["id" => Requirement::DIGITS])]
    public function remove(Category $category, Request $request, EntityManagerInterface $em) {
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', 'La catégorie a bien été supprimée');
        return $this->redirectToRoute('admin.category.index');
    }
}