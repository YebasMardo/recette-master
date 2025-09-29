<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route("/admin/category", "admin.category.")]
final class CategoryController extends AbstractController {

    #[Route("/", "index")]
    public function index() {

    }

    #[Route("/create", "create")]
    public function create() {

    }

    #[Route("/{id}", "edit", methods: ['POST', 'GET'], requirements: ["id" => Requirement::DIGITS])]
    public function edit() {

    }

    #[Route("/{id}", "delete", methods: ['DELETE'], requirements: ["id" => Requirement::DIGITS])]
    public function remove() {

    }
}