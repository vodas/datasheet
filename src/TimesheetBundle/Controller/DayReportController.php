<?php

namespace TimesheetBundle\Controller;

use TimesheetBundle\Entity\DayReport;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;
use \DateInterval;
use TimesheetBundle\Entity\DayReportForm;
use TimesheetBundle\Entity\ProjectReport;

/**
 * Dayreport controller.
 *
 */
class DayReportController extends Controller
{
    const STARTDATE = '2016-01-01';
    /**
     * Lists all dayReport entities.
     *
     */
    public $freeDays;

    function __construct() {
        if($this->freeDays === null) {
            #dodanie listy swiat ruchomych
            #wialkanoc
            $startDate = new DateTime(self::STARTDATE);
            $easter = date('m-d', easter_date($startDate->format('Y')));
            #poniedzialek wielkanocny
            $easterSec = date('m-d', strtotime('+1 day', strtotime( $startDate->format('Y') . '-' . $easter) ));
            #boze cialo
            $bozeCialo = date('m-d', strtotime('+60 days', strtotime( $startDate->format('Y') . '-' . $easter) ));
            #Zesłanie Ducha Świętego
            $zeslanie = date('m-d', strtotime('+49 days', strtotime( $startDate->format('Y') . '-' . $easter) ));
            $this->freeDays = array('01-01', '01-06', '05-01', '05-03', '08-15', '11-01', '11-11', '12-25', '12-26', $easter, $easterSec, $bozeCialo, $zeslanie);
        }
    }

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dayReports = $em->getRepository('TimesheetBundle:DayReport')->findAll();

        return $this->render('dayreport/index.html.twig', array(
            'dayReports' => $dayReports,
        ));
    }

    public function employeeAction($userid) {
        $em = $this->getDoctrine()->getManager();
        $startDate = new DateTime(self::STARTDATE);
        $endDate = clone $startDate;
        $endDate->add(new DateInterval("P1Y"));
        while ($startDate->getTimestamp() < $endDate->getTimestamp()) {
            $Dates[$startDate->format('F')][$startDate->format('Y-m-d')]['day'] = $startDate->format('D');
            if(in_array($startDate->format('m-d'),$this->freeDays)) {
                $Dates[$startDate->format('F')][$startDate->format('Y-m-d')]['free'] = 1;
            }
            $startDate->add(new DateInterval("P1D"));
        }

        $dayReports = $em->getRepository('TimesheetBundle:DayReport')->findBy(array('userId' => $userid));
        foreach ($dayReports as $dayReport) {
            $date = $dayReport->getDate()->format('Y-m-d');
            $month = $dayReport->getDate()->format('F');
            $Dates[$month][$date]['start'] = $dayReport->getStart();
            $Dates[$month][$date]['end'] = $dayReport->getEnd();
            $Dates[$month][$date]['id'] = $dayReport->getId();
            $Dates[$month][$date]['can_edit'] = $dayReport->getCanEdit();
            $time = new DateTime();
            date_timestamp_set($time, $dayReport->getEnd()->getTimestamp() - $dayReport->getStart()->getTimestamp() -60*60);
            $Dates[$month][$date]['time'] = $time->format('H:i');

        }

        $leaves = $em->getRepository('TimesheetBundle:Leaves')->findBy(array('userId' => $userid));
        foreach ($leaves as $leave) {
            $date = $leave->getDate()->format('Y-m-d');
            $month = $leave->getDate()->format('F');
            $Dates[$month][$date]['free'] = 'leave';
        }

        $currentMonth = date('F');
        return $this->render('dayreport/index.html.twig', array(
            'dates' => $Dates,
            'currentMonth' => $currentMonth,
            'userid' => $userid,
            'username' => $this->get('fos_user.user_manager')->findUserBy(array('id' => $userid))->getUsername()
        ));
    }

    public function mysheetAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $startDate = new DateTime(self::STARTDATE);
        $endDate = clone $startDate;
        $endDate->add(new DateInterval("P1Y"));
        while ($startDate->getTimestamp() < $endDate->getTimestamp()) {
            $Dates[$startDate->format('F')][$startDate->format('Y-m-d')]['day'] = $startDate->format('D');
            if(in_array($startDate->format('m-d'),$this->freeDays)) {
                $Dates[$startDate->format('F')][$startDate->format('Y-m-d')]['free'] = 1;
            }
            $startDate->add(new DateInterval("P1D"));
        }

        $dayReports = $em->getRepository('TimesheetBundle:DayReport')->findBy(array('userId' => $user->getId()));
        foreach ($dayReports as $dayReport) {
            $date = $dayReport->getDate()->format('Y-m-d');
            $month = $dayReport->getDate()->format('F');
            $Dates[$month][$date]['start'] = $dayReport->getStart();
            $Dates[$month][$date]['end'] = $dayReport->getEnd();
            $Dates[$month][$date]['id'] = $dayReport->getId();
            $Dates[$month][$date]['can_edit'] = $dayReport->getCanEdit();
            $time = new DateTime();
            date_timestamp_set($time, $dayReport->getEnd()->getTimestamp() - $dayReport->getStart()->getTimestamp() -60*60);
            $Dates[$month][$date]['time'] = $time->format('H:i');
        }
        $leaves = $em->getRepository('TimesheetBundle:Leaves')->findBy(array('userId' => $user->getId()));
        foreach ($leaves as $leave) {
            $date = $leave->getDate()->format('Y-m-d');
            $month = $leave->getDate()->format('F');
            $Dates[$month][$date]['free'] = 'leave';
        }
        $currentMonth = date('F');
        return $this->render('dayreport/mysheet.html.twig', array(
            'dates' => $Dates,
            'currentMonth' => $currentMonth
        ));
    }

    /**
     * Creates a new dayReport entity.
     *
     */
    public function newAction(Request $request, $date)
    {
        $dayReportForm = new DayReportForm();
        $dayReport = new DayReport();
        $dayReportForm->setDate(new DateTime($date));
        $projects = array();
        $projects['nie wybrano'] = 0;
        $projectEntity = $this->getDoctrine()->getRepository('TimesheetBundle:Projects')->findAll();
        foreach($projectEntity as $entity) {
            $projects[$entity->getName()] = $entity->getId();
        }
        $form = $this->createForm('TimesheetBundle\Form\DayReportType', $dayReportForm, array('projects' => $projects));
        $form->handleRequest($request);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userId = $user->getId();
        $dayReports = $this->getDoctrine()->getManager()->getRepository('TimesheetBundle:DayReport')->findBy(
            array('userId' => $userId, 'date' => $dayReport->getDate()));

        if($dayReports!=null) {
            return $this->render('dayreport/new.html.twig', array(
                'dayReport' => $dayReport,
                'form' => $form->createView(),
                'error' => 1
            ));
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $dayReport->setStart($dayReportForm->getStart());
            $dayReport->setEnd($dayReportForm->getEnd());
            $dayReport->setDate($dayReportForm->getDate());


            if (($dayReport->getEnd()->getTimestamp()-$dayReport->getStart()->getTimestamp()) <= 0 ) {
                return $this->render('dayreport/new.html.twig', array(
                    'dayReport' => $dayReport,
                    'form' => $form->createView(),
                    'error' => 2
                ));
            }
            
            if ($dayReport->getDate()->format('D') == 'Sat' or $dayReport->getDate()->format('D') == 'Sun') {
                return $this->render('dayreport/new.html.twig', array(
                    'dayReport' => $dayReport,
                    'form' => $form->createView(),
                    'error' => 3
                ));
            }

           if(in_array($dayReport->getDate()->format('m-d'),$this->freeDays)) {
               return $this->render('dayreport/new.html.twig', array(
                   'dayReport' => $dayReport,
                   'form' => $form->createView(),
                   'error' => 4
               ));
           }

            $dayReport->setUserId($userId);
            $dayReport->setCanEdit(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($dayReport);
            $em->flush($dayReport);

            if($dayReportForm->getProjectId1() != 0 && $dayReportForm->getTimeSpent1()->getTimestamp() >= 0) {
                $projectReport = new ProjectReport();
                $projectReport->setProjectId($dayReportForm->getProjectId1());
                $projectReport->setTimeSpent($dayReportForm->getTimeSpent1());
                $projectReport->setDayReportId($dayReport->getId());
                $projectReport->setComment($dayReportForm->getComment1());
                $em->persist($projectReport);
                $em->flush($projectReport);
            }

            if($dayReportForm->getProjectId2() != 0 && $dayReportForm->getTimeSpent2()->getTimestamp() >= 0) {
                $projectReport = new ProjectReport();
                $projectReport->setProjectId($dayReportForm->getProjectId2());
                $projectReport->setTimeSpent($dayReportForm->getTimeSpent2());
                $projectReport->setDayReportId($dayReport->getId());
                $projectReport->setComment($dayReportForm->getComment2());
                $em->persist($projectReport);
                $em->flush($projectReport);
            }

            if($dayReportForm->getProjectId3() != 0 && $dayReportForm->getTimeSpent3()->getTimestamp() >= 0) {
                $projectReport = new ProjectReport();
                $projectReport->setProjectId($dayReportForm->getProjectId3());
                $projectReport->setTimeSpent($dayReportForm->getTimeSpent3());
                $projectReport->setDayReportId($dayReport->getId());
                $projectReport->setComment($dayReportForm->getComment3());
                $em->persist($projectReport);
                $em->flush($projectReport);
            }
            
            
            return $this->redirectToRoute('dayreport_show', array('id' => $dayReport->getId()));
        }

        return $this->render('dayreport/new.html.twig', array(
            'dayReport' => $dayReport,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a dayReport entity.
     *
     */

    public function employeeShowAction(DayReport $dayReport)
    {
        $reports = array();
        $projectReports = $this->getDoctrine()->getManager()->getRepository('TimesheetBundle:ProjectReport')->findBy(
            array('dayReportId' => $dayReport->getId()));
        foreach ($projectReports as $projectReport) {
            $projectName = $this->getDoctrine()->getRepository('TimesheetBundle:Projects')->findOneBy(array('id' => $projectReport->getProjectId()))->getName();
            array_push($reports, array('projectName' => $projectName, 'time' => $projectReport->getTimeSpent(), 'comment' => $projectReport->getComment()));
        }

        $deleteForm = $this->createDeleteForm($dayReport);
        return $this->render('dayreport/employee_show.html.twig', array(
            'dayReport' => $dayReport,
            'username' => $this->get('fos_user.user_manager')->findUserBy(array('id' => $dayReport->getUserId()))->getUsername(),
            'delete_form' => $deleteForm->createView(),
            'reports' => $reports
        ));
    }


    public function showAction(DayReport $dayReport)
    {
        $reports = array();
        $projectReports = $this->getDoctrine()->getManager()->getRepository('TimesheetBundle:ProjectReport')->findBy(
            array('dayReportId' => $dayReport->getId()));
        foreach ($projectReports as $projectReport) {
            $projectName = $this->getDoctrine()->getRepository('TimesheetBundle:Projects')->findOneBy(array('id' => $projectReport->getProjectId()))->getName();
            array_push($reports, array('projectName' => $projectName, 'time' => $projectReport->getTimeSpent(), 'comment' => $projectReport->getComment()));
        }
        $deleteForm = $this->createDeleteForm($dayReport);

        return $this->render('dayreport/show.html.twig', array(
            'dayReport' => $dayReport,
            'delete_form' => $deleteForm->createView(),
            'reports' => $reports
        ));
    }

    /**
     * Displays a form to edit an existing dayReport entity.
     *
     */
    public function editAction(Request $request, DayReport $dayReport)
    {
        $deleteForm = $this->createDeleteForm($dayReport);
        $dayReportForm = new DayReportForm();
        $projectReport = $this->getDoctrine()->getManager()->getRepository('TimesheetBundle:ProjectReport')->findBy(
            array('dayReportId' => $dayReport->getId()));
        $dayReportForm->setStart($dayReport->getStart());
        $dayReportForm->setEnd($dayReport->getEnd());
        $dayReportForm->setDate($dayReport->getDate());
        if($projectReport != null) {
            $i = 1;
            foreach ($projectReport as $report) {
                //$dayReportForm->setTimeSpent($report->getTimeSpent());
                //$dayReportForm->setProjectId($report->getProjectId());
                $functionName = '';
                $$functionName = 'setProjectId'.$i;
                $dayReportForm->${$functionName}($report->getProjectId());
                $$functionName = 'setTimeSpent'.$i;
                $dayReportForm->${$functionName}($report->getTimeSpent());
                $$functionName = 'setComment'.$i;
                $dayReportForm->${$functionName}($report->getComment());
                $i++;
            }
        }

        $projects = array();
        $projects['nie wybrano'] = 0;
        $projectEntity = $this->getDoctrine()->getRepository('TimesheetBundle:Projects')->findAll();
        foreach($projectEntity as $entity) {
            $projects[$entity->getName()] = $entity->getId();
        }

        $editForm = $this->createForm('TimesheetBundle\Form\DayReportType', $dayReportForm, array('projects' => $projects));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            if (($dayReport->getEnd()->getTimestamp()-$dayReport->getStart()->getTimestamp()) <= 0 ) {
                return $this->render('dayreport/edit.html.twig', array(
                    'dayReport' => $dayReport,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'error' => 2
                ));
            }

            $dayReport->setStart($dayReportForm->getStart());
            $dayReport->setEnd($dayReportForm->getEnd());

            $i = 1;
            foreach ($projectReport as $report) {
                $functionGetTime = '';
                $$functionGetTime = 'getTimeSpent'.$i;
                $functionGetProject = '';
                $$$functionGetProject = 'getProjectId'.$i;
                $functionGetComment = '';
                $$$$functionGetComment = 'getComment'.$i;


                if ($dayReportForm->${$$functionGetProject}() != 0 && $dayReportForm->${$functionGetTime}()->getTimestamp() >= 0) {
                    $report->setTimeSpent($dayReportForm->${$functionGetTime}());
                    $report->setProjectId($dayReportForm->${$$functionGetProject}());
                    $report->setComment($dayReportForm->${$$$functionGetComment}());
                }
                $i ++;
            }
           /*
            if ($dayReportForm->getProjectId1() != 0 && $dayReportForm->getTimeSpent1()->getTimestamp() >= 0) {
                $projectReport->setTimeSpent($dayReportForm->getTimeSpent1());
                $projectReport->setProjectId($dayReportForm->getProjectId1());
            }*/


            
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dayreport_edit', array('id' => $dayReport->getId()));
        }

        return $this->render('dayreport/edit.html.twig', array(
            'dayReport' => $dayReport,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a dayReport entity.
     *
     */
    public function deleteAction(Request $request, DayReport $dayReport)
    {
        $form = $this->createDeleteForm($dayReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $projectReports = $this->getDoctrine()->getManager()->getRepository('TimesheetBundle:ProjectReport')->findBy(
                array('dayReportId' => $dayReport->getId()));
            foreach ($projectReports as $projectReport) {
                $em->remove($projectReport);
                $em->flush($projectReport);
            }

            $em->remove($dayReport);
            $em->flush($dayReport);
        }

        return $this->redirectToRoute('dayreport_mysheet');
    }

    /**
     * Creates a form to delete a dayReport entity.
     *
     * @param DayReport $dayReport The dayReport entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DayReport $dayReport)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dayreport_delete', array('id' => $dayReport->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function massAction(Request $request) {
        $canEdit = $request->request->get('can_edit');
        $userId = $request->request->get('employee_id');
        $em = $this->getDoctrine()->getManager();
        $dayReports = $em->getRepository('TimesheetBundle:DayReport')->findBy(array('userId' => $userId));
        foreach ($dayReports as $dayReport) {
            if(array_key_exists($dayReport->getId(), $canEdit)) {
                $dayReport->setCanEdit($canEdit[$dayReport->getId()]);
                $em->persist($dayReport);
                $em->flush($dayReport);

            }
        }
        return $this->redirectToRoute('dayreport_employee', array('userid' => $userId));

    }
}
