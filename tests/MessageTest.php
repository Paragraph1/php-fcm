<?php
namespace paragraph1\phpFCM\Tests;

use paragraph1\phpFCM\Recipient;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Topic;
use function GuzzleHttp\json_encode;
use paragraph1\phpFCM\Notification;

class MessageTest extends PhpFcmTestCase
{
    private $fixture;

    protected function setUp()
    {
        parent::setUp();
        $this->fixture = new Message();
    }

    public function testThrowsExceptionWhenDifferentRecepientTypesAreRegistered()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->fixture->addRecipient(new Topic('breaking-news'))
            ->addRecipient(new Recipient());
    }

    public function testJsonEncodeWorksOnRecipients()
    {
        $body = '{"to":"\'breaking-news\' in topics || \'less-breaking-news\' in topics","notification":{"title":"test","body":"a nice testing notification"}}';

        $notification = new Notification();
        $notification->setTitle('test')
            ->setBody('a nice testing notification');
        $message = new Message();
        $message->setNotification($notification);

        $message->addRecipient(new Topic('breaking-news'))
            ->addRecipient(new Topic('less-breaking-news'));

        $this->assertSame(
            $body,
            json_encode($message)
        );
    }
}