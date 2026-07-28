<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Writer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // URL::forceScheme('https');
        Writer::listen(BeforeWriting::class, function (BeforeWriting $event) {
            $export = $event->getConcernable();

            if (!is_object($export) || !str_starts_with(get_class($export), 'App\\Exports\\')) {
                return;
            }

            foreach ($event->writer->getDelegate()->getAllSheets() as $worksheet) {
                foreach ($worksheet->getCellCollection()->getCoordinates() as $coordinate) {
                    $cell = $worksheet->getCell($coordinate);
                    $value = $cell->getValue();

                    if (is_string($value) && $value !== '' && $value[0] !== '=') {
                        $cell->setValue(mb_strtoupper($value, 'UTF-8'));
                    }
                }
            }
        });
    }
}
