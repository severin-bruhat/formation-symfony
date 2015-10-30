<?php

// src/BUILDY/PlatformBundle/Controller/AdvertController.php

namespace BUILDY\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use BUILDY\PlatformBundle\Entity\Advert;
use BUILDY\PlatformBundle\Entity\Image;
use BUILDY\PlatformBundle\Entity\Application;
use BUILDY\PlatformBundle\Entity\AdvertSkill;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if ($page < 1) {
          // On déclenche une exception NotFoundHttpException, cela va afficher
          // une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
          throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }

        $listAdverts = array(
          array(
            'title'   => 'Recherche développpeur Symfony2',
            'id'      => 1,
            'author'  => 'Alexandre',
            'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
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

        return $this->render('BUILDYPlatformBundle:Advert:index.html.twig', array('listAdverts' => $listAdverts));
    }

    public function viewAction($id){

        $em = $this->getDoctrine()->getManager();


        // On récupère l'entité correspondante à l'id $id
        $advert = $em
          ->getRepository('BUILDYPlatformBundle:Advert')
          ->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // On récupère la liste des candidatures de cette annonce
        $listApplications = $em
          ->getRepository('BUILDYPlatformBundle:Application')
          ->findBy(array('advert' => $advert))
        ;

        // On récupère maintenant la liste des AdvertSkill
        $listAdvertSkills = $em
          ->getRepository('BUILDYPlatformBundle:AdvertSkill')
          ->findBy(array('advert' => $advert))
        ;

        return $this->render('BUILDYPlatformBundle:Advert:view.html.twig', array(
          'advert'           => $advert,
          'listApplications' => $listApplications,
          'listAdvertSkills' => $listAdvertSkills
        ));
    }

    public function addAction(Request $request){

      $em = $this->getDoctrine()->getManager();

      // Création de l'entité
      $advert = new Advert();
      $advert->setTitle('Recherche développeur Symfony2.');
      $advert->setAuthor('Séverin');
      $advert->setContent("Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…");

      // Création de l'entité Image
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

      // On récupère toutes les compétences possibles
      $listSkills = $em->getRepository('BUILDYPlatformBundle:Skill')->findAll();

      // Pour chaque compétence
      foreach ($listSkills as $skill) {
        // On crée une nouvelle « relation entre 1 annonce et 1 compétence »
        $advertSkill = new AdvertSkill();

        // On la lie à l'annonce, qui est ici toujours la même
        $advertSkill->setAdvert($advert);
        // On la lie à la compétence, qui change ici dans la boucle foreach
        $advertSkill->setSkill($skill);

        // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
        $advertSkill->setLevel('Expert');

        // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
        $em->persist($advertSkill);
      }


      // Étape 1 : On « persiste » l'entité
      $em->persist($advert);

      // Étape 1 bis : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
      // définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
      $em->persist($application1);
      $em->persist($application2);

      // Étape 2 : On « flush » tout ce qui a été persisté avant
      $em->flush();


        // La gestion d'un formulaire est particulière, mais l'idée est la suivante :
        // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
        if ($request->isMethod('POST')) {

            // Ici, on s'occupera de la création et de la gestion du formulaire
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
            // Puis on redirige vers la page de visualisation de cettte annonce
            return $this->redirect($this->generateUrl('buildy_platform_view', array('id' => $advert->getId())));
        }

        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->render('BUILDYPlatformBundle:Advert:add.html.twig');
    }

    public function editAction($id, Request $request)
    {
        if ($request->isMethod('POST')) {
          $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
          return $this->redirect($this->generateUrl('buildy_platform_view', array('id' => 5)));
        }

        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository('BUILDYPlatformBundle:Advert')->find($id);

        if (null === $advert) {
          throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // La méthode findAll retourne toutes les catégories de la base de données
        $listCategories = $em->getRepository('BUILDYPlatformBundle:Category')->findAll();

        // On boucle sur les catégories pour les lier à l'annonce
        foreach ($listCategories as $category) {
          $advert->addCategory($category);
        }

        $em->flush();


        return $this->render('BUILDYPlatformBundle:Advert:edit.html.twig', array(
          'advert' => $advert
        ));
    }

    public function deleteAction($id)
    {
      $em = $this->getDoctrine()->getManager();

      // On récupère l'annonce $id
      $advert = $em->getRepository('BUILDYPlatformBundle:Advert')->find($id);

      if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
      }

      // On boucle sur les catégories de l'annonce pour les supprimer
      foreach ($advert->getCategories() as $category) {
      $advert->removeCategory($category);
      }
      $em->flush();

      return $this->render('BUILDYPlatformBundle:Advert:delete.html.twig');
    }

    public function menuAction($limit)
    {
        // On fixe en dur une liste ici, bien entendu par la suite
        // on la récupérera depuis la BDD !
        $listAdverts = array(
          array('id' => 2, 'title' => 'Recherche développeur Symfony2'),
          array('id' => 5, 'title' => 'Mission de webmaster'),
          array('id' => 9, 'title' => 'Offre de stage webdesigner')
        );

        return $this->render('BUILDYPlatformBundle:Advert:menu.html.twig', array(
          // Tout l'intérêt est ici : le contrôleur passe
          // les variables nécessaires au template !
          'listAdverts' => $listAdverts
        ));
    }

    public function testAction(Request $request){

            $em = $this->getDoctrine()->getManager();

            $advert = $em->getRepository('BUILDYPlatformBundle:Advert')->find(2);

            // Création d'une première candidature
            $application1 = new Application();
            $application1->setAuthor('moi');
            $application1->setContent("test");
            $application1->setAdvert($advert);

            $em->persist($application1);
            $em->flush();

            //message flash
            $session = $request->getSession();
            $session->getFlashBag()->add('info', 'done !');

            return $this->render('BUILDYPlatformBundle:Advert:test.html.twig');

    }
}
