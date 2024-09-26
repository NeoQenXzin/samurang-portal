<?php

namespace App\Controller\Admin;

use App\Entity\Student;
use App\Entity\Instructor;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InstructorCrudController extends AbstractCrudController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public static function getEntityFqcn(): string
    {
        return Instructor::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('firstname')
            ->add('lastname')
            ->add('mail')
            ->add('adress')
            ->add('sexe')
            ->add('grade')
            ->add('dojang')
            ->add('students');
    }

    public function configureFields(string $pageName): iterable
    {

        yield TextField::new('firstName');
        yield TextField::new('lastName');
        yield ChoiceField::new('sexe')
            ->setChoices([
                'Masculin' => 'M',
                'Féminin' => 'F'
            ])
            ->renderExpanded()
            ->renderAsBadges()
            ->setFormTypeOption('expanded', true)
            ->setFormTypeOption('multiple', false);
        // ->setColumns('col-sm-12 col-md-6'),
        yield DateField::new('birthdate');
        yield EmailField::new('mail')->setLabel('Email');
        yield TextField::new('tel');
        yield TextField::new('adress');
        yield TextField::new('passport');

        yield AssociationField::new('grade');
        yield AssociationField::new('dojang');

        // CollectionField::new('students')
        //     ->setEntryType(Student::class)
        //     ->onlyOnForms()
        //     ->allowAdd()
        //     ->allowDelete(),
        yield AssociationField::new('students')
            ->onlyOnIndex()
            ->setFormTypeOption('by_reference', false);
        yield ChoiceField::new('roles')
            ->setChoices([
                'Instructor' => 'ROLE_INSTRUCTOR',
                'Student' => 'ROLE_STUDENT',
                'Admin' => 'ROLE_ADMIN',
            ])
            ->allowMultipleChoices()
            ->setRequired(true);

        if ($pageName === Crud::PAGE_NEW || $pageName === Crud::PAGE_EDIT) {
            yield TextField::new('password')
                ->setFormType(PasswordType::class)
                ->setRequired($pageName === Crud::PAGE_NEW)
                ->onlyOnForms();
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL);
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
        if (!$entity instanceof Instructor) {
            return;
        }

        $plainPassword = $entity->getPassword();
        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($entity, $plainPassword);
            $entity->setPassword($hashedPassword);
        }
    }
}
