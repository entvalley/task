<?php

namespace Entvalley\AppBundle\Controller;

use Mzz\MzzBundle\Controller\Controller;
use Entvalley\AppBundle\Domain\Command\ReceivedCommand;
use Entvalley\AppBundle\Entity\User;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->view();
    }
}
