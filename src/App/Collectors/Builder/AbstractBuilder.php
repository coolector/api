<?php

namespace App\Collectors\Builder;
use App\Entity\Item;

/**
 * Class AbstractBuilder
 * @package App\Collectors\Builder
 */
abstract class AbstractBuilder
{
    protected $hostPattern;

    /**
     * @return mixed
     */
    public function getHostPatter()
    {
        return $this->hostPattern;
    }

    /**
     * Parse incoming item and return managed item
     *
     * @param Item $data
     * @return Item $item
     */
    abstract public function build(Item $data);
}