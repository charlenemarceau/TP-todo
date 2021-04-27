<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Todo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        # 1 Tableau de catégories

        $categories = ['Professionnel', 'Personnel', 'Important'];
        #Je stocke tous les objets crées dans la bouche dans le tableau $tabObjCategory
        $tabObjCategory = [];

        # 2 Créer autant d'objet de type Category qu'il y en a dans le tableau
        foreach($categories as $c) {
            $cat = new Category;
            $cat-> setName($c);
            $manager->persist($cat);
            array_push($tabObjCategory,$cat);
        }

        # 3 Créer une ou plusieurs Todos
        $todo = new Todo;
        $todo 
            ->setTitle('Initialiser le projet')
            ->setContent('Un tas de trucs à dire la dessus...')
            ->setDateFor(new \DateTime('Europe/Paris'))
            ->setCategory($tabObjCategory[0]);
        $manager->persist($todo);

        $manager->flush();
    }
}
