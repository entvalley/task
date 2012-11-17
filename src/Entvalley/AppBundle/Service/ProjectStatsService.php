<?php

namespace Entvalley\AppBundle\Service;

class ProjectStatsService
{
    private $unresolvedNumber;
    private $inprogressNumber;
    private $totalNumber;
    private $loaded = false;
    private $loadCallback;

    public function __construct(\Closure $loadCallback = null)
    {
        $this->loadCallback = $loadCallback;
    }

    public function getInprogressNumber()
    {
        $this->_load();
        return $this->inprogressNumber;
    }

    public function getTotalNumber()
    {
        $this->_load();
        return $this->totalNumber;
    }

    public function getUnresolvedNumber()
    {
        $this->_load();
        return $this->unresolvedNumber;
    }

    public function setInprogressNumber($inprogressNumber)
    {
        $this->loaded = true;
        $this->inprogressNumber = $inprogressNumber;
    }

    public function setTotalNumber($totalNumber)
    {
        $this->loaded = true;
        $this->totalNumber = $totalNumber;
    }

    public function setUnresolvedNumber($unresolvedNumber)
    {
        $this->loaded = true;
        $this->unresolvedNumber = $unresolvedNumber;
    }

    public function setLoadCallback(\Closure $loadCallback)
    {
        $this->loadCallback = $loadCallback;
    }

    private function _load()
    {
        if (!is_callable($this->loadCallback) || $this->loaded) {
            return;
        }
        $callback = $this->loadCallback;
        $callback($this);
    }
}
