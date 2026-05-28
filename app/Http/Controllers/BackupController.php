<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    protected string $disk = 'local';

    public function index()
    {
        return view('pages.sa.backups.index', [
            'summary' => $this->summary(),
            'backups' => $this->backups(),
        ]);
    }

    public function list()
    {
        return response()->json([
            'summary' => $this->summary(),
            'backups' => $this->backups(),
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:full,db,files']
        ]);

        try {

            $type = $request->type;

            if ($type === 'db') {

                Artisan::call('backup:run', [
                    '--only-db' => true,
                ]);

            } elseif ($type === 'files') {

                Artisan::call('backup:run', [
                    '--only-files' => true,
                ]);

            } else {

                Artisan::call('backup:run');

            }

            sleep(1);

            clearstatcache();

            $latestFile = collect(
                File::allFiles(
                    storage_path('app')
                )
            )
                ->filter(function ($file) {

                    return strtolower(
                            $file->getExtension()
                        ) === 'zip';

                })
                ->sortByDesc(function ($file) {

                    return $file->getMTime();

                })
                ->first();

            if (!$latestFile) {
                throw new \Exception(
                    'Generated backup file not found.'
                );
            }

            $existing = Backup::where(
                'filepath',
                $latestFile->getPathname()
            )->exists();

            if (!$existing) {

                Backup::create([
                    'filename' => $latestFile->getFilename(),
                    'filepath' => $latestFile->getPathname(),
                    'backup_type' => $type,
                    'file_size' => $latestFile->getSize(),
                    'status' => 'completed',
                    'created_by' => auth()->id(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Backup generated successfully.',
                'summary' => $this->summary(),
                'backups' => $this->backups(),
            ]);

        } catch (\Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Backup generation failed.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : 'Unexpected server error.',
            ], 500);
        }
    }


    public function download(
        Request $request
    ): BinaryFileResponse
    {

        $id = decrypt($request->segment(4));

        $backup = Backup::findOrFail($id);

        abort_unless(
            File::exists($backup->filepath),
            404
        );

        return response()->download(
            $backup->filepath,
            $backup->filename
        );
    }

    public function destroy(Request $request)
    {
        try {

            $id = decrypt(
                $request->segment(3)
            );

            $backup = Backup::findOrFail($id);

            if (
                $backup->filepath &&
                File::exists($backup->filepath)
            ) {
                File::delete(
                    $backup->filepath
                );
            }

            $backup->delete();

            return response()->json([
                'success' => true,
                'message' => 'Backup deleted successfully.',
                'summary' => $this->summary(),
                'backups' => $this->backups(),
            ]);

        } catch (\Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Unable to delete backup.',
                'error' => config('app.debug')
                    ? $e->getMessage()
                    : 'Unexpected server error.',
            ], 500);
        }
    }

    protected function backups(): array
    {
        return Backup::query()
            ->latest()
            ->get()
            ->map(function ($backup) {
                $backupType = match ($backup->backup_type) {
                    'full' => 'Full System Backup',
                    'db' => 'Database Backup',
                    'files' => 'Files Backup',
                    default => 'Unknown Backup',
                };

                return [
                    'id' => encrypt($backup->id),
                    'name' => $backup->filename,
                    'size' => $this->formatBytes(
                        $backup->file_size
                    ),
                    'backup_type' => $backupType,
                    'type' => strtoupper(
                        $backup->backup_type
                    ),
                    'date' => Carbon::parse(
                        $backup->created_at
                    )->format('M d, Y h:i A'),
                    'status' => ucfirst(
                        $backup->status
                    ),
                ];

            })
            ->toArray();
    }

    protected function summary(): array
    {
        $totalSize = Backup::sum('file_size');

        $latestBackup = Backup::latest()->first();

        return [
            'total_backups' => Backup::count(),
            'storage_used' => $this->formatBytes($totalSize),
            'last_backup' => $latestBackup
                ? Carbon::parse(
                    $latestBackup->created_at
                )->format('M d, Y h:i A')
                : 'No backups',
        ];
    }

    protected function backupPath(): string
    {
        $path = storage_path(
            'app/Laravel'
        );

        if (!File::exists($path)) {
            File::makeDirectory(
                $path,
                0755,
                true
            );
        }

        return $path;
    }

    protected function formatBytes(
        $bytes,
        $precision = 2
    ): string {

        $units = [
            'B',
            'KB',
            'MB',
            'GB',
            'TB'
        ];

        $bytes = max($bytes, 0);

        $pow = floor(
            ($bytes ? log($bytes) : 0)
            / log(1024)
        );

        $pow = min(
            $pow,
            count($units) - 1
        );

        $bytes /= pow(1024, $pow);

        return round(
                $bytes,
                $precision
            ) . ' ' . $units[$pow];
    }
}
