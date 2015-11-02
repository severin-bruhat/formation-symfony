<?php

namespace BUILDY\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BUILDYUserBundle extends Bundle
{
  public function getParent()
  {
    return 'FOSUserBundle';
  }
}
