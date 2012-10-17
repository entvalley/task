<?php

namespace Entvalley\AppBundle\Service;

use BadFaith\Negotiator;

class ContentNegotiator
{
    private $negotiator;

    public function __construct(Negotiator $negotiator)
    {
        $this->negotiator = $negotiator;
    }

    public function getPreferredType($acceptHeader)
    {
        $className = $this->negotiator->listClass('ACCEPT');
        $best_encoding = new $className($acceptHeader);
        $preferred = $best_encoding->getPreferred();
        if (!empty($preferred)) {
            return $preferred->pref;
        }
    }
}
