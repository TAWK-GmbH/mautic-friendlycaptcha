<?php

declare(strict_types=1);

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
        private Config $config,
    ) {
    }

    public function verify(string $solution): bool
    {
        if (empty($solution)) {
            return false;
        }

      $url = $this->config->getVersion() === 'v1'
        ? 'https://api.friendlycaptcha.com/api/v1/siteverify'
        : 'https://global.frcapi.com/api/v2/captcha/siteverify';

        $headers = ['Content-Type' => 'application/json'];

      $siteKey = $this->config->getApiKeys()['site_key'];
      $body    = $this->config->getVersion() === 'v1'
        ? ['solution' => $solution, 'sitekey' => $siteKey]
        : ['response'  => $solution, 'sitekey' => $siteKey];

        if ('v1' == $this->config->getVersion()) {
            $body['secret'] = $this->config->getApiKeys()['secret_key'];
        } else {
            $headers['X-API-Key'] = $this->config->getApiKeys()['secret_key'];
        }

        $request = new Request('POST', $url, $headers, json_encode($body));

        try {
            $response = $this->httpClient->sendRequest($request);

            return $this->isValidResponse($response->getStatusCode(), (string) $response->getBody());
        } catch (\Exception $e) {
            $this->logger->error('FriendlyCaptcha: Verification failed. Accept form submission anyways', ['exception' => $e]);

            return true;
        }
    }

    private function isValidResponse(int $statusCode, string $body): bool
    {
      if ($statusCode !== 200) {
        throw new \RuntimeException(
          sprintf('Friendly Captcha verification failed with status code %d.', $statusCode)
        );
      }

        $response = json_decode($body, true);

        return !empty($response['success']) && true === $response['success'];
    }
}
