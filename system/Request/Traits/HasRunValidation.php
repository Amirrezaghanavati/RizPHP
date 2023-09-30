<?php

namespace Sysetm\Request\Traits;

trait HasRunValidation
{

    protected function errorRedirect()
    {
        if (!$this->errorExist) {
            return $this->requset;
        }
        return back();
    }

    private function checkFirstError($name): bool
    {
        return !errorExist($name) && !in_array($name, $this->errorVariablesName, true);
    }

    private function checkFieldExist($name): bool
    {
        return isset($this->request[$name]) && !empty(isset($this->request[$name]));
    }

    private function checkFileExist($name): bool
    {
        if (isset($this->file[$name]['name'])) {
            if (!empty(isset($this->request[$name]['name']))) {
                return true;
            }
        }
        return false;
    }

    private function setError($name, $errorMessage): void
    {
        $this->errorVariablesName[] = $name;
        error($name, $errorMessage);
        $this->errorExist = true;
    }
}