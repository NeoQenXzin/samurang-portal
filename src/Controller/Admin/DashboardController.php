<?php

namespace App\Controller\Admin;

use App\Entity\Grade;
use App\Entity\Dojang;
use App\Entity\Student;
use App\Entity\NextOrder;
use App\Entity\Instructor;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Controller\Admin\InstructorCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(InstructorCrudController::class)->generateUrl();

        return $this->redirect($url);
    
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Samurang Portal');
    }
    


    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoRoute(
            'Back to the website',
            'fas fa-home',
            'homepage'
        );
        yield MenuItem::linkToDashboard('Dashboard', 'fas fa-meteor');
        yield MenuItem::linkToCrud('Next Order', 'fas fa-shopping-cart', NextOrder::class);
        yield MenuItem::linkToCrud(
            'Dojang',
            'fas fa-vihara',
            Dojang::class
        );
        yield MenuItem::linkToCrud(
            'Grade',
            'fas fa-dragon',
            Grade::class
        );
        yield MenuItem::linkToCrud(
            'Student',
            'fas fa-user-check',
            Student::class
        );
        yield MenuItem::linkToCrud(
            'Instructor',
            'fab fa-studiovinari',
            Instructor::class
        );
    }

    // public function configureUserMenu(UserInterface $user): UserMenu
    // {
    //     return parent::configureUserMenu($user)
    //         ->setName($user->getUsername())
    //         ->setGravatarEmail($user->getEmail())
    //         ->setAvatarUrl('https://example.com/avatar.jpg')
    //         ->addMenuItems([
    //             MenuItem::linkToRoute('My Profile', 'fa fa-id-card', '...'),
    //             MenuItem::linkToRoute('Settings', 'fa fa-user-cog', '...'),
    //             MenuItem::section(),
    //             MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
    //         ]);
    // }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('css/admin.css');
    }
}
