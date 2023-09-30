<?php

namespace System\Database\Traits;

trait HasSoftDeletes
{

    protected function deleteMethod($id = null)
    {
        $object = $this;
        if ($id) {
            $this->resetQuery();
            $object = $this->findMethod($id);
        }
        if ($object) {
            $object->resetQuery();
            $object->setSql('UPDATE ' . $object->getTableName() . ' SET ' . $this->getAttributeName($this->deletedAt) . ' = NOW()');
            $object->setWhere('AND', $this->getAttributeName($object->primaryKey) . ' = ?');
            $object->addValue($object->primaryKey, $object->$object->primaryKey);
            return $object->executeQuery();
        }
        return null;
    }

    protected function allMethod(): array
    {
        $this->setSql('SELECT ' . $this->getTableName() . '.* FROM ' . $this->getTableName() );
        $this->setWhere('AND', $this->getAttributeName($this->deletedAt) . ' IS NULL');
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
        $this->resetQuery();
        $this->setSql('SELECT ' . $this->getTableName() . '.* FROM ' . $this->getTableName() );
        $this->setWhere('AND', $this->getAttributeName($this->primaryKey) . ' = ?');
        $this->addValue($this->primaryKey, $id);
        $this->setWhere('AND', $this->getAttributeName($this->deletedAt) . ' IS NULL');
        $statement = $this->executeQuery();
        $data = $statement->fetch();
        $this->setAllowedMethods(['update', 'delete', 'save']);
        if ($data) {
            return $this->arrayToAttributes($data);
        }
        return null;
    }

    protected function getMethod(array $array = null): array
    {
        if ($this->sql === '') {
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
        $this->setWhere('AND', $this->getAttributeName($this->deletedAt) . ' IS NULL');
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
        $this->setWhere('AND', $this->getAttributeName($this->deletedAt) . ' IS NULL');
        $totalRows = $this->getCount();
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $totalPages = ceil($totalRows / $perPage);
        $currentPage = min($currentPage, $totalPages);
        $currentPage = max($currentPage, 1);
        $currentRow = ($currentPage - 1) * $perPage;
        $this->setLimit($currentRow, $perPage);

        if ($this->sql === '') {
            $this->setSql('SELECT ' . $this->getTableName() . '.*' . $this->getTableName());
        }
        $statement = $this->executeQuery();
        $data = $statement->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];

    }




}