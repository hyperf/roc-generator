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

use Protobuf\Stream;

class ProtobufReader
{
    private Stream $stream;

    public function setStream(Stream $stream): static
    {
        $this->stream = $stream;
        return $this;
    }

    public function getStream(): Stream
    {
        return $this->stream;
    }
}
