<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Book;
use App\Repository\FormationRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'catalogue_index')]
    public function index(
        Request $request,
        FormationRepository $formationRepo,
        BookRepository $ebookRepo
    ): Response {
        $query = $request->query->get('q', '');

        $formations = $formationRepo->searchByTitle($query);
        $ebooks = $ebookRepo->searchByTitle($query);

        return $this->render('catalogue/index.html.twig', [
            'formations' => $formations,
            'ebooks' => $ebooks,
            'searchQuery' => $query,
        ]);
    }



    #[Route('/catalogue/formation/{id}', name: 'catalogue_formation_show')]
    public function showFormation(Formation $formation): Response
    {
        return $this->render('catalogue/show_formation.html.twig', [
            'formation' => $formation,
        ]);
    }

    #[Route('/catalogue/ebook/{id}', name: 'catalogue_ebook_show')]
    public function showEbook(Book $ebook): Response
    {
        return $this->render('catalogue/show_ebook.html.twig', [
            'ebook' => $ebook,
        ]);
    }



}
