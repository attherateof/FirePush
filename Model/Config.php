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

use MageStack\FirePush\Api\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;
use RuntimeException;

/**
 * Get configuration from admin;
 *
 * Class Config
 * @namespace MageStack\GoogleLogin\Model
 */
class Config implements ConfigInterface
{
    /**
     * Configuration paths
     */
    public const IS_ENABLED_FIRE_BASE_WEBSOCKET_CONFIG_XML_PATH = 'websocket/firebase/is_enabled';
    public const CONFIG_FIRE_BASE_WEBSOCKET_CONFIG_XML_PATH = 'websocket/firebase/firebase_config';
    public const CONFIG_FIRE_BASE_FRONT_END_WEBSOCKET_CONFIG_XML_PATH = 'websocket/firebase/frontend_firebase_config';
    public const VAP_PUBLIC_KEY_FIRE_BASE_WEBSOCKET_CONFIG_XML_PATH = 'websocket/firebase/vap_pub_key';

    /**
     * Class constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor
    ) {
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(?int $webSiteId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::IS_ENABLED_FIRE_BASE_WEBSOCKET_CONFIG_XML_PATH,
            ScopeInterface::SCOPE_WEBSITE,
            $webSiteId
        );
    }

    /**
     * @inheritDoc
     */
    public function getFirebaseConfig(): string
    {
        $clientKey = $this->scopeConfig->getValue(
            self::CONFIG_FIRE_BASE_WEBSOCKET_CONFIG_XML_PATH
        );

        if (!is_string($clientKey)) {
            throw new RuntimeException(__('Firebase config must be set.')->render());
        }

        return $this->encryptor->decrypt($clientKey);
    }

    /**
     * @inheritDoc
     */
    public function getFrontendConfig(): string
    {
        $clientKey = $this->scopeConfig->getValue(
            self::CONFIG_FIRE_BASE_FRONT_END_WEBSOCKET_CONFIG_XML_PATH
        );

        if (!is_string($clientKey)) {
            throw new RuntimeException(__('Firebase config must be set.')->render());
        }

        return $this->encryptor->decrypt($clientKey);
    }

    /**
     * @inheritDoc
     */
    public function getVAPPubKey(): string
    {
        $clientSecret = $this->scopeConfig->getValue(
            self::VAP_PUBLIC_KEY_FIRE_BASE_WEBSOCKET_CONFIG_XML_PATH
        );

        if (!is_string($clientSecret)) {
            throw new RuntimeException(__('Client key can not be empty.')->render());
        }

        return $this->encryptor->decrypt($clientSecret);
    }
}
