<?php


namespace Leyton\ClevExport\Events;


use Leyton\ClevExport\Models\Export;

class ExportMergedSuccessfully
{
    public Export $export;

    /**
     * @param Export $export
     */
    public function __construct(Export $export)
    {
        $this->export = $export;
    }
}