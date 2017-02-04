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

use Respect\Validation\Result;
use Respect\Validation\Rule;

/**
 * Negates the given rule.
 *
 * @author Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 * @author Henrique Moody <henriquemoody@gmail.com>
 *
 * @since 0.3.9
 */
final class Not implements Rule
{
    /**
     * @var Rule
     */
    private $rule;

    /**
     * Initializes the rule.
     *
     * @param Rule $rule
     */
    public function __construct(Rule $rule)
    {
        $this->rule = $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($input): Result
    {
        $ruleResult = $this->rule->validate($input);
        $ruleResult = $ruleResult->invert();

        return new Result($ruleResult->isValid(), $input, $this, [], $ruleResult);
    }
}
