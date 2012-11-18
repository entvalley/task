<?php

namespace Entvalley\AppBundle\Domain;

class CanonicalNameGenerator
{
    const MAX_CANONICAL_NAME_LENGTH = 20;

    public function generate($name)
    {
        $cleanName = preg_replace('/[^a-zа-яё0-9 _-]/ui', '', $name);
        $finalName = preg_replace('/ {1,}/', '-', mb_strtolower($cleanName));

        return substr(trim($finalName, '-'), 0, self::MAX_CANONICAL_NAME_LENGTH);
    }
}
