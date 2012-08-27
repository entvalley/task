<?php

namespace Entvalley\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class EntvalleyUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
