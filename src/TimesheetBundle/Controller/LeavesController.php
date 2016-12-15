<?php
namespace TimesheetBundle\Controller;

use TimesheetBundle\Entity\Leaves;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LeavesController extends Controller
{
    function myLeavesAction() {

        
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $myLeaves = $em->getRepository('TimesheetBundle:Leaves')->findBy(array('userId' => $user->getId()));
        

        dump($myLeaves);


        return $this->render('leaves/index.html.twig', array(
            'myleaves' => $myLeaves,
            'username' => $user->getUsername()
        ));
        
    }
    
    function newAction(Request $request) {

        $leave = new Leaves();
        $form = $this->createForm('TimesheetBundle\Form\LeavesType', $leave);
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $leave->setUserId($user->getId());
            $em->persist($leave);
            $em->flush($leave);

            return $this->redirectToRoute('leaves', array());
        }

        return $this->render('leaves/new.html.twig', array(
            'leaves' => $leave,
            'form' => $form->createView(),
        ));
    }
}