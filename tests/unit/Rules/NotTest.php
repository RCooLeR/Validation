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

use Respect\Validation\Test\RuleTestCase;

/**
 * @group rule
 *
 * @covers \Respect\Validation\Rules\Not
 *
 * @author Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 * @author Henrique Moody <henriquemoody@gmail.com>
 *
 * @since 0.3.9
 */
final class NotTest extends RuleTestCase
{
    /**
     * {@inheritdoc}
     */
    public function providerForValidInput(): array
    {
        return [
            [new Not($this->createRuleMock('foo', false)), 'foo'],
            [new Not(new Not($this->createRuleMock('foo', true))), 'foo'],
            [new Not(new Not(new Not($this->createRuleMock('foo', false)))), 'foo'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function providerForInvalidInput(): array
    {
        return [
            [new Not($this->createRuleMock('foo', true)), 'foo'],
            [new Not(new Not($this->createRuleMock('foo', false))), 'foo'],
            [new Not(new Not(new Not($this->createRuleMock('foo', true)))), 'foo'],
        ];
    }

    /**
     * @test
     */
    public function shouldHaveChildResultAsChildren()
    {
        $input = 'baz';

        $childRule = $this->createRuleMock($input, true);

        $rule = new Not($childRule);
        $result = $rule->validate($input);

        $childResult = current($result->getChildren());

        self::assertSame($childRule, $childResult->getRule());
    }

    /**
     * @test
     */
    public function shouldHaveAnInvertedChildResultAsChildren()
    {
        $input = 'baz';

        $childRule = $this->createRuleMock($input, false);

        $rule = new Not($childRule);
        $result = $rule->validate($input);

        $childResult = current($result->getChildren());

        self::assertTrue($childResult->isInverted());
    }
}
