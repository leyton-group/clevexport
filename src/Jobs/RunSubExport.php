<?php

namespace Leyton\ClevExport\Jobs;

use Excel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Leyton\ClevExport\ExportTransformed;
use Leyton\ClevExport\IsExportable;
use Leyton\ClevExport\QueryFinder;
use Leyton\ClevExport\Models\SubExport;
use Leyton\ClevExport\ShouldHandleResult;
use Leyton\ClevExport\Exports\ExportTemplate;
class RunSubExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1200*10;

    public $data;
    public $per;
    public $page;
    public $subExport;
    public IsExportable $exportable;
    private ?ShouldHandleResult $transofrmer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $per, $page, SubExport $subExport, IsExportable $exportable, ShouldHandleResult $transofrmer = null)
    {
        $this->data = $data;
        $this->per = $per;
        $this->page = $page;
        $this->subExport = $subExport;
        $this->exportable = $exportable;
        $this->transofrmer = $transofrmer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->subExport->toStatus(SubExport::IN_PROGRESS);

            $records = QueryFinder::getInstance($this->exportable)->with($this->data)->query()->paginate($this->per, [], 'page', $this->page);

            $results = $this->processRecords($records);

            $file_path = $this->subExport->getPath();

            Excel::store(new ExportTemplate($this->subExport->pagination == 1 ? $results->headers() : [], collect($results->columns())), $file_path, "local");

            $this->subExport->update([
                'status' => SubExport::EXPORTED,
                'file_path' => $file_path
            ]);

        }catch (\Exception $exception){
            report($exception);
            $this->subExport->toStatus(SubExport::CREATED);
        }
    }


    /**
     * @param LengthAwarePaginator $records
     * @return ExportTransformed
     */
    private function processRecords($records): ExportTransformed
    {
        if(is_null($this->transofrmer)){
            return new ExportTransformed(
                collect($records->first())->keys()->toArray(),
                $records->items()
            );
        }

        return  $this->transofrmer->transform($records);
    }
}
