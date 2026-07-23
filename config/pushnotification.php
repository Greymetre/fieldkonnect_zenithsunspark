<?php
/**
 * @see https://github.com/Edujugon/PushNotification
 */

return [
    'gcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => 'AAAAqvNhkm8:APA91bEUUXBPXcifR7wS_jN6NEY7fhDkcDmlBa9wUa31l7TwtLFBDbv4Uz6fHhzvRppBdXJa7B6eMMGlxB2lx7ueqHsXOgs_svETB6AoWROrXPP1XgWik_Oyn0B0g4Fcq8CGzsmldDxX',
        // Optional: Default Guzzle request options for each GCM request
        // See https://docs.guzzlephp.org/en/stable/request-options.html
        'guzzle' => [],
    ],
    'fcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => 'AAAAqvNhkm8:APA91bEUUXBPXcifR7wS_jN6NEY7fhDkcDmlBa9wUa31l7TwtLFBDbv4Uz6fHhzvRppBdXJa7B6eMMGlxB2lx7ueqHsXOgs_svETB6AoWROrXPP1XgWik_Oyn0B0g4Fcq8CGzsmldDxX',
        // Optional: Default Guzzle request options for each FCM request
        // See https://docs.guzzlephp.org/en/stable/request-options.html
        'guzzle' => [],
    ],
    'apn' => [
        'certificate' => __DIR__ . '/iosCertificates/apns-dev-cert.pem',
        'passPhrase' => 'secret', //Optional
        'passFile' => __DIR__ . '/iosCertificates/yourKey.pem', //Optional
        'dry_run' => true,
    ],
];
