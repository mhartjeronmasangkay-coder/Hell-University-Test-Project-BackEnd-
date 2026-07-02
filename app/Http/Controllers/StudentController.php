<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'           => 'required|string',
            'email'          => 'required|email|unique:users',
            'year_level'     => 'required|string',
            'department'     => 'required|string',
            'age'            => 'required|integer',
            'birthday'       => 'required|date',
            'contact_number' => 'required|string',
        ]);
        $user = User::create($request->all());
        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user
        ], 201);
    }
    public function index()
    {
        return response()->json(User::all());
    }
    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
    return response()->json([
        'message'=> 'Updated Successfully',
        'user'=> $user
        ],);
    }
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    return response()->json([
        'message'=> 'Student has been Kicked Out'
        ],);}

    public function export()
    {
        $students = User::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $headers = ['ID', 'Name', 'Email', 'Year Level', 'Department', 'Age', 'Birthday', 'Contact Number'];
        $sheet->fromArray($headers, null, 'A1');

        // Bold the header row
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        // Data rows
        $row = 2;
        foreach ($students as $student) {
            $sheet->fromArray([
                $student->id,
                $student->name,
                $student->email,
                $student->year_level,
                $student->department,
                $student->age,
                $student->birthday,
                $student->contact_number,
            ], null, 'A' . $row);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'students_' . now()->format('Y-m-d_His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
    public function importBatch(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('file')->store('imports');

        \App\Jobs\ProcessStudentImport::dispatch($path);

        return response()->json([
            'message' => 'Import queued. Students will be added shortly.',
        ], 202);
    }
}