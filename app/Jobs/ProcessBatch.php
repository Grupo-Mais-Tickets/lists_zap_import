<?php

namespace App\Jobs;

use Carbon\Carbon;

class ProcessBatch extends Job
{
    protected $batch;
    protected $list;

    /**
     * Create a new job instance.
     *
     * @param array $batch
     * @param $list
     * @return void
     */
    public function __construct(array $batch, $list)
    {
        $this->batch = $batch;
        $this->list = $list;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('ProcessBatch job started.');

        foreach ($this->batch as $rowIndex => $rowData) {
            $name = $rowData[0];
            $cellphone = $rowData[1];
            $datebirth = $this->excelDateToDate($rowData[2]);

            $existingRecipient = $this->list->recipients()->where('phone', $cellphone)->first();

            if (!$existingRecipient) {
                try {
                    $this->list->recipients()->create([
                        'name' => $name,
                        'phone' => $cellphone,
                        'birthdate' => $datebirth,
                    ]);
                } catch (\Exception $e) {
                    \Log::error("Error saving recipient for row {$rowIndex}: " . $e->getMessage());
                }
            } else {
                \Log::info("Duplicate recipient found for row {$rowIndex}, skipping.");
            }
        }

        \Log::info('ProcessBatch job completed.');

        $pendingJobs = \Queue::size();
        if ($pendingJobs === 0) {
            $this->list->status = 'finalizado';
            $this->list->save();
        }
    }

    /**
     * Convert Excel date to a readable date format.
     *
     * @param int $serial
     * @return string
     */
    private function excelDateToDate($serial)
    {
        // A data base do Excel é 30 de dezembro de 1899, então adicionamos esse offset
        $unix_date = ($serial - 25569) * 86400;
        return Carbon::createFromTimestamp($unix_date)->format('Y-m-d');
    }
}
