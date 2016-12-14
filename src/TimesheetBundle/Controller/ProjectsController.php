<?php
namespace TimesheetBundle\Controller;

use TimesheetBundle\Entity\Projects;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Project controller.
 *
 */
class ProjectsController extends Controller
{
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
        $deleteForm = $this->createDeleteForm($project);
        $timeReports = array();
        $projectReport = $this->getDoctrine()->getManager()->getRepository('TimesheetBundle:ProjectReport')->findBy(
            array('projectId' => $project->getId()));


        foreach ($projectReport as $report) {
            $dayReport = $this->getDoctrine()->getManager()->getRepository('TimesheetBundle:DayReport')->findOneBy(
                array('id' => $report->getDayReportId()));
            $username = $this->get('fos_user.user_manager')->findUserBy(array('id' => $dayReport->getUserId()))->getUsername();
            array_push($timeReports, array('time' => $report->getTimeSpent(),'date' => $dayReport->getDate(), 'user' => $username));
        }

        return $this->render('projects/show.html.twig', array(
            'project' => $project,
            'delete_form' => $deleteForm->createView(),
            'timereports' => $timeReports
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
