<?php
namespace phpFCM;

class Notification
{
    public function toJson()
    {
        return json_encode($this);
    }
}