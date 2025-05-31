<?php


namespace App\DataFixtures;

use App\Entity\Formation;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Add some formations
        for ($i = 1; $i <= 5; $i++) {
            $formation = new Formation();
            $formation->setTitle("Formation $i");
            $formation->setDescription("Description de la formation $i");
            $formation->setPrice(100 + $i * 10);
            $formation->setPublished(true);
            $manager->persist($formation);
        }

        // Add some ebooks
        for ($i = 1; $i <= 5; $i++) {
            $book = new Book();
            $book->setTitle("EBook $i");
            $book->setDescription("Description de l'eBook $i");
            $book->setPrice(20 + $i * 5);
            $manager->persist($book);
        }


        $manager->flush();
    }
}