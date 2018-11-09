<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Combinator;

use Marcosh\PhpValidationDSL\Result\ValidationResult;
use Marcosh\PhpValidationDSL\Validation;
use Webmozart\Assert\Assert;
use function is_callable;

final class Any implements Validation
{
    public const NOT_EVEN_ONE = 'any.not-even-one';

    /**
     * @var Validation[]
     */
    private $validations;

    /**
     * @var callable $messages -> array
     */
    private $errorFormatter;

    /**
     * @param Validation[] $validations
     * @param callable|null $errorFormatter
     */
    public function __construct(array $validations, ?callable $errorFormatter = null)
    {
        Assert::allIsInstanceOf($validations, Validation::class);

        $this->validations = $validations;
        $this->errorFormatter = is_callable($errorFormatter) ?
            $errorFormatter :
            function (array $messages) {
                return [
                    self::NOT_EVEN_ONE => $messages
                ];
            };
    }

    /**
     * @param Validation[] $validations
     * @return self
     */
    public static function validations(array $validations): self
    {
        return new self($validations);
    }

    /**
     * @param Validation[] $validations
     * @param callable $errorFormatter
     * @return self
     */
    public static function validationsWithFormatter(array $validations, callable $errorFormatter): self
    {
        return new self($validations, $errorFormatter);
    }

    public function validate($data, array $context = []): ValidationResult
    {
        $result = ValidationResult::errors([]);

        foreach ($this->validations as $validation) {
            $result = $result->meet($validation->validate($data, $context), 'array_merge');
        }

        $result = $result->mapErrors($this->errorFormatter);
        return $result;
    }
}
