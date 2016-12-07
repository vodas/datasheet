<?php

namespace TimesheetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TimesheetBundle:Default:index.html.twig');
    }
}
