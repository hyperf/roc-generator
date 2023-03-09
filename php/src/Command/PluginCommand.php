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

use google\protobuf\compiler\CodeGeneratorRequest;
use google\protobuf\FileDescriptorProto;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ROCGenerator\ProtobufReader;
use Protobuf\Compiler\Compiler;
use Psr\Container\ContainerInterface;
use Throwable;

#[Command]
class PluginCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('protobuf');
    }

    public function handle()
    {
        $stream = di()->get(ProtobufReader::class)->getStream();

        try {
            $request = CodeGeneratorRequest::fromStream($stream);

            /** @var FileDescriptorProto $item */
            foreach ($request->getProtoFileList() as $item);
        } catch (Throwable $throwable) {
            echo (string) $throwable;
        }

        return 0;
        // $request = CodeGeneratorRequest::fromStream($stream);
        // fwrite(STDOUT, "1");
        // $compiler = new Compiler(di()->get(StdoutLoggerInterface::class));
        // $stream = $compiler->compile(di()->get(ProtobufReader::class)->getStream());
        // fwrite(STDOUT, $stream);
    }
}
