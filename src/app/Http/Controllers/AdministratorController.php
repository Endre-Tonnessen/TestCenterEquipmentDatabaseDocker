<?php

namespace App\Http\Controllers;

use App\Helpers\CreateDatabaseBackup;
use App\Helpers\RestoreFromDatabaseBackup;
use App\Models\Borrow;
use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
//use PHPUnit\Util\Filesystem;
use Illuminate\Filesystem\Filesystem;



class AdministratorController extends Controller
{

    /**
     *  Returns administrator page
     */
    public function index() {
        $allBorrowed = Borrow::getAllBorrowedEquipment();
        $allDeleted = Equipment::getAllDeletedEquipment();
        $allCategories = Category::getAllCategories();
        return view('administrator-homepage')->with(compact('allBorrowed', 'allDeleted', 'allCategories'));
    }


    public function createDatabaseBackup() {
        return $this->createBackupFile();
    }

    private function createBackupFile() {
        $backup = new CreateDatabaseBackup();
        $backup = $backup->createBackup(); // Get return value if successfull or not

        if ($backup) {
            return Redirect::back()->with('modalResponse', [
                'icon' => 'success',
                'title' => "Created backup successfully!",
            ]);
        } else {
            return Redirect::back()->with('modalResponse', [
                'icon' => 'error',
                'title' => "Failed to create backup!",
            ]);
        }
    }

    /**
     * Sends a zip to user, containing the newest database backup and images corresponding to that database-backup.
     */
    public function downloadNewestBackup() {
        $files = array_filter(Storage::disk('databaseBackups')->allFiles(), function ($file) {
            if (str_ends_with($file, '.sql')) return $file;
        });

        if (empty($files)) return Redirect::back()->with('modalResponse', ['icon' => 'warning', 'title' => "Unable to find any backups",]);

        $newestBackupFileName = end($files);

        $storagePath = storage_path('BackupZip');
        if (!file_exists($storagePath)) {
            mkdir($storagePath);
        }

        //Get all Images and create a Zip
        $zip = new \ZipArchive();
        $fileName = "backup.zip";
        //Delete old zip
        File::delete($storagePath.'/'.$fileName);

        if ($zip->open($storagePath . '/' . $fileName, \ZipArchive::CREATE))
        {
            $zip->addEmptyDir('Images');
            $zip->addEmptyDir('SQL');

            //Add SQLDump backup
            $SQLBackup = storage_path() ."/Backups/".  $newestBackupFileName;
            $zip->addFile($SQLBackup, 'SQL/'.$newestBackupFileName);

            //Add all images
            $files = File::files(public_path('storage/uploadedEquipmentImages'));
            foreach ($files as $key => $value){
                $relativeName = basename($value);
                $zip->addFile($value, 'Images/'.$relativeName);
            }
            $zip->close();
        }

        return response()->download($storagePath.'/'.$fileName);
    }

    /**
     * Deletes entire database and replaces it with the given .sql file. //TODO: Clean this code
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function restoreFromBackup(Request $request): RedirectResponse
    {
        $request->validate([
            'user_input_backup_file' => "required"
        ]);

        try {
            $file = $request->file('user_input_backup_file');
            if ($file->getClientOriginalExtension() !== "zip") return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => "Only .zip files are allowed."]);

            $storagePath = storage_path('RestorationBackup'); // Where the restoration zip is stored
            if (!file_exists($storagePath)) {
                mkdir($storagePath);
            }
            //Delete old restoration files
            Storage::disk('restorationbackup')->deleteDirectory('SQL');
            Storage::disk('restorationbackup')->deleteDirectory('Images');

            //Store zip
            $fileName = "RestorationBackup.zip";
            Storage::disk('restorationbackup')->put($fileName, $file->get());

            // Open and Unzip file and get contents
            $zip = new \ZipArchive();
            if ($zip->open($storagePath . '/' . $fileName)) {
                // Unzip
                Storage::disk('restorationbackup')->makeDirectory("SQL");
                Storage::disk('restorationbackup')->makeDirectory("Images");
                $zip->extractTo($storagePath, array('Images', 'SQL'));

                $all_files_in_directory = Storage::disk('restorationbackup')->allFiles();
                foreach ($all_files_in_directory as $f) {
                    if (substr($f, 0,3) == "SQL") {
                        Storage::disk('restorationbackup')->move($f, substr($f, 0,3));
                        Storage::disk('restorationbackup')->move($f, "SQL/".substr($f, 0,3));
                    }
                    if (substr($f, 0,6) == "Images") {
                        Storage::disk('restorationbackup')->move($f, "Images/".$f);
                    }
                }


                // Get SQL file
                $array = Storage::disk('restorationbackup')->allFiles('SQL');
                dd($array);
                $SQL_file = end($array);
                dd($SQL_file);
                //Replace database with uploaded SQL file
                DB::unprepared(Storage::disk('restorationbackup')->get($SQL_file)); //This is dangerous/not best practice. Have yet to find alternatives.

                // Replace images
                // Delete old images in public folder
                $old_images = Storage::disk('public')->allFiles('uploadedEquipmentImages');
                Storage::disk('public')->delete($old_images);
                //Insert new images
                File::copyDirectory(storage_path('RestorationBackup/Images'), storage_path('app/public/uploadedEquipmentImages'));
            } else {
                // Failed to open ZIP
                return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => "Error in processing backup file. Restoration failed."]);
            }
            $zip->close();
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => "Failed to restore database!"]);
        }

        return Redirect::back()->with('modalResponse', [
            'icon' => 'success',
            'title' => "Database restored successfully!"
        ]);
    }
}
