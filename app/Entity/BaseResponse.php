<?php

namespace App\Entity;

class BaseResponse
{
    public $status;
    public $message;
    public $data;

    public function createResponse($status, $message, $data)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;

        return $this;
    }
}

