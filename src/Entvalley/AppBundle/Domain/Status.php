<?php

namespace Entvalley\AppBundle\Domain;

class Status
{
    const UNASSIGNED = 1;
    const ACCEPTED = 2;
    const CLOSED = 3;
    const REOPENED = 4;
    const REJECTED = 5;
    const WONTFIX = 6;

    public static function supports($name)
    {
        return defined('self::' . strtoupper($name));
    }
}
