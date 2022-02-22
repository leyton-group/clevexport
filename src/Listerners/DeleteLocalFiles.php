<?php


namespace Leyton\ClevExport\Listerners;


use Leyton\ClevExport\Events\ExportMergedSuccessfully;
use Leyton\ClevExport\Jobs\DeleteLocalExports;

class DeleteLocalFiles
{

    public function handle(ExportMergedSuccessfully $event)
    {
        DeleteLocalExports::dispatch($event->export)->delay(
            now()->addMinute()
        );
    }
}