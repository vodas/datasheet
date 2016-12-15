<?php
namespace TimesheetBundle\Controller;

use TimesheetBundle\Entity\Projects;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;
use \DateInterval;

/**
 * Project controller.
 *
 */
class ProjectsController extends Controller
{

    const STARTDATE = '2016-01-01';
    /**
     * Lists all project entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $projects = $em->getRepository('TimesheetBundle:Projects')->findAll();

        return $this->render('projects/index.html.twig', array(
            'projects' => $projects,
        ));
    }

    /**
     * Creates a new project entity.
     *
     */
    public function newAction(Request $request)
    {
        $project = new Projects();
        $form = $this->createForm('TimesheetBundle\Form\ProjectsType', $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush($project);

            return $this->redirectToRoute('projects_show', array('id' => $project->getId()));
        }

        return $this->render('projects/new.html.twig', array(
            'project' => $project,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a project entity.
     *
     */
    public function showAction(Projects $project)
    {
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



        $deleteForm = $this->createDeleteForm($project);
        $timeReports = array();
        $projectReport = $this->getDoctrine()->getManager()->getRepository('TimesheetBundle:ProjectReport')->findBy(
            array('projectId' => $project->getId()));

        $users = array();
        foreach ($projectReport as $report) {
            $dayReport = $this->getDoctrine()->getManager()->getRepository('TimesheetBundle:DayReport')->findOneBy(
                array('id' => $report->getDayReportId()));
            $username = $this->get('fos_user.user_manager')->findUserBy(array('id' => $dayReport->getUserId()))->getUsername();
            if(!array_key_exists($username, $users)) {
                $users[$username] = $dayReport->getUserId();
            }
            array_push($timeReports, array('time' => $report->getTimeSpent(),'date' => $dayReport->getDate(),'comment' => $report->getComment(), 'user' => $username, 'userid' => $dayReport->getUserId()));
        }

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

        foreach ($timeReports as $timeReport) {
            $Dates[$timeReport['date']->format('F')][$timeReport['date']->format('Y-m-d')]['users'][$timeReport['userid']][$timeReport['comment']] = $timeReport['time'];
        }


        return $this->render('projects/show.html.twig', array(
            'project' => $project,
            'delete_form' => $deleteForm->createView(),
            'users' => $users,
            'dates' => $Dates
        ));
    }

    /**
     * Displays a form to edit an existing project entity.
     *
     */
    public function editAction(Request $request, Projects $project)
    {
        $deleteForm = $this->createDeleteForm($project);
        $editForm = $this->createForm('TimesheetBundle\Form\ProjectsType', $project);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('projects_edit', array('id' => $project->getId()));
        }

        return $this->render('projects/edit.html.twig', array(
            'project' => $project,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a project entity.
     *
     */
    public function deleteAction(Request $request, Projects $project)
    {
        $form = $this->createDeleteForm($project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $projectReports = $this->getDoctrine()->getManager()->getRepository('TimesheetBundle:ProjectReport')->findBy(
                array('projectId' => $project->getId()));
            foreach ($projectReports as $projectReport) {
                $em->remove($projectReport);
                $em->flush($projectReport);
            }
            $em->remove($project);
            $em->flush($project);
        }

        return $this->redirectToRoute('projects_index');
    }

    /**
     * Creates a form to delete a project entity.
     *
     * @param Projects $project The project entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Projects $project)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('projects_delete', array('id' => $project->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
