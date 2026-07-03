<?php

namespace App\GraphQL\Mutations;

use Illuminate\Http\UploadedFile;

class ImportStudentsCsv
{
    public function __invoke($_, array $args): array
    {
        /** @var UploadedFile $file */
        $file = $args['file'];

        $path = $file->store('imports');

        \App\Jobs\ProcessStudentImport::dispatch($path);

        return [
            'message' => 'Import queued. Students will be added shortly.',
        ];
    }
}