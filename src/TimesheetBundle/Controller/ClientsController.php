<?php
namespace TimesheetBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TimesheetBundle\Entity\Clients;


class ClientsController extends Controller {

    function indexAction() {

        $em = $this->getDoctrine()->getManager();
        $clients = $em->getRepository('TimesheetBundle:Clients')->findAll();
        $projects = array();
        foreach ($clients as $client) {
            $projects[$client->getId()] = sizeof($em->getRepository('TimesheetBundle:Projects')->findBy(array('clientId' => $client->getId())));
        }
        dump($projects);

        return $this->render('clients/index.html.twig', array(
            'clients' => $clients,
            'projects' => $projects
        ));

    }

    public function newAction(Request $request)
    {
        $clients = new Clients();
        $form = $this->createForm('TimesheetBundle\Form\ClientsType', $clients);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clients);
            $em->flush($clients);

            return $this->redirectToRoute('clients_index',array());
        }

        return $this->render('clients/new.html.twig', array(
            'clients' => $clients,
            'form' => $form->createView(),
        ));
    }
}