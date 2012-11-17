<?php

namespace Entvalley\AppBundle\Entity;

use Entvalley\AppBundle\Service\ProjectStatsService;

class Project
{
    private $id;
    private $name;
    private $createdAt;
    private $projectStatsService;
    private $inprogressNumber = 0;
    private $totalNumber = 0;
    private $unresolvedNumber = 0;

    const MAX_CANONICAL_NAME_LENGTH = 20;

    public function __construct($id = null)
    {
        $this->createdAt = new \DateTime();
        if ($id) {
            $this->id = $id;
        }
        $this->projectStatsService = new ProjectStatsService();
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

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getInprogressNumber()
    {
        return $this->projectStatsService->getInprogressNumber();
    }

    public function getTotalNumber()
    {
        return $this->projectStatsService->getTotalNumber();
    }

    public function getUnresolvedNumber()
    {
        return $this->projectStatsService->getUnresolvedNumber();
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

    public function getProjectStatsService()
    {
        if (empty($this->projectStatsService)) {
            $this->projectStatsService = new ProjectStatsService();
        }
        return $this->projectStatsService;
    }

    public function loadStats()
    {
        $this->inprogressNumber = $this->projectStatsService->getInprogressNumber();
        $this->totalNumber = $this->projectStatsService->getTotalNumber();
        $this->unresolvedNumber = $this->projectStatsService->getUnresolvedNumber();
    }

    private function generateCanonicalName()
    {
        $cleanName = preg_replace('/[^a-zа-яё0-9 _-]/ui', '', $this->getName());
        $finalName = preg_replace('/ {1,}/', '-', mb_strtolower($cleanName));

        return substr(trim($finalName, '-'), 0, self::MAX_CANONICAL_NAME_LENGTH);
    }
}
