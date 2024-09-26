<?php

namespace App\Controller\Admin;

use App\Entity\Student;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    }
}