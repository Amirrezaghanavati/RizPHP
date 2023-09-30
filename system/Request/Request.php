<?php

namespace Sysetm\Request;

use Sysetm\Request\Traits\HasFileValidationRules;
use Sysetm\Request\Traits\HasRunValidation;
use Sysetm\Request\Traits\HasValidationRules;

class Request
{
    use HasFileValidationRules, HasRunValidation, HasValidationRules;

    protected $errorExist = false;
    protected $request;
    protected $files = null;
    protected $errorVariablesName = [];

    public function __construct()
    {
        if (isset($_POST)) {
            $this->postAttributes();
        }
        if ($_FILES) {
            $this->files = $_FILES;
        }
        $rules = $this->rules();
        empty($rules) ?: $this->run($rules);
        $this->errorRedirect();
    }

    protected function rules(): array
    {
        return [];
    }

    protected function run($rules): void
    {
        foreach ($rules as $key => $values) {
            $ruleArray = explode('|', $values);
            if (in_array('file', $ruleArray)) {
                unset($ruleArray[array_search('file', $ruleArray)]);
                $this->fileValidaion($key, $ruleArray);
            } elseif (in_array('number', $ruleArray)) {
                $this->numberValidation($key, $ruleArray);
            } else {
                $this->normalValidation($key, $ruleArray);
            }
        }
    }

    public function file($name)
    {
        return $this->files[$name] ?? false;
    }

    public function all()
    {
        return $this->request;
    }

    protected function postAttributes()
    {
        foreach ($_POST as $key => $value) {
            $this->$key = htmlentities($value);
            $this->request[$key] = htmlentities($value);

        }
    }
}