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

namespace MageStack\FirePush\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Data\Form\FormKey;
use MageStack\FirePush\Api\ConfigInterface;
use Magento\Framework\UrlInterface;

class Config extends Template
{
    public function __construct(
        private readonly ConfigInterface $configInterface,
        private readonly UrlInterface $urlBuilder,
        private readonly FormKey $formKey,
        Context $context,
        array $data = [],
    ) {
        parent::__construct($context, $data);
    }

    public function getSubscribeUrl(): string
    {
        return $this->urlBuilder->getUrl('firebase-messaging-sw/subscribe/index');
    }

    public function getSWUrl(): string
    {
        return $this->urlBuilder->getUrl('firebase-messaging-sw');
    }

    public function getFEConfig(): string
    {
        return $this->configInterface->getFrontendConfig();
    }

    public function getVAPPubKey(): string
    {
        return $this->configInterface->getVAPPubKey();
    }

    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }
}
