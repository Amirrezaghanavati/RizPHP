<?php

namespace System\Database\Traits;

trait HasMethodCaller
{

    private array $allMethods = ['create', 'update', 'delete', 'find', 'all', 'get', 'save', 'where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull', 'paginate', 'limit', 'orderBy'];
    private array $allowedMethods = ['create', 'update', 'delete', 'find', 'all', 'get', 'save', 'where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull', 'paginate', 'limit', 'orderBy'];


    public function __call($method, $args)
    {
        return $this->methodCaller($this, $method, $args);
    }

    public static function __callStatic($method, $args)
    {
        $className = static::class;
        $instance = new $className;
        return $instance->methodCaller($instance, $method, $args);
    }


    protected function setAllowedMethods(array $array): void
    {
        $this->allowedMethods = $array;
    }

    private function methodCaller($object, $method, $arguments)
    {
        $suffix = 'Method';
        $methodName = $method . $suffix;
        if (in_array($method, $this->allowedMethods, true)) {
            return call_user_func_array([$object, $methodName], $arguments);
        }
    }

}