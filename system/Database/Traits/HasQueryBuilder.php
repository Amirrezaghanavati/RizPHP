<?php

namespace System\Database\Traits;

use System\Database\DBConnection\DBConnection;

trait HasQueryBuilder
{

    private string $sql = '';
    protected array $where = [];
    private array $orderBy = [];
    private array $limit = [];
    private array $values = [];
    private array $bindValues = [];


    protected function getSql(): string
    {
        return $this->sql;
    }

    protected function setSql(string $sql): void
    {
        $this->sql = $sql;
    }

    protected function resetSql(): void
    {
        $this->sql = '';
    }


    protected function setWhere(string $operator, string $condition): void
    {
        $this->where[] = compact('operator', 'condition');
    }

    public function resetWhere(): void
    {
        $this->where = [];
    }


    protected function setOrderBy(string $name, string $expression): void
    {
        $this->orderBy[] = $this->getAttributeName($name) . ' ' . $expression;
    }


    protected function resetOrderBy(): void
    {
        $this->orderBy = [];
    }


    public function setLimit($from, $number): void
    {
        $this->limit['from'] = (int)$from;
        $this->limit['number'] = (int)$number;
    }

    public function resetLimit(): void
    {
        unset($this->limit['from'], $this->limit['number']);
    }

    protected function addValue(string $attribute, string $value): void
    {
        $this->values[$attribute] = $value;
        $this->bindValues[] = $value;
    }

    protected function removeValues(): void
    {
        $this->values = [];
        $this->bindValues = [];
    }

    protected function resetQuery(): void
    {
        $this->resetSql();
        $this->resetWhere();
        $this->resetOrderBy();
        $this->resetLimit();
        $this->removeValues();
    }

    // Create queries structure
    protected function executeQuery(): mixed
    {
        $query = $this->sql;
        if ($this->where) {
            $whereString = '';
            foreach ($this->where as $where) {
                $whereString === ''
                    ? $whereString .= $where['condition']
                    : $whereString .= ' ' . $where['operator'] . ' ' . $where['condition'];
            }
            $query .= ' WHERE ' . $whereString;
        }
        if ($this->orderBy) {
            $query .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }
        if ($this->limit) {
            $query .= ' LIMIT ' . $this->limit['from'] . ', ' . $this->limit['number'];
        }
        $query .= ';';
        echo $query . '<hr/>';

        return $this->createPdoInstance($query);
    }


    //paginator
    protected function getCount(): mixed
    {
        // SELECT COUNT(ProductID) AS NumberOfProducts FROM Products;
        $query = 'SELECT COUNT('. $this->getAttributeName($this->primaryKey).') FROM ' . $this->getTableName();
        if ($this->where) {
            $whereString = '';
            foreach ($this->where as $where) {
                $whereString === ''
                    ? $whereString .= $where['condition']
                    : $whereString .= ' ' . $where['operator'] . ' ' . $where['condition'];
            }
            $query .= ' WHERE ' . $whereString;
        }

        $query .= ';';
        return $this->createPdoInstance($query)->fetchColumn();
    }

    // Get or create instance of PDO
    protected function createPdoInstance(string $query): mixed
    {
        $pdoInstance = DBConnection::getDbConnectionInstance();
        $statement = $pdoInstance->prepare($query);
        if (count($this->bindValues) > count($this->values)) {
            count($this->bindValues) > 0
                ? $statement->execute($this->bindValues)
                : $statement->execute();
        } else {
            count($this->values) > 0
                ? $statement->execute(array_values($this->values))
                : $statement->execute();
        }
        return $statement;
    }


    // Add backtick to table and column name
    protected function getTableName(): string
    {
        return '`' . $this->table . '`';
    }

    protected function getAttributeName(string $attribute): string
    {
        return '`' . $this->table . '`.`' . $attribute . '`';
    }
}