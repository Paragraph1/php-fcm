<?php
namespace paragraph1\phpFCM;

/**
 * @author palbertini
 *
 */
class Message
{

	private $notification;


	public function setNotification(Notification $notification)
	{
		$this->notification = $notification;
		return $this;
	}

}