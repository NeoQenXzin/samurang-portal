<?php

namespace App\Controller\Admin;

use App\Entity\Formation;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FormationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Formation::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new('type')
            ->setChoices([
                'Parcours Instructeur' => 'Parcours Instructeur',
                'Parcours Initiateur' => 'Parcours Initiateur',
                'Parcours Assistant' => 'Parcours Assistant',
                'Stage' => 'Stage',
                'Démonstration' => 'Démonstration',
                'Initiation' => 'Initiation',
                'Event' => 'Event',
            ])
            ->setRequired(true);
        yield TextEditorField::new('description');
        yield DateTimeField::new('startDate');
        yield DateTimeField::new('endDate');
        yield TextField::new('location');
        yield TextField::new('url');
        yield AssociationField::new('organizer');
        yield IntegerField::new('participantsCount')->setLabel('Nombre de participants');
        yield AssociationField::new('organizer')->setLabel('Organisateur');
        // yield AssociationField::new('instructorParticipants')->setLabel('Instructeurs participants');
        // yield AssociationField::new('studentParticipants')->setLabel('Étudiants participants');
        yield CollectionField::new('instructorParticipants')
            ->setLabel('Instructeurs inscrits')
            ->formatValue(function ($value, $entity) {
                return implode(', ', array_map(function($instructor) {
                    return $instructor->getFirstname() . ' ' . $instructor->getLastname();
                }, $entity->getInstructorParticipants()->toArray()));
            });
        yield CollectionField::new('studentParticipants')
            ->setLabel('Étudiants inscrits')
            ->formatValue(function ($value, $entity) {
                return implode(', ', array_map(function($student) {
                    return $student->getFirstname() . ' ' . $student->getLastname();
                }, $entity->getStudentParticipants()->toArray()));
            });
    
    }
    
}
