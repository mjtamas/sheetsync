<?php

namespace App\Console\Commands;

use App\Services\GoogleSheet;
use App\TimeEntry;
use App\Variable;
use Illuminate\Console\Command;

class SyncTimeEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:entries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will sync the new entries in the DB with the Google Sheet';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(GoogleSheet $googleSheet)
    {
        $variable = Variable::query()
        ->where('name', 'LastSyncedId')
        ->first();

    $rows = TimeEntry::query()
        ->where('id', '>', $variable->value)
        ->orderBy('id')
        ->limit(10)
        ->get();

    if ($rows->count() === 0) {
        return true;
    }

    $finalData = collect();
    $lastId = 0;

    foreach ($rows as $row) {
        $finalData->push([
            $row->id,
            $row->username,
            $row->project,
            $row->date,
            $row->time,
        ]);

        $lastId = $row->id;
    }

    $googleSheet->saveDataToSheet($finalData->toArray());

    $variable->value = $lastId;
    $variable->save();

    return true;
    }
}
