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
namespace Protobuf;

use ArrayObject;
use InvalidArgumentException;
use ReturnTypeWillChange;

/**
 * Message collection.
 */
class MessageCollection extends ArrayObject implements Collection
{
    /**
     * @param array<\Protobuf\Message> $values
     */
    public function __construct(array $values = [])
    {
        array_walk($values, [$this, 'add']);
    }

    /**
     * Adds a message to this collection.
     *
     * @param \Protobuf\Message $message
     */
    public function add(Message $message)
    {
        parent::offsetSet(null, $message);
    }

    /**
     * {@inheritdoc}
     */
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (! $value instanceof Message) {
            throw new InvalidArgumentException(sprintf(
                'Argument 2 passed to %s must implement interface \Protobuf\Message, %s given',
                __METHOD__,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        parent::offsetSet($offset, $value);
    }
}
