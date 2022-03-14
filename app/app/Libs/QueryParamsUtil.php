<?php

namespace App\Libs;

use Illuminate\Http\Request;

class QueryParamsUtil
{
    public function appendQueryParams(Request $request): array
    {
        $queryParams = [];

        foreach ($request->query() as $name => $value) {
            $queryParams[$name] = $value;
        }

        return $queryParams;
    }
}
