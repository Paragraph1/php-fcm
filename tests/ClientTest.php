<?php
namespace paragraph1\phpFCM\Tests;

use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Notification;
use paragraph1\phpFCM\Topic;
use paragraph1\phpFCM\Recipient;

use GuzzleHttp;
use GuzzleHttp\Psr7\Response;
use paragraph1\phpFCM\Message;

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
        $apiKey = 'key';
        
        $headers = array(
            'Authorization' => sprintf('key=%s', $apiKey),
            'Content-Type' => 'application/json'
        );
        $body = '{"notification":"{\\"title\\":\\"test\\",\\"body\\":\\"anicetestingnotification\\"}"}';
        $guzzle = \Mockery::mock(\GuzzleHttp\Client::class);
        $guzzle->shouldReceive('post')
            ->once()
            ->with(Client::DEFAULT_API_URL, array('headers' => $headers, 'body' => $body))
            ->andReturn(\Mockery::mock(Response::class));
        $this->fixture->injectGuzzleHttpClient($guzzle);
        $this->fixture->setApiKey($apiKey);

        $notification = new Notification();
        $notification->setTitle('test')
            ->setBody('a nice testing notification');
        $message = new Message();
        $message->setNotification($notification);
        echo $message->toJson();
        $this->fixture->addRecipient(new Topic('breaking-news'))
            ->send($message);
    }
}