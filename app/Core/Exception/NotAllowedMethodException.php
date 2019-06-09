<?php
namespace App\Core\Exception;

class NotAllowedMethodException extends \Exception
{
    public function __construct()
    {
        parent::__construct('403 method not allowed');
    }
}