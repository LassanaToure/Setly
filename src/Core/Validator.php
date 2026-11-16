<?php

declare(strict_types=1);

namespace App\Core;

final class Validator
{
    private array $data;
    private array $errors = [];

    public const PASSWORD_REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{10,}$/';

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    private function value(string $field): string
    {
        return trim((string) ($this->data[$field] ?? ''));
    }

    public function required(string $field, string $label): self
    {
        if ($this->value($field) === '') {
            $this->errors[$field] = "Le champ « {$label} » est obligatoire.";
        }
        return $this;
    }

    public function email(string $field): self
    {
        $v = $this->value($field);
        if ($v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "L'adresse email n'est pas valide.";
        }
        return $this;
    }

    public function password(string $field): self
    {
        $v = $this->value($field);
        if ($v !== '' && !preg_match(self::PASSWORD_REGEX, $v)) {
            $this->errors[$field] = "Le mot de passe doit faire au moins 10 caractères et contenir une majuscule, une minuscule, un chiffre et un caractère spécial.";
        }
        return $this;
    }

    public function matches(string $field, string $other, string $label): self
    {
        if ($this->value($field) !== $this->value($other)) {
            $this->errors[$field] = "Le champ « {$label} » ne correspond pas.";
        }
        return $this;
    }

    public function integer(string $field, string $label, ?int $min = null, ?int $max = null): self
    {
        $v = $this->value($field);
        if ($v === '') {
            return $this;
        }
        if (!ctype_digit($v)) {
            $this->errors[$field] = "Le champ « {$label} » doit être un nombre entier.";
            return $this;
        }
        $n = (int) $v;
        if ($min !== null && $n < $min) {
            $this->errors[$field] = "Le champ « {$label} » doit être au minimum {$min}.";
        }
        if ($max !== null && $n > $max) {
            $this->errors[$field] = "Le champ « {$label} » doit être au maximum {$max}.";
        }
        return $this;
    }

    public function date(string $field, string $label): self
    {
        $v = $this->value($field);
        if ($v !== '' && !\DateTime::createFromFormat('Y-m-d', $v)) {
            $this->errors[$field] = "Le champ « {$label} » doit être une date valide.";
        }
        return $this;
    }

    public function in(string $field, array $allowed, string $label): self
    {
        $v = $this->value($field);
        if ($v !== '' && !in_array($v, $allowed, true)) {
            $this->errors[$field] = "La valeur du champ « {$label} » est invalide.";
        }
        return $this;
    }

    public function addError(string $field, string $message): self
    {
        $this->errors[$field] = $message;
        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
