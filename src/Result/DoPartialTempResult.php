<?php

declare(strict_types=1);

namespace Marcosh\PhpValidationDSL\Result;

final class DoPartialTempResult
{
    /**
     * @var callable
     */
    private $f;

    /**
     * @var array
     */
    private $arguments;

    private function __construct(callable $f, array $arguments)
    {
        $this->f = $f;
        $this->arguments = $arguments;
    }

    public static function fromCallableAndArguments(callable $f, array $arguments): ValidationResult
    {
        return ValidationResult::valid(new self($f, $arguments));
    }

    public static function fromPreviousAndCallable(ValidationResult $previous, callable $f): ValidationResult
    {
        return $previous->bind(function (DoPartialTempResult $doPartialTempResult) use ($f): ValidationResult {
            $lastArgumentResult = $doPartialTempResult();

            /** @psalm-suppress MissingClosureParamType */
            return $lastArgumentResult->bind(function ($lastArgument) use ($doPartialTempResult, $f): ValidationResult {
                $fArguments = array_merge($doPartialTempResult->arguments, [$lastArgument]);

                return self::fromCallableAndArguments($f, $fArguments);
            });
        });
    }

    public function __invoke(): ValidationResult
    {
        return call_user_func_array($this->f, $this->arguments);
    }
}
