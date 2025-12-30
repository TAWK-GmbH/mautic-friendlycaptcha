<?php

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Service;

use GuzzleHttp\Psr7\Request;
use MauticPlugin\MauticFriendlyCaptchaBundle\Integration\Config;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

class FriendlyCaptchaClient
{
    public function __construct(
        private ClientInterface $httpClient,
        private LoggerInterface $logger,
        private Config $config
    ) {
    }

    public function verify(string $solution): bool
    {
        if (empty($solution)) {
            return false;
        }

        $url = 'v1' == $this->config->getVersion()
            ? 'https://api.friendlycaptcha.com/api/v1/siteverify'
            : 'https://global.frcapi.com/api/v2/captcha/siteverify';

        $headers = ['Content-Type' => 'application/json'];

        $body    = 'v1' == $this->config->getVersion()
            ? ['solution' => $solution, 'sitekey' => $this->config->getApiKeys()['site_key']]
            : ['response' => $solution, 'sitekey' => $this->config->getApiKeys()['site_key']];

        if ('v1' == $this->config->getVersion()) {
            $body['secret'] = $this->config->getApiKeys()['secret_key'];
        } else {
            $headers['X-API-Key'] = $this->config->getApiKeys()['secret_key'];
        }

        $request = new Request('POST', $url, $headers, json_encode($body));

        try {
            $response = $this->httpClient->sendRequest($request);

            return $this->isValidResponse($response->getStatusCode(), $response->getBody());
        } catch (\Exception $e) {
            $this->logger->error('FriendlyCaptcha: Verification failed. Accept form submission anyways', ['exception' => $e]);

            return true;
        }
    }

    private function isValidResponse(int $statusCode, string $body): bool
    {
        if (200 !== $statusCode) {
            throw new \Exception('Check if secret and solution are sent in the request body.');
        }

        $response = json_decode($body, true);

        return !empty($response['success']) && true === $response['success'];
    }
}
