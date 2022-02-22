<?php

namespace Leyton\ClevExport\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Class ClientsExport
 * @package App\Services\Exports
 */
class ClientsExport implements WithHeadings, ShouldAutoSize, FromCollection
{

    protected $headers;
    protected $results;

    /**
     * ClientsExport constructor.
     */
    public function __construct($headers, $results)
    {
        $this->headers = $headers;
        $this->results = $results;
    }

    /**
     * Description=retourn l'en-tete du fichier excel
     * @return array
     */
    public function headings(): array
    {
        return $this->headers;
    }

    /**
     * Description= retour les lignes qui contient les resultats
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->results;
    }
}
