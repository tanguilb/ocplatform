<?php
/**
 * Created by PhpStorm.
 * User: tangui
 * Date: 28/02/17
 * Time: 10:06
 */

namespace OC\PlatformBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    public function getLikeQueryBuilder($pattern)
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.name LIKE :pattern')
            ->setParameter('pattern', $pattern);
    }
}