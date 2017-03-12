<?php

namespace App\Parser;

use App\Entity\Item;

/**
 * Class ItemParser
 *
 * Parse and array to an item object
 *
 * @package App\Parser
 */
class ItemParser
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return Item
     */
    public function parse()
    {
        $item = new Item();

        $item->setUrl($this->parseUrl());
        $item->setTags($this->parseTags());
        $item->setCollectors($this->parseCollectors());

        return $item;
    }

    /**
     * @return mixed
     */
    protected function parseUrl()
    {
        if (!isset($this->data['url']) || empty($this->data['url'])) {
            throw new \RuntimeException('Malformed data. url index is missing.');
        }

        return $this->data['url'];
    }

    /**
     * @return array
     */
    protected function parseTags()
    {
        if (isset($this->data['tags']) && !is_array($this->data['tags'])) {
            throw new \RuntimeException('Malformed tags index. Expecting array.');
        }
        $tags = array();
        if (isset($this->data['tags'])) {
            foreach ($this->data['tags'] as $tag) {
                if (!is_string($tag)) {
                    throw new \RuntimeException('Malformed tags index. Expecting string elements.');
                }

                $tags[] = (string)$tag;
            }
        }

        return $tags;
    }

    /**
     * @return array
     */
    protected function parseCollectors()
    {
        if (isset($this->data['collectors']) && !is_array($this->data['collectors'])) {
            throw new \RuntimeException('Malformed collectors index. Expecting array.');
        }

        $collectors = array();
        if (isset($this->data['collectors'])) {
            foreach ($this->data['collectors'] as $collector) {
                $parser = new CollectorParser($collector);

                $collectors[] = $parser->parse();
            }
        }

        return $collectors;
    }
}