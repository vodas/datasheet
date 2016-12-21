<?php
namespace TimesheetBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TimesheetBundle\Entity\AdminConfig;

class LoadAdminConfigData implements FixtureInterface {
    public function load(ObjectManager $manager) {

        $adminConfig = new AdminConfig();
        $adminConfig->setParameter('start_year');
        $adminConfig->setValue('2016');
        $manager->persist($adminConfig);
        $manager->flush();

        $adminConfig = new AdminConfig();
        $adminConfig->setParameter('current_year');
        $adminConfig->setValue('2016');
        $manager->persist($adminConfig);
        $manager->flush();
    }
}