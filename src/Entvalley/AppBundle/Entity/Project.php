<?php

namespace Entvalley\AppBundle\Entity;

class Project
{
    private $id;
    private $name;
    private $inprogressNumber;
    private $totalNumber;
    private $unresolvedNumber;
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @var Company $company
     */
    private $company;

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    public function setInprogressNumber($inprogressNumber)
    {
        $this->inprogressNumber = $inprogressNumber;
    }

    public function getInprogressNumber()
    {
        return $this->inprogressNumber;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setTotalNumber($totalNumber)
    {
        $this->totalNumber = $totalNumber;
    }

    public function getTotalNumber()
    {
        return $this->totalNumber;
    }

    public function setUnresolvedNumber($unresolvedNumber)
    {
        $this->unresolvedNumber = $unresolvedNumber;
    }

    public function getUnresolvedNumber()
    {
        return $this->unresolvedNumber;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getId()
    {
        return $this->id;
    }
}
