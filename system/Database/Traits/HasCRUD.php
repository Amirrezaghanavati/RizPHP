<?php

namespace System\Database\Traits;


use System\Database\DBConnection\DBConnection;

trait HasCRUD
{

    protected function createMethod($values){
        $values = $this->arrayToCastEncodeValue($values);
        $this->arrayToAttributes($values, $this);
        return $this->saveMethod();
    }

    protected function updateMethod($values){
        $values = $this->arrayToCastEncodeValue($values);
        $this->arrayToAttributes($values, $this);
        return $this->saveMethod();
    }


    protected function allMethod(): array
    {
        // Set Sql
        $this->setSql('SELECT * FROM ' . $this->getTableName());
        $statement = $this->executeQuery();
        $data = $statement->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];
    }

    protected function findMethod($id)
    {
        $this->setSql('SELECT * FROM ' . $this->getTableName());
        $this->setWhere('AND', $this->getAttributeName($this->primaryKey) . ' = ?');
        $this->addValue($this->primaryKey, $id);
        $statement = $this->executeQuery();
        $data = $statement->fetch();
        $this->setAllowedMethods(['update', 'delete', 'save']);
        if ($data) {
            return $this->arrayToAttributes($data);
        }
        return null;
    }

    protected function whereMethod($attribute, $firstValue, $secondValue = null)
    {
        if (is_null($secondValue)) {
            $condition = $this->getAttributeName($attribute) . ' = ?';
            $this->addValue($attribute, $firstValue);
        } else {
            $condition = $this->getAttributeName($attribute) . ' ' . $firstValue . ' ?';
            $this->addValue($attribute, $secondValue);
        }

        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull', 'limit', 'orderBy', 'get', 'paginate']);
        return $this;
    }

    protected function whereOrMethod($attribute, $firstValue, $secondValue = null)
    {
        if (is_null($secondValue)) {
            $condition = $this->getAttributeName($attribute) . ' = ?';
            $this->addValue($attribute, $firstValue);
        } else {
            $condition = $this->getAttributeName($attribute) . ' ' . $firstValue . ' ?';
            $this->addValue($attribute, $secondValue);
        }

        $operator = 'Or';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull', 'limit', 'orderBy', 'get', 'paginate']);
        return $this;
    }

    protected function whereNullMethod($attribute)
    {

        $condition = $this->getAttributeName($attribute) . ' IS NULL';
        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull', 'limit', 'orderBy', 'get', 'paginate']);
        return $this;
    }

    protected function whereNotNullMethod($attribute)
    {

        $condition = $this->getAttributeName($attribute) . ' IS NOT NULL';
        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull', 'limit', 'orderBy', 'get', 'paginate']);
        return $this;
    }

    protected function whereInMethod(string $attribute, array $values)
    {
        $valuesArray = [];
        foreach ($values as $value) {
            $this->addValue($attribute, $value);
            $valuesArray[] = '?';
        }
        $valuesString = implode(', ', $valuesArray);
        $condition = $this->getAttributeName($attribute) . ' IN(' . $valuesString . ')';
        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull', 'limit', 'orderBy', 'get', 'paginate']);
        return $this;
    }

    protected function orderByMethod($attribute, $expression)
    {
        $this->setOrderBy($attribute, $expression);
        $this->setAllowedMethods(['limit', 'orderBy', 'get', 'paginate']);
        return $this;
    }

    protected function limitMethod($offset, $number)
    {
        $this->setLimit($offset, $number);
        $this->setAllowedMethods(['limit', 'get', 'paginate']);
        return $this;
    }

    protected function getMethod(array $array = null): array
    {
        if ($this->sql === '') {
            // Select *
            if (!$array) {
                $fields = $this->getTableName() . '.*';
            } else {
                foreach ($array as $key => $attribute) {
                    $array[$key] = $this->getAttributeName($attribute);
                }
                $fields = implode(', ', $array);
            }

            $this->setSql('SELECT ' . $fields . ' FROM ' . $this->getTableName());
        }
        $statement = $this->executeQuery();
        $data = $statement->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];
    }

    protected function paginateMethod($perPage): array
    {

        $totalRows = $this->getCount();

        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $totalPages = ceil($totalRows / $perPage);
        $currentPage = min($currentPage, $totalPages);
        $currentPage = max($currentPage, 1);
        $currentRow = ($currentPage - 1) * $perPage;

        $this->setLimit($currentRow, $perPage);

        if ($this->sql === '') {

            $this->setSql('SELECT ' . $this->getTableName() . '.* FROM ' . $this->getTableName());
        }
        $statement = $this->executeQuery();
        $data = $statement->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];

    }

    protected function deleteMethod($id = null)
    {
        $object = $this;
        $this->resetQuery();
        if ($id) {
            $object = $this->findMethod($id);
            $this->resetQuery();
        }
        $object->setSql('DELETE FROM ' . $object->getTableName());
        $object->setWhere('AND', $this->getAttributeName($this->primaryKey) . ' = ?');
        $object->addValue($object->primaryKey, $object->$object->primaryKey);
        return $object->executeQuery();
    }

    protected function saveMethod()
    {

        $fillString = $this->fill();
        if (!isset($this->$this->primaryKey)) { // Insert query
            $this->setSql('INSERT INTO ' . $this->getTableName() . ' SET ' . $fillString . $this->getAttributeName($this->createdAt) . '=NOW()');
        } else { // Update query
            $this->setSql('UPDATE ' . $this->getTableName() . ' SET ' . $fillString . $this->getAttributeName($this->updatedAt) . '=NOW()');
            $this->setWhere('AND', $this->getAttributeName($this->primaryKey) . ' = ?');
            $this->addValue($this->primaryKey, $this->$this->primaryKey);
        }
        $this->executeQuery();
        $this->resetQuery();
        // New query
        if (!isset($this->$this->primaryKey)) {

            $object = $this->findMethod(DBConnection::getNewInsertId());
            $defaultVars = get_class_vars(static::class);
            $allVars = get_object_vars($object);
            $differentVars = array_diff(array_keys($allVars), array_keys($defaultVars));

            foreach ($differentVars as $attribute) {
                $this->inCastAttributes($attribute)
                    ? $this->registerAttribute($this, $attribute, $this->castEncodeValue($attribute, $object->$attribute))
                    : $this->registerAttribute($this, $attribute, $object->$attribute);
            }
        }

        $this->resetQuery();
        $this->setAllowedMethods(['update', 'delete', 'find']);
        return $this;

    }

    protected function fill(): string
    {
        $fillArray = [];
        foreach ($this->fillable as $attribute) {
            if (isset($this->$attribute)) {
                $fillArray[] = $this->getAttributeName($attribute) . ' = ?';
                $this->inCastAttributes($attribute)
                    ? $this->addValue($attribute, $this->castEncodeValue($attribute, $this->$attribute))
                    : $this->addValue($attribute, $this->$attribute);
            }
        }

        return implode(', ', $fillArray);
    }

}