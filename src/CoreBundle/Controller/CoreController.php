<?php
/**
 * Created by PhpStorm.
 * User: tangui
 * Date: 24/02/17
 * Time: 14:58
 */

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class CoreController extends Controller
{
    public function indexAction()
    {

        return $this->render("CoreBundle:Core:index.html.twig");
    }

    public function contactAction(Request $request)
    {
        $session = $request->getSession();
        $session->getFlashBag()->add('info', 'La page de contact n\'est pas encore disponible, merci de revenir plus tard.');

        return $this->redirectToRoute('core_homepage');
    }

}