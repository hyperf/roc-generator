#!/usr/bin/env php
<?php
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\Di\Container;
use Hyperf\Di\Definition\DefinitionSourceFactory;
use Hyperf\ROCGenerator\ArgvInput;
use Hyperf\Utils\ApplicationContext;

ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

// error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

! defined('BASE_PATH') && define('BASE_PATH', __DIR__);
! defined('MAIN_PATH') && define('MAIN_PATH', getcwd() . '/' . $argv[0]);
! defined('SWOOLE_HOOK_ALL') && define('SWOOLE_HOOK_ALL', 0);

require BASE_PATH . '/vendor/autoload.php';

// Self-called anonymous function that creates its own scope and keep the global namespace clean.
(function () {
    Hyperf\Di\ClassLoader::init();
    /** @var Psr\Container\ContainerInterface $container */
    $container = new Container((new DefinitionSourceFactory())());
    ApplicationContext::setContainer($container);

    /** @var Symfony\Component\Console\Application $application */
    $application = $container->get(Hyperf\Contract\ApplicationInterface::class);
    $application->run(new ArgvInput());
})();
