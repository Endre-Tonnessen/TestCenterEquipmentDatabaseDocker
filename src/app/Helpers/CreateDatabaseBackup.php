<?php

namespace App\Helpers;

use Carbon;
use Illuminate\Support\Facades\Config;
use Storage;
use DB;
use Mail;
use Symfony\Component\Console\Output\ConsoleOutput;

class CreateDatabaseBackup {

    public function __construct()
    {
    }

    public function createBackup(): bool
    {
        $filename = $this->makeFileName();
        $storagePath = $this->getStoragePath();

        $DB_HOST = Config::get('database.connections.mysql.host');
        $DB_DATABASE = Config::get('database.connections.mysql.database');
        $DB_PASSWORD = Config::get('database.connections.mysql.password');
        $DB_USERNAME = Config::get('database.connections.mysql.username');

        //mysqldump command with account credentials from .env file. storage_path() adds default local storage path
        //Deployment in Laerdal Test Center cannot use --column-statistics=0. MUST BE REMOVED BEFORE DEPLOY TO PROD SERVER.
        //$command = "mysqldump --column-statistics=0 --user=" . $DB_USERNAME ." --password=" . $DB_PASSWORD . " --host=" . $DB_HOST . " " . $DB_DATABASE . "  > " . $storagePath . "/" . $filename;
        // ---- NOTE!  The above information may no longer be accurate. Further testing is required. ----

        $command = "mysqldump --user=" . $DB_USERNAME ." --password=" . $DB_PASSWORD . " --host=" . $DB_HOST . " " . $DB_DATABASE . " > " . $storagePath . "\\" . $filename;

        /* NOTE: Old config from prod env.
        //mysqldump command with account credentials from .env file. storage_path() adds default local storage path
        //--column-statistics=0
        $command = "mysqldump --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  > " . $storagePath . "/" . $filename;
        $returnVar = NULL;
        $output  = NULL;
        exec($command, $output, $returnVar);
         */

        $returnVar = NULL;
        $output  = array();
        exec($command, $output, $returnVar);
        //if nothing (error) is returned
        if(!$returnVar){
            return true;
        }else{
            return false;
        }
    }

    private function makeFileName(): string
    {
        return "backup-" . Carbon\Carbon::now()->format('Y-m-d_H-i-s') . ".sql";
    }

    private function getStoragePath(): string
    {
        $storagePath = storage_path('Backups');

        if (!file_exists($storagePath)) {
            mkdir($storagePath);
        }
        return $storagePath;
    }



}
