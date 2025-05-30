<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Book;
use App\Form\BookTypeForm;
use App\Form\FormationTypeForm;
use App\Repository\FormationRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $minPrice = $request->query->get('minPrice');
        $maxPrice = $request->query->get('maxPrice');
        $minPrice = is_numeric($minPrice) ? (float)$minPrice : null;
        $maxPrice = is_numeric($maxPrice) ? (float)$maxPrice : null;
        $type = $request->query->get('type');
        $category = $request->query->get('category');
        $sort = $request->query->get('sort');


        $formations = [];
        $ebooks = [];

        if (!$type || $type === 'formation') {
            $formations = $formationRepo->searchAdvanced($query, $minPrice, $maxPrice, $sort);
        }

        if (!$type || $type === 'book') {
            $ebooks = $ebookRepo->searchAdvanced($query, $minPrice, $maxPrice, $category, $sort);
        }

        $allCategories = $ebookRepo->findAllCategories();

        return $this->render('catalogue/admin.html.twig', [
            'formations' => $formations,
            'ebooks' => $ebooks,
            'searchQuery' => $query,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'selectedType' => $type,
            'selectedCategory' => $category,
            'allCategories' => $allCategories,
            'selectedSort' => $sort,
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


    #[Route('/admin/catalogue', name: 'admin_catalogue_index')]
    public function adminIndex(
        Request $request,
        FormationRepository $formationRepo,
        BookRepository $ebookRepo
    ): Response {
        // Check admin role
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $query = $request->query->get('q', '');
        $minPrice = $request->query->get('minPrice');
        $maxPrice = $request->query->get('maxPrice');
        $type = $request->query->get('type');
        $category = $request->query->get('category');
        $sort = $request->query->get('sort');

        $formations = [];
        $ebooks = [];

        if (!$type || $type === 'formation') {
            $formations = $formationRepo->searchAdvanced($query, $minPrice, $maxPrice, $sort);
        }

        if (!$type || $type === 'book') {
            $ebooks = $ebookRepo->searchAdvanced($query, $minPrice, $maxPrice, $category, $sort);
        }

        $allCategories = $ebookRepo->findAllCategories();

        return $this->render('admin/catalogue/admin.html.twig', [
            'formations' => $formations,
            'ebooks' => $ebooks,
            'searchQuery' => $query,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'selectedType' => $type,
            'selectedCategory' => $category,
            'allCategories' => $allCategories,
            'selectedSort' => $sort,
        ]);
    }

    // Edit Formation
    #[Route('/admin/catalogue/formation/{id}/edit', name: 'admin_catalogue_formation_edit', methods: ['GET', 'POST'])]
    public function editFormation(Request $request, Formation $formation, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(FormationTypeForm::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Formation modifiée avec succès.');
            return $this->redirectToRoute('admin_catalogue_index');
        }

        return $this->render('admin/catalogue/edit_formation.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
        ]);
    }

    // Delete Formation
    #[Route('/admin/catalogue/formation/{id}/delete', name: 'admin_catalogue_formation_delete', methods: ['POST'])]
    public function deleteFormation(Request $request, Formation $formation, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $em->remove($formation);
            $em->flush();
            $this->addFlash('success', 'Formation supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_catalogue_index');
    }

    #[Route('/admin/catalogue/book/{id}/edit', name: 'admin_catalogue_book_edit', methods: ['GET', 'POST'])]
    public function editBook(Request $request, Book $book, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(BookTypeForm::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Book modifiée avec succès.');
            return $this->redirectToRoute('admin_catalogue_index');
        }

        return $this->render('admin/catalogue/edit_book.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    // Delete Formation
    #[Route('/admin/catalogue/book/{id}/delete', name: 'admin_catalogue_book_delete', methods: ['POST'])]
    public function deleteBook(Request $request, Book $book, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $em->remove($book);
            $em->flush();
            $this->addFlash('success', 'Book supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_catalogue_index');
    }



}
