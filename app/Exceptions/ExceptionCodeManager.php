<?php
namespace App\Exceptions;

class ExceptionCodeManager
{
    private $baseCode = [
        'App\\Exceptions\\OpenweathermapException' => 90000,
    ];

    /**
     * @param $class
     * @return int
     */
    public function getBaseCode($class): int
    {
        return $this->baseCode[$class] ?? 999900;
    }
}
