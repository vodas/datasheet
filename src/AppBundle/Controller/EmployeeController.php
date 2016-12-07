<?php
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\UserBundle\Doctrine\UserManager;

class EmployeeController extends Controller
{
    /**
     * Matches /employee/
     *
     * @Route("/employee/", name="employee_index")
     */
    public function indexAction()
    {
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();


        return $this->render('employee.html.twig', array( 'users' => $users
        ));
    }

//    /**
//     * Matches /employee/*
//     *
//     * @Route("/employee/{userid}", name="employee_employee")
//     */
//    public function employeeAction($userid)
//    {
//        $userManager = $this->get('fos_user.user_manager');
//        $users = $userManager->findUsers();
//
//
//        return $this->render('employee.html.twig', array( 'users' => $users
//        ));
//    }
}