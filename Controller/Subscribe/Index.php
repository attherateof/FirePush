<?php

/**
 * Copyright © 2025 MageStack. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MageStack\FirePush\Controller\Subscribe;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use MageStack\FirePush\Model\Client as FirebaseClient;
use Psr\Log\LoggerInterface;

class Index implements HttpPostActionInterface
{
    /**
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var FirebaseClient
     */
    protected FirebaseClient $firebaseClient;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param RequestInterface $request
     * @param JsonFactory $resultJsonFactory
     * @param FirebaseClient $firebaseClient
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        JsonFactory $resultJsonFactory,
        FirebaseClient $firebaseClient,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->firebaseClient = $firebaseClient;
        $this->logger = $logger;
    }

    /**
     * Execute action to subscribe a token to multiple topics.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $token = $this->request->getParam('token');
        $topics = $this->request->getParam('topics');

        if (!$token) {
            return $result->setData([
                'success' => false,
                'message' => 'FCM token is required.'
            ])->setHttpResponseCode(400);
        }

        if (!is_array($topics) || empty($topics)) {
            return $result->setData([
                'success' => false,
                'message' => 'Topics must be a non-empty array.'
            ])->setHttpResponseCode(400);
        }

        try {
            $messaging = $this->firebaseClient->get();
            $messaging->subscribeToTopics($topics, [$token]);

            $this->logger->info(
                'Successfully subscribed token to topics: ' . implode(', ', $topics),
                ['token' => $token]
            );

            return $result->setData([
                'success' => true,
                'message' => 'Successfully subscribed to topics.'
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Error subscribing to topics: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return $result->setData([
                'success' => false,
                'message' => 'Failed to subscribe to topics: ' . $e->getMessage()
            ])->setHttpResponseCode(500);
        }
    }
}