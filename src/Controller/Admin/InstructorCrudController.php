<?php

namespace App\Controller\Admin;

use App\Entity\Student;
use App\Entity\Instructor;
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
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class InstructorCrudController extends AbstractCrudController
{
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
            // ->setColumns('col-sm-12 col-md-6'),
            DateField::new('birthdate'),
            EmailField::new('mail'),
            TextField::new('tel'),
            TextField::new('adress'),
            TextField::new('passport'),
            
            AssociationField::new('grade'),
            AssociationField::new('dojang'),

            // CollectionField::new('students')
            //     ->setEntryType(Student::class)
            //     ->onlyOnForms()
            //     ->allowAdd()
            //     ->allowDelete(),
            AssociationField::new('students')
                ->onlyOnIndex()
                ->setFormTypeOption('by_reference', false),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL);
    }
    }
    

