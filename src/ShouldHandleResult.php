<?php


namespace Leyton\ClevExport;


interface ShouldHandleResult
{
    public function transform($data): ExportTransformed;
}