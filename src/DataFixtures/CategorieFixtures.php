<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategorieFixtures extends Fixture
{
    private SluggerInterface $slugger;
    public function  __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create("fr_FR");
        for ($i=0; $i<=8; $i ++){
            $categorie = new Categorie();
            $categorie->setTitre($faker->word() );
            $categorie->setSlug($this->slugger->slug($categorie->getTitre())->lower());
            // Génerer une référence sur la catégorie qu'on veut créer
            $this->addReference("categorie".$i, $categorie);
            $manager->persist($categorie);
        }

        $manager->flush();
    }
}
