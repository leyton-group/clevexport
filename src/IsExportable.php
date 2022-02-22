<?php


namespace Leyton\ClevExport;

use Illuminate\Database\Eloquent\Builder;

interface IsExportable
{
    public function query(array $params): Builder;
}