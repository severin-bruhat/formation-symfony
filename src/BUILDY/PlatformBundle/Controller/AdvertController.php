<?php

// src/BUILDY/PlatformBundle/Controller/AdvertController.php

namespace BUILDY\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if ($page < 1) {
          // On déclenche une exception NotFoundHttpException, cela va afficher
          // une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
          throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
        return $this->render('BUILDYPlatformBundle:Advert:index.html.twig', array('page' => $page));
    }
    
    public function viewAction($id){
        return $this->render('BUILDYPlatformBundle:Advert:view.html.twig', array( 'id' => $id ));
    }
    
    public function addAction(Request $request){
        // La gestion d'un formulaire est particulière, mais l'idée est la suivante :
        // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
        if ($request->isMethod('POST')) {
            // Ici, on s'occupera de la création et de la gestion du formulaire
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
            // Puis on redirige vers la page de visualisation de cettte annonce
            return $this->redirect($this->generateUrl('buildy_platform_view', array('id' => 5)));
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
        
        return $this->render('BUILDYPlatformBundle:Advert:edit.html.twig');
    }

    public function deleteAction($id)
    {
        // Ici, on récupérera l'annonce correspondant à $id
        // Ici, on gérera la suppression de l'annonce en question
        return $this->render('BUILDYPlatformBundle:Advert:delete.html.twig');
    }
    
}
