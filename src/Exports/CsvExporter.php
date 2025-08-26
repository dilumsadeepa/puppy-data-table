<?php

namespace Dilum\PuppyDataTable\Exports;

use Illuminate\Database\Eloquent\Builder;

class CsvExporter
{
    public function export(Builder $query)
    {
        $data = $query->get()->toArray();
        $filename = 'export_' . time() . '.csv';
        $path = storage_path('app/' . $filename);
        $handle = fopen($path, 'w+');
        if (!empty($data)) {
            fputcsv($handle, array_keys($data[0]));
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
        }
        fclose($handle);
        return response()->download($path)->deleteFileAfterSend(true);
    }
}
