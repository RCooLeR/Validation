<?php

/*
 * This file is part of Respect/Validation.
 *
 * (c) Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation;

/**
 * @covers \Respect\Validation\Result
 *
 * @author Henrique Moody <henriquemoody@gmail.com>
 *
 * @since 2.0.0
 */
final class ResultTest extends \PHPUnit_Framework_TestCase
{
    private function getResultsByQuantity(int $quantity, bool $isValid, $input, Rule $rule)
    {
        $results = [];
        for ($index = 0; $index < $quantity; ++$index) {
            $results[] = new Result($isValid, $input, $rule);
        }

        return $results;
    }

    /**
     * @test
     */
    public function shouldBeAbleToGetTheDefinedStatus()
    {
        $isValid = true;

        $result = new Result($isValid, 'input', $this->createMock(Rule::class));

        self::assertSame($isValid, $result->isValid());
    }

    /**
     * @test
     */
    public function shouldBeAbleToGetTheDefinedInput()
    {
        $input = 'some input';

        $result = new Result(false, $input, $this->createMock(Rule::class));

        self::assertSame($input, $result->getInput());
    }

    /**
     * @test
     */
    public function shouldBeAbleToGetTheDefinedRule()
    {
        $rule = $this->createMock(Rule::class);

        $result = new Result(false, 'input', $rule);

        self::assertSame($rule, $result->getRule());
    }

    /**
     * @test
     */
    public function shouldAcceptPropertiesOnConstructor()
    {
        $properties = [
            'foo' => new \stdClass(),
        ];

        $result = new Result(true, 'input', $this->createMock(Rule::class), $properties);

        self::assertSame($properties, $result->getProperties());
    }

    /**
     * @test
     */
    public function shouldAcceptChildrenOnConstructor()
    {
        $isValid = true;
        $input = 'input';
        $rule = $this->createMock(Rule::class);

        $children = $this->getResultsByQuantity(3, $isValid, $input, $rule);

        $result = new Result($isValid, $input, $rule, [], ...$children);

        self::assertSame($children, $result->getChildren());
    }

    /**
     * @test
     */
    public function shouldCreateANewResultWhenInverting()
    {
        $result1 = new Result(true, 'input', $this->createMock(Rule::class));
        $result2 = $result1->invert();

        self::assertNotSame($result1, $result2);
    }

    /**
     * @test
     */
    public function shouldCreateANewResultWithADifferentStatusWhenInverting()
    {
        $result1 = new Result(true, 'input', $this->createMock(Rule::class));
        $result2 = $result1->invert();

        self::assertNotEquals($result1->isValid(), $result2->isValid());
    }

    /**
     * @test
     */
    public function shouldCreateANewResultWithTheSameInputWhenInverting()
    {
        $result1 = new Result(true, 'input', $this->createMock(Rule::class));
        $result2 = $result1->invert();

        self::assertSame($result1->getInput(), $result2->getInput());
    }

    /**
     * @test
     */
    public function shouldCreateANewResultWithTheSameRuleWhenInverting()
    {
        $result1 = new Result(true, 'input', $this->createMock(Rule::class));
        $result2 = $result1->invert();

        self::assertSame($result1->getRule(), $result2->getRule());
    }

    /**
     * @test
     */
    public function shouldCreateANewResultWithTheSamePropertiesWhenInverting()
    {
        $result1 = new Result(true, 'input', $this->createMock(Rule::class), ['foo' => true]);
        $result2 = $result1->invert();

        self::assertSame($result1->getProperties(), $result2->getProperties());
    }

    /**
     * @test
     */
    public function shouldCreateANewResultWithTheSameChildrenWhenInverting()
    {
        $isValid = true;
        $input = 'input';
        $rule = $this->createMock(Rule::class);

        $children = $this->getResultsByQuantity(1, $isValid, $input, $rule);

        $result1 = new Result($isValid, $input, $rule, [], ...$children);
        $result2 = $result1->invert();

        self::assertSame($result1->getChildren(), $result2->getChildren());
    }

    /**
     * @test
     */
    public function shouldCreateANewResultWithTheDefinedPropertiesWhenInverting()
    {
        $properties1 = ['foo' => 123, 'bar' => 42];
        $properties2 = ['foo' => 456];

        $result1 = new Result(true, 'input', $this->createMock(Rule::class), $properties1);
        $result2 = $result1->invert();

        self::assertArrayHasKey('bar', $result2->getProperties());
    }

    /**
     * @test
     */
    public function shouldNotBeBeInvertedByDefault()
    {
        $result = new Result(true, 'input', $this->createMock(Rule::class));

        self::assertFalse($result->isInverted());
    }

    /**
     * @test
     */
    public function shouldBeAbleToIdentifyWhenAResultIsInverted()
    {
        $result1 = new Result(true, 'input', $this->createMock(Rule::class));
        $result2 = $result1->invert();

        self::assertTrue($result2->isInverted());
    }

    /**
     * @test
     */
    public function shouldSetInvertedResultAsNonInvertedWhenInvertingIt()
    {
        $result1 = new Result(true, 'input', $this->createMock(Rule::class));
        $result2 = $result1->invert();
        $result3 = $result2->invert();

        self::assertFalse($result3->isInverted());
    }
}
