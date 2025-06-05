<?php

namespace Domnikl\Statsd;

/**
 * Basic implementation of StatsdAwareInterface.
 */
trait StatsdAwareTrait
{
    protected Client $statsd;

    /**
     * Sets the StatsD client.
     */
    public function setStatsdClient(Client $client): void
    {
        $this->statsd = $client;
    }
}
