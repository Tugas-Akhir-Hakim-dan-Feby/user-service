<?php

namespace App\Http\Filters;

class Name
{
    public function handle($query, $next)
    {
        if (!request()->has('name')) {
            return $next($query);
        }

        $query->where('name', 'like', '%' . request()->name . '%');

        return $next($query);
    }
}
