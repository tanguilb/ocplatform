<?php
/**
 * Created by PhpStorm.
 * User: tangui
 * Date: 02/02/17
 * Time: 14:44
 */

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Form\AdvertEditType;
use OC\PlatformBundle\Form\AdvertType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Application;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        if($page < 1)
        {
            throw new NotFoundHttpException('page "' . $page .'" inexistante');
        }
        $nbPerPage = 3;

        $listAdverts = $em->getRepository("OCPlatformBundle:Advert")->getAdverts($page, $nbPerPage);

        $nbPages = ceil(count($listAdverts) / $nbPerPage);
        if($page > $nbPages)
        {
            throw $this->createNotFoundException('La page ' . $page . " n'existe pas.");
        }
        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts,
            'nbPages'     => $nbPages,
            'page'        => $page,
        ));

    }

    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce ".$id." n'existe pas.");
        }

        // On récupère la liste des candidatures de cette annonce
        $listApplications = $em
            ->getRepository('OCPlatformBundle:Application')
            ->findBy(array('advert' => $advert))
        ;

        $listAdvertSkills = $em
            ->getRepository("OCPlatformBundle:AdvertSkill")
            ->findBy(array('advert' => $advert));

        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
            'advert'           => $advert,
            'listApplications' => $listApplications,
            'listAdvertSkills' => $listAdvertSkills,
        ));
    }

    public function addAction(Request $request)
    {
        $advert = new Advert;
        $form = $this->get('form.factory')->create(AdvertType::class, $advert);


        if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistré');
            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }
        return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository("OCPlatformBundle:Advert")->find($id);

        if(null === $advert)
        {
            throw new NotFoundHttpException("L'annonce d'id " . $advert->getId() . " n'existe pas.");
        }
        $form = $this->get('form.factory')->create(AdvertEditType::class, $advert);
        if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifié');
            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }
        return $this->render('OCPlatformBundle:Advert:edit.html.twig',array(
            'advert' => $advert,
            'form' => $form->createView()));
    }

    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        $form = $this->get('form.factory')->create();

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em->remove($advert);
            $em->flush();
            $request->getSession()->getFlashBag()->add("info", "L'annonce à bien été supprimé.");
            return $this->redirectToRoute('oc_platform_home');
        }

        return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(
            'advert' => $advert,
            'form'   => $form->createView(),
        ));
    }

    public function formAction()
    {
        return $this->render('OCPlatformBundle:Advert:form.html.twig');
    }

    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();
        $listAdverts = $em->getRepository("OCPlatformBundle:Advert")->findBy(
            array(),
            array('date' => 'desc'),
            $limit,
            0
        );

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
            'listAdverts' => $listAdverts,
        ));
    }
    public function lastThreeAdvertAction()
    {
        $em = $this->getDoctrine()->getManager();
        $listAdverts = $em->getRepository("OCPlatformBundle:Advert")->findAll();

        return $this->render('OCPlatformBundle:Advert:lastThreeAdvert.html.twig', array(
            'listAdverts' => $listAdverts,
        ));
    }
}