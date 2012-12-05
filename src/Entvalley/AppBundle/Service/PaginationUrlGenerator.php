<?php

namespace Entvalley\AppBundle\Service;

interface PaginationUrlGenerator
{
    public function generate($page);
}