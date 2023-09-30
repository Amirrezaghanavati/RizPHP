<?php

namespace Sysetm\Request\Traits;

use System\Database\DBConnection\DBConnection;


trait HasValidationRules
{

    public function normalValidation($attribute, $ruleArray): void
    {

        foreach ($ruleArray as $rule) {
            if ($rule === 'required') {
                $this->required($attribute);
            } elseif (str_starts_with($rule, 'max:')) {
                $rule = str_replace('max:', '', $rule);
                $this->maxStr($attribute, $rule);
            } elseif (str_starts_with($rule, 'min:')) {
                $rule = str_replace('min:', '', $rule);
                $this->minStr($attribute, $rule);
            } elseif (str_starts_with($rule, 'exists:')) {
                $rule = str_replace('exists:', '', $rule);
                $rule = explode(',', $rule);
                $key = $rule[1] ?? null;
                $this->existsIn($attribute, $rule[0], $key);
            } elseif ($rule === 'email') {
                $this->email($attribute);
            } elseif ($rule === 'date') {
                $this->date($attribute);
            }
        }


    }

    public function numberValidation($attribute, $ruleArray): void
    {

        foreach ($ruleArray as $rule) {
            if ($rule === 'required') {
                $this->required($attribute);
            } elseif (str_starts_with($rule, 'max:')) {
                $rule = str_replace('max:', '', $rule);
                $this->maxNumber($attribute, $rule);
            } elseif (str_starts_with($rule, 'min:')) {
                $rule = str_replace('min:', '', $rule);
                $this->minNumber($attribute, $rule);
            } elseif (str_starts_with($rule, 'exists:')) {
                $rule = str_replace('exists:', '', $rule);
                $rule = explode(',', $rule);
                $key = $rule[1] ?? null;
                $this->existsIn($attribute, $rule[0], $key);
            } elseif ($rule === 'number') {
                $this->number($attribute);
            }
        }
    }

    protected function maxStr($name, $count): void
    {
        if ($this->checkFieldExist($name) && strlen($this->request[$name]) >= $count && $this->checkFirstError($name)) {
            $this->setError($name, 'max length equal or lower than ' . $count . ' character');
        }
    }

    protected function minStr($name, $count): void
    {
        if ($this->checkFieldExist($name) && strlen($this->request[$name]) <= $count && $this->checkFirstError($name)) {
            $this->setError($name, 'min length equal or upper than ' . $count . 'character');
        }
    }

    protected function maxNumber($name, $count): void
    {
        if ($this->checkFieldExist($name)) {
            if ($this->request[$name] >= $count && $this->checkFirstError($name)) {
                $this->setError($name, 'max number equal or lower than ' . $count . ' digit');
            }
        }
    }

    protected function minNumber($name, $count): void
    {
        if ($this->checkFieldExist($name)) {
            if ($this->request[$name] <= $count && $this->checkFirstError($name)) {
                $this->setError($name, 'min number equal or upper than ' . $count . ' digit');
            }
        }
    }

    protected function required($name): void
    {
        if ($this->checkFieldExist($name)) {
            if ((!isset($this->request[$name]) || empty($this->request[$name])) && $this->checkFirstError($name)) {
                $this->setError($name, $name . ' is required');
            }
        }
    }

    protected function number($name): void
    {
        if ($this->checkFieldExist($name)) {
            if (!is_numeric($name) && $this->checkFirstError($name)) {
                $this->setError($name, $name . ' must be number');
            }
        }
    }

    protected function date($name): void
    {
        if ($this->checkFieldExist($name)) {
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->request[$name]) && $this->checkFirstError($name)) {
                $this->setError($name, $name . ' must be date format');
            }
        }
    }

    protected function email($name): void
    {
        if ($this->checkFieldExist($name)) {
            if (!filter_var($this->request[$name], FILTER_VALIDATE_EMAIL) && $this->checkFirstError($name)) {
                $this->setError($name, $name . ' must be email format');
            }
        }
    }

    public function existsIn($name, $table, $field = 'id')
    {
        if ($this->checkFieldExist($name)) {
            if ($this->checkFirstError($name)) {
                $value = $this->$name;
                $sql = 'SELECT COUNT(*) FROM ' . $table . ' WHERE ' . $field = ' = ?';
                $statement = DBConnection::getDbConnectionInstance()->prepare($sql);
                $statement->execute([$value]);
                $result = $statement->fetchColumn();
                if ($result == 0){
                    $this->setError($name, $name . ' not already exists');
                }
            }

        }

    }

}