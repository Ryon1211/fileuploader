<?php

namespace App\Libs;

use Carbon\Carbon;

class ExpireDateUtil
{
    public function generateExpireDatetime(?string $date): ?string
    {
        if (array_key_exists($date, \DateOptionsConstants::EXPIRE_OPTIONS)) {
            return $date ? Carbon::now()->addDay($date) : null;
        }

        return $date;
    }

    public function checkExpireDate(?string $dateTime): bool
    {
        return Carbon::now()->lte(Carbon::parse($dateTime));
    }

    public function formatShowExpireDatetime(?string $date): ?string
    {
        return $date ? Carbon::parse($date) : null;
    }
}
