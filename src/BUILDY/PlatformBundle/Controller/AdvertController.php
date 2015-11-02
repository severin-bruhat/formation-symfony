<?php
// src/BUILDY/PlatformBundle/Controller/AdvertController.php

namespace BUILDY\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BUILDY\PlatformBundle\Entity\Advert;
use BUILDY\PlatformBundle\Form\AdvertType;
use BUILDY\PlatformBundle\Form\AdvertEditType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class AdvertController extends Controller
{
  public function indexAction($page)
  {
    if ($page < 1) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    $nbPerPage = $this->container->getParameter('nb_per_page_home');

    // Pour récupérer la liste de toutes les annonces : on utilise findAll()
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('BUILDYPlatformBundle:Advert')
      ->getAdverts($page, $nbPerPage)
    ;

    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($listAdverts)/$nbPerPage);

    // Si la page n'existe pas, on retourne une 404
    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    // L'appel de la vue ne change pas
    return $this->render('BUILDYPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts,
      'nbPages'     => $nbPages,
      'page'        => $page
    ));
  }

  public function viewAction($id)
  {
    // On récupère l'EntityManager
    $em = $this->getDoctrine()->getManager();

    // Pour récupérer une annonce unique : on utilise find()
    $advert = $em->getRepository('BUILDYPlatformBundle:Advert')->find($id);

    // On vérifie que l'annonce avec cet id existe bien
    if ($advert === null) {
      throw $this->createNotFoundException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On récupère la liste des advertSkill pour l'annonce $advert
    $listAdvertSkills = $em->getRepository('BUILDYPlatformBundle:AdvertSkill')->findByAdvert($advert);

    // Puis modifiez la ligne du render comme ceci, pour prendre en compte les variables :
    return $this->render('BUILDYPlatformBundle:Advert:view.html.twig', array(
      'advert'           => $advert,
      'listAdvertSkills' => $listAdvertSkills,
    ));
  }

  public function addAction(Request $request)
  {

     // On vérifie que l'utilisateur dispose bien du rôle ROLE_AUTEUR
     if (!$this->get('security.context')->isGranted('ROLE_AUTEUR')) {
       // Sinon on déclenche une exception « Accès interdit »
       throw new AccessDeniedException('Accès limité aux auteurs.');
     }

     $advert = new Advert();
     $form = $this->get('form.factory')->create(new AdvertType, $advert);

     if ($form->handleRequest($request)->isValid()) {

         $em = $this->getDoctrine()->getManager();
         $em->persist($advert);
         $em->flush();

         $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

         // On redirige vers la page de visualisation de l'annonce nouvellement créée
         return $this->redirect($this->generateUrl('buildy_platform_view', array('id' => $advert->getId())));
       }

    return $this->render('BUILDYPlatformBundle:Advert:add.html.twig', array(
                         'form' => $form->createView())
                     );
  }

  public function editAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('BUILDYPlatformBundle:Advert')->find($id);

    if ($advert == null) {
      throw $this->createNotFoundException("L'annonce d'id ".$id." n'existe pas.");
    }

    $form = $this->get('form.factory')->create(new AdvertEditType, $advert);

    if ($form->handleRequest($request)->isValid()) {
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

        // On redirige vers la page de visualisation de l'annonce nouvellement créée
        return $this->redirect($this->generateUrl('buildy_platform_view', array('id' => $advert->getId())));
      }

      return $this->render('BUILDYPlatformBundle:Advert:edit.html.twig', array(
                         'form' => $form->createView(),
                         'advert' => $advert)
                     );
  }

  public function deleteAction($id, Request $request)
  {
    // On récupère l'EntityManager
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('BUILDYPlatformBundle:Advert')->find($id);

    if ($advert == null) {
      throw $this->createNotFoundException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
    $form = $this->createFormBuilder()->getForm();

    if ($form->handleRequest($request)->isValid()) {
      $em->remove($advert);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

      return $this->redirect($this->generateUrl('buildy_platform_home'));
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
    return $this->render('BUILDYPlatformBundle:Advert:delete.html.twig', array(
      'advert' => $advert,
      'form'   => $form->createView()
    ));

  }

  public function menuAction($limit = 3)
  {
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('BUILDYPlatformBundle:Advert')
      ->findBy(
        array(),                 // Pas de critère
        array('date' => 'desc'), // On trie par date décroissante
        $limit,                  // On sélectionne $limit annonces
        0                        // À partir du premier
    );

    return $this->render('BUILDYPlatformBundle:Advert:menu.html.twig', array(
      'listAdverts' => $listAdverts
    ));
  }
}
