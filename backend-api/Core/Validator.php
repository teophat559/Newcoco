<?php

namespace BackendApi\Core;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class Validator {
    protected static $instance = null;
    protected $validator;

    public function __construct() {
        $this->validator = Validation::createValidator();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function validate($data, $rules) {
        $constraints = $this->buildConstraints($rules);
        $violations = $this->validator->validate($data, $constraints);

        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors
        ];
    }

    protected function buildConstraints($rules) {
        $constraints = [];

        foreach ($rules as $field => $rule) {
            if (is_string($rule)) {
                $rule = explode('|', $rule);
            }

            $fieldConstraints = [];
            foreach ($rule as $constraint) {
                if (is_string($constraint)) {
                    $fieldConstraints[] = $this->parseConstraint($constraint);
                } else {
                    $fieldConstraints[] = $constraint;
                }
            }

            $constraints[$field] = $fieldConstraints;
        }

        return new Assert\Collection($constraints);
    }

    protected function parseConstraint($constraint) {
        if (strpos($constraint, ':') !== false) {
            list($name, $params) = explode(':', $constraint, 2);
            $params = explode(',', $params);
        } else {
            $name = $constraint;
            $params = [];
        }

        switch ($name) {
            case 'required':
                return new Assert\NotBlank();
            case 'email':
                return new Assert\Email();
            case 'min':
                return new Assert\Length(['min' => $params[0]]);
            case 'max':
                return new Assert\Length(['max' => $params[0]]);
            case 'numeric':
                return new Assert\Type(['type' => 'numeric']);
            case 'integer':
                return new Assert\Type(['type' => 'integer']);
            case 'string':
                return new Assert\Type(['type' => 'string']);
            case 'array':
                return new Assert\Type(['type' => 'array']);
            case 'url':
                return new Assert\Url();
            case 'date':
                return new Assert\Date();
            case 'datetime':
                return new Assert\DateTime();
            default:
                return new Assert\NotBlank();
        }
    }
}