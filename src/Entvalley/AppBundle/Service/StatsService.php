<?php

namespace Entvalley\AppBundle\Service;

use Liuggio\StatsdClient\Factory\StatsdDataFactoryInterface;
use Liuggio\StatsdClient\StatsdClientInterface;

class StatsService implements StatsServiceInterface
{
    private $statsdFactory;
    private $statsdClient;

    public function __construct(StatsdDataFactoryInterface $statsdFactory, StatsdClientInterface $statsdClient)
    {
        $this->statsdClient = $statsdClient;
        $this->statsdFactory = $statsdFactory;
    }

    public function count($tag)
    {
        $data = $this->statsdFactory->increment($tag);
        $this->statsdClient->send($data);
    }
}
