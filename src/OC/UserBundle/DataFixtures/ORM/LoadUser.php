<?php
/**
 * Created by PhpStorm.
 * User: tangui
 * Date: 28/02/17
 * Time: 15:52
 */

namespace OC\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use OC\UserBundle\Entity\User;

class LoadUser implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $listNames = array('allexandre', 'Marine', 'Anna');

        foreach ($listNames as $name)
        {
            $user = new User;

            $user->setUsername($name);
            $user->setPassword($name);

            $user->setSalt('');
            $user->setRoles('ROLE_USER');

            $manager->persist($user);
        }

        $manager->flush($user);
    }
}