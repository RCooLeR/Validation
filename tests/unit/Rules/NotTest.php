<?php

/*
 * This file is part of Respect/Validation.
 *
 * (c) Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

/**
 * @group  rule
 * @covers \Respect\Validation\Rules\Not
 * @covers \Respect\Validation\Exceptions\NotException
 */
class NotTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->markTestSkipped('Not needs to be refactored');
    }

    /**
     * @dataProvider providerForValidNot
     */
    public function testNot($v, $input)
    {
        $not = new Not($v);
        $this->assertTrue($not->assert($input));
    }

    /**
     * @dataProvider providerForInvalidNot
     * @expectedException \Respect\Validation\Exceptions\ValidationException
     */
    public function testNotNotHaha($v, $input)
    {
        $not = new Not($v);
        $this->assertFalse($not->assert($input));
    }

    /**
     * @dataProvider providerForSetName
     */
    public function testNotSetName($v)
    {
        $not = new Not($v);
        $not->setName('Foo');

        $this->assertEquals('Foo', $not->getName());
        $this->assertEquals('Foo', $v->getName());
    }

    public function providerForValidNot()
    {
        return [
            [new IntVal(), ''],
            [new IntVal(), 'aaa'],
        ];
    }

    public function providerForInvalidNot()
    {
        return [
            [new IntVal(), 123],
        ];
    }

    public function providerForSetName()
    {
        return [
            [new IntVal()],
            [new Not(new Not(new IntVal()))],
        ];
    }
}
