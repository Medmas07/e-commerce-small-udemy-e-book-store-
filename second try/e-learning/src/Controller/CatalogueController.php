<?php

namespace App\Controller;

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
}
