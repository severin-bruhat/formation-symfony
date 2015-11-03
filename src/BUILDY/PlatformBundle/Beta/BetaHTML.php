<?php
// src/BUILDY/PlatformBundle/Beta/BetaHTML.php

namespace BUILDY\PlatformBundle\Beta;

use Symfony\Component\HttpFoundation\Response;

class BetaHTML
{
  public function displayBeta(Response $response, $remainingDays)
  {
    $content = $response->getContent();

    // Code à rajouter
    $html = '<span style="color: red; font-size: 0.5em;"> - Beta J-'.(int) $remainingDays.' !</span>';

    // Insertion du code dans la page, dans le premier <h1>
    $content = preg_replace(
      '#<h1>(.*?)</h1>#iU',
      '<h1>$1'.$html.'</h1>',
      $content,
      1
    );

    // Modification du contenu dans la réponse
    $response->setContent($content);

    return $response;
  }
}
