<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    protected string $backupFolder;

    public function __construct()
    {
        $this->backupFolder = config('backup.backup.name', "Yanas Fashion");
    }

    public function index()
    {
        $disk = Storage::disk('local');
        $files = $disk->allFiles($this->backupFolder);

        // Also check root or alternative folder name if any
        if (empty($files)) {
            $files = $disk->allFiles("Yanas Fashion");
        }

        $backups = [];
        foreach ($files as $file) {
            if (str_ends_with($file, '.zip')) {
                $sizeInBytes = $disk->size($file);
                $lastModified = $disk->lastModified($file);

                $backups[] = [
                    'file_path' => $file,
                    'file_name' => basename($file),
                    'file_size' => $this->humanFileSize($sizeInBytes),
                    'raw_size' => $sizeInBytes,
                    'last_modified' => Carbon::createFromTimestamp($lastModified)->format('d M, Y h:i A'),
                    'age' => Carbon::createFromTimestamp($lastModified)->diffForHumans(),
                ];
            }
        }

        // Sort latest backups first
        usort($backups, function ($a, $b) {
            return strcmp($b['file_name'], $a['file_name']);
        });

        return view('admin.backups.index', compact('backups'));
    }

    public function createDb()
    {
        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            return redirect()->route('admin.backups.index')->with('success', 'ডাটাবেস ব্যাকআপ সফলভাবে সম্পন্ন হয়েছে!');
        } catch (\Exception $e) {
            return redirect()->route('admin.backups.index')->with('error', 'ব্যাকআপে সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    public function createFull()
    {
        try {
            Artisan::call('backup:run');
            return redirect()->route('admin.backups.index')->with('success', 'সম্পূর্ণ স্টোর ব্যাকআপ (ডাটাবেস + মিডিয়া) সফলভাবে তৈরি হয়েছে!');
        } catch (\Exception $e) {
            return redirect()->route('admin.backups.index')->with('error', 'ব্যাকআপে সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    public function download(Request $request)
    {
        $fileName = $request->get('file');
        $disk = Storage::disk('local');

        // Locate file
        $filePath = $this->backupFolder . '/' . $fileName;
        if (!$disk->exists($filePath)) {
            $filePath = "Yanas Fashion/" . $fileName;
        }

        if ($disk->exists($filePath)) {
            return $disk->download($filePath, $fileName);
        }

        return redirect()->route('admin.backups.index')->with('error', 'ব্যাকআপ ফাইলটি পাওয়া যায়নি।');
    }

    public function destroy(Request $request)
    {
        $fileName = $request->get('file');
        $disk = Storage::disk('local');

        $filePath = $this->backupFolder . '/' . $fileName;
        if (!$disk->exists($filePath)) {
            $filePath = "Yanas Fashion/" . $fileName;
        }

        if ($disk->exists($filePath)) {
            $disk->delete($filePath);
            return redirect()->route('admin.backups.index')->with('success', "ব্যাকআপ ফাইল '{$fileName}' মুছে ফেলা হয়েছে!");
        }

        return redirect()->route('admin.backups.index')->with('error', 'ফাইলটি পাওয়া যায়নি।');
    }

    protected function humanFileSize($bytes, $decimals = 2): string
    {
        $size = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    }
}

