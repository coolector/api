<?php

namespace App\Parser;

use App\Entity\Collector;

/**
 * Class CollectorParser
 *
 * @package App\Parser
 */
class CollectorParser
{
    protected $data;

    /**
     * CollectorParser constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Parse array data to Collector object
     *
     * @return Collector
     */
    public function parse()
    {
        $collector = new Collector();

        if (!isset($this->data['name'])) {
            throw new \RuntimeException('Malformed collector data. Missing name index.');
        }
        $collector->setName($this->data['name']);

        if (isset($this->data['value'])) {
            $collector->setValue($this->data['value']);
        }

        return $collector;
    }
}