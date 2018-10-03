<?php

namespace pvsaintpe\performance\interfaces;

/**
 * Interface LanguageInterface
 * @package pvsaintpe\performance\interfaces
 */
interface LanguageInterface
{
    /**
     * @param string $code
     * @return int
     */
    public static function getIdByCode($code);
}