<?php

namespace App\Collectors\Builder;

use App\Entity\Item;

/**
 * Class InstagramBuilder
 *
 * @package App\Collectors\Builder
 */
class InstagramBuilder extends AbstractBuilder
{
    protected $hostPattern = '/^(|.+\.)instagram\.com$/i';
    protected $pathPattern = '/^(\/p\/.+?\/)(\?.+|)$/i';

    public function build(Item $data)
    {
        $url = $data->getUrl();
        $parts = parse_url($url);

        if ($parts['path'] == '/' || empty($parts['path'])) {
            // Homepage
            $item = $this->buildHomepageItem($data);
        }
        elseif (preg_match($this->pathPattern, $parts['path'], $matches) !== false) {
            // Media page
            $item = $this->buildMediapageItem($data);
        }
        else {
            throw new \RuntimeException('Could not parse this instagram URL');
        }

        return $item;
    }

    /**
     * @param $data Item
     * @return Item
     */
    protected function buildHomepageItem($data)
    {
        $item = new Item();

        foreach ($data->getCollectors() as $collector) {
            if ($collector->getName() == '') {

            }
        }

        return $item;
    }

    /**
     * @param $data Item
     * @return Item
     */
    protected function buildMediapageItem($data)
    {
        $item = new Item();

        return $item;
    }
}