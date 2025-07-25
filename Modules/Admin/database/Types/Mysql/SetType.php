<?php

namespace Modules\Admin\database\Types\Mysql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Illuminate\Support\Facades\DB;
use Modules\Admin\database\Types\Type;

class SetType extends Type
{
    public const NAME = 'set';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        throw new \Exception('Set type is not supported');
        // we're going to store SET values in the comment since DBAL doesn't support
        $allowed = explode(',', trim($fieldDeclaration['comment']));

        $pdo = DB::connection()->getPdo();

        // trim the values
        $fieldDeclaration['allowed'] = array_map(function ($value) use ($pdo) {
            return $pdo->quote(trim($value));
        }, $allowed);

        return 'set('.implode(', ', $field['allowed']).')';
    }
}
