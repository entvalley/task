<?php

namespace Entvalley\AppBundle\Entity;

class Project
{
    private $id;
    private $name;
    private $inprogressNumber = 0;
    private $totalNumber = 0;
    private $unresolvedNumber = 0;
    private $createdAt;

    const MAX_CANONICAL_NAME_LENGTH = 20;

    public function __construct($id = null)
    {
        $this->createdAt = new \DateTime();
        if ($id) {
            $this->id = $id;
        }
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

    public function getCanonicalName()
    {
        return $this->generateCanonicalName();
    }

    public function belongsToCompany(Company $company)
    {
        return $company->equals($this->company);
    }

    private function generateCanonicalName()
    {
        $cleanName = preg_replace('/[^a-zа-яё0-9 _-]/ui', '', $this->getName());
        $finalName = preg_replace('/ {1,}/', '-', mb_strtolower($cleanName));

        return substr(trim($finalName, '-'), 0, self::MAX_CANONICAL_NAME_LENGTH);
    }
}
