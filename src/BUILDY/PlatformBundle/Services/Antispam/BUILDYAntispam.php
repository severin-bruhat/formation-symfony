<?php
// src/BUILDY/PlatformBundle/Services/Antispam/BUILDYAntispam.php

namespace BUILDY\PlatformBundle\Services\Antispam;

class BUILDYAntispam extends \Twig_Extension
{
  private $mailer;
  private $locale;
  private $minLength;

  public function __construct(\Swift_Mailer $mailer, $minLength)
  {
    $this->mailer    = $mailer;
    $this->minLength = (int) $minLength;
  }

  /**
   * Vérifie si le texte est un spam ou non
   *
   * @param string $text
   * @return bool
   */
    public function isSpam($text)
    {
      return strlen($text) < $this->minLength;
    }

    // Et on ajoute un setter
    public function setLocale($locale)
    {
      $this->locale = $locale;
    }

  // Twig va exécuter cette méthode pour savoir quelle(s) fonction(s) ajoute notre service
  public function getFunctions()
  {
    return array(
      'checkIfSpam' => new \Twig_Function_Method($this, 'isSpam')
    );
  }


  // La méthode getName() identifie votre extension Twig, elle est obligatoire
  public function getName()
  {
    return 'OCAntispam';
  }
}
