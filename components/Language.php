<?php

namespace pvsaintpe\performance\components;

use pvsaintpe\performance\interfaces\LanguageInterface;

/**
 * Class Language
 * @package pvsaintpe\performance\components
 */
class Language implements LanguageInterface
{
    /**
     * Английский
     * @message const
     */
    const EN = 1;

    /**
     * Русский
     * @message const
     */
    const RU = 2;

    /**
     * @param string $code
     * @return int
     */
    public static function getIdByCode($code)
    {
        if ($code == 'ru') {
            return static::RU;
        } else {
            return static::EN;
        }
    }
}