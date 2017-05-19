<?php
namespace paragraph1\phpFCM;

use GuzzleHttp;

/**
 * @author palbertini
 */
class Client implements ClientInterface
{
    const DEFAULT_API_URL = 'https://fcm.googleapis.com/fcm/send';

	const FCM_SEND_HOST = 'fcm.googleapis.com';
	const FCM_SEND_PATH = '/fcm/send';
	const FCM_TOPIC_MANAGEMENT_HOST = 'iid.googleapis.com';
	const FCM_TOPIC_MANAGEMENT_ADD_PATH = '/iid/v1:batchAdd';
	const FCM_TOPIC_MANAGEMENT_REMOVE_PATH = '/iid/v1:batchRemove';

    /** @var string */
    private $apiKey;

    /** @var string */
    private $proxyApiUrl;

    /** @var GuzzleHttp\ClientInterface */
    private $guzzleClient;

    public function injectHttpClient(GuzzleHttp\ClientInterface $client)
    {
        $this->guzzleClient = $client;
    }

    /**
     * add your server api key here
     * read how to obtain an api key here: https://firebase.google.com/docs/server/setup#prerequisites
     *
     * @param string $apiKey
     *
     * @return \paragraph1\phpFCM\Client
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * people can overwrite the api url with a proxy server url of their own
     *
     * @param string $url
     *
     * @return \paragraph1\phpFCM\Client
     */
    public function setProxyApiUrl($url)
    {
        $this->proxyApiUrl = $url;
        return $this;
    }

    /**
     * sends your notification to the google servers and returns a guzzle repsonse object
     * containing their answer.
     *
     * @param Message $message
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function send(Message $message)
    {
	    return $this->request(
		    self::FCM_SEND_HOST,
		    self::FCM_SEND_PATH,
		    $message
	    );
    }

	/**
	 * @param array $registrationTokens
	 * @param Recipient\Topic $topic
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function subscribeToTopic( array $registrationTokens, Recipient\Topic $topic )
	{
		return $this->sendTopicManagementRequest(
			$registrationTokens,
			$topic,
			'subscribeToTopic',
			self::FCM_TOPIC_MANAGEMENT_ADD_PATH
		);
	}

	/**
	 * @param array $registrationTokens
	 * @param Recipient\Topic $topic
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function unsubscribeFromTopic( array $registrationTokens, Recipient\Topic $topic )
	{
		return $this->sendTopicManagementRequest(
			$registrationTokens,
			$topic,
			'unsubscribeFromTopic',
			self::FCM_TOPIC_MANAGEMENT_REMOVE_PATH
		);
	}

	/**
	 * @param array $registrationTokens
	 * @param Recipient\Topic $topic
	 * @param $method
	 * @param $path
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	protected function sendTopicManagementRequest( array $registrationTokens, Recipient\Topic $topic, $method, $path )
	{
		$this->validateRegistrationTokens( $registrationTokens, $method );

		$data = [
			'to'                  => $this->normalizeTopic( $topic ),
			'registration_tokens' => $registrationTokens,
		];

		return $this->request(
			self::FCM_TOPIC_MANAGEMENT_HOST,
			$path,
			$data
		);
	}

	/**
	 * @param $host
	 * @param $path
	 * @param $data
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	private function request($host, $path, $data)
	{
		return $this->guzzleClient->post(
			sprintf( 'https://%s/%s', $host, $path ),
			[
				'headers' => [
					'Authorization' => sprintf('key=%s', $this->apiKey),
					'Content-Type' => 'application/json'
				],
				'body' => json_encode($data)
			]
		);
	}

	/**
	 * @param array $registrationTokens
	 * @param $method
	 *
	 * @throws Exception
	 */
	protected function validateRegistrationTokens( array $registrationTokens, $method )
	{
		if ( count( $registrationTokens ) > 1000 ) {
			throw new Exception(
				"Too many registration tokens provided in a single request to $method(). Batch" .
				' your requests to contain no more than 1,000 registration tokens per request.'
			);
		}

		foreach ($registrationTokens as $index => $token) {
			if ( empty( trim( $token ) ) ) {
				throw new Exception(
					"Registration token provided to methodName() at index $index must be a non-empty string."
				);
			}
		}
	}

	/**
	 * @param Recipient\Topic $topic
	 *
	 * @return string
	 */
	private function normalizeTopic( Recipient\Topic $topic )
	{
		return '/topics/' . $topic->getIdentifier();
	}
}