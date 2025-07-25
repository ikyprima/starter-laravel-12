<?php

namespace Modules\Admin\database\Types\Mysql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Modules\Admin\database\Types\Type;

class PointType extends Type
{
    public const NAME = 'point';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform): string
    {
        return 'point';
    }
}
