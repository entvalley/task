<?php

namespace Entvalley\AppBundle\Domain;

use Entvalley\AppBundle\Entity\Project;

class TaskFilter
{
    private $map = [
        'unresolved' => [
            Status::UNASSIGNED
        ],
        'inprogress' => [
            Status::REOPENED,
            Status::ACCEPTED
        ]
    ];

    private $project = null;
    private $statusGroup = '';

    public function thatAre($statusGroup)
    {
        $this->statusGroup = strtolower($statusGroup);
    }

    public function withinProject(Project $project)
    {
        $this->project = $project;
    }

    public function getWithinProject()
    {
        return $this->project;
    }

    public function getStatuses()
    {
        if (isset($this->map[$this->statusGroup])) {
            return $this->map[$this->statusGroup];
        }

        return null;
    }
}