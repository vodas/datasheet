<?php
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Doctrine\UserManager;
class MenuController extends Controller
{

    public function menuAction()
    {
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();


        return $this->render('menu.html.twig', array( 'users' => $users
        ));
    }
}