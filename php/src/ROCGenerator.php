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

use Google\Protobuf\Compiler\CodeGeneratorRequest;
use Google\Protobuf\Compiler\CodeGeneratorResponse;
use Google\Protobuf\FileDescriptorProto;
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
            $request = new CodeGeneratorRequest();
            $request->mergeFromString((string) $this->stream);
            $file = $request->getProtoFile()->getIterator();
            /** @var FileDescriptorProto $value */
            foreach ($file as $value);
            // var_dump($value->getOptions()->getPhpNamespace());

            $response = new CodeGeneratorResponse();
            $file = new CodeGeneratorResponse\File([
                'name' => 'Test.php',
                'content' => '<?php',
            ]);
            $response->setFile([
                $file,
            ]);

            fwrite(STDOUT, $response->serializeToString());
        } catch (Throwable $throwable) {
            echo (string) $throwable;
        }

        return 0;
    }
}
