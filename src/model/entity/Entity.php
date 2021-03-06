<?php

namespace TeamBuilder\model\entity;

use PDOException;
use TeamBuilder\model\Accessors;
use TeamBuilder\model\Database;

abstract class Entity
{

    use Accessors;

    //region Fields
    protected const TABLE_NAME = '';

    protected int $id;
    //endregion

    //region Methods
    /**
     *
     * @return \TeamBuilder\model\entity\Entity
     * @var mixed|null
     */
    public static function make(array $fields): Entity
    {
        return static::hydrate($fields);
    }

    protected static function hydrate(array $fields): Entity
    {
        $entity = new (get_called_class())();

        foreach ($fields as $key => $value) {
            if (property_exists(static::class, $key)) {
                $entity->$key = $value;
            }
        }

        return $entity;
    }

    public static function all(): array
    {
        $tableName = self::getTableName();
        $query = "SELECT * FROM $tableName";

        return self::createDatabase()->fetchRecords($query, static::class);
    }

    private static function getTableName(): string
    {
        return static::TABLE_NAME;
    }

    public static function createDatabase(): Database
    {

        return new Database();
    }

    public static function find($id): ?Entity
    {
        $tableName = self::getTableName();
        $query = "SELECT * FROM $tableName WHERE id=:id";
        $queryArray = ["id" => $id];
        $result = self::createDatabase()->fetchOne($query, static::class, $queryArray);

        return $result ?: null;
    }

    public function create(): bool
    {
        $columns = [];
        $valueParams = [];
        foreach ($this->toArray() as $key => $value) {
            array_push($columns, $key);
            array_push($valueParams, ":$key");
        }
        $columns = implode(',', $columns);
        $valueParams = implode(',', $valueParams);

        $tableName = self::getTableName();
        $query = "INSERT INTO $tableName ($columns) VALUES ($valueParams)";

        try {
            $this->id = self::createDatabase()->insert($query, $this->toArray());
            return true;
        } catch (PDOException) {
            return false;
        }
    }

    protected function toArray(): array
    {
        return get_object_vars($this);
    }

    public function save(): bool
    {
        $array = [];
        foreach ($this->toArray() as $key => $value) {
            if ($key != 'id') {
                array_push($array, "$key=:$key");
            }
        }
        $setLine = implode(',', $array);

        $tableName = self::getTableName();
        $query = "UPDATE $tableName SET $setLine WHERE id=:id";

        try {
            self::createDatabase()->update($query, $this->toArray());
            return true;
        } catch (PDOException) {
            return false;
        }
    }

    public function delete(): bool
    {
        return self::destroy($this->id);
    }

    public static function destroy(int $id): bool
    {
        $tableName = self::getTableName();
        $query = "DELETE FROM $tableName WHERE id=:id";
        $queryArray = ["id" => $id];

        try {
            self::createDatabase()->delete($query, $queryArray);
            return true;
        } catch (PDOException) {
            return false;
        }
    }
    //endregion
}
