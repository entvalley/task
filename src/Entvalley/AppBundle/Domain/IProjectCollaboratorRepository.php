<?php

namespace Entvalley\AppBundle\Domain;

use Entvalley\AppBundle\Entity\Project;
use Entvalley\AppBundle\Entity\User;

interface IProjectCollaboratorRepository
{
    function findByUserAndProject(User $user, Project $project);
}