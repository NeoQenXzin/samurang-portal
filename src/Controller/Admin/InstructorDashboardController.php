<?php

namespace App\Controller\Admin;

use App\Entity\Student;
use App\Entity\Formation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class InstructorDashboardController extends AbstractDashboardController
{
    #[Route('/mydojang', name: 'instructor_dashboard')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_INSTRUCTOR');

        return $this->render('admin/instructor_dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('My Dojang');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Students', 'fas fa-user-check', Student::class)
            ->setController(InstructorStudentCrudController::class);
        yield MenuItem::linkToCrud('Formations', 'fas fa-graduation-cap', Formation::class)
            ->setController(InstructorFormationCrudController::class);
    }
}
