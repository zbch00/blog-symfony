<?php

namespace App\DataFixtures;

use App\Entity\Commentaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{

    public function getDependencies()
    {
        return [
            ArticleFixtures::class,
            UtilisateurFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        for ($i=0; $i<50; $i ++){
            $commentaire = new Commentaire();
            $commentaire->setContenu($faker->paragraph("3",true) );
            $commentaire->setCreatedAt($faker->dateTimeBetween("-6 month","now"));



                $numUtilisateur = $faker->numberBetween(1, 30);

            $commentaire->setUtilisateurs($this->getReference("utilisateur". $numUtilisateur));

            // Associer un commentaire à l'article
            // Récuperer une référence d'un article
            $numArticle = $faker->numberBetween(1,30);
            $commentaire->setArticle($this->getReference("article". $numArticle));





            $manager->persist($commentaire);
        }

        $manager->flush();

    }


}
