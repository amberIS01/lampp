<?php

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? '';

            foreach ($fieldRules as $rule) {
                $param = null;
                if (str_contains($rule, ':')) {
                    [$rule, $param] = explode(':', $rule, 2);
                }

                match ($rule) {
                    'required' => empty(trim((string)$value))
                        ? $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . ' is required.')
                        : null,
                    'email' => !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)
                        ? $this->addError($field, 'Invalid email address.')
                        : null,
                    'max' => strlen((string)$value) > (int)$param
                        ? $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must be at most {$param} characters.")
                        : null,
                    'min' => strlen((string)$value) < (int)$param
                        ? $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . " must be at least {$param} characters.")
                        : null,
                    'date' => !empty($value) && !strtotime((string)$value)
                        ? $this->addError($field, 'Invalid date format.')
                        : null,
                    'numeric' => !empty($value) && !is_numeric($value)
                        ? $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . ' must be a number.')
                        : null,
                    'in' => !empty($value) && !in_array($value, explode(',', $param))
                        ? $this->addError($field, ucfirst(str_replace('_', ' ', $field)) . ' has an invalid value.')
                        : null,
                    default => null,
                };
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }
}
