<?php


namespace Leyton\ClevExport;


class ExportTransformed
{
    protected array $columns;
    protected array $headers;

    /**
     * @param array $headers
     * @param array $columns
     * @return void
     */
    public function __construct(array $headers, array $columns)
    {
        $this->headers = $headers;
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function columns(): array
    {
        return $this->columns;
    }
}