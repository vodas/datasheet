<?php

namespace TimesheetBundle\Controller;

use TimesheetBundle\Entity\DayReport;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Dayreport controller.
 *
 */
class DayReportController extends Controller
{
    /**
     * Lists all dayReport entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dayReports = $em->getRepository('TimesheetBundle:DayReport')->findAll();

        return $this->render('dayreport/index.html.twig', array(
            'dayReports' => $dayReports,
        ));
    }

    /**
     * Creates a new dayReport entity.
     *
     */
    public function newAction(Request $request)
    {
        $dayReport = new Dayreport();
        $form = $this->createForm('TimesheetBundle\Form\DayReportType', $dayReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dayReport);
            $em->flush($dayReport);

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
    public function editAction(Request $request, DayReport $dayReport)
    {
        $deleteForm = $this->createDeleteForm($dayReport);
        $editForm = $this->createForm('TimesheetBundle\Form\DayReportType', $dayReport);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
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

        return $this->redirectToRoute('dayreport_index');
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
}
