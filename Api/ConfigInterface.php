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
 * @package   MageStack_SocialLogin
 * @author    Amit Biswas <amit.biswas.webdeveloper@gmail.com>
 * @copyright 2025 MageStack
 * @license   https://opensource.org/licenses/MIT  MIT License
 * @link      https://github.com/attherateof/SocialLogin
 */

declare(strict_types=1);

namespace MageStack\FirePush\Api;

/**
 * Used to receive module configurations.
 *
 * @namespace MageStack\SocialLogin\Api
 * @interface ConfigInterface
 *
 * @api
 */
interface ConfigInterface
{
    /**
     * Is fuzzy search enabled
     *
     * @return bool
     */
    public function isEnabled(?int $webSiteId = null): bool;

    /**
     * Get firebase config
     *
     * @return string
     */
    public function getFirebaseConfig(): string;

    /**
     * Get firebase config
     *
     * @return string
     */
    public function getFrontendConfig(): string;

    /**
     * Get VAP public key
     *
     * @return string
     */
    public function getVAPPubKey(): string;
}
