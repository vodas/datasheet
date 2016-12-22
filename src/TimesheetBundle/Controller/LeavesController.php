<?php
namespace TimesheetBundle\Controller;

use TimesheetBundle\Entity\Leaves;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LeavesController extends Controller
{
    function myLeavesAction(Request $request, $year) {

        $error = $request->query->get('error');
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $myLeaves = array();
        $leaves = $em->getRepository('TimesheetBundle:Leaves')->findBy(array('userId' => $user->getId()));
        foreach ($leaves as $leave) {
            if($leave->getDate()->format('Y') == $year) {
                array_push($myLeaves, $leave);
            }
        }
        
        $now = new \DateTime('now');
        $month = $now->format('m');
        $summary = floor($month*1.67);
        $mySummary = sizeof($myLeaves);
        
        $currentYear = (int)date('Y');
        $years = array();
        $startYear=2016;
        while($startYear<= $currentYear) {
            array_push($years, $startYear);
            $startYear++;
        }

        return $this->render('leaves/index.html.twig', array(
            'myleaves' => $myLeaves,
            'username' => $user->getUsername(),
            'summary' => $summary,
            'mysummary' => $mySummary,
            'error' => $error,
            'year' => $year,
            'years' => $years,
            'currentYear' => $currentYear
        ));
    }

    public function redirectAction(Request $request) {
        return $this->redirectToRoute('leaves', array(
                'year' => $request->request->get('year'))
        );
    }

    public function employeeRedirectAction(Request $request) {
        return $this->redirectToRoute('leaves_employee', array(
                'year' => $request->request->get('year'),
                'userid' => $request->request->get('userid')
            )
        );
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
                return $this->redirectToRoute('leaves', array('error' => $error, 'year' => date('Y')));
            }
            $myReports = $em->getRepository('TimesheetBundle:DayReport')->findBy(array('date' => $leave->getDate(),'userId' => $user->getId()));
            if ($myReports != null) {
                $error = 2;
                return $this->redirectToRoute('leaves', array('error' => $error, 'year' => date('Y')));
            }
            if($leave->getDate()->format('D') == 'Sun' || $leave->getDate()->format('D') == 'Sat') {
                $error = 3;
                return $this->redirectToRoute('leaves', array('error' => $error, 'year' => date('Y')));
            }

            $leave->setUserId($user->getId());
            $em->persist($leave);
            $em->flush($leave);
            return $this->redirectToRoute('leaves', array('year' => date('Y')));
        }
        return $this->render('leaves/new.html.twig', array(
            'leaves' => $leave,
            'form' => $form->createView(),
            'year' => date('Y')
        ));
    }

    function employeeAction($userid, $year) {
        
        
        $user = $this->get('fos_user.user_manager')->findUserBy(array('id' => $userid));
        $em = $this->getDoctrine()->getManager();

        $leaves = $em->getRepository('TimesheetBundle:Leaves')->findBy(array('userId' => $user->getId()));
        dump($leaves);
        foreach ($leaves as $index => $leave) {
            if($leave->getDate()->format('Y') != $year) {
                unset($leaves[$index]);
            }
        }


        $currentYear = (int)date('Y');
        $years = array();
        $startYear=2016;
        while($startYear<= $currentYear) {
            array_push($years, $startYear);
            $startYear++;
        }

        return $this->render('leaves/employee.html.twig', array(
            'username' => $user->getUsername(),
            'leaves' => $leaves,
            'year' => $year,
            'years' => $years,
            'currentYear' => $currentYear,
            'userid' => $userid
        ));
    }
    
    function deleteAction() {
    }
}