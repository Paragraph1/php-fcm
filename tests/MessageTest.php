<?php
namespace paragraph1\phpFCM\Tests;

use paragraph1\phpFCM\Recipient\Recipient;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Topic;
use paragraph1\phpFCM\Notification;
use paragraph1\phpFCM\Recipient\Device;

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
        $this->setExpectedException(\InvalidArgumentException::class, 'mixed recepient types are not supported by FCM');
        $this->fixture->addRecipient(new Topic('breaking-news'))
            ->addRecipient(new Device('token'));
    }

    public function testThrowsExceptionWhenNoRecepientWasAdded()
    {
        $this->setExpectedException(\UnexpectedValueException::class, 'message must have at least one recipient');
        $this->fixture->jsonSerialize();
    }

    public function testWorksCorrectlyWithMultipleTopics()
    {
        $body = '{"condition":"\'topic1\' in topics || \'topic2\' in topics","data":{"foo":"bar"},"priority":"high"}';

        $this->fixture->addRecipient(new Topic('topic1'))
            ->addRecipient(new Topic('topic2'))
            ->setData(['foo' => 'bar']);

        $this->assertSame(
            $body,
            json_encode($this->fixture)
        );
    }

    public function testJsonEncodeWorksOnTopicRecipients()
    {
        $body = '{"to":"\/topics\/breaking-news","priority":"high","notification":{"title":"test","body":"a nice testing notification"}}';

        $notification = new Notification('test', 'a nice testing notification');
        $this->fixture->setNotification($notification);

        $this->fixture->addRecipient(new Topic('breaking-news'));
        $this->assertSame(
            $body,
            json_encode($this->fixture)
        );
    }

    public function testJsonEncodeWorksOnDeviceRecipients()
    {
        $body = '{"to":"deviceId","priority":"high","notification":{"title":"test","body":"a nice testing notification"}}';

        $notification = new Notification('test', 'a nice testing notification');
        $this->fixture->setNotification($notification);

        $this->fixture->addRecipient(new Device('deviceId'));
        $this->assertSame(
            $body,
            json_encode($this->fixture)
        );
    }

    public function testAddingMultipleDeviceRecipientsAddsRegistrationIds()
    {
        $body = '{"registration_ids":["deviceId","anotherDeviceId"],"priority":"high","notification":{"title":"test","body":"a nice testing notification"}}';

        $notification = new Notification('test', 'a nice testing notification');
        $this->fixture->setNotification($notification);

        $this->fixture->addRecipient(new Device('deviceId'))
            ->addRecipient(new Device('anotherDeviceId'));

        $this->assertSame(
            $body,
            json_encode($this->fixture)
        );
    }

    public function testJsonEncodeCorrectlyHandlesCollapseKeyAndData()
    {
        $body = '{"to":"\/topics\/testing","collapse_key":"collapseMe","data":{"foo":"bar"},"priority":"normal"}';

        $this->fixture->setData(['foo' => 'bar'])
            ->setCollapseKey('collapseMe')
            ->setPriority(Message::PRIORITY_NORMAL);

        $this->fixture->addRecipient(new Topic('testing'));
        $this->assertSame(
            $body,
            json_encode($this->fixture)
        );
    }

    public function testJsonEncodeHandlesTTL()
    {
        $body = '{"to":"\/topics\/testing","data":{"foo":"bar"},"priority":"high","time_to_live":3}';

        $this->fixture->setData(['foo' => 'bar'])
            ->setTimeToLive(3);

        $this->fixture->addRecipient(new Topic('testing'));

        $this->assertSame(
            $body,
            json_encode($this->fixture)
        );
    }

    public function testJsonEncodeHandlesDelayIdle()
    {
        $body = '{"to":"\/topics\/testing","data":{"foo":"bar"},"priority":"high","delay_while_idle":true}';

        $this->fixture->setData(['foo' => 'bar'])
            ->setDelayWhileIdle(true);

        $this->fixture->addRecipient(new Topic('testing'));

        $this->assertSame(
            $body,
            json_encode($this->fixture)
        );
    }

    public function testAddingNewAndUnknownRecipientTypesYieldsException()
    {
        $this->setExpectedException(\UnexpectedValueException::class);
        $this->fixture->addRecipient(\Mockery::mock(Recipient::class));
    }
}
