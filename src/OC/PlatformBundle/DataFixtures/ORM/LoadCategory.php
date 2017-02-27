<?php
/**
 * Created by PhpStorm.
 * User: tangui
 * Date: 27/02/17
 * Time: 10:27
 */

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Category;

class LoadCategory implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $names = array(
            'Dévellopement web',
            'Développement mobile',
            'Graphisme',
            'Intégration',
            'Réseau',
        );

        foreach ($names as $name)
        {
            $category = new Category();
            $category->setName($name);

            $manager->persist($category);
        }

        $manager->flush();
    }

}