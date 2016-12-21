<?php
namespace TimesheetBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TimesheetBundle\Entity\Clients;
use \DateTime;
use \DateInterval;


class ClientsController extends Controller {

    function indexAction() {

        $em = $this->getDoctrine()->getManager();
        $clients = $em->getRepository('TimesheetBundle:Clients')->findAll();
        $projects = array();
        foreach ($clients as $client) {
            $projects[$client->getId()] = sizeof($em->getRepository('TimesheetBundle:Projects')->findBy(array('clientId' => $client->getId())));
        }

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

    /**
     * @return mixed
     */
    public function showAction(Clients $clients) {

        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository('TimesheetBundle:Projects')->findBy(array('clientId' => $clients->getId()));

        $usersArray = array();
        $users = $this->get('fos_user.user_manager')->findUsers();
        foreach ($users as $user) {
            $usersArray[$user->getId()] = $user->getUsername();
        }

        $projectReports = array();
        foreach ($projects as $project) {
            $reports = $em->getRepository('TimesheetBundle:ProjectReport')->findBy(array('projectId' => $project->getId()));
            foreach ($reports as $report) {
                array_push($projectReports, $report);
            }
        }


        $startDate = new DateTime(date("Y")."-01-01");
        $endDate = clone $startDate;
        $endDate->add(new DateInterval("P1Y"));
        while ($startDate->getTimestamp() < $endDate->getTimestamp()) {
            $Dates[$startDate->format('F')][$startDate->format('Y-m-d')]['day'] = $startDate->format('D');
            $startDate->add(new DateInterval("P1D"));
        }

        foreach ($projectReports as $projectReport) {
            $dayReport = $em->getRepository('TimesheetBundle:DayReport')->find($projectReport->getDayReportId());
            $Dates[$dayReport->getDate()->format('F')][$dayReport->getDate()->format('Y-m-d')]['reports'][$projectReport->getId()]['report'] = $projectReport;
            $Dates[$dayReport->getDate()->format('F')][$dayReport->getDate()->format('Y-m-d')]['reports'][$projectReport->getId()]['user'] = $usersArray[$dayReport->getUserId()];
            $Dates[$dayReport->getDate()->format('F')][$dayReport->getDate()->format('Y-m-d')]['reports'][$projectReport->getId()]['project'] = $em->getRepository('TimesheetBundle:Projects')->find($projectReport->getProjectId())->getName();
        }

        $month = date('F');
        
        return $this->render('clients/show.html.twig', array(
            'client' => $clients,
            'projects' => $projects,
            'dates' => $Dates,
            'currentMonth' => $month
        ));
    }
}