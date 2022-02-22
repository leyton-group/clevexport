<?php

namespace Leyton\ClevExport\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Leyton\ClevExport\Models\Export;

class DeleteLocalExports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Export $export;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Export $export)
    {
        //
        $this->export = $export;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        File::cleanDirectory(storage_path("app/exports/{$this->export->id}"));
        File::deleteDirectory(storage_path("app/exports/{$this->export->id}"));

        $this->export->subExports()->delete();
    }
}
