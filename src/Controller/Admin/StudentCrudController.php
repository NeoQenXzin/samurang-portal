<?php

namespace App\Controller\Admin;

use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StudentCrudController extends AbstractCrudController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    
    public static function getEntityFqcn(): string
    {
        return Student::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
    return $crud
    ->setEntityLabelInSingular('Instructor Student')
    ->setEntityLabelInPlural('Instructor Students')
    ->setSearchFields(['instructors'
   ,
   'text'
   ,
   'email'])
    ->setDefaultSort(['lastname'
   => 'ASC']);
    }
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('firstname')
            ->add('lastname')
            ->add('mail')
            ->add('grade')
            ->add('dojang')
            ->add('instructor');
    }
  
    public function configureFields(string $pageName): iterable
    {
        $fields =[
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
            AssociationField::new('instructor'),

            //Config password role 
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

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
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
}

        
