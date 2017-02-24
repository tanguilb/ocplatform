<?php
/**
 * Created by PhpStorm.
 * User: tangui
 * Date: 24/02/17
 * Time: 14:58
 */

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class CoreController extends Controller
{
    public function indexAction()
    {
        return $this->render("CoreBundle:Core:index.html.twig");
    }

}