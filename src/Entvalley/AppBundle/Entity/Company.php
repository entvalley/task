<?php

namespace Entvalley\AppBundle\Entity;

class Company
{
    private $id;
    private $name;
    private $owner;

    public function __construct($id = null)
    {
        if ($id) {
            $this->id = $id;
        }
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function equals(Company $another = null)
    {
        return $another !== null && $another->getId() == $this->id;
    }
}