<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    'scan' => [
        'paths' => [
            BASE_PATH . '/src',
        ],
        'class_map' => [
            Protobuf\MessageCollection::class => BASE_PATH . '/config/class_map/MessageCollection.php',
            Protobuf\ScalarCollection::class => BASE_PATH . '/config/class_map/ScalarCollection.php',
        ],
    ],
];
