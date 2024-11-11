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

    public function verify(string $solution)
    {
        if ('v1' === $this->config->getVersion()) {
            return $this->verifyCaptcha('https://api.friendlycaptcha.com/api/v1/siteverify', $solution);
        }

        return $this->verifyCaptcha('https://global.frcapi.com/api/v2/captcha/siteverify', $solution, true);
    }

    private function verifyCaptcha(string $url, string $solution, bool $useApiKeyHeader = false): bool
    {
        if (empty($solution)) {
            return false;
        }

        $headers = ['Content-Type' => 'application/json'];
        $body    = ['solution' => $solution, 'sitekey' => $this->config->getSiteKey()];

        if ($useApiKeyHeader) {
            $headers['X-API-Key'] = $this->config->getSecretKey();
        } else {
            $body['secret'] = $this->config->getSecretKey();
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
