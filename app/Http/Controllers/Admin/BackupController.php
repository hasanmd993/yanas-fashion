<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PDO;
use ZipArchive;

use Inertia\Inertia;

class BackupController extends Controller
{
    /**
     * Display a listing of existing backup archives.
     */
    public function index()
    {
        $backups = [];
        $seenFiles = [];

        // Search across all storage backup directories
        $patterns = [
            storage_path('app/private/Yanas Fashion/*.zip'),
            storage_path('app/private/*/*.zip'),
            storage_path('app/private/*.zip'),
            storage_path('app/*/*.zip'),
            storage_path('app/*.zip'),
            storage_path('app/private/*/*/*.zip'),
        ];

        $matchedFiles = [];
        foreach ($patterns as $pattern) {
            $globResults = glob($pattern);
            if (!empty($globResults)) {
                foreach ($globResults as $path) {
                    $matchedFiles[] = $path;
                }
            }
        }

        foreach ($matchedFiles as $filePath) {
            if (is_file($filePath)) {
                $fileName = basename($filePath);
                if (!isset($seenFiles[$fileName])) {
                    $seenFiles[$fileName] = true;
                    $sizeInBytes = filesize($filePath);
                    $lastModified = filemtime($filePath);

                    $backups[] = [
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                        'file_size' => $this->humanFileSize($sizeInBytes),
                        'raw_size' => $sizeInBytes,
                        'last_modified' => Carbon::createFromTimestamp($lastModified)->timezone(config('app.timezone', 'Asia/Dhaka'))->format('d M, Y h:i A'),
                        'age' => Carbon::createFromTimestamp($lastModified)->diffForHumans(),
                    ];
                }
            }
        }

        // Sort latest backups first (by modification time or filename)
        usort($backups, function ($a, $b) {
            return strcmp($b['file_name'], $a['file_name']);
        });

        return Inertia::render('Admin/Backups/Index', [
            'backups' => $backups
        ]);
    }

    /**
     * Create a Database-only backup (.zip containing SQL dump).
     */
    public function createDb()
    {
        try {
            set_time_limit(300);
            ini_set('memory_limit', '512M');

            $backupDir = $this->getBackupDirectory();
            $timestamp = now()->format('Y-m-d');
            $tempDir = storage_path('app/backup-temp');
            File::ensureDirectoryExists($tempDir);

            $tempSqlPath = $tempDir . '/db_dump_' . $timestamp . '.sql';
            $this->generateSqlDumpFile($tempSqlPath);

            $zipFileName = 'yanas-fashion-db-' . $timestamp . '.zip';
            $zipFilePath = $backupDir . DIRECTORY_SEPARATOR . $zipFileName;

            $zip = new ZipArchive();
            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception("Failed to create zip archive at {$zipFilePath}");
            }

            $zip->addFile($tempSqlPath, 'db_dump_' . $timestamp . '.sql');
            $zip->close();

            @unlink($tempSqlPath);

            $size = $this->humanFileSize(filesize($zipFilePath));
            return redirect()->route('admin.backups.index')->with('success', "Database backup created successfully! ({$zipFileName} - {$size})");
        } catch (\Exception $e) {
            return redirect()->route('admin.backups.index')->with('error', 'Database backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Create a Full backup (Database SQL + Uploaded Media Assets).
     */
    public function createFull()
    {
        try {
            set_time_limit(600);
            ini_set('memory_limit', '1024M');

            $backupDir = $this->getBackupDirectory();
            $timestamp = now()->format('Y-m-d-H-i-s');
            $tempDir = storage_path('app/backup-temp');
            File::ensureDirectoryExists($tempDir);

            $tempSqlPath = $tempDir . '/database_' . $timestamp . '.sql';
            $this->generateSqlDumpFile($tempSqlPath);

            $zipFileName = 'yanas-fashion-full-' . $timestamp . '.zip';
            $zipFilePath = $backupDir . DIRECTORY_SEPARATOR . $zipFileName;

            $zip = new ZipArchive();
            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception("Failed to create zip archive at {$zipFilePath}");
            }

            // 1. Add SQL Dump to Zip
            $zip->addFile($tempSqlPath, 'database/database_' . $timestamp . '.sql');

            // 2. Add storage/app/public media files (products, banners, etc.)
            $publicStoragePath = storage_path('app/public');
            if (is_dir($publicStoragePath)) {
                $this->addDirectoryToZip($publicStoragePath, $zip, 'storage');
            }

            // 3. Add public/uploads if exists
            $publicUploadsPath = public_path('uploads');
            if (is_dir($publicUploadsPath)) {
                $this->addDirectoryToZip($publicUploadsPath, $zip, 'uploads');
            }

            $zip->close();

            @unlink($tempSqlPath);

            $size = $this->humanFileSize(filesize($zipFilePath));
            return redirect()->route('admin.backups.index')->with('success', "Full store backup (database + media) created successfully! ({$zipFileName} - {$size})");
        } catch (\Exception $e) {
            return redirect()->route('admin.backups.index')->with('error', 'Full backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a specific backup archive.
     */
    public function download(Request $request)
    {
        $fileName = basename($request->get('file'));

        $patterns = [
            storage_path('app/private/Yanas Fashion/' . $fileName),
            storage_path('app/private/*/' . $fileName),
            storage_path('app/private/' . $fileName),
            storage_path('app/*/' . $fileName),
            storage_path('app/' . $fileName),
            storage_path('app/private/*/*/' . $fileName),
        ];

        foreach ($patterns as $pattern) {
            $files = glob($pattern);
            if (!empty($files) && is_file($files[0])) {
                return response()->download($files[0], $fileName);
            }
        }

        return redirect()->route('admin.backups.index')->with('error', 'Backup archive file not found.');
    }

    /**
     * Delete a specific backup archive.
     */
    public function destroy(Request $request)
    {
        $fileName = basename($request->get('file'));

        $patterns = [
            storage_path('app/private/Yanas Fashion/' . $fileName),
            storage_path('app/private/*/' . $fileName),
            storage_path('app/private/' . $fileName),
            storage_path('app/*/' . $fileName),
            storage_path('app/' . $fileName),
            storage_path('app/private/*/*/' . $fileName),
        ];

        foreach ($patterns as $pattern) {
            $files = glob($pattern);
            if (!empty($files) && is_file($files[0])) {
                @unlink($files[0]);
                return redirect()->route('admin.backups.index')->with('success', "Backup file '{$fileName}' has been deleted!");
            }
        }

        return redirect()->route('admin.backups.index')->with('error', 'File not found or already deleted.');
    }

    /**
     * Generate complete MySQL dump using active PDO connection.
     */
    protected function generateSqlDumpFile(string $targetFilePath): void
    {
        $pdo = DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();

        $handle = fopen($targetFilePath, 'w');
        if (!$handle) {
            throw new \Exception("Could not open file {$targetFilePath} for writing SQL dump.");
        }

        fwrite($handle, "-- --------------------------------------------------------\n");
        fwrite($handle, "-- Yanas Fashion Database Backup\n");
        fwrite($handle, "-- Database: `{$dbName}`\n");
        fwrite($handle, "-- Generated At: " . now()->toDateTimeString() . " (" . config('app.timezone') . ")\n");
        fwrite($handle, "-- --------------------------------------------------------\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
        fwrite($handle, "SET AUTOCOMMIT = 0;\n");
        fwrite($handle, "START TRANSACTION;\n");
        fwrite($handle, "SET time_zone = \"+00:00\";\n\n");

        $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_NUM);

        foreach ($tables as $tableRow) {
            $table = $tableRow[0];

            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "-- Table structure for `{$table}`\n");
            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");

            $createTableStmt = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_NUM);
            if ($createTableStmt && isset($createTableStmt[1])) {
                fwrite($handle, $createTableStmt[1] . ";\n\n");
            }

            // Dump Table Data
            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "-- Dumping data for `{$table}`\n");
            fwrite($handle, "-- --------------------------------------------------------\n");

            $rowsStmt = $pdo->query("SELECT * FROM `{$table}`");
            $batch = [];
            $columns = [];

            while ($row = $rowsStmt->fetch(PDO::FETCH_ASSOC)) {
                if (empty($columns)) {
                    $columns = array_map(function ($col) {
                        return "`{$col}`";
                    }, array_keys($row));
                }

                $values = [];
                foreach ($row as $val) {
                    if ($val === null) {
                        $values[] = 'NULL';
                    } elseif (is_numeric($val) && !is_string($val)) {
                        $values[] = $val;
                    } else {
                        $values[] = $pdo->quote($val);
                    }
                }

                $batch[] = "(" . implode(", ", $values) . ")";

                if (count($batch) >= 100) {
                    fwrite($handle, "INSERT INTO `{$table}` (" . implode(", ", $columns) . ") VALUES\n" . implode(",\n", $batch) . ";\n");
                    $batch = [];
                }
            }

            if (count($batch) > 0 && !empty($columns)) {
                fwrite($handle, "INSERT INTO `{$table}` (" . implode(", ", $columns) . ") VALUES\n" . implode(",\n", $batch) . ";\n");
            }

            fwrite($handle, "\n");
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fwrite($handle, "COMMIT;\n");

        fclose($handle);
    }

    /**
     * Recursively add directory contents to ZipArchive.
     */
    protected function addDirectoryToZip(string $folderPath, ZipArchive $zip, string $zipPrefix = ''): void
    {
        if (!is_dir($folderPath)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folderPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($folderPath) + 1);
                $zipEntryName = $zipPrefix !== '' ? $zipPrefix . '/' . str_replace('\\', '/', $relativePath) : str_replace('\\', '/', $relativePath);
                $zip->addFile($filePath, $zipEntryName);
            }
        }
    }

    /**
     * Get backup storage directory.
     */
    protected function getBackupDirectory(): string
    {
        $dir = storage_path('app/private/Yanas Fashion');
        File::ensureDirectoryExists($dir);
        return $dir;
    }

    /**
     * Format bytes into human-readable size.
     */
    protected function humanFileSize($bytes, $decimals = 2): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        $size = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    }
}
