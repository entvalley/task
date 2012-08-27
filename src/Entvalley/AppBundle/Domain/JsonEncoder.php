<?php

namespace Entvalley\AppBundle\Domain;

class JsonEncoder
{
    public static function encode($object)
    {
        if (is_array($object) && 0 === count($object)) {
            $object = new \ArrayObject();
        }

        return json_encode($object, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }
}