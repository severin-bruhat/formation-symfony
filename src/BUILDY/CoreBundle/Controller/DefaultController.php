<?php

namespace BUILDY\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use BUILDY\PlatformBundle\Entity\Advert;

class DefaultController extends Controller
{
    public function indexAction()
    {
      $em = $this->getDoctrine()->getManager();

      $adverts = $em
        ->getRepository('BUILDYPlatformBundle:Advert')
        ->findAll(
          array('date' => 'desc'),
          3,
          0
        );

        return $this->render('BUILDYCoreBundle:Default:index.html.twig', array('listAdverts' => $adverts));
    }

    public function contactAction(Request $request)
    {
      //message flash
      $session = $request->getSession();
      $session->getFlashBag()->add('info', 'La page de contact n’est pas encore disponible, merci de revenir plus tard');

      //redirection
      $url = $this->get('router')->generate('buildy_core_homepage');
      return new RedirectResponse($url);
    }
}
