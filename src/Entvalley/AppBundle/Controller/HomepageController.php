<?php

namespace Entvalley\AppBundle\Controller;

class HomepageController extends Controller
{
    public function indexAction()
    {
        return $this->view();
    }
}
