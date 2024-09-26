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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InstructorStudentCrudController extends AbstractCrudController
{
    private $security;
    private $passwordHasher;

    public function __construct(Security $security, UserPasswordHasherInterface $passwordHasher)
    {
        $this->security = $security;
        $this->passwordHasher = $passwordHasher;
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
        $fields = [
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
            AssociationField::new('instructor')
                ->setFormTypeOption('disabled', true)
                ->setFormTypeOption('data', $this->security->getUser())
                ->setRequired(true),
            ChoiceField::new('roles')
                ->setChoices([
                    'Student' => 'ROLE_STUDENT',
                ])
                ->allowMultipleChoices()
                ->setRequired(true)
                ->setFormTypeOption('disabled', true),
        ];
    
        if ($pageName === Crud::PAGE_NEW || $pageName === Crud::PAGE_EDIT) {
            $fields[] = TextField::new('password')
                ->setFormType(PasswordType::class)
                ->setRequired($pageName === Crud::PAGE_NEW)
                ->onlyOnForms();
        }
    
        return $fields;
    }

   

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function hashPassword($entity): void
    {
        if (!$entity instanceof Student) {
            return;
        }

        $plainPassword = $entity->getPassword();
        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($entity, $plainPassword);
            $entity->setPassword($hashedPassword);
        }
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
        $entityInstance->setRoles(['ROLE_STUDENT']);
        $this->hashPassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }
}
