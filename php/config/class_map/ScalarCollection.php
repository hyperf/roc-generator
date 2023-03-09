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
 * Scalar collection.
 */
class ScalarCollection extends ArrayObject implements Collection
{
    /**
     * @param array<scalar> $values
     */
    public function __construct(array $values = [])
    {
        array_walk($values, [$this, 'add']);
    }

    /**
     * Adds a value to this collection.
     *
     * @param scalar $value
     */
    public function add($value)
    {
        if (! is_scalar($value)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s must be a scalar value, %s given',
                __METHOD__,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        parent::offsetSet(null, $value);
    }

    /**
     * {@inheritdoc}
     */
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (! is_scalar($value)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 2 passed to %s must be a scalar value, %s given',
                __METHOD__,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        parent::offsetSet($offset, $value);
    }
}
