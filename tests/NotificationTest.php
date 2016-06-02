<?php
namespace paragraph1\phpFCM\Tests;

use paragraph1\phpFCM\Notification;

class NotificationTest extends PhpFcmTestCase
{
    private $fixture;

    protected function setUp()
    {
        parent::setUp();
        $this->fixture = new Notification('foo', 'bar');
    }

    public function testJsonSerializeWithMinimalConfigurations()
    {
        $this->assertEquals(array('title' => 'foo', 'body' =>'bar'), $this->fixture->jsonSerialize());
    }

    public function testJsonSerializeWithBadge()
    {
        $this->fixture->setBadge(1);
        $this->assertEquals(array('title' => 'foo', 'body' =>'bar', 'badge' => 1), $this->fixture->jsonSerialize());
    }

    public function testJsonSerializeWithIcon()
    {
        $this->fixture->setIcon('name');
        $this->assertEquals(array('title' => 'foo', 'body' =>'bar', 'icon' => 'name'), $this->fixture->jsonSerialize());
    }

    public function testJsonSerializeWithClickAction()
    {
        $this->fixture->setClickAction('INTENT_NAME');
        $this->assertEquals(array('title' => 'foo', 'body' =>'bar', 'click_action' => 'INTENT_NAME'), $this->fixture->jsonSerialize());
    }

    public function testJsonSerializeWithSound()
    {
        $this->fixture->setSound('mySound.mp3');
        $this->assertEquals(array('title' => 'foo', 'body' =>'bar', 'sound' => 'mySound.mp3'), $this->fixture->jsonSerialize());
    }

    public function testJsonSerializeWithColor()
    {
        $this->fixture->setColor('#ffffff');
        $this->assertEquals(array('title' => 'foo', 'body' =>'bar', 'color' => '#ffffff'), $this->fixture->jsonSerialize());
    }

    public function testJsonSerializeWithTag()
    {
        $this->fixture->setTag('foo');
        $this->assertEquals(array('title' => 'foo', 'body' =>'bar', 'tag' => 'foo'), $this->fixture->jsonSerialize());
    }
}