<?php
/**
 * Created by PhpStorm.
 * User: tangui
 * Date: 02/02/17
 * Time: 14:44
 */

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\AdvertSkill;
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
        if($page < 1)
        {
            throw new NotFoundHttpException('page "' . $page .'" inexistante');
        }

        $listAdverts = array(
            array(
                'title'   => 'Recherche développpeur Symfony',
                'id'      => 1,
                'author'  => 'Alexandre',
                'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
                'date'    => new \Datetime()),
            array(
                'title'   => 'Mission de webmaster',
                'id'      => 2,
                'author'  => 'Hugo',
                'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
                'date'    => new \Datetime()),
            array(
                'title'   => 'Offre de stage webdesigner',
                'id'      => 3,
                'author'  => 'Mathieu',
                'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
                'date'    => new \Datetime())
        );
        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts,
        ));

    }

    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
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
        $em = $this->getDoctrine()->getManager();

        $advert = new Advert();
        $advert->setTitle('Recherche développeur Symfony.');
        $advert->setAuthor('Alexandre');
        $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");

        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');

        // On lie l'image à l'annonce
        $advert->setImage($image);

        // Création d'une première candidature
        $application1 = new Application();
        $application1->setAuthor('Marine');
        $application1->setContent("J'ai toutes les qualités requises.");

        // Création d'une deuxième candidature par exemple
        $application2 = new Application();
        $application2->setAuthor('Pierre');
        $application2->setContent("Je suis très motivé.");

        // On lie les candidatures à l'annonce
        $application1->setAdvert($advert);
        $application2->setAdvert($advert);

        $listSkills = $em->getRepository("OCPlatformBundle:Skill")->findAll();

        foreach ($listSkills as $skill)
        {
            $advertSkill = new AdvertSkill();
            $advertSkill->setAdvert($advert);
            $advertSkill->setSkill($skill);
            $advertSkill->setLevel('Expert');
            $em->persist($advertSkill);
        }


        // Étape 1 : On « persiste » l'entité
        $em->persist($advert);

        // Étape 1 ter : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
        // définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
        $em->persist($application1);
        $em->persist($application2);

        // Étape 2 : On « flush » tout ce qui a été persisté avant
        $em->flush();
        if($request->isMethod('POST'))
        {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistré');
            return $this->redirectToRoute('oc_platform_view', array('id' => 5));
        }
        return $this->render('OCPlatformBundle:Advert:add.html.twig');
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository("OCPlatformBundle:Advert")->find($id);

        if(null === $advert)
        {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }

        $listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();


        foreach ($listCategories as $category)
        {
            $advert->addCategory($category);
        }

        $em->flush();

        if($request->isMethod('POST'))
        {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifié');

            return $this->redirectToRoute('oc_platform_view', array('id' => 5));
        }
        $advert = array(
            'title'   => 'Recherche développpeur Symfony',
            'id'      => $id,
            'author'  => 'Alexandre',
            'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
            'date'    => new \Datetime()
        );
        return $this->render('OCPlatformBundle:Advert:edit.html.twig',array(
        'advert' => $advert));
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // On boucle sur les catégories de l'annonce pour les supprimer
        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

        // On déclenche la modification
        $em->flush();
        return $this->render('OCPlatformBundle:Advert:delete.html.twig');
    }

    public function formAction()
    {
        return $this->render('OCPlatformBundle:Advert:form.html.twig');
    }

    public function menuAction()
    {
        $listAdverts = array(
          array('id' => 2, 'title' => 'Recherche des développeurs Symfony'),
          array('id' => 5, 'title' => 'Mission de webmaster'),
          array('id' => 9, 'title' => 'Offre de stage de webdesigner'),

        );

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
            'listAdverts' => $listAdverts,
        ));
    }
    public function lastThreeAdvertAction()
    {
        $listAdverts = array(
            array('id' => 2, 'title' => 'Recherche des développeurs Symfony'),
            array('id' => 5, 'title' => 'Mission de webmaster'),
            array('id' => 9, 'title' => 'Offre de stage de webdesigner'),
        );

        return $this->render('OCPlatformBundle:Advert:lastThreeAdvert.html.twig', array(
            'listAdverts' => $listAdverts,
        ));
    }
}