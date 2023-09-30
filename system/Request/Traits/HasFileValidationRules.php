<?php

namespace Sysetm\Request\Traits;

trait HasFileValidationRules
{

    protected function fileValidation($attribute, $ruleArray): void
    {
        foreach ($ruleArray as $rule) {
            if ($rule === 'required') {
                $this->fileRequired($attribute);
            } elseif (str_starts_with($rule, 'mimes:')) {
                $rule = str_replace('mimes:', '', $rule);
                $rule = explode(',', $rule);
                $this->fileType($attribute, $rule);
            } elseif (str_starts_with($rule, 'max:')) {
                $rule = str_replace('max:', '', $rule);
                $this->maxFile($attribute, $rule);
            } elseif (str_starts_with($rule, 'min:')) {
                $rule = str_replace('min:', '', $rule);
                $this->minFile($attribute, $rule);
            }

        }
    }

    protected function fileRequired($name): void
    {
        if ((!isset($this->files[$name]['name']) || empty($this->files[$name]['name'])) && $this->checkFirstError($name)) {
            $this->setError($name, $name . ' is required');
        }
    }

    protected function fileType($name, $typesArray): void
    {
        if ($this->checkFieldExist($name) && $this->checkFirstError($name)) {
            $currentFileType = explode('/', $this->files[$name]['type'])[1];
            if (!in_array($currentFileType, $typesArray, true)) {
                $this->setError($name, $name . ' type must be ' . implode(', ', $typesArray));
            }
        }
    }

    protected function maxFile($name, $size): void
    {
        $size *= 1024;
        if ($this->checkFieldExist($name) && $this->checkFirstError($name)) {
            if ($this->files[$name]['size'] > $size) {
                $this->setError($name, 'size must be lower than ' . $size / 1024);
            }
        }

    }

    protected function minFile($name, $size): void
    {
        $size *= 1024;
        if ($this->checkFieldExist($name) && $this->checkFirstError($name)) {
            if ($this->files[$name]['size'] < $size) {
                $this->setError($name, 'size must be upper than ' . $size / 1024);
            }
        }

    }

}