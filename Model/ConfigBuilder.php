<?php

/**
 * Copyright © 2025 MageStack. All rights reserved.
 * See COPYING.txt for license details.
 *
 * DISCLAIMER
 *
 * Do not make any kind of changes to this file if you
 * wish to upgrade this extension to newer version in the future.
 *
 * @category  MageStack
 * @package   MageStack_GoogleLogin
 * @author    Amit Biswas <amit.biswas.webdeveloper@gmail.com>
 * @copyright 2025 MageStack
 * @license   https://opensource.org/licenses/MIT  MIT License
 * @link      https://github.com/attherateof/GoogleLogin
 */

declare(strict_types=1);

namespace MageStack\FirePush\Model;

use MageStack\FirePush\Api\ConfigBuilderInterface;
use MageStack\FirePush\Api\ConfigInterface;

/**
 * Get configuration from admin;
 *
 * Class Config
 * @namespace MageStack\GoogleLogin\Model
 */
class ConfigBuilder implements ConfigBuilderInterface
{
    private array $fireBaseconfig = [];

    /**
     * Class constructor
     *
     * @param ConfigInterface $config
     */
    public function __construct(
        private readonly ConfigInterface $config
    ) {
    }

    /**
     * @inheritDoc
     */
    public function build(): array
    {
        if (empty($this->fireBaseconfig)) {
            $this->fireBaseconfig = [
                'type' => 'service_account',
                'project_id' => $this->config->getProjectId(),
                'private_key_id' => $this->config->getPrivateKeyId(),
                'private_key' => $this->config->getPrivateKey(),
                'client_email' => $this->config->getClientEmail(),
                'client_id' => $this->config->getClientId(),
                'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
                'token_uri' => 'https://oauth2.googleapis.com/token',
                'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
                'client_x509_cert_url' => "https://www.googleapis.com/robot/v1/metadata/x509/" . $this->config->getClientEmail(),
                'universe_domain' => 'googleapis.com',
            ];
        }

        return $this->fireBaseconfig;
    }
}
