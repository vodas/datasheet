<?php
namespace TimesheetBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TimesheetBundle\Entity\AdminConfig;
use \DateTime;
use \DateInterval;


class AdminConfigController extends Controller {

    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $adminConfig = $em->getRepository('TimesheetBundle:AdminConfig')->findAll();
        $startYear = (int)$em->getRepository('TimesheetBundle:AdminConfig')->findOneBy(array('parameter' => 'start_year'))->getValue();
        $currentYear = (int)date('Y');
        $years = array();
        $startYear++;
        while($startYear<= $currentYear) {
            array_push($years, $startYear);
            $startYear++;
        }
        return $this->render('admin/index.html.twig', array(
            'adminconfig' => $adminConfig,
            'years' => $years
        ));
        
    }
    
    public function editAction(Request $request) {
        $currentYear = $request->request->get('current_year');
        $em = $this->getDoctrine()->getManager();
        $adminConfig = $em->getRepository('TimesheetBundle:AdminConfig')->findOneBy(array('parameter' => 'current_year'));
        $adminConfig->setValue($currentYear);
        $em->persist($adminConfig);
        $em->flush($adminConfig);
        return $this->redirectToRoute('config_index');

    }
}