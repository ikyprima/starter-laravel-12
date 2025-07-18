<?php

namespace Modules\Admin\database\Types\Common;

use Doctrine\DBAL\Types\DecimalType as DoctrineDecimalType;

class NumericType extends DoctrineDecimalType
{
    public const NAME = 'numeric';

    public function getName()
    {
        return static::NAME;
    }
}
