<?php
namespace App\Core\Exception;

class NotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct('404 not found');
    }
}