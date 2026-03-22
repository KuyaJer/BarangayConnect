<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    private const DISK      = 'local';
    private const DIRECTORY = 'backups';

    public function index()
    {
        $files = collect(Storage::disk(self::DISK)->files(self::DIRECTORY))
            ->map(function ($path) {
                return [
                    'path'     => $path,
                    'filename' => basename($path),
                    'size'     => $this->formatBytes(Storage::disk(self::DISK)->size($path)),
                    'modified' => \Carbon\Carbon::createFromTimestamp(
                        Storage::disk(self::DISK)->lastModified($path)
                    ),
                ];
            })
            ->sortByDesc('modified')
            ->values();

        return view('admin.security.backup', compact('files'));
    }

    public function create()
    {
        $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '_' . Str::random(6) . '.sql';
        $path     = self::DIRECTORY . '/' . $filename;

        $sql = $this->dumpDatabase();

        Storage::disk(self::DISK)->put($path, $sql);

        ActivityLogger::log(
            'created',
            "Database backup created: {$filename}",
            'Backup'
        );

        return back()->with('success', "Backup '{$filename}' created successfully.");
    }

    public function download(string $filename)
    {
        $path = self::DIRECTORY . '/' . basename($filename);   // basename prevents path traversal

        abort_unless(Storage::disk(self::DISK)->exists($path), 404);

        ActivityLogger::log(
            'exported',
            "Database backup downloaded: {$filename}",
            'Backup'
        );

        return Storage::disk(self::DISK)->download($path, $filename);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'sql_file' => ['required', 'file', 'mimes:sql,txt', 'max:51200'],   // 50 MB
        ]);

        $sql = file_get_contents($request->file('sql_file')->getRealPath());

        // Execute statement by statement
        DB::unprepared($sql);

        ActivityLogger::log(
            'updated',
            'Database restored from uploaded SQL file: ' . $request->file('sql_file')->getClientOriginalName(),
            'Backup'
        );

        return back()->with('success', 'Database restored successfully.');
    }

    public function destroy(string $filename)
    {
        $path = self::DIRECTORY . '/' . basename($filename);

        abort_unless(Storage::disk(self::DISK)->exists($path), 404);

        Storage::disk(self::DISK)->delete($path);

        ActivityLogger::log(
            'deleted',
            "Database backup deleted: {$filename}",
            'Backup'
        );

        return back()->with('success', "Backup '{$filename}' deleted.");
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function dumpDatabase(): string
    {
        $tables = DB::select('SHOW TABLES');
        $dbKey  = 'Tables_in_' . config('database.connections.mysql.database');
        $output = [];

        $output[] = '-- BarangayConnect Database Backup';
        $output[] = '-- Generated: ' . now()->toDateTimeString();
        $output[] = '-- Laravel ' . app()->version();
        $output[] = '';
        $output[] = 'SET FOREIGN_KEY_CHECKS=0;';
        $output[] = '';

        foreach ($tables as $tableObj) {
            $table = $tableObj->$dbKey;

            // CREATE TABLE
            $create = DB::select("SHOW CREATE TABLE `{$table}`")[0];
            $output[] = "-- Table: {$table}";
            $output[] = "DROP TABLE IF EXISTS `{$table}`;";
            $output[] = $create->{'Create Table'} . ';';
            $output[] = '';

            // INSERT rows
            $rows = DB::table($table)->get();
            if ($rows->isNotEmpty()) {
                $cols = '`' . implode('`, `', array_keys((array) $rows->first())) . '`';
                $output[] = "INSERT INTO `{$table}` ({$cols}) VALUES";

                $inserts = $rows->map(function ($row) {
                    $values = array_map(function ($v) {
                        if ($v === null) return 'NULL';
                        return "'" . addslashes((string) $v) . "'";
                    }, (array) $row);
                    return '(' . implode(', ', $values) . ')';
                })->implode(",\n");

                $output[] = $inserts . ';';
                $output[] = '';
            }
        }

        $output[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return implode("\n", $output);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024)        return $bytes . ' B';
        if ($bytes < 1048576)     return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 2) . ' MB';
    }
}
