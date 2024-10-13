<?php

namespace App\Controller\Admin;

use App\Entity\NextOrder;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class NextOrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return NextOrder::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('sendDate'),
            DateTimeField::new('receiveDate'),
            ChoiceField::new('status')
                ->setChoices([
                    'Pas encore passée' => 'not_ordered',
                    'En attente' => 'pending',
                    'Arrivée' => 'received'
                ])
        ];
    }
}
