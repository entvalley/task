<?php

namespace Entvalley\AppBundle\Domain;

class Status
{
    const UNASSIGNED = 1;
    const ACCEPTED = 2;
    const CLOSED = 3;
    const REJECTED = 4;
    const REOPENED = 5;
    const WILLNOTFIX = 6;
}
