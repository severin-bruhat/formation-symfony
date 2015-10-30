<?php
// src/BUILDY/PlatformBundle/DoctrineListener/ApplicationNotification.php

namespace BUILDY\PlatformBundle\DoctrineListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use BUILDY\PlatformBundle\Entity\Application;

class ApplicationNotification
{
  private $mailer;

  public function __construct(\Swift_Mailer $mailer)
  {
    $this->mailer = $mailer;
  }

  public function postPersist(LifecycleEventArgs $args)
  {
    $entity = $args->getEntity();

    // On veut envoyer un email que pour les entités Application
    if (!$entity instanceof Application) {
      return;
    }

    $message = new \Swift_Message(
      'Nouvelle candidature',
      'Vous avez reçu une nouvelle candidature.'
    );

    $message
      ->addTo('sbruhat@gmail.com')
      ->addFrom('admin@votresite.com')
    ;

    $this->mailer->send($message);
  }
}
