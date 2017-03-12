<?php

namespace App\Collectors;
use App\Collectors\Builder\AbstractBuilder;
use App\Collectors\Builder\InstagramBuilder;

/**
 * Class BuilderSwitcher
 *
 * Based on received URL a builder is selected to create an item
 *
 * @package App\Collectors
 */
class BuilderSwitcher
{
    protected $url;

    /**
     * @var AbstractBuilder[]
     */
    protected $builders;

    /**
     * BuilderSwitcher constructor.
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $url;

        $this->builders = [
            new InstagramBuilder()
        ];
    }

    /**
     * Select builder based on submitted URL
     */
    public function getBuilder()
    {
        $parts = parse_url($this->url);
        if (!isset($parts['host'])) {
            throw new \RuntimeException('Malformed URL. Could not determine a valid host name.');
        }

        foreach ($this->builders as $builder) {
            $pattern = $builder->getHostPatter();

            if (preg_match($pattern, $parts['host']) !== false) {
                return $builder;
            }
        }

        return null;
    }
}