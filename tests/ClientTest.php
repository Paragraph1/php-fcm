<?php
namespace paragraph1\phpFCM\Tests;

use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Recipient\Topic;
use paragraph1\phpFCM\Message;

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

    public function testSendConstruesValidJsonForNotificationWithTopic()
    {
        $apiKey = 'key';
        $headers = array(
            'Authorization' => sprintf('key=%s', $apiKey),
            'Content-Type' => 'application/json'
        );

        $guzzle = \Mockery::mock(\GuzzleHttp\Client::class);
        $guzzle->shouldReceive('post')
            ->once()
            ->with(Client::DEFAULT_API_URL, array('headers' => $headers, 'body' => '{"to":"\\/topics\\/test","priority":"high"}'))
            ->andReturn(\Mockery::mock(Response::class));

        $this->fixture->injectGuzzleHttpClient($guzzle);
        $this->fixture->setApiKey($apiKey);

        $message = new Message();
        $message->addRecipient(new Topic('test'));

        $this->fixture->send($message);
    }
}
