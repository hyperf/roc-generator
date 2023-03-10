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
use Google\Protobuf\DescriptorProto;
use Google\Protobuf\FieldDescriptorProto;
use Google\Protobuf\FileDescriptorProto;
use Google\Protobuf\MethodDescriptorProto;
use Google\Protobuf\ServiceDescriptorProto;
use PhpParser\Comment\Doc;
use PhpParser\Lexer\Emulative;
use PhpParser\Node;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\PrettyPrinterAbstract;
use Throwable;

class ROCGenerator
{
    private Stream $stream;

    private Parser $parser;

    private PrettyPrinterAbstract $printer;

    public function __construct()
    {
        $lexer = new Emulative([
            'usedAttributes' => [
                'comments',
                'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            ],
        ]);
        $this->parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7, $lexer);
        $this->printer = new Standard();
    }

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
            $files = [];
            /** @var FileDescriptorProto $value */
            foreach ($request->getProtoFile()->getIterator() as $value) {
                $files = array_merge($files, $this->generate($value));
            }

            $response = new CodeGeneratorResponse();
            $response->setFile($files);

            fwrite(STDOUT, $response->serializeToString());
        } catch (Throwable $throwable) {
            echo (string) $throwable;
        }

        return 0;
    }

    public function generate(FileDescriptorProto $proto): array
    {
        $result = [];
        $namespace = $proto->getOptions()->getPhpNamespace() ?: $proto->getPackage() ?: 'RPC';

        // Build Messages
        /** @var DescriptorProto $message */
        foreach ($proto->getMessageType()->getIterator() as $message) {
            $result[] = $this->buildMessageCode($message, $namespace);
        }

        // Build Services
        /** @var ServiceDescriptorProto $service */
        foreach ($proto->getService()->getIterator() as $service) {
            $result[] = $this->buildServiceCode($service, $namespace);
        }

        return $result;
    }

    protected function buildServiceCode(ServiceDescriptorProto $service, string $namespace): CodeGeneratorResponse\File
    {
        $namespace = $this->buildNamespace($namespace);

        $namespace->stmts[] = $this->buildInterface($service);

        $content = $this->printer->prettyPrint([
            $this->buildDeclare(),
            $namespace,
        ]);
        return new CodeGeneratorResponse\File([
            'name' => $service->getName() . '.php',
            'content' => '<?php' . PHP_EOL . $content,
        ]);
    }

    protected function buildInterface(ServiceDescriptorProto $service): Node\Stmt\Interface_
    {
        $methods = [];
        /** @var MethodDescriptorProto $value */
        foreach ($service->getMethod()->getIterator() as $value) {
            $methods[] = new Node\Stmt\ClassMethod(
                $value->getName(),
                [
                    'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                    'stmts' => null,
                    'params' => [
                        new Node\Param(
                            new Node\Expr\Variable('input'),
                            null,
                            new Node\Identifier($this->toClassType($value->getInputType())),
                        ),
                    ],
                    'returnType' => $this->toClassType($value->getInputType()),
                ]
            );
        }
        return new Node\Stmt\Interface_($service->getName(), [
            'stmts' => $methods,
        ]);
    }

    protected function buildMessageCode(DescriptorProto $message, string $namespace): CodeGeneratorResponse\File
    {
        $namespace = $this->buildNamespace($namespace);
        $class = $this->buildClass($message);

        $namespace->stmts[] = $class;

        $content = $this->printer->prettyPrint([
            $this->buildDeclare(),
            $namespace,
        ]);

        return new CodeGeneratorResponse\File([
            'name' => $message->getName() . '.php',
            'content' => '<?php' . PHP_EOL . $content,
        ]);
    }

    protected function buildDeclare(): Node\Stmt\Declare_
    {
        return new Node\Stmt\Declare_([
            new Node\Stmt\DeclareDeclare(new Node\Identifier('strict_types'), new Node\Scalar\LNumber(1)),
        ]);
    }

    protected function buildNamespace(string $namespace): Node\Stmt\Namespace_
    {
        return new Node\Stmt\Namespace_(
            new Node\Name($namespace),
            [
                $this->buildUse(),
            ],
            [
                'comments' => [$this->buildComment()],
            ]
        );
    }

    protected function buildConstructor(DescriptorProto $message): Node\Stmt\ClassMethod
    {
        $params = [];
        /** @var FieldDescriptorProto $field */
        foreach ($message->getField()->getIterator() as $field) {
            $params[] = new Node\Param(
                new Node\Expr\Variable($field->getName()),
                null,
                new Node\Identifier($this->toPHPType($field->getType())),
                flags: Node\Stmt\Class_::MODIFIER_PUBLIC
            );
        }

        return new Node\Stmt\ClassMethod(
            new Node\Identifier('__construct'),
            [
                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                'params' => $params,
            ]
        );
    }

    protected function buildJsonDeSerialize(DescriptorProto $message): Node\Stmt\ClassMethod
    {
        $args = [];
        /** @var FieldDescriptorProto $field */
        foreach ($message->getField()->getIterator() as $field) {
            $args[] = new Node\Arg(
                new Node\Expr\ArrayDimFetch(
                    new Node\Expr\Variable('data'),
                    new Node\Scalar\String_($field->getName()),
                ),
            );
        }

        return new Node\Stmt\ClassMethod(
            new Node\Identifier('jsonDeSerialize'),
            [
                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC | Node\Stmt\Class_::MODIFIER_STATIC,
                'returnType' => new Node\Identifier('static'),
                'params' => [
                    new Node\Param(
                        new Node\Expr\Variable('data'),
                        null,
                        new Node\Identifier('array')
                    ),
                ],
                'stmts' => [
                    new Node\Stmt\Return_(
                        new Node\Expr\New_(
                            new Node\Name($message->getName()),
                            $args
                        )
                    ),
                ],
            ]
        );
    }

    protected function buildJsonSerialize(DescriptorProto $message): Node\Stmt\ClassMethod
    {
        $items = [];
        /** @var FieldDescriptorProto $field */
        foreach ($message->getField()->getIterator() as $field) {
            $items[] = new Node\Expr\ArrayItem(
                new Node\Expr\PropertyFetch(
                    new Node\Expr\Variable('this'),
                    new Node\Identifier($field->getName())
                ),
                new Node\Scalar\String_($field->getName())
            );
        }

        return new Node\Stmt\ClassMethod(
            new Node\Identifier('jsonSerialize'),
            [
                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                'returnType' => new Node\Identifier('mixed'),
                'stmts' => [
                    new Node\Stmt\Return_(
                        new Node\Expr\Array_($items, [
                            'kind' => Node\Expr\Array_::KIND_SHORT,
                        ]),
                    ),
                ],
            ]
        );
    }

    protected function buildClass(DescriptorProto $message): Node\Stmt\Class_
    {
        return new Node\Stmt\Class_($message->getName(), [
            'implements' => [
                new Node\Name('JsonSerializable'),
            ],
            'stmts' => [
                $this->buildConstructor($message),
                $this->buildJsonSerialize($message),
                $this->buildJsonDeSerialize($message),
            ],
        ]);
    }

    protected function buildUse(): Node\Stmt\Use_
    {
        return new Node\Stmt\Use_([
            new Node\Stmt\UseUse(
                new Node\Name('JsonSerializable')
            ),
        ]);
    }

    protected function buildComment(): Doc
    {
        return new Doc('/**
 * Generated by the ROC Generator.  DO NOT EDIT!
 */');
    }

    protected function toClassType(string $input): string
    {
        return pathinfo($input)['extension'];
    }

    protected function toPHPType(int $type): string
    {
        return match ($type) {
            FieldDescriptorProto\Type::TYPE_BOOL => 'bool',
            FieldDescriptorProto\Type::TYPE_INT32, FieldDescriptorProto\Type::TYPE_INT64 => 'int',
            FieldDescriptorProto\Type::TYPE_STRING => 'string',
            FieldDescriptorProto\Type::TYPE_MESSAGE => 'object',
            default => 'mixed',
        };
    }
}
