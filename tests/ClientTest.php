<?php
namespace paragraph1\phpFCM\Tests;

use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Notification;
use paragraph1\phpFCM\Topic;
use paragraph1\phpFCM\Recipient;

use GuzzleHttp;
use GuzzleHttp\Psr7\Response;

class ClientTest extends PhpFcmTestCase
{
    private $fixture;

    protected function setUp()
    {
        parent::setUp();
        $this->fixture = new Client();
    }

    public function testThrowsExceptionWhenDifferentRecepientTypesAreRegistered()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        $this->fixture->addRecipient(new Topic('breaking-news'))
            ->addRecipient(new Recipient());
    }

    public function testSendConstruesValidJsonForNotificationWithTopic()
    {
        $guzzle = \Mockery::mock(\GuzzleHttp\Client::class);
        $guzzle->shouldReceive('post')
            ->once()
            ->with(Client::DEFAULT_API_URL, [])
            ->andReturn(\Mockery::mock(Response::class));
        $this->fixture->injectGuzzleHttpClient($guzzle);

        $message = new Notification();
        $this->fixture->addRecipient(new Topic('breaking-news'))
            ->send($message);
    }
}