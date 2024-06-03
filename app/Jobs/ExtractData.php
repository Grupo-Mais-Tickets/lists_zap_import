<?php

namespace App\Jobs;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Storage;

class ExtractData extends Job
{
    protected $filePath;
    protected $list;

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     * @param $list
     * @return void
     */
    public function __construct($filePath, $list)
    {
        $this->filePath = $filePath;
        $this->list = $list;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('ExtractData job started.');

        try {
            // Carregar o arquivo XLS/XLSX
            $spreadsheet = IOFactory::load($this->filePath);
            \Log::info('Spreadsheet loaded.');

            // Selecionar a primeira planilha
            $sheet = $spreadsheet->getActiveSheet();

            $batchSize = 500; // Tamanho do lote
            $rows = [];
            foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                $rows[] = $rowData;

                // Quando atingimos o tamanho do lote, despachamos um job para processá-lo
                if (count($rows) == $batchSize) {
                    \Log::info('Dispatching ProcessBatch job.');
                    dispatch(new ProcessBatch($rows, $this->list));
                    $rows = []; // Reiniciar o array de linhas
                }
            }

            // Processar as linhas restantes
            if (count($rows) > 0) {
                \Log::info('Dispatching ProcessBatch job for remaining rows.');
                dispatch(new ProcessBatch($rows, $this->list));
            }

            // Opcional: Excluir o arquivo original após o processamento
            Storage::delete($this->filePath);

        } catch (\Exception $e) {
            // Tratar erros de processamento do arquivo
            \Log::error('Error processing XLS file: ' . $e->getMessage());
        }

        \Log::info('ExtractData job completed.');
    }
}
