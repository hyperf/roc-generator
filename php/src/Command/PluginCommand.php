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
namespace Hyperf\ROCGenerator\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\ROCGenerator\ROCGenerator;
use Psr\Container\ContainerInterface;

#[Command]
class PluginCommand extends HyperfCommand
{
    protected bool $coroutine = false;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('protobuf');
    }

    public function handle()
    {
        return di()->get(ROCGenerator::class)->handle();
    }
}
