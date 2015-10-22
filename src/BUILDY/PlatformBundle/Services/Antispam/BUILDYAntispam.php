<?php
// src/BUILDY/PlatformBundle/Services/Antispam/BUILDYAntispam.php

namespace BUILDY\PlatformBundle\Services\Antispam;

class BUILDYAntispam
{
  /**
   * Vérifie si le texte est un spam ou non
   *
   * @param string $text
   * @return bool
   */
    public function isSpam($text)
    {
      return strlen($text) < 50;
    }
}
