<?php

namespace MageStack\FirePush\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use MageStack\FirePush\Api\ConfigInterface;

class Index implements HttpGetActionInterface
{
    public function __construct(
        private readonly ConfigInterface $configInterface,
        private readonly ResultFactory $resultFactory
    ) {
    }

    /**
     * Serve Firebase Messaging Service Worker
     * This responds to: http://baseUrl/firebase-messaging-sw/
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setHeader('Content-Type', 'application/javascript', true);
        $resultRaw->setHeader('Service-Worker-Allowed', '/', true); // Allow SW to control entire site

        $resultRaw->setHeader('Access-Control-Allow-Origin', '*', true);
        $resultRaw->setHeader('Access-Control-Allow-Methods', 'GET', true);

        $serviceWorkerScript = <<<JS
            // Import Firebase scripts using importScripts (not ES6 imports)
            importScripts('https://www.gstatic.com/firebasejs/12.2.1/firebase-app-compat.js');
            importScripts('https://www.gstatic.com/firebasejs/12.2.1/firebase-messaging-compat.js');

            // Your web app's Firebase configuration
            const firebaseConfig = %s;

            // Initialize Firebase in the service worker
            firebase.initializeApp(firebaseConfig);

            // Retrieve Firebase Messaging object
            const messaging = firebase.messaging();

            // Handle background messages
            messaging.onBackgroundMessage(function(payload) {
                console.log('[firebase-messaging-sw.js] Received background message ', payload);
                
                // Safely access notification properties
                const notificationTitle = payload.notification?.title || 'New Message';
                const notificationOptions = {
                    body: payload.notification?.body || '',
                    icon: payload.notification?.icon || '/default-icon.png',
                    badge: '/badge-icon.png',
                    tag: 'firebase-notification',
                    requireInteraction: false,
                    data: {
                        ...payload.data,
                        click_action: payload.data?.click_action || payload.notification?.click_action || '/'
                    }
                };

                return self.registration.showNotification(notificationTitle, notificationOptions);
            });

            // Handle notification clicks
            self.addEventListener('notificationclick', function(event) {
                console.log('[firebase-messaging-sw.js] Notification click received.');
                
                event.notification.close();
                
                // Get click action from notification data
                const clickAction = event.notification.data?.click_action || '/';
                
                event.waitUntil(
                    clients.matchAll({
                        type: 'window',
                        includeUncontrolled: true
                    }).then(function(clientList) {
                        // Check if there's already a window/tab open with our origin
                        for (let i = 0; i < clientList.length; i++) {
                            const client = clientList[i];
                            if (client.url.indexOf(self.location.origin) === 0 && 'focus' in client) {
                                return client.focus();
                            }
                        }
                        // If no window is open, open a new one
                        if (clients.openWindow) {
                            return clients.openWindow(clickAction);
                        }
                    })
                );
            });

            // Handle service worker activation
            self.addEventListener('activate', function(event) {
                console.log('[firebase-messaging-sw.js] Service Worker activated.');
                event.waitUntil(
                    self.clients.claim().then(() => {
                        console.log('[firebase-messaging-sw.js] Service Worker claimed all clients');
                    })
                );
            });

            // Handle service worker installation
            self.addEventListener('install', function(event) {
                console.log('[firebase-messaging-sw.js] Service Worker installed.');
                self.skipWaiting();
            });

            // Handle messages from main thread
            self.addEventListener('message', function(event) {
                if (event.data && event.data.type === 'SKIP_WAITING') {
                    self.skipWaiting();
                }
            });
        JS;

        $formattedJs = sprintf(
            $serviceWorkerScript,
            $this->configInterface->getFrontendConfig()
        );

        $resultRaw->setContents($formattedJs);

        return $resultRaw;
    }
}