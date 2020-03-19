<?php

namespace App\Enums;

class Paginate extends Enum
{
    const PERPAGE = 10;
    const PAGE = 1;
    const PAGEATTRIBUTE = 'page';
    const FIELDS = '*';
    
    public static function get(
        ?int $perPage = null,
        ?int $page = null,
        $fields = null,
        $pageAttribute = null
    )
    {
        return [
            $perPage ?? self::PERPAGE,
            $fields ?? self::FIELDS,
            $pageAttribute ?? self::PAGEATTRIBUTE,
            $page ?? self::PAGE,
        ];
    }
    
}
