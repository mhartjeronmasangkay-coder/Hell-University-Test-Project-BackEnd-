<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ProcessStudentImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle(): void
    {
        $fullPath = Storage::path($this->filePath);
        $handle = fopen($fullPath, 'r');

        $header = fgetcsv($handle); // first row = column names

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            $validator = Validator::make($data, [
                'name'           => 'required|string',
                'email'          => 'required|email|unique:users',
                'year_level'     => 'required|string',
                'department'     => 'required|string',
                'age'            => 'required|integer',
                'birthday'       => 'required|date',
                'contact_number' => 'required|string',
            ]);

            if ($validator->fails()) {
              Log::warning('CSV import row failed', [
                    'row' => $data,
                    'errors' => $validator->errors()->all(),
                ]);
                continue;
            }

            User::create($data);
        }

        fclose($handle);

        // Clean up the uploaded file once processing is done
        Storage::delete($this->filePath);
    }
}