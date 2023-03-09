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

use Symfony\Component\Console\Input\InputDefinition;

class ArgvInput extends \Symfony\Component\Console\Input\ArgvInput
{
    public function __construct(array $argv = null, InputDefinition $definition = null)
    {
        $stream = $this->getStdinStream();

        // var_dump($argv, $stream->getSize());

        parent::__construct($argv, $definition);
    }

    protected function getStdinStream()
    {
        $handle = fopen('php://stdin', 'r');
        $stream = Stream::create();
        $counter = 0;

        stream_set_blocking($handle, false);

        while (! feof($handle) && ($counter++ < 10)) {
            $buffer = fread($handle, 1024);
            $length = mb_strlen($buffer, '8bit');

            if ($length > 0) {
                $stream->write($buffer, $length);
                $counter = 0;

                continue;
            }

            usleep(1000);
        }

        $stream->seek(0);
        fclose($handle);

        return $stream;
    }
}
