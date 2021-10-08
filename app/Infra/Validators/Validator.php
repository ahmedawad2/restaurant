<?php
/**
 * NOTE: decide whether u need the validator to continue check over inputs even if one fails,
 *or not.
 * current implementation does, if u don't want to go further if one condition fails,
 * simply add a check in every function if($this->result){DO IMPLEMENTATION}
 */

namespace App\Infra\Validators;

use App\Infra\Classes\Common\Constants;
use App\Infra\Interfaces\Resources\ResourceInterface;
use App\Infra\Interfaces\Validators\ValidatorInterface;
use Illuminate\Support\Facades\Validator as LaravelValidator;

class Validator implements ValidatorInterface
{
    private ResourceInterface $resource;
    private bool $result = true;
    private array $errors = [];

    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function required(array $inputs): ValidatorInterface
    {
        if ($this->result) {
            foreach ($inputs as $input) {
                $value = $this->resource->get($input);
                if (is_bool($value)) {
                    if (!isset($value)) {
                        $this->result = false;
                        $this->errors[$input][] = 'is required';
                    }
                } else {
                    if (null == $value) {
                        $this->result = false;
                        $this->errors[$input][] = 'is required';
                        break;
                    }
                }
            }
        }
        return $this;
    }

    public function unique($input, $table, $column, int $ignoreId = null): ValidatorInterface
    {
        if (LaravelValidator::make([$input => $this->resource->get($input)],
            [
                $input => "unique:{$table},{$column}" . ($ignoreId ? (',' . $ignoreId) : '')
            ])->fails()) {
            $this->result = false;
            $this->errors[$input][] = 'already exists';
        }
        return $this;
    }

    public function max($input, int $max): ValidatorInterface
    {
        if (LaravelValidator::make([$input => $this->resource->get($input)], [
            $input => 'max:' . $max
        ])->fails()) {
            $this->result = false;
            $this->errors[$input][] = 'max value is ' . $max;
        }
        return $this;
    }

    public function in($input, array $options): ValidatorInterface
    {
        if ($this->result) {
            $inputValue = $this->resource->get($input);
            if (!in_array($inputValue, $options)) {
                $this->result = false;
                $this->errors[$input][] = 'must be in ' . implode(',', $options);
            }
        }
        return $this;
    }

    public function min($input, int $min): ValidatorInterface
    {
        if (LaravelValidator::make([$input => $this->resource->get($input)], [
            $input => 'min:' . $min
        ])->fails()) {
            $this->result = false;
            $this->errors[$input][] = 'min value is ' . $min;
        }
        return $this;
    }

    public function matchDateFormat($input, string $format = Constants::DEFAULT_DATE_FORMAT): ValidatorInterface
    {
        $date = \DateTime::createFromFormat($format, trim($this->resource->get($input)));
        if (!$date || $date->format($format) !== trim($this->resource->get($input))) {
            $this->result = false;
            $this->errors[$input][] = 'must match the format ' . $format;
        }
        return $this;
    }

    public function getResource(): ResourceInterface
    {
        return $this->resource;
    }

    public function has(string $input): bool
    {
        $value = $this->resource->get($input);
        if (!isset($value)) {
            return false;
        }
        return true;
    }

    public function sent(string $input): bool
    {
        return array_key_exists($input, $this->resource->all());
    }

    public function validate(): bool
    {
        return (bool)$this->result;
    }

    public function integer(array $inputs): ValidatorInterface
    {
        if ($this->result) {
            foreach ($inputs as $input) {
                if (!is_int($this->resource->get($input))) {
                    $this->result = false;
                    $this->errors[$input][] = 'must be an integer';
                    break;
                }
            }
        }
        return $this;
    }

    public function numeric(array $inputs): ValidatorInterface
    {
        if ($this->result) {
            foreach ($inputs as $input) {
                if (!is_numeric($this->resource->get($input))) {
                    $this->result = false;
                    $this->errors[$input][] = 'must be numeric';
                    break;
                }
            }
        }
        return $this;
    }

    public function string(array $inputs): ValidatorInterface
    {
        if ($this->result) {
            foreach ($inputs as $input) {
                if (!is_string($this->resource->get($input))) {
                    $this->result = false;
                    $this->errors[$input][] = 'must be string';
                    break;
                }
            }
        }
        return $this;
    }

    public function email(array $inputs): ValidatorInterface
    {
        if ($this->result) {
            foreach ($inputs as $input) {
                if (!filter_var($this->resource->get($input), FILTER_VALIDATE_EMAIL)) {
                    $this->result = false;
                    $this->errors[$input][] = 'must be a valid email';
                    break;
                }
            }
        }
        return $this;
    }

    public function double(array $inputs): ValidatorInterface
    {
        if ($this->result) {
            foreach ($inputs as $input) {
                if (!is_double($this->resource->get($input))) {
                    $this->result = false;
                    $this->errors[$input][] = 'must be double';
                    break;
                }
            }
        }
        return $this;
    }

    public function boolean(array $inputs): ValidatorInterface
    {
        if ($this->result) {
            foreach ($inputs as $input) {
                if (!is_bool($this->resource->get($input))) {
                    $this->result = false;
                    $this->errors[$input][] = 'must be boolean';
                    break;
                }
            }
        }
        return $this;
    }

    public function isArray(array $inputs): ValidatorInterface
    {
        if ($this->result) {
            foreach ($inputs as $input) {
                if (!is_array($this->resource->get($input))) {
                    $this->result = false;
                    $this->errors[$input][] = 'must be an array';
                    break;
                }
            }
        }
        return $this;
    }

    public function keysInArray(array $keys, $input): ValidatorInterface
    {
        $this->isArray([$input]);
        if ($this->result) {
            foreach ($keys as $key) {
                if (!array_key_exists($key, $this->resource->get($input))) {
                    $this->result = false;
                    $this->errors[$input][] = 'must contain the key ' . $key;
                    break;
                }
            }
        }
        return $this;
    }

    public function atLeastOneInArray(array $keys, $input): ValidatorInterface
    {
        $this->isArray([$input]);
        if ($this->result) {
            foreach ($keys as $key) {
                if (array_key_exists($key, $this->resource->get($input))) {
                    return $this;
                }
            }
            $this->result = false;
            $this->errors[$input][] = 'must contain at least one from: ' . implode(',', $keys);
        }
        return $this;
    }
}
