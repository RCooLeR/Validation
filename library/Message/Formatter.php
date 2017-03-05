<?php

/*
 * This file is part of Respect/Validation.
 *
 * (c) Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Message;

use DateTimeInterface;
use Exception;
use Traversable;

/**
 * Message creator.
 *
 * @author Henrique Moody <henriquemoody@gmail.com>
 *
 * @since 2.0.0
 */
final class Formatter
{
    /**
     * @var int
     */
    private $maxDepth;

    /**
     * @var int
     */
    private $maxChildren;

    /**
     * Initializes the message creator.
     *
     * @param int $maxDepth    Maximum depth level to show when normalizing data
     * @param int $maxChildren Maximum children level to show when normalizing data
     */
    public function __construct(int $maxDepth, int $maxChildren)
    {
        $this->maxDepth = $maxDepth;
        $this->maxChildren = $maxChildren;
    }

    /**
     * Creates a message based on a result and some templates.
     *
     * @param mixed  $input
     * @param array  $properties
     * @param string $template
     *
     * @return string
     */
    public function create($input, array $properties, string $template): string
    {
        $properties += ['placeholder' => $this->normalize($input)];

        return preg_replace_callback(
            '/{{(\w+)}}/',
            function ($matches) use ($properties) {
                $value = $matches[0];
                if (array_key_exists($matches[1], $properties)) {
                    $value = $properties[$matches[1]];
                }

                if ($matches[1] === 'placeholder' && is_string($value)) {
                    return $value;
                }

                return $this->normalize($value);
            },
            $template
        );
    }

    private function quoteCode(string $string, $currentDepth): string
    {
        if ($currentDepth === 0) {
            $string = sprintf('`%s`', $string);
        }

        return $string;
    }

    private function normalize($raw, int $currentDepth = 0): string
    {
        if (is_object($raw)) {
            return $this->normalizeObject($raw, $currentDepth);
        }

        if (is_array($raw)) {
            return $this->normalizeArray($raw, $currentDepth);
        }

        if (is_float($raw)) {
            return $this->normalizeFloat($raw, $currentDepth);
        }

        if (is_resource($raw)) {
            return $this->normalizeResource($raw, $currentDepth);
        }

        if (is_bool($raw)) {
            return $this->normalizeBool($raw, $currentDepth);
        }

        return json_encode($raw, (JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function normalizeObject($object, int $currentDepth): string
    {
        $nextDepth = $currentDepth + 1;

        if ($object instanceof Traversable) {
            $array = iterator_to_array($object);
            $normalized = sprintf('[traversable] (%s: %s)', get_class($object), $this->normalize($array, $nextDepth));

            return $this->quoteCode($normalized, $currentDepth);
        }

        if ($object instanceof DateTimeInterface) {
            return sprintf('"%s"', $object->format('c'));
        }

        if ($object instanceof Exception) {
            $normalized = $this->normalizeException($object, $nextDepth);

            return $this->quoteCode($normalized, $currentDepth);
        }

        if (method_exists($object, '__toString')) {
            return $this->normalize($object->__toString(), $nextDepth);
        }

        $normalized = sprintf(
            '[object] (%s: %s)',
            get_class($object),
            $this->normalize(get_object_vars($object), $currentDepth)
        );

        return $this->quoteCode($normalized, $currentDepth);
    }

    private function normalizeException(Exception $exception, int $currentDepth): string
    {
        $properties = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => str_replace(getcwd().'/', '', $exception->getFile()).':'.$exception->getLine(),
        ];

        return sprintf('[exception] (%s: %s)', get_class($exception), $this->normalize($properties, $currentDepth));
    }

    private function normalizeArray(array $array, int $currentDepth): string
    {
        if ($currentDepth >= $this->maxDepth) {
            return '...';
        }

        if (empty($array)) {
            return '{ }';
        }

        $string = '';
        $total = count($array);
        $current = 0;
        $nextDepth = $currentDepth + 1;
        foreach ($array as $key => $value) {
            if ($current++ >= $this->maxChildren) {
                $string .= ' ... ';
                break;
            }

            if (!is_int($key)) {
                $string .= sprintf('%s: ', $this->normalize($key, $nextDepth));
            }

            $string .= $this->normalize($value, $nextDepth);

            if ($current !== $total) {
                $string .= ', ';
            }
        }

        return sprintf('{ %s }', $string);
    }

    private function normalizeFloat(float $float, int $currentDepth): string
    {
        if (is_infinite($float)) {
            return $this->quoteCode(($float > 0 ? '' : '-').'INF', $currentDepth);
        }

        if (is_nan($float)) {
            return $this->quoteCode('NaN', $currentDepth);
        }

        return var_export($float, true);
    }

    private function normalizeResource($raw, int $currentDepth): string
    {
        return $this->quoteCode(sprintf('[resource] (%s)', get_resource_type($raw)), $currentDepth);
    }

    private function normalizeBool(bool $raw, int $currentDepth): string
    {
        return $this->quoteCode(var_export($raw, true), $currentDepth);
    }
}
