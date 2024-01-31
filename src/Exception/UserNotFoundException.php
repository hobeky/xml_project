<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserNotFoundException extends NotFoundHttpException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function noUserWithId(string $id): self
    {
        return new self("No user found for id: '{$id}'.");
    }
}
