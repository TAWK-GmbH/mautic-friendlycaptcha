<?php

declare(strict_types=1);

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Tests\Unit\Service;

use GuzzleHttp\Psr7\Request;
use MauticPlugin\MauticFriendlyCaptchaBundle\Integration\Config;
use MauticPlugin\MauticFriendlyCaptchaBundle\Service\FriendlyCaptchaClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class FriendlyCaptchaClientTest extends TestCase
{
    private const CAPTCHA_TOKEN = 'atesttoken';
    private $config;
    private FriendlyCaptchaClient $fcClient;
    private $httpClient;
    private $response;
    private $logger;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->config->method('getSecretKey')->willReturn('a');
        $this->config->method('getSiteKey')->willReturn('b');

        $this->logger     = $this->createMock(LoggerInterface::class);
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->response   = $this->createMock(ResponseInterface::class);
        $this->fcClient   = new FriendlyCaptchaClient(
            $this->httpClient,
            $this->logger,
            $this->config
        );
    }

    public function verify(string $method, string $url, array $headers, string $body)
    {
        $this->response->method('getStatusCode')->willReturn(200);
        $this->response->method('getBody')->willReturn(json_encode(['success' => true]));

        $callback = $this->callback(function (Request $arg) use ($method, $url, $headers, $body) {
            $this->assertEquals($method, $arg->getMethod(), 'Method should be POST');
            $this->assertEquals($url, $arg->getUri(), 'Wrong endpoint given');
            foreach ($headers as $header => $value) {
                $this->arrayHasKey($header)->evaluate($arg->getHeaders());
                $this->assertEquals($value, $arg->getHeader($header), 'Wrong headers');
            }
            $this->assertEquals($body, $arg->getBody()->__toString(), 'Wrong body');

            return true;
        });

        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($callback)
            ->willReturn($this->response);

        $this->assertTrue($this->fcClient->verify(FriendlyCaptchaClientTest::CAPTCHA_TOKEN));
    }

    public function testVerifyV1()
    {
        $this->config->method('getVersion')->willReturn(Config::FC_API_V1);

        $this->verify(
            'POST',
            'https://api.friendlycaptcha.com/api/v1/siteverify',
            ['Content-Type' => ['application/json']],
            json_encode([
                'solution' => FriendlyCaptchaClientTest::CAPTCHA_TOKEN,
                'sitekey'  => $this->config->getSiteKey(),
                'secret'   => $this->config->getSecretKey(),
            ])
        );
    }

    public function testVerifyV2()
    {
        $this->config->method('getVersion')->willReturn(Config::FC_API_V2);

        $this->verify(
            'POST',
            'https://global.frcapi.com/api/v2/captcha/siteverify',
            ['Content-Type' => ['application/json'], 'X-API-Key' => [$this->config->getSecretKey()]],
            json_encode([
                'solution' => FriendlyCaptchaClientTest::CAPTCHA_TOKEN,
                'sitekey'  => $this->config->getSiteKey(),
            ])
        );
    }
}
