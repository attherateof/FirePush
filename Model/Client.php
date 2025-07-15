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

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use MageStack\FirePush\Api\ConfigInterface;

class Client
{
    private ?Messaging $messaging = null;

    private ?string $fireBaseConfig = null;

    public function __construct(
        private readonly Factory $firebaseFactory,
        private readonly ConfigInterface $config
    ) {
    }

    public function get(): Messaging
    {
        if (!$this->messaging instanceof Messaging) {
            $config = $this->getConfig();
            $factory = $this->firebaseFactory->withServiceAccount($config);

            $this->messaging = $factory->createMessaging();
        }

        return $this->messaging;
    }


    private function getConfig(): string
    {
        if (empty($this->fireBaseConfig)) {
            $this->fireBaseConfig = $this->config->getFirebaseConfig();
        }

        return $this->fireBaseConfig;
    }
}
