<?php

namespace Entvalley\AppBundle\Domain;

/**
 * Class CanonicalNameGenerator
 *
 * Generator for canonical names.
 *
 *
 * @package Entvalley\AppBundle\Domain
 */
class CanonicalNameGenerator
{
    const MAX_CANONICAL_NAME_LENGTH = 20;
    const ALLOWED_CHARS = 'a-zа-яё0-9 _-';

    /**
     * Transforms a name to a safe canonical name that can be used as a part of URIs.
     *
     * @param $name
     * @return string
     */
    public function generate($name)
    {
        $cleanName = preg_replace('/[^' . self::ALLOWED_CHARS . ']/ui', '', $name);
        $finalName = preg_replace('/ {1,}/', '-', mb_strtolower($cleanName));

        return substr(trim($finalName, '-'), 0, self::MAX_CANONICAL_NAME_LENGTH);
    }
}
