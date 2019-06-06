<?php
namespace App\task;

class SendSmsTask
{
    public function handler($data=[])
    {
        echo "start send sms" . PHP_EOL;
        sleep(10);
        echo 'send sms finished' . PHP_EOL;
        return true;
    }
}