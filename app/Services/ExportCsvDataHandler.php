<?php

namespace App\Services;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;

class ExportCsvDataHandler
{
    public function handleTest(): HttpResponse
    {
        $fileName = time();

        $columns = $this->getColumns();

        $entites = $this->getEntities();

        $file = fopen('php://temp', 'w');
        fputcsv($file, $columns);

        collect($entites)
            ->each(function ($el) use ($file) {
                fputcsv($file, $el);
            });

        rewind($file);

        $csv = stream_get_contents($file);

        fclose($file);

        $headers = array(
            'Content-Encoding' => 'UTF-8',
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '.csv"',
        );

        // Inaczej nie wyświetlają się polskie znaki
        $csv = "\xEF\xBB\xBF" . $csv; // dodaj BOM na początku pliku

        return Response::make($csv, 200, $headers);
    }

    private function getColumns(): array
    {
        return [
            'id',
            'title',
            'description',
            'meta:title',
            'meta:description',
        ];
    }

    private function getEntities(): array
    {
        return collect([
                [
                    'id' => 1,
                    'title' => 'Losowy tytul bez polskich znakow',
                    'description' => 'Losowy opis bez polskich znakow. Losowy opis bez polskich znakow. Losowy opis bez polskich znakow. Losowy opis bez polskich znakow. Losowy opis bez polskich znakow. ',
                    'meta:title' => 'Losowy inny tytul bez polskich znakow.',
                    'meta:description' => 'Losowy inny opis bez polskich znakow',
                ],
                [
                    'id' => 2,
                    'title' => 'Losowy tytul z polskimi znakami ęółśążźćń',
                    'description' => 'Losowy inny opis z polskimi znakami ęółśążźćń. Losowy inny opis z polskimi znakami ęółśążźćń. Losowy inny opis z polskimi znakami ęółśążźćń. Losowy inny opis z polskimi znakami ęółśążźćń. Losowy inny opis z polskimi znakami ęółśążźćń. ',
                    'meta:title' => 'Losowy inny tytuł z polskimi znakami ęółśążźćń',
                    'meta:description' => 'Losowy inny opis z polskimi znakami ęółśążźćń. Losowy inny opis z polskimi znakami ęółśążźćń. Losowy inny opis z polskimi znakami ęółśążźćń. Losowy inny opis z polskimi znakami ęółśążźćń. Losowy inny opis z polskimi znakami ęółśążźćń. ',
                ],
            ])
            ->toArray();
    }
}
