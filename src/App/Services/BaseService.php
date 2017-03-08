<?php

namespace App\Services;

use Doctrine\DBAL\Connection;

class BaseService
{
    /**
     * @var Connection
     */
    protected $db;

    /**
     * BaseService constructor.
     *
     * @param $db Connection
     */
    public function __construct($db)
    {
        $this->db = $db;
    }
}
