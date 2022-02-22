<?php


namespace Leyton\ClevExport;


use Leyton\ClevExport\Jobs\RunSubExport;

class Exporter
{
    private array $filters;
    private $per;
    private $page;
    private $subExport;
    private IsExportable $exportable;
    private ?ShouldHandleResult $transformer;

    /**
     * @param array $filters
     * @param $per
     * @param $page
     * @param $subExport
     * @param IsExportable $exportable
     * @param ShouldHandleResult|null $transformer
     */
    public function __construct(array $filters, $per, $page, $subExport, IsExportable $exportable, ShouldHandleResult $transformer = null)
    {
        $this->filters = $filters;
        $this->per = $per;
        $this->page = $page;
        $this->subExport = $subExport;
        $this->exportable = $exportable;
        $this->transformer = $transformer;
    }

    /**
     * @return RunSubExport
     */
    public function newJob(): RunSubExport
    {
        return new RunSubExport($this->filters, $this->per, $this->page, $this->subExport, $this->exportable, $this->transformer);
    }
}