<?php

namespace Entvalley\AppBundle\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->view();
    }
}
