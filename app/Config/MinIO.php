<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Minio extends BaseConfig
{
    public $endpoint = 'http://localhost:9000'; 
    public $key = 'minio_access_key';
    public $secret = 'minio_secret_key';
    public $bucket = 'pr-app';
}
