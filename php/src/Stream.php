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

use InvalidArgumentException;
use RuntimeException;

/**
 * PHP stream implementation.
 */
class Stream
{
    /**
     * @var resource
     */
    private $stream;

    /**
     * @param resource $stream
     * @param int $size
     *
     * @throws InvalidArgumentException if the stream is not a stream resource
     */
    public function __construct($stream, private ?int $size = null)
    {
        if (! is_resource($stream)) {
            throw new InvalidArgumentException('Stream must be a resource');
        }

        $this->stream = $stream;
    }

    /**
     * Closes the stream when the destructed.
     */
    public function __destruct()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }

        $this->stream = null;
    }

    public function __toString()
    {
        return $this->getContents();
    }

    /**
     * Returns the remaining contents of the stream as a string.
     */
    public function getContents(): string
    {
        if (! $this->stream) {
            return '';
        }

        $this->seek(0);

        return stream_get_contents($this->stream);
    }

    /**
     * Get the size of the stream.
     *
     * @throws InvalidArgumentException If cannot find out the stream size
     */
    public function getSize(): ?int
    {
        if ($this->size !== null) {
            return $this->size;
        }

        if (! $this->stream) {
            return null;
        }

        $stats = fstat($this->stream);

        if (isset($stats['size'])) {
            return $this->size = $stats['size'];
        }

        throw new RuntimeException('Unknown stream size');
    }

    /**
     * Returns true if the stream is at the end of the stream.
     */
    public function eof(): bool
    {
        return feof($this->stream);
    }

    /**
     * Returns the current position of the file read/write pointer.
     *
     * @throws RuntimeException If cannot find out the stream position
     */
    public function tell(): int
    {
        $position = ftell($this->stream);

        if ($position === false) {
            throw new RuntimeException('Unable to get stream position');
        }

        return $position;
    }

    /**
     * Seek to a position in the stream.
     *
     * @throws RuntimeException If cannot find out the stream position
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (fseek($this->stream, $offset, $whence) !== 0) {
            throw new RuntimeException('Unable to seek stream position to ' . $offset);
        }
    }

    /**
     * Read data from the stream.
     */
    public function read(int $length): string
    {
        if ($length < 1) {
            return '';
        }

        $buffer = fread($this->stream, $length);

        if ($buffer === false) {
            throw new RuntimeException('Failed to read ' . $length . ' bytes');
        }

        return $buffer;
    }

    /**
     * Read stream.
     *
     * @throws RuntimeException
     */
    public function readStream(int $length): static
    {
        $stream = self::fromString();
        $target = $stream->stream;
        $source = $this->stream;

        if ($length < 1) {
            return $stream;
        }

        $written = stream_copy_to_stream($source, $target, $length);

        if ($written !== $length) {
            throw new RuntimeException('Failed to read stream with ' . $length . ' bytes');
        }

        $stream->seek(0);

        return $stream;
    }

    /**
     * Write data to the stream.
     *
     * @throws RuntimeException
     */
    public function write(string $bytes, int $length): int
    {
        $written = fwrite($this->stream, $bytes, $length);

        if ($written !== $length) {
            throw new RuntimeException('Failed to write ' . $length . ' bytes');
        }

        $this->size = null;

        return $written;
    }

    /**
     * Write stream.
     *
     * @throws RuntimeException
     */
    public function writeStream(Stream $stream, int $length): int
    {
        $target = $this->stream;
        $source = $stream->stream;
        $written = stream_copy_to_stream($source, $target);

        if ($written !== $length) {
            throw new RuntimeException('Failed to write stream with ' . $length . ' bytes');
        }

        $this->size = null;

        return $written;
    }

    /**
     * Wrap the input resource in a stream object.
     *
     * @param resource|Stream|string $resource
     *
     * @throws InvalidArgumentException if the $resource arg is not valid
     */
    public static function wrap(mixed $resource = '', ?int $size = null): static
    {
        if ($resource instanceof Stream) {
            return $resource;
        }

        $type = gettype($resource);

        if ($type == 'string') {
            return static::fromString($resource, $size);
        }

        if ($type == 'resource') {
            return new static($resource, $size);
        }

        throw new InvalidArgumentException('Invalid resource type: ' . $type);
    }

    /**
     * Create a new stream.
     */
    public static function create(): static
    {
        return new static(fopen('php://temp', 'r+'));
    }

    /**
     * Create a new stream from a string.
     */
    public static function fromString(string $resource = '', ?int $size = null): static
    {
        $stream = fopen('php://temp', 'r+');

        if ($resource !== '') {
            fwrite($stream, $resource);
            fseek($stream, 0);
        }

        return new static($stream, $size);
    }
}
