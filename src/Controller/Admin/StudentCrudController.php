<?php

namespace App\Controller\Admin;

use App\Entity\Student;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class StudentCrudController extends AbstractCrudController
{
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
            AssociationField::new('instructor'),

        ];
    }

}
