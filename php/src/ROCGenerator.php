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
namespace Hyperf\ROCGenerator;

use google\protobuf\compiler\CodeGeneratorRequest;
use google\protobuf\FileDescriptorProto;
use Protobuf\Stream;
use Throwable;

class ROCGenerator
{
    private Stream $stream;

    public function setStream(Stream $stream): static
    {
        $this->stream = $stream;
        return $this;
    }

    public function handle(): int
    {
        try {
            $request = CodeGeneratorRequest::fromStream($this->stream);

            /** @var FileDescriptorProto $item */
            foreach ($request->getProtoFileList() as $item) {
                print_r($item->getOptions()->unknownFieldSet()[41]);
            }
            // foreach ($item->get)
        } catch (Throwable $throwable) {
            echo (string) $throwable;
        }

        return 0;
    }
}
