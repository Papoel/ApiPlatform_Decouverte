<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /** @var array<array-key, Genre > $genres */
        $genres= $manager->getRepository(Genre::class)->findAll();

        /** @var array<array-key, Person > $people */
        $people= $manager->getRepository(Person::class)->findAll();

        foreach ($genres as $genre) {
            for ($i = 1; $i <= 10; $i++) {
                $movie = (new Movie())
                    ->setTitle(sprintf('Titre %d', $i))
                    ->setSynopsis(sprintf('Synopsis %d', $i))
                    ->setDuration(rand(80, 300))
                    ->setProductionYear(rand(1930, 2022))
                    ->setGenre($genre);

                shuffle($people);

                foreach (array_slice($people, 3, 3) as $person) {
                    $movie->getActors()->addActor($person);
                }

                foreach (array_slice($people, 3, 2) as $person) {
                    $movie->getDirectors()->addDirector($person);
                }


                $manager->persist($movie);
            }
        }


        $manager->flush();
    }
}
