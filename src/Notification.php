<?php
namespace phpFCM;

class Notification extends Message
{
    public function toJson()
    {
        return json_encode($this);
    }
}