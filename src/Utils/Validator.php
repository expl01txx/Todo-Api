<?php

namespace App\Utils;


use Exception;
use DateTime;

class Validator
{
    public function validate(array $data, array $rules): void
    {
        foreach ($rules as $field => $ruleString) {
            if (strpos($ruleString, 'sometimes') !== false && !isset($data[$field])) {
                continue;
            }

            // Skip validation if field is nullable and not set
            if (strpos($ruleString, 'nullable') !== false && !isset($data[$field])) {
                continue;
            }

            $rules = explode('|', $ruleString);
            
            foreach ($rules as $rule) {
                if (in_array($rule, ['sometimes', 'nullable'])) continue;
                
                $this->applyRule($field, $data[$field] ?? null, $rule, $data);
            }
        }
    }

    private function applyRule(string $field, $value, string $rule, array $data): void
    {
        $params = [];
        if (strpos($rule, ':') !== false) {
            [$rule, $param] = explode(':', $rule);
            $params = explode(',', $param);
        }

        $methodName = 'validate' . ucfirst($rule);
        if (!method_exists($this, $methodName)) {
            throw new Exception("Validation rule '$rule' does not exist");
        }

        $this->$methodName($field, $value, $params, $data);
    }

    private function validateRequired(string $field, $value): void
    {
        if (empty($value)) {
            throw new Exception("The $field field is required", 400);
        }
    }

    private function validateEmail(string $field, $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("The $field must be a valid email address", 400);
        }
    }

    private function validateMin(string $field, $value, array $params): void
    {
        $min = (int) $params[0];
        if (strlen($value) < $min) {
            throw new Exception("The $field must be at least $min characters", 400);
        }
    }

    private function validateMax(string $field, $value, array $params): void
    {
        $max = (int) $params[0];
        if (strlen($value) > $max) {
            throw new Exception("The $field may not be greater than $max characters", 400);
        }
    }

    private function validateIn(string $field, $value, array $params): void
    {
        if (!in_array($value, $params)) {
            $options = implode(', ', $params);
            throw new Exception("The $field must be one of: $options", 400);
        }
    }
    private function validateNullable(string $field, $value): bool
    {
        // The actual handling is in the validate() method
        return true;
    }

    private function validateDate(string $field, $value): void
    {
        if ($value === null) return;
        
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $value);
        if (!$date || $date->format('Y-m-d H:i:s') !== $value) {
            // Try with just date (no time)
            $date = DateTime::createFromFormat('Y-m-d', $value);
            if (!$date || $date->format('Y-m-d') !== $value) {
                throw new Exception("The $field must be a valid date (format: Y-m-d or Y-m-d H:i:s)", 400);
            }
        }
    }

}