<?php

namespace App\Service;

use DateTimeImmutable;
use DateTimeZone;

class DateTimeProviderService
{
    public function getCurrentDateTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
