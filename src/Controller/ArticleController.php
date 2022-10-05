<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    private ArticleRepository $articleRepository;
    // Demander à symfony d'injecter une instance de articleRepository
    // à la création du controleur (instance de ArticleController)
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    #[Route('/articles', name: 'app_articles')]
    // à l'appel de la méthode, symfony va créer un objet de la classe ArticleRepository et le passer
        // en paramètre de la méthode
        // MECANISME : INJECTION DE DEPENDANCE
        //autowiring
    public function getArticle(PaginatorInterface $paginator, Request $request): Response
    {
        // Récuperer les informations dans la base de donnée
        // Le controleur fait appel au model (une classe du model) afin de récuperer la listes des articles
        // Mise en place de la pagination
        $articles = $paginator->paginate(
            $this->articleRepository->findBy([], ['createdAt' => "DESC"]), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('article/index.html.twig', [
            "articles" => $articles
        ]);
    }

    #[Route('articles/{slug}', name: 'app_article_slug')]
    public function getArticleBySlug($slug): Response
    {
        $article = $this->articleRepository->findOneBy(["slug" => $slug]);

        return $this->render('article/article.html.twig', [
            "article" => $article
        ]);
    }

    #[Route('articles/nouveau', name: 'app_articles_nouveau',methods: ['GET','POST'], priority: 1
         )]
    public function insert(SluggerInterface $slugger,Request $request): Response
    {
        $article = new Article();
        // Création du formulaire
        $formArticle = $this->createForm(ArticleType::class,$article);

        //Reconnaitre si le formulaire a été soumis ou pas
        $formArticle->handleRequest($request);
        // Est ce que le formulaire a été soumis
        if ($formArticle->isSubmitted() && $formArticle->isValid()){
            $article->setSlug($slugger->slug($article->getTitre())->lower())
                    ->setCreatedAt(new \DateTime());
            // Insérer l'article dans la base de données
            $this->articleRepository->add($article,true);
            return $this->redirectToRoute('app_articles');
        }

        // Appel de la vue twig permettant d'afficher le formulaire
        return $this->renderForm('article/nouveau.html.twig',[
            'formArticle'=> $formArticle
        ]);


        /*$article->setTitre('Nouvel article 2');
        $article->setContenu('Contenu du nouvel article 2');
        $article->setSlug($slugger->slug($article->getTitre())->lower());
        $article->setCreatedAt(new \DateTime());
        // symfony 6
        $this->articleRepository->add($article, true);
        return $this->redirectToRoute('app_articles');
        */
    }


}
