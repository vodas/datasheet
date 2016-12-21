<?php
namespace TimesheetBundle\Controller;

use TimesheetBundle\Entity\Leaves;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LeavesController extends Controller
{
    function myLeavesAction(Request $request) {

        $error = $request->query->get('error');
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $myLeaves = $em->getRepository('TimesheetBundle:Leaves')->findBy(array('userId' => $user->getId()));

        $now = new \DateTime('now');
        $month = $now->format('m');
        $summary = floor($month*1.67);
        $mySummary = sizeof($myLeaves);

        return $this->render('leaves/index.html.twig', array(
            'myleaves' => $myLeaves,
            'username' => $user->getUsername(),
            'summary' => $summary,
            'mysummary' => $mySummary,
            'error' => $error
        ));
        
    }
    
    function newAction(Request $request) {

        $leave = new Leaves();
        $leave->setDate(new \DateTime('now'));
        $form = $this->createForm('TimesheetBundle\Form\LeavesType', $leave);
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $myLeaves = $em->getRepository('TimesheetBundle:Leaves')->findBy(array('date' => $leave->getDate(),'userId' => $user->getId()));
            if ($myLeaves != null) {
                $error = 1;
                return $this->redirectToRoute('leaves', array('error' => $error));
            }
            $myReports = $em->getRepository('TimesheetBundle:DayReport')->findBy(array('date' => $leave->getDate(),'userId' => $user->getId()));
            if ($myReports != null) {
                $error = 2;
                return $this->redirectToRoute('leaves', array('error' => $error));
            }
            if($leave->getDate()->format('D') == 'Sun' || $leave->getDate()->format('D') == 'Sat') {
                $error = 3;
                return $this->redirectToRoute('leaves', array('error' => $error));
            }

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

    function employeeAction($userid) {

        $user = $this->get('fos_user.user_manager')->findUserBy(array('id' => $userid));
        $em = $this->getDoctrine()->getManager();
        $leaves = $em->getRepository('TimesheetBundle:Leaves')->findBy(array('userId' => $user->getId()));
        return $this->render('leaves/employee.html.twig', array(
            'username' => $user->getUsername(),
            'leaves' => $leaves
        ));
    }
    
    function deleteAction() {
    }
}