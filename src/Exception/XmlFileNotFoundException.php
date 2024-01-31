<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
class XmlFileNotFoundException extends Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }


    public static function fileNotFound(string $filePath): self
    {
        return new self("The XML file at '{$filePath}' does not exist.");
    }

}
