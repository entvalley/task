<?php

namespace Entvalley\AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class HomepageController extends Controller
{
    public function indexAction()
    {
        return $this->view();
    }
}
