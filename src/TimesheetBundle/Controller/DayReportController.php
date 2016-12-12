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
            $Dates[$month][$date]['comment'] = $dayReport->getComment();
            $Dates[$month][$date]['id'] = $dayReport->getId();
            $Dates[$month][$date]['can_edit'] = $dayReport->getCanEdit();
            $time = new DateTime();
            date_timestamp_set($time, $dayReport->getEnd()->getTimestamp() - $dayReport->getStart()->getTimestamp() -60*60);
            $Dates[$month][$date]['time'] = $time->format('H:i');

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
            $Dates[$month][$date]['comment'] = $dayReport->getComment();
            $Dates[$month][$date]['id'] = $dayReport->getId();
            $Dates[$month][$date]['can_edit'] = $dayReport->getCanEdit();
            $time = new DateTime();
            date_timestamp_set($time, $dayReport->getEnd()->getTimestamp() - $dayReport->getStart()->getTimestamp() -60*60);
            $Dates[$month][$date]['time'] = $time->format('H:i');

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
            $dayReport->setComment($dayReportForm->getComment());
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
            
            $projectReport = new ProjectReport();
            $projectReport->setProjectId($dayReportForm->getProjectId());
            $projectReport->setTimeSpent($dayReportForm->getTimeSpent());
            $projectReport->setDayReportId($dayReport->getId());
            $em->persist($projectReport);
            $em->flush($projectReport);
            
            
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
        $deleteForm = $this->createDeleteForm($dayReport);
        return $this->render('dayreport/employee_show.html.twig', array(
            'dayReport' => $dayReport,
            'username' => $this->get('fos_user.user_manager')->findUserBy(array('id' => $dayReport->getId()))->getUsername(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    public function showAction(DayReport $dayReport)
    {
        $deleteForm = $this->createDeleteForm($dayReport);

        return $this->render('dayreport/show.html.twig', array(
            'dayReport' => $dayReport,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing dayReport entity.
     *
     */
    public function editAction(Request $request, DayReportForm $dayReport)
    {
        $deleteForm = $this->createDeleteForm($dayReport);
        $editForm = $this->createForm('TimesheetBundle\Form\DayReportType', $dayReport);
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
