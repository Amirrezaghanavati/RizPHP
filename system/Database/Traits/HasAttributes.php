<?php

namespace System\Database\Traits;

trait HasAttributes
{

    // Third step
    private function registerAttribute($object, string $attribute, $value): void
    {
        $this->inCastAttributes($attribute)
            ? $object->$attribute = $this->castDecodeValue($attribute, $value)
            : $object->$attribute = $value;

    }


    // Second step
    protected function arrayToAttributes(array $array, $object = null): mixed
    {
        // create object
        if (!$object) {
            $className = static::class;
            $object = new $className;
        }


        // Create records
        foreach ($array as $attribute => $value) {
            if ($this->inHiddenAttributes($attribute)) {
                continue;
            }
            $this->registerAttribute($object, $attribute, $value);
        }

        return $object;
    }

    // First step
    protected function arrayToObjects(array $array): void
    {
        $collection = [];
        foreach ($array as $record){
            $object = $this->arrayToAttributes($record);
            $collection[] = $object;
        }

        $this->collection = $collection;

    }


    private function inHiddenAttributes(string $attribute): bool
    {
        return in_array($attribute, $this->hidden, true);
    }

    private function inCastAttributes(string $attribute): bool
    {
        return array_key_exists($attribute, $this->casts);
    }

    private function castDecodeValue($attributeKey, $attributeValue)
    {
        if ($this->casts($attributeKey) === 'array' || $this->casts($attributeKey) === 'object'){
            return unserialize($attributeValue, false);
        }

        return $attributeValue;
    }

    private function castEncodeValue($attributeKey, $attributeValue)
    {
        if ($this->casts($attributeKey) === 'array' || $this->casts($attributeKey) === 'object'){
            return serialize($attributeValue);
        }

        return $attributeValue;
    }

    private function arrayToCastEncodeValue($values): array
    {
        $newArray = [];

        foreach ($values as $attribute => $value){
            $this->inCastAttributes($attribute)
                ? $newArray[$attribute] = $this->castEncodeValue($attribute,$value)
                : $newArray[$attribute] = $value;
        }

        return $newArray;
    }
}