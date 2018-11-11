<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Basic;

use Marcosh\PhpValidationDSL\Translator\Translator;
use Marcosh\PhpValidationDSL\Validation;
use function is_callable;

final class IsInteger extends ComposingAssertion implements Validation
{
    public const NOT_AN_INTEGER = 'is-integer.not-an-integer';

    public function __construct(?callable $errorFormatter = null)
    {
        $this->isAsAsserted = IsAsAsserted::withAssertionAndErrorFormatter(
            'is_int',
            is_callable($errorFormatter) ?
                $errorFormatter :
                function ($data) {
                    return [self::NOT_AN_INTEGER];
                }
        );
    }

    public static function withTranslator(Translator $translator): self
    {
        return new self(function ($data) use ($translator) {
            return [$translator->translate(self::NOT_AN_INTEGER)];
        });
    }
}
