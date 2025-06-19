<?php

namespace Domnikl\Statsd;

/**
 * Basic implementation of StatsdAwareInterface.
 */
trait StatsdAwareTrait
{
    /**
     * @var Client
     */
    protected $statsd;

    /**
     * Sets the StatsD client.
     */
    public function setStatsdClient(Client $client): void
    {
        $this->statsd = $client;
    }
}
