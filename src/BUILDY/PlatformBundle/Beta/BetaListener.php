<?php
// src/BUILDY/PlatformBundle/Beta/BetaListener.php

namespace BUILDY\PlatformBundle\Beta;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener
{
   protected $betaHTML;

    // La date de fin de la version bêta :
    // - Avant cette date, on affichera un compte à rebours (J-3 par exemple)
    // - Après cette date, on n'affichera plus le « bêta »
    protected $endDate;

    public function __construct(BetaHTML $betaHTML, $endDate)
    {
      $this->betaHTML = $betaHTML;
      $this->endDate  = new \Datetime($endDate);
    }
    public function processBeta(FilterResponseEvent $event)
    {
      // On teste si la requête est bien la requête principale (et non une sous-requête)
      if (!$event->isMasterRequest()) {
        return;
      }

      $remainingDays = $this->endDate->diff(new \Datetime())->format('%d');

       // Si la date est dépassée, on ne fait rien
       if ($remainingDays <= 0) {
        return;
       }

       // On utilise notre BetaHRML
       $response = $this->betaHTML->displayBeta($event->getResponse(), $remainingDays);
      // Puis on insère la réponse modifiée dans l'évènement
       $event->setResponse($response);
    }
}
