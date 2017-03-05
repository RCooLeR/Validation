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

use ArrayIterator;
use DateTime;
use DateTimeImmutable;
use Exception;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @group engine
 *
 * @covers \Respect\Validation\Message\Formatter
 *
 * @author Henrique Moody <henriquemoody@gmail.com>
 *
 * @since 2.0.0
 */
final class FormatterTest extends PHPUnit_Framework_TestCase
{
    public function normalizerDataProvider(): array
    {
        $arrayThreeLevels = ['foo' => ['bar' => ['baz' => 'Here!']]];
        $arrayFourLevels = ['foo' => ['bar' => ['baz' => ['qux' => 'Here!']]]];

        return [
            [
                new Exception(),
                '`[exception] (Exception: { "message": "", "code": 0, "file": "tests/library/Message/FormatterTest.php:39" })`',
            ],
            [
                $this->arrayToObject($arrayThreeLevels),
                '`[object] (stdClass: { "foo": [object] (stdClass: { "bar": [object] (stdClass: { "baz": "Here!" }) }) })`',
            ],
            [
                $this->arrayToObject($arrayFourLevels),
                '`[object] (stdClass: { "foo": [object] (stdClass: { "bar": [object] (stdClass: { "baz": [object] (stdClass: ...) }) }) })`',
            ],
            [new stdClass(), '`[object] (stdClass: { })`'],
            [new DateTime('2017-03-05T15:20:05+00:00'), '"2017-03-05T15:20:05+00:00"'],
            [new DateTimeImmutable('2017-03-05T15:20:05+00:00'), '"2017-03-05T15:20:05+00:00"'],
            [new ArrayIterator(range(1, 3)), '`[traversable] (ArrayIterator: { 1, 2, 3 })`'],
            [$this->getObjectWithToString(), '"__toString"'],
            [[], '{ }'],
            [range(1, 5), '{ 1, 2, 3, 4, 5 }'],
            [range(1, 9), '{ 1, 2, 3, 4, 5,  ...  }'],
            [$arrayThreeLevels, '{ "foo": { "bar": { "baz": "Here!" } } }'],
            [$arrayFourLevels, '{ "foo": { "bar": { "baz": ... } } }'],
            [tmpfile(), '`[resource] (stream)`'],
            [1.0, '1.0'],
            [INF, '`INF`'],
            [INF * -1, '`-INF`'],
            [acos(8), '`NaN`'],
            [true, '`true`'],
            [false, '`false`'],
            ['Something', '"Something"'],
            ['What "if"', '"What \"if\""'],
            ['What \'if\'', '"What \'if\'"'],
            [42, '42'],
        ];
    }

    private function arrayToObject(array $array): stdClass
    {
        $object = (object) $array;
        foreach ($object as &$property) {
            if (is_array($property)) {
                $property = $this->arrayToObject($property);
            }
        }

        return $object;
    }

    private function getObjectWithToString()
    {
        return new class() {
            public function __toString()
            {
                return __FUNCTION__;
            }
        };
    }

    /**
     * @test
     *
     * @dataProvider normalizerDataProvider
     *
     * @param mixed  $input
     * @param string $expectedNormalized
     */
    public function shouldNormalizeValuesMessage($input, string $expectedNormalized): void
    {
        $messageCreator = new Formatter(3, 5);

        $actualNormalized = $messageCreator->create($input, [], '{{placeholder}}');

        self::assertSame($expectedNormalized, $actualNormalized);
    }
}
