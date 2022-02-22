<?php

namespace Leyton\ClevExport\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Leyton\ClevExport\Events\ExportMergedSuccessfully;
use Leyton\ClevExport\Models\Export;
use Leyton\ClevExport\Models\SubExport;
use Throwable;

class MergeSubExportsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Export $export;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1200*10;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Export $export)
    {
        $this->export = $export;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       if($this->export->load('subExports')->subExports()->get()->every(fn(SubExport $sub) => $sub->isHavingStatus(SubExport::EXPORTED) )){
           $this->export->toStatus(Export::MERGING);

           try{

               $subExports = $this->export->subExports()->pluck('file_path');
               $source = $this->export->getPath();
               foreach ($subExports as $path){
                   Storage::disk('local')->append($source, Storage::disk('local')->get($path));
               }


               $this->export->update([
                    'status' => Export::EXPORTED,
                   'file_path' => $source,
                   'reason' => null,
                   'exported_at' => now()
               ]);

               event(new ExportMergedSuccessfully($this->export));

           }catch (\Exception $exception){
               $this->fail($exception);
           }
       }
    }

    /**
     * @param Throwable $exception
     * @return void
     */
    public function fail(Throwable $exception)
    {
        $this->export->toStatus(Export::FAILED, [
            'message' => $exception->getMessage(),
        ]);
    }
}
