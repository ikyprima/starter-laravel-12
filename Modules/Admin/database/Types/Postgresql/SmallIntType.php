<?php

namespace Modules\Admin\database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Modules\Admin\database\Types\Type;

class SmallIntType extends Type
{
    public const NAME = 'smallint';
    public const DBTYPE = 'int2';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        $commonIntegerTypeDeclaration = call_protected_method($platform, '_getCommonIntegerTypeDeclarationSQL', $field);

        $type = $field['autoincrement'] ? 'smallserial' : 'smallint';

        return $type.$commonIntegerTypeDeclaration;
    }
}
