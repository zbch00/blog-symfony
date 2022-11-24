<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleCrudController extends AbstractCrudController
{
    private SluggerInterface $slugger;
    // Injection du slugger au niveau du constructeur

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getEntityFqcn(): string
    {
        return Article::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('titre'),
            TextEditorField::new('contenu')->setSortable(false)->hideOnIndex(),
            AssociationField::new('categorie'),
            DateTimeField::new('createdAt')->hideOnForm()->setLabel('Crée le'),
            TextField::new('slug')->hideOnForm()
        ];
    }

    // Redefinir la méthode persist entity qui va être appelee lors de la creation de l'arcticle en BDD
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // verifier que l'entity instance est bien une instance de la classe article
        if (! $entityInstance instanceof Article) return;
        $entityInstance->setCreatedAt(new \DateTime());
        $entityInstance->setSlug($this->slugger->slug($entityInstance->getTitre())->lower());
        // Appel de la méthode héritée afin de persister  l'entité
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setPageTitle(Crud::PAGE_INDEX,'Liste des articles');
        $crud->setPageTitle(Crud::PAGE_NEW,'Ajouter un article');
        $crud->setPageTitle(Crud::PAGE_NEW,'Modifier un article');
        $crud->setPaginatorPageSize(10);
        $crud->setDefaultSort(['createdAt'=>'DESC']);
        return $crud;

    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->update(Crud::PAGE_INDEX,Action::NEW, function (Action $action){
            return $action->setLabel("Ajouter article")
                ->setIcon("fa fa-plus");
        });
        $actions->update(Crud::PAGE_NEW,Action::SAVE_AND_RETURN, function (Action $action){
            return $action->setLabel('Valider')
                ->setIcon("fa fa-check");
        });
        $actions->remove(Crud::PAGE_NEW,Action::SAVE_AND_ADD_ANOTHER);
        $actions->add(Crud::PAGE_INDEX,Action::DETAIL);
        return $actions;

    }

    public function configureFilters(Filters $filters): Filters
    {
        $filters->add('titre');
        $filters->add('createdAt');
        return $filters;

    }


}
