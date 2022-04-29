<?php

namespace App\Libs;

use Ramsey\Uuid\Uuid;

class UuidClass
{

    public static function getUuidBytes(){
        $uuid = Uuid::uuid4();
        return $uuid->getBytes();
    }

    public static function getUuidString(){
        $uuid = Uuid::uuid4();
        return $uuid->toString();
    }

    public static function getUuidByteToString($uuidByte){
        $uuid = Uuid::fromBytes($uuidByte);
        return $uuid->toString();
    }

    public static function getUuidStringToByte($uuidString){
        $uuid = Uuid::fromString($uuidString);
        return $uuid->getBytes();
    }
}