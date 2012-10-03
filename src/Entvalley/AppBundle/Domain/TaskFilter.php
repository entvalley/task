<?php

namespace Entvalley\AppBundle\Domain;

class TaskFilter
{
    private $map = array(
        'unresolved' => array(
            Status::UNASSIGNED
        ),
        'inprogress' => array(
            Status::REOPENED,
            Status::ACCEPTED
        )
    );

    private $statusGroup = '';

    public function thatAre($statusGroup)
    {
        $this->statusGroup = strtolower($statusGroup);
    }

    public function getStatuses()
    {
        if (isset($this->map[$this->statusGroup])) {
            return $this->map[$this->statusGroup];
        }

        return null;
    }
}