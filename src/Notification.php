<?php
namespace phpFCM;

class Notification
{
	private $title;
	private $body;
	private $badge;

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBadge($badge)
    {
        $this->badge = $badge;
        return $this;
    }

    public function getBadge()
    {
        return $this->badge;
    }

    public function toJson()
    {
    	$json = array(
    		'title' => $this->getTitle(),
    		'body' => $this->getBody()
    	);
    	if ($this->getBadge()) {
    		$json['badge'] = $this->getBadge();
    	}

        return json_encode($json);
    }
}