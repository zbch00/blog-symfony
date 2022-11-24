<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    private SluggerInterface $slugger;

    // Demander à symfony d'injecter le slugger au niveau du constructeur

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        // installation Faker
        $faker = Factory::create("fr_FR");
        for ($i=0; $i<50; $i++) {
            $article = new Article();
            $article->setTitre($faker->words($faker->numberBetween(3,10),true));
            $article->setContenu($faker->paragraph("3",true));
            $article->setCreatedAt($faker->dateTimeBetween("-6 month","now"));
            $article->setSlug($this->slugger->slug($article->getTitre())->lower());
            $article->setPubli($faker->numberBetween(0,1));
            $this->addReference("article".$i,$article);

            // Associer l'article à une catégorie
            // Récuperer une référence d'une catégorie
            $numCategorie = $faker->numberBetween(0,8);
            $article->setCategorie($this->getReference("categorie". $numCategorie));



            // générer l'ordre INSERT
            // INSERT INTO aricle values ("Titre1" , 'Contenu de l'article")
            $manager->persist($article);
        }
        // le flush va permettre d'envoyer l'ordre INSERT vers la base
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategorieFixtures::class
        ];
    }
}
