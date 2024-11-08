<?php

/*
 * @copyright   2024 Tax Academy Prof. Dr. Wolfgang Kessler GmbH. All rights reserved
 * @author      Daniel Band
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticFriendlyCaptchaBundle\Service;

use GuzzleHttp\Client as GuzzleClient;
use Mautic\FormBundle\Entity\Field;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticFriendlyCaptchaBundle\Integration\FriendlyCaptchaIntegration;
use Mautic\PluginBundle\Integration\AbstractIntegration;

class FriendlyCaptchaClient
{
    protected string $siteKey;

    protected string $secretKey;

    protected string $version;

    protected $url;

    public function __construct(private IntegrationHelper $integrationHelper)
    {
        $integrationObject = $integrationHelper->getIntegrationObject(FriendlyCaptchaIntegration::INTEGRATION_NAME);

        if ($integrationObject instanceof AbstractIntegration) {
            $keys            = $integrationObject->getKeys();
            $this->siteKey   = isset($keys['site_key']) ? $keys['site_key'] : null;
            $this->secretKey = isset($keys['secret_key']) ? $keys['secret_key'] : null;
            $this->version = isset($keys['version']) ? $keys['version'] : 'v1';

            if ($this->version == 'v1') $this->url = "https://api.friendlycaptcha.com/api/v1/siteverify";
            if ($this->version == 'v2') $this->url = "https://global.frcapi.com/api/v2/captcha/siteverify";
        }
    }

    public static function getSubscribedEvents()
    {
        return [];
    }

    public function verify(string $response)
    {
        $client   = new GuzzleClient(['timeout' => 10]);
        $response = $client->post(
            $this->url,
            [
                'json' => [
                    'solution' => $response,
                    'secret'   => $this->secretKey,
                    'sitekey' => $this->siteKey,
                ],
            ]
        );

        $response = json_decode($response->getBody(), true);
        if (array_key_exists('success', $response) && $response['success'] === true) {
            return true;
        }

        //TODO send alerts or log alerts on failure?

        return false;
    }
}
