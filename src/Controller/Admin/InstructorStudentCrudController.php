<?php

namespace App\Controller\Admin;

use App\Entity\Student;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class InstructorStudentCrudController extends AbstractCrudController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    public static function getEntityFqcn(): string
    {
        return Student::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Student')
            ->setEntityLabelInPlural('Students')
            ->setPageTitle('index', 'My Students')
            ->setPageTitle('new', 'Add Student')
            ->setPageTitle('edit', 'Edit Student');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('firstName'),
            TextField::new('lastName'),
            ChoiceField::new('sexe')
            ->setChoices([
                'Masculin' => 'M',
                'Féminin' => 'F'
            ])
            ->renderExpanded()
            ->renderAsBadges()
            ->setFormTypeOption('expanded', true)
            ->setFormTypeOption('multiple', false),
            DateField::new('birthdate'),
            EmailField::new('mail'),
            TextField::new('tel'),
            TextField::new('adress'),
            TextField::new('passport'),
            
            AssociationField::new('grade'),
            AssociationField::new('dojang'),
            AssociationField::new('instructor') // Chaamps auto rempli non changeable
            ->setFormTypeOption('disabled', true)
            ->setFormTypeOption('data', $this->security->getUser())
            ->setRequired(true)
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // Filtrer les étudiants pour n'afficher que ceux de l'instructeur connecté
        $qb->andWhere('entity.instructor = :instructor')
            ->setParameter('instructor', $this->getUser());

        return $qb;
    }
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
{
    if (!$entityInstance->getInstructor()) {
        $entityInstance->setInstructor($this->security->getUser());
    }
    parent::persistEntity($entityManager, $entityInstance);
}
}
