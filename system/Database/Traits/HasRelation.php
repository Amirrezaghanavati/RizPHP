<?php

namespace System\Database\Traits;

trait HasRelation
{

    protected function hasOne($model, $foreignKey, $localKey)
    {
        if ($this->{$this->primaryKey}) {
            return (new $model())->getHasOneRelation($this->table, $foreignKey, $localKey, $this->$localKey);
        }

    }

    public function getHasOneRelation($table, $foreignKey, $otherKey, $otherKeyValue)
    {
        $this->setSql("SELECT `b`.* FROM `{$table}` AS `a` JOIN " . $this->getTableName() . " AS `b` ON `a`.`{$otherKey}` = `b`.`{$foreignKey}`");
        $this->setWhere('AND', "`a`.`{$otherKey}` = ?");
        $this->table = 'b';
        $this->addValue($otherKey, $otherKeyValue);
        $statement = $this->executeQuery();
        $data = $statement->fetch();
        if ($data) {
            return $this->arrayToAttributes($data);
        }
        return null;
    }

    protected function hasMany($model, $foreignKey, $otherKey)
    {
        if ($this->{$this->primaryKey}) {
            return (new $model())->getHasManyRelation($this->table, $foreignKey, $otherKey, $this->$otherKey);
        }

    }

    public function getHasManyRelation($table, $foreignKey, $otherKey, $otherKeyValue)
    {
        $this->setSql("SELECT `b`.* FROM `{$table}` AS `a` JOIN " . $this->getTableName() . " AS `b` ON `a`.`{$otherKey}` = `b`.`{$foreignKey}`");
        $this->setWhere('AND', "`a`.`{$otherKey}` = ?");
        $this->table = 'b';
        $this->addValue($otherKey, $otherKeyValue);
        return $this;
    }

    protected function belongsTo($model, $foreignKey, $localKey)
    {
        if ($this->{$this->primaryKey}) {

            return (new $model())->getBelongsToRelation($this->table, $foreignKey, $localKey, $this->$foreignKey);
        }

    }

    public function getBelongsToRelation($table, $foreignKey, $otherKey, $foreignKeyValue)
    {
        $this->setSql("SELECT `b`.* FROM `{$table}` AS `a` JOIN " . $this->getTableName() . " AS `b` ON `a`.`{$foreignKey}` = `b`.`{$otherKey}`");
        $this->setWhere('AND', "`a`.`{$foreignKey}` = ?");
        $this->table = 'b';
        $this->addValue($foreignKey, $foreignKeyValue);
        $statement = $this->executeQuery();
        $data = $statement->fetch();
        if ($data) {
            return $this->arrayToAttributes($data);
        }
        return null;
    }

    protected function belongsToMany($model, $commonTable, $localKey, $middleForeignKey, $middleRelation, $foreignKey)
    {
        if ($this->{$this->primaryKey}) {
            return (new $model())->getBelongsToManyRelation($this->table, $commonTable, $localKey, $this->$localKey, $middleForeignKey, $middleRelation, $foreignKey);
        }
    }

    protected function getBelongsToManyRelation($table, $commonTable, $localKey, $localKeyValue, $middleForeignKey, $middleRelation, $foreignKey)
    {
//        $sql = "SELECT posts.* FROM ( SELECT category_post.* FROM categories JOIN category_post on categories.id = category_post.cat_id WHERE  categories.id = ? ) as relation JOIN posts on relation.post_id=posts.id ";
        $this->setSql("SELECT `c`.* FROM ( SELECT `b`.* FROM `{$table}` AS `a` JOIN `{$commonTable}` AS `b` on `a`.`{$localKey}` = `b`.`{$middleForeignKey}` WHERE  `a`.`{$localKey}` = ? ) AS `relation` JOIN " . $this->getTableName() . " AS `c` ON `relation`.`{$middleRelation}` = `c`.`$foreignKey`");
        $this->addValue("{$table}_{$localKey}", $localKeyValue);
        $this->table = 'c';
        return $this;
    }
}