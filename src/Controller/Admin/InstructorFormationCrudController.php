<?php

namespace App\Controller\Admin;

use App\Entity\Formation;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class InstructorFormationCrudController extends AbstractCrudController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Formation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Formation')
            ->setEntityLabelInPlural('Formations')
            ->setPageTitle('index', 'My Formations')
            ->setPageTitle('new', 'Create Formation')
            ->setPageTitle('edit', 'Edit Formation');
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
        yield AssociationField::new('organizer')
            ->setFormTypeOption('disabled', true)
            ->setFormTypeOption('data', $this->security->getUser())
            ->setRequired(true);
        yield IntegerField::new('participantsCount')
            ->setLabel('Nombre de participants')
            ->setFormTypeOption('disabled', true);
        yield CollectionField::new('instructorParticipants')
            ->setLabel('Instructeurs inscrits')
            ->setFormTypeOption('disabled', true)
            ->formatValue(function ($value, $entity) {
                return implode(', ', array_map(function ($instructor) {
                    return $instructor->getFirstname() . ' ' . $instructor->getLastname();
                }, $entity->getInstructorParticipants()->toArray()));
            });
        yield CollectionField::new('studentParticipants')
            ->setLabel('Étudiants inscrits')
            ->setFormTypeOption('disabled', true)
            ->formatValue(function ($value, $entity) {
                return implode(', ', array_map(function ($student) {
                    return $student->getFirstname() . ' ' . $student->getLastname();
                }, $entity->getStudentParticipants()->toArray()));
            });
    }


    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // Filtrer les formations pour n'afficher que celles de l'instructeur connecté
        $qb->andWhere('entity.organizer = :instructor')
            ->setParameter('instructor', $this->getUser());

        return $qb;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance->getOrganizer()) {
            $entityInstance->setOrganizer($this->security->getUser());
        }
        parent::persistEntity($entityManager, $entityInstance);
    }
}
