<?php


namespace Leyton\ClevExport\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Leyton\ClevExport\Models\Export;
use Leyton\ClevExport\Models\SubExport;
use Leyton\ClevExport\QueryFinder;

class PreparingExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public QueryFinder $queryFinder;
    public array $params;

    public function __construct(QueryFinder $queryFinder, array $params)
    {
        $this->queryFinder = $queryFinder;
        $this->params      = $params;
    }


    public function handle()
    {
        $chunks = config('clevexport.chunks');

        $perPage = ceil($this->queryFinder->with($this->params)->query()->count() / $chunks);

        $data = [
            'criterias' => json_encode($this->params, true),
            'status' => Export::CREATED
        ];

        if(config('clevexport.with_owner')){
            $data = array_merge($data, [
               config('clevexport.owner_id') =>  auth(config('clevexport.guard'))->user()->getAuthIdentifier()
            ]);
        }

        $export = Export::create($data);
        $chain = [];
        foreach (range(1, $chunks) as $page){
            $subExport = $export->subExports()
                                ->create([
                                             'status' => SubExport::CREATED,
                                             'pagination' => $page
                                         ]);

            $chain [] = $this->queryFinder->with($this->params)->forSubExport($subExport)->paginate($perPage, $page)->newJob();
        }

        $chain[] = new MergeSubExportsJob($export);

        StarterJob::withChain($chain)->dispatch()->delay(now()->addSecond());
    }
}
