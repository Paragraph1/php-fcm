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
}