<?php
namespace App\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use App\Component\ORMDate;

class ORMDateType extends Type {

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return 'date';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return new ORMDate($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        return $value->format('Y-m-d');
    }

    public function getName() {
        return "ormdate";
    }

    public function canRequireSQLConversion()
    {
        return false;
    }

}