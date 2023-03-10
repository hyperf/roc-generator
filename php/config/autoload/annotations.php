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
            Hyperf\Di\Resolver\ResolverDispatcher::class => BASE_PATH . '/src/Kernel/ClassMap/ResolverDispatcher.php',
            Hyperf\Phar\BuildCommand::class => BASE_PATH . '/src/Kernel/ClassMap/BuildCommand.php',
        ],
    ],
];
