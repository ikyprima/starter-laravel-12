<?php

namespace Modules\Admin\database\Schema;

use Doctrine\DBAL\Schema\Column as DoctrineColumn;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Modules\Admin\database\Types\Type;

abstract class Column
{
    public static function make(array $column, string $tableName = null)
    {
        $name = Identifier::validate($column['name'], 'Column');
        $type = $column['type'];
        $type = ($type instanceof DoctrineType) ? $type : DoctrineType::getType(trim($type['name']));
        $type->tableName = $tableName;

        $options = array_diff_key($column, ['name' => $name, 'type' => $type]);

        return new DoctrineColumn($name, $type, $options);
    }

    /**
     * @return array
     */
    public static function toArray(DoctrineColumn $column)
    {
        $columnArr = $column->toArray();
        $columnArr['type'] = Type::toArray($columnArr['type']);
        // $columnArr['oldName'] = $columnArr['name'];
        $columnArr['null'] = $columnArr['notnull'] ? 'NO' : 'YES';
        $columnArr['extra'] = static::getExtra($column);
        $columnArr['composite'] = false;

        return $columnArr;
    }

    /**
     * @return string
     */
    protected static function getExtra(DoctrineColumn $column)
    {
        $extra = '';

        $extra .= $column->getAutoincrement() ? 'auto_increment' : '';
        // todo: Add Extra stuff like mysql 'onUpdate' etc...

        return $extra;
    }
}
