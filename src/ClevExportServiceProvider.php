<?php

namespace Leyton\ClevExport;

use Leyton\ClevExport\Events\ExportMergedSuccessfully;
use Leyton\ClevExport\Listerners\DeleteLocalFiles;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;

class ClevExportServiceProvider extends EventServiceProvider
{

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens(): array
    {
        $listeners                                    = [
            ExportMergedSuccessfully::class => [
                DeleteLocalFiles::class,
            ]
        ];

        if(config('clevexport.listeners')){
            foreach (config('clevexport.listeners') as $customListener){
                $listeners[ExportMergedSuccessfully::class][] = $customListener;
            }
        }
        return $listeners;
    }

    /**
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        $this->publishes(
            [
                __DIR__ . '/config/clevexport.php' => config_path('clevexport.php'),
            ]
        );


        $timestamp = date('Y_m_d_His', time());
        $timestamp2 = date('Y_m_d_His', time());
        $this->publishes([
                             __DIR__ . "/database/migrations/clevexport_create_exports_table.php" => database_path("/migrations/{$timestamp}_create_exports_table.php"),
                             __DIR__ . "/database/migrations/clevexport_create_sub_exports_table.php" => database_path("/migrations/{$timestamp2}_create_sub_exports_table.php"),

                         ], 'migrations');
    }
}