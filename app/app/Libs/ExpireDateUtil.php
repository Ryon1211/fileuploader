<?php

namespace App\Libs;

use Carbon\Carbon;

class ExpireDateUtil
{
    public function generateExpireDatetime(?string $date): mixed
    {
        if (array_key_exists($date, \DateOptionsConstants::EXPIRE_OPTIONS)) {
            return $date ? (string)Carbon::now()->addDay($date) : null;
        }

        return false;
    }

    public function checkExpireDate(?string $dateTime): bool
    {
        return Carbon::now()->lte(Carbon::parse($dateTime));
    }
}
