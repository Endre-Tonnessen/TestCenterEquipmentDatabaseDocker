<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon;
use Storage;
use DB;
use Mail;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a mysqldump of entire database.';

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
    public function handle()
    {
        //set filename with date and time of backup
        $filename = "backup-" . Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".sql";
        $storagePath = storage_path('Backups');

        if (!file_exists($storagePath)) {
            mkdir($storagePath);
        }

        //mysqldump command with account credentials from .env file. storage_path() adds default local storage path
        $command = "mysqldump --column-statistics=0 --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  > " . $storagePath . "/" . $filename;

        $returnVar = NULL;
        $output  = NULL;
        exec($command, $output, $returnVar);

        //if nothing (error) is returned
        if(!$returnVar){
            //get mysqldump output file from local storage
            //$getFile = Storage::disk('local')->get($filename);
            return 0;
            // delete local copy
            //Storage::disk('local')->delete($filename);
        }else{
            // if there is an error send an email
            return 1;
        }

    }
}
