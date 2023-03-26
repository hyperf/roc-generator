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
use Hyperf\Utils\Codec\Json;
use Hyperf\Utils\Str;
use Psr\Container\ContainerInterface;

#[Command]
class VersionCommand extends HyperfCommand
{
    protected bool $coroutine = false;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('version');
    }

    public function handle()
    {
        $data = Json::decode(file_get_contents(BASE_PATH . '/config/autoload/roc.json'));
        foreach ($data as $key => $value) {
            $this->output->writeln(Str::camel($key) . ': ' . $value);
        }
    }
}
