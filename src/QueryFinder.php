<?php


namespace Leyton\ClevExport;


use Exception;
use Illuminate\Database\Eloquent\Builder;
use Leyton\ClevExport\Models\SubExport;

use function app;

class QueryFinder
{

    protected $data = null;
    protected $query = null;
    protected $subExport = null;
    protected IsExportable $exportable;
    private ?ShouldHandleResult $transformer;

    private function __construct(IsExportable $exportable, ShouldHandleResult $transformer = null){
        $this->exportable = $exportable;
        $this->transformer = $transformer;
    }

    /**
     * Generator
     *
     * @param IsExportable $exportable
     * @param ShouldHandleResult|null $transformer
     * @return QueryFinder
     */
    public static function getInstance(IsExportable $exportable, ShouldHandleResult $transformer = null): QueryFinder
    {
        return new self($exportable, $transformer);
    }

    /**
     * QueryBuilder
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->exportable->query($this->data);
    }

    /**
     * Get per page
     *
     * @param $per
     * @param $page
     * @return Exporter
     */
    public function paginate($per, $page): Exporter
    {
        return new Exporter($this->data, $per, $page, $this->subExport, $this->exportable, $this->transformer);
    }

    /**
     * With Data
     *
     * @param $data
     * @return $this
     */
    public function with($data): self
    {
        $this->data = $data;

        return $this;
    }


    /**
     * For subExport
     *
     * @param SubExport $subExport
     * @return $this
     */
    public function forSubExport(SubExport $subExport): self
    {
        $this->subExport = $subExport;

        return $this;
    }

    /**
     * How many pages
     *
     * @param $perPage
     * @return float
     * @throws Exception
     */
    public function pages($perPage): float
    {
        if($perPage === 0){
            throw new Exception("Bad Argument provided exception");
        }
        return round($this->query()->count()/$perPage);
    }
}