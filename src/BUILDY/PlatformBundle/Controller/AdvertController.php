<?php

// src/BUILDY/PlatformBundle/Controller/AdvertController.php

namespace BUILDY\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdvertController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BUILDYPlatformBundle:Advert:index.html.twig', array('name' => $name));
    }
}
