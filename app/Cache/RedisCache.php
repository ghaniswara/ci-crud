<?php

namespace App\Cache;
use Predis\Client;
use Config\Redis;

class RedisCache 
{
    private static $instance = null;
    private $client;

    public $DurationMonth = 60 * 60 * 24 * 30;
    public $DurationHour = 60 * 60;
    
    private function __construct()
    {
        $redisConfig = new Redis();
        $client = new Client([
            'scheme' => 'tcp',
            'host'   => $redisConfig->host,
            'port'   => $redisConfig->port,
        ]);
        $this->client = $client;
    }

    private function __clone() {}

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getClient()
    {
        return $this->client;
    }
}

