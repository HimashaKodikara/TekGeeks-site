<?php

namespace App\Http\Controllers\Backend\InstitutePortal;

use App\Http\Controllers\Controller;
use App\Models\DeclarantRegistration;
use App\Models\InstituteNicUpload;
use App\Models\InstituteNicUploadDetail;
use App\Models\MonetaryInstituteDetail;
use App\Models\PublicAuthority;
use App\Models\StatusOfDeclaration;
use App\Services\SupportApiService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;

class InstitutePortalController extends Controller
{
    protected SupportApiService $apiService;

    public function __construct(SupportApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:institute-dashboard', only: ['index']),
        ];
    }

    private function getExistingDataFromApi($currentTab)
    {
        $user = Auth::user();
        $publicAuthority = PublicAuthority::find($user->public_authority_id);
        
        $userLevel = (int)($publicAuthority->level_id ?? 1);

        $token = session('api_token') ?? '';

        $queryParams = [
            'levelId'     => (int)$currentTab,
            'instituteId' => $user->public_authority_id,
            'userLevel'   => $userLevel
        ];

        $response = $this->apiService->withToken($token)->get('institute/portal-data', $queryParams);

        return $response['data'] ?? [];
    }

    public function indexs(Request $request, $id)
    {
        $instituteId = decrypt($id);
        $isAdmin     = $request->query('admin') === '1';

        $monetaryInstitutes = MonetaryInstituteDetail::where('public_authority_id', $instituteId)->get();

        $declarationYears = DB::table('status_of_declarations as sod')
            ->join('declarant_registrations as dr', 'dr.id', '=', 'sod.declarant_registration_id')
            ->where('dr.institute_id', $instituteId)
            ->where('sod.is_delete', 0)
            ->whereNotNull('sod.declaration_year')
            ->distinct()
            ->orderBy('sod.declaration_year', 'desc')
            ->pluck('sod.declaration_year');

        if ($monetaryInstitutes->isEmpty()) {
            return view('backend.institute-portal.designation-dashboard', [
                'groupedData'      => collect(),
                'id'               => $id,
                'isAdmin'          => $isAdmin,
                'declarationYears' => $declarationYears,
                'routePrefix'      => 'institute',
            ]);
        }

        $designationIds       = $monetaryInstitutes->pluck('designation_id')->unique();

        $declarantRegistrationIds = DB::table('status_of_declarations')
            ->where('institution_id', $instituteId)
            ->whereIn('designation_id', $designationIds)
            ->where('is_delete', 0)
            ->distinct()
            ->pluck('declarant_registration_id');

        if ($declarantRegistrationIds->isEmpty()) {
            return view('backend.institute-portal.designation-dashboard', [
                'groupedData'      => collect(),
                'id'               => $id,
                'isAdmin'          => $isAdmin,
                'declarationYears' => $declarationYears,
                'routePrefix'      => 'institute',
            ]);
        }

        $registrations = DeclarantRegistration::with([
                                'designation',
                                'declarationStatuses' => function ($query) use ($instituteId) {
                                    $query->where('institution_id', $instituteId)->where('is_delete', 0);
                                },
                                'declarationStatuses.declarationType',
                                'personalInfo',
                            ])
                            ->whereIn('id', $declarantRegistrationIds)
                            ->where('status', 'V')
                            ->get();

        $sodDesignationIds = $registrations->flatMap->declarationStatuses->pluck('designation_id')->unique()->filter();

        $designationNames = DB::table('designations')
            ->whereIn('id', $sodDesignationIds)
            ->pluck('designation_name_en', 'id');

        $groupedData = $registrations->groupBy(function ($item) use ($designationNames) {
            $designationId = $item->declarationStatuses->first()?->designation_id;
            return $designationNames[$designationId] ?? $item->designation->designation_name_en ?? 'Unassigned Designation';
        });

        $routePrefix = 'institute';
        return view('backend.institute-portal.designation-dashboard', compact('groupedData', 'id', 'isAdmin', 'declarationYears', 'routePrefix'));
    }

    public function downloadSampleExcel()
    {
        $pathToExcel = storage_path('app/public/sample_nic_list.xlsx');

        SimpleExcelWriter::create($pathToExcel)
            ->addRow([
                'National Identity Card No' => '971652899V',
                'Name in Full'              => 'John Doe',
                'Designation'               => 'Director',
                'Email Address'             => 'john.doe@example.com',
                'Institution Name'          => 'Sample Institution',
            ])
            ->addRow([
                'National Identity Card No' => '200187654321',
                'Name in Full'              => 'Jane Smith',
                'Designation'               => 'Manager',
                'Email Address'             => 'jane.smith@example.com',
                'Institution Name'          => 'Sample Institution',
            ]);

        return response()->download($pathToExcel)->deleteFileAfterSend(true);
    }

    public function checkNicCompliance(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $file = $request->file('excel_file');
            $extension = $file->getClientOriginalExtension();

            $reader = SimpleExcelReader::create($file->getRealPath(), $extension);

            $uploadedNics = $reader->getRows()
                ->map(function (array $row) {
                    
                    $normalizedRow = array_change_key_case($row, CASE_LOWER);
                    return isset($normalizedRow['nic']) ? trim($normalizedRow['nic']) : null;
                })
                ->filter()
                ->unique()
                ->toArray();

            if (empty($uploadedNics)) {
                return back()->withErrors(['excel_file' => 'No valid NICs found in the "nic" column.']);
            }

            $user = Auth::user();
            $publicAuthority = PublicAuthority::find($user->public_authority_id);
            $userLevel = $publicAuthority->level_id;
            $currentTab = $request->get('level', $userLevel);

            $existingData = $this->getExistingDataFromApi($currentTab);
            $existingNics = collect($existingData)->pluck('nic')->toArray();

            $missing = array_diff($uploadedNics, $existingNics);

            $totalUploaded = count($uploadedNics);
            $foundCount = $totalUploaded - count($missing);

            return view('backend.nic-view.nic-report', [
                'missing'       => $missing,
                'totalUploaded' => $totalUploaded,
                'foundCount'    => $foundCount,
                'currentLevel'  => $currentTab
            ]);

        } catch (\Exception $e) {
            dd($e->getMessage());
            Log::error("Excel Read Error: " . $e->getMessage());
            return back()->withErrors(['excel_file' => 'Error reading file: ' . $e->getMessage()]);
        }
    }

    public function checkDesignationNics(Request $request, $id)
    {
        $request->validate([
            'excel_file'       => 'required|mimes:xlsx,xls,csv|max:5120',
            'declaration_year' => 'required|string',
        ]);

        $instituteId  = decrypt($id);
        $selectedYear = $request->input('declaration_year');

        try {
            $file      = $request->file('excel_file');
            $extension = $file->getClientOriginalExtension();
            $reader    = SimpleExcelReader::create($file->getRealPath(), $extension);

            $uploadedRows = $reader->getRows()
                ->map(function (array $row) {
                    $n           = array_change_key_case($row, CASE_LOWER);
                    $nic         = $n['national identity card no'] ?? $n['nic'] ?? null;
                    $name        = $n['name in full'] ?? $n['full_name'] ?? $n['name'] ?? null;
                    $email       = $n['email address'] ?? $n['email'] ?? null;
                    $designation = $n['designation'] ?? null;
                    $institution = $n['institution name'] ?? $n['institution_name'] ?? null;
                    return [
                        'nic'              => $nic         !== null ? trim($nic)         : null,
                        'name'             => $name        !== null ? trim($name)        : null,
                        'email'            => $email       !== null ? trim($email)       : null,
                        'designation'      => $designation !== null ? trim($designation) : null,
                        'institution_name' => $institution !== null ? trim($institution) : null,
                    ];
                })
                ->filter(fn($row) => !empty($row['nic']))
                ->unique('nic')
                ->values()
                ->toArray();

            if (empty($uploadedRows)) {
                return back()->withErrors(['excel_file' => 'No valid NICs found in the "nic" column.']);
            }

            $nics = array_column($uploadedRows, 'nic');

            $query = DeclarantRegistration::with(['designation'])
                ->where('institute_id', $instituteId)
                ->where('status', 'V')
                ->whereIn('nic', $nics);

            if ($selectedYear) {
                $query->whereExists(function ($sub) use ($selectedYear) {
                    $sub->select(DB::raw(1))
                        ->from('status_of_declarations')
                        ->whereColumn('declarant_registration_id', 'declarant_registrations.id')
                        ->where('declaration_year', $selectedYear)
                        ->where('is_delete', 0);
                });
            }

            $registrations = $query
                ->get(['id', 'nic', 'surname', 'other_names', 'email', 'designation_id'])
                ->keyBy('nic');

            $rows = [];
            foreach ($uploadedRows as $index => $row) {
                if ($registrations->has($row['nic'])) {
                    $reg        = $registrations->get($row['nic']);
                    $emailMatch = null;
                    if (!empty($row['email']) && !empty($reg->email)) {
                        $uploadedEmail = strtolower(trim($row['email']));
                        $systemEmail   = strtolower(trim($reg->email));
                        $emailMatch    = stripos($systemEmail, $uploadedEmail) !== false
                                      || stripos($uploadedEmail, $systemEmail) !== false;
                    }
                    $rows[] = [
                        'index'                    => $index + 1,
                        'nic'                      => $row['nic'],
                        'uploaded_name'            => $row['name'],
                        'uploaded_email'           => $row['email'],
                        'uploaded_designation'     => $row['designation'],
                        'uploaded_institution_name'=> $row['institution_name'],
                        'system_name'              => trim($reg->surname . ' ' . $reg->other_names),
                        'email'                    => $reg->email ?? 'N/A',
                        'designation'              => $reg->designation->designation_name_en ?? 'N/A',
                        'found'                    => true,
                        'email_match'              => $emailMatch,
                    ];
                } else {
                    $rows[] = [
                        'index'                    => $index + 1,
                        'nic'                      => $row['nic'],
                        'uploaded_name'            => $row['name'],
                        'uploaded_email'           => $row['email'],
                        'uploaded_designation'     => $row['designation'],
                        'uploaded_institution_name'=> $row['institution_name'],
                        'system_name'              => null,
                        'email'                    => null,
                        'designation'              => null,
                        'found'                    => false,
                        'email_match'              => null,
                    ];
                }
            }

            $foundCount    = count(array_filter($rows, fn($r) => $r['found']));
            $notFoundCount = count($rows) - $foundCount;

            DB::statement("SELECT setval('institute_nic_uploads_id_seq', (SELECT COALESCE(MAX(id), 1) FROM institute_nic_uploads))");

            $upload = InstituteNicUpload::create([
                'institute_id'     => $instituteId,
                'uploaded_by'      => Auth::id(),
                'declaration_year' => $selectedYear,
                'total_count'      => count($rows),
                'found_count'      => $foundCount,
                'not_found_count'  => $notFoundCount,
            ]);

            $detailRows = array_map(fn($r) => [
                'institute_nic_upload_id'  => $upload->id,
                'nic'                      => $r['nic'],
                'uploaded_name'            => $r['uploaded_name'],
                'uploaded_email'           => $r['uploaded_email'],
                'uploaded_designation'     => $r['uploaded_designation'],
                'uploaded_institution_name'=> $r['uploaded_institution_name'],
                'system_name'              => $r['system_name'],
                'email'                    => $r['email'],
                'designation'              => $r['designation'],
                'is_found'                 => $r['found'],
                'name_match'               => $r['found'] && !empty($r['uploaded_name'])
                    ? (stripos($r['system_name'] ?? '', $r['uploaded_name']) !== false
                       || stripos($r['uploaded_name'], $r['system_name'] ?? '') !== false)
                    : null,
                'email_match'              => $r['email_match'],
                'created_at'               => now(),
                'updated_at'               => now(),
            ], $rows);

            InstituteNicUploadDetail::insert($detailRows);

            session()->put('nic_check_results', [
                'rows'             => $rows,
                'total'            => count($rows),
                'found_count'      => $foundCount,
                'not_found_count'  => $notFoundCount,
                'declaration_year' => $selectedYear,
            ]);

            return back();

        } catch (\Exception $e) {
            Log::error('NIC Check Error: ' . $e->getMessage());
            return back()->withErrors(['excel_file' => 'Error reading file: ' . $e->getMessage()]);
        }
    }

    public function downloadNicReport(Request $request)
    {
        $results = session('nic_check_results');

        if (empty($results['rows'])) {
            return back()->withErrors(['excel_file' => 'No upload results found. Please upload a file first.']);
        }

        $type     = $request->query('type', 'all'); // all | found | not_found
        $found    = array_values(array_filter($results['rows'], fn($r) => $r['found']));
        $notFound = array_values(array_filter($results['rows'], fn($r) => !$r['found']));

        $spreadsheet = new Spreadsheet();

        if ($type === 'not_found') {
            $sheet   = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Not Found Users');
            $headers = ['#', 'NIC', 'Name (from file)', 'Designation (from file)', 'Institution (from file)', 'Email (from file)'];
            $this->writeSheetHeaders($sheet, $headers, 'DC3545');

            $row = 2;
            foreach ($notFound as $i => $r) {
                $sheet->fromArray([
                    $i + 1,
                    $r['nic'],
                    $r['uploaded_name'] ?? '',
                    $r['uploaded_designation'] ?? '',
                    $r['uploaded_institution_name'] ?? '',
                    $r['uploaded_email'] ?? '',
                ], null, "A{$row}");
                $row++;
            }
            $this->autoSizeColumns($sheet, count($headers));
            $filename = 'not_found_users_' . now()->format('Ymd_His') . '.xlsx';

        } elseif ($type === 'found') {
            $sheet   = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Found Users');
            $headers = ['#', 'NIC', 'Name (from file)', 'System Name', 'Email (system)', 'Designation (system)', 'Name Match', 'Email Match'];
            $this->writeSheetHeaders($sheet, $headers, '198754');

            $row = 2;
            foreach ($found as $i => $r) {
                $nameMatch = !empty($r['uploaded_name'])
                    && (stripos($r['system_name'] ?? '', $r['uploaded_name']) !== false
                        || stripos($r['uploaded_name'], $r['system_name'] ?? '') !== false);
                $sheet->fromArray([
                    $i + 1, $r['nic'], $r['uploaded_name'] ?? '',
                    $r['system_name'] ?? '', $r['email'] ?? '', $r['designation'] ?? '',
                    empty($r['uploaded_name']) ? 'No name provided' : ($nameMatch ? 'Match' : 'Mismatch'),
                    isset($r['email_match']) ? ($r['email_match'] ? 'Match' : 'Mismatch') : 'No email',
                ], null, "A{$row}");
                if (!empty($r['uploaded_name']) && !$nameMatch) {
                    $sheet->getStyle("A{$row}:H{$row}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FFF3CD');
                }
                $row++;
            }
            $this->autoSizeColumns($sheet, count($headers));
            $filename = 'found_users_' . now()->format('Ymd_His') . '.xlsx';

        } else {
            $foundSheet   = $spreadsheet->getActiveSheet();
            $foundSheet->setTitle('Found Users');
            $foundHeaders = ['#', 'NIC', 'Name (from file)', 'System Name', 'Email (system)', 'Designation (system)', 'Name Match', 'Email Match'];
            $this->writeSheetHeaders($foundSheet, $foundHeaders, '198754');

            $row = 2;
            foreach ($found as $i => $r) {
                $nameMatch = !empty($r['uploaded_name'])
                    && (stripos($r['system_name'] ?? '', $r['uploaded_name']) !== false
                        || stripos($r['uploaded_name'], $r['system_name'] ?? '') !== false);
                $foundSheet->fromArray([
                    $i + 1, $r['nic'], $r['uploaded_name'] ?? '',
                    $r['system_name'] ?? '', $r['email'] ?? '', $r['designation'] ?? '',
                    empty($r['uploaded_name']) ? 'No name provided' : ($nameMatch ? 'Match' : 'Mismatch'),
                    isset($r['email_match']) ? ($r['email_match'] ? 'Match' : 'Mismatch') : 'No email',
                ], null, "A{$row}");
                if (!empty($r['uploaded_name']) && !$nameMatch) {
                    $foundSheet->getStyle("A{$row}:H{$row}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FFF3CD');
                }
                $row++;
            }
            $this->autoSizeColumns($foundSheet, count($foundHeaders));

            $notFoundSheet   = $spreadsheet->createSheet();
            $notFoundSheet->setTitle('Not Found Users');
            $notFoundHeaders = ['#', 'NIC', 'Name (from file)', 'Designation (from file)', 'Institution (from file)', 'Email (from file)'];
            $this->writeSheetHeaders($notFoundSheet, $notFoundHeaders, 'DC3545');

            $row = 2;
            foreach ($notFound as $i => $r) {
                $notFoundSheet->fromArray([
                    $i + 1,
                    $r['nic'],
                    $r['uploaded_name'] ?? '',
                    $r['uploaded_designation'] ?? '',
                    $r['uploaded_institution_name'] ?? '',
                    $r['uploaded_email'] ?? '',
                ], null, "A{$row}");
                $row++;
            }
            $this->autoSizeColumns($notFoundSheet, count($notFoundHeaders));

            $summarySheet = $spreadsheet->createSheet();
            $summarySheet->setTitle('Summary');
            $summarySheet->fromArray([['Metric', 'Count']], null, 'A1');
            $summarySheet->fromArray([
                ['Total Uploaded',  $results['total']],
                ['Found in System', $results['found_count']],
                ['Not Found',       $results['not_found_count']],
                ['Generated At',    now()->format('Y-m-d H:i:s')],
            ], null, 'A2');
            $summarySheet->getStyle('A1:B1')->getFont()->setBold(true);
            $this->autoSizeColumns($summarySheet, 2);

            $filename = 'nic_check_report_' . now()->format('Ymd_His') . '.xlsx';
        }

        $spreadsheet->setActiveSheetIndex(0);
        $tempPath = tempnam(sys_get_temp_dir(), 'nic_report_');

        (new XlsxWriter($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function writeSheetHeaders($sheet, array $headers, string $bgColorRgb): void
    {
        $sheet->fromArray([$headers], null, 'A1');

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $range   = "A1:{$lastCol}1";

        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColorRgb]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }

    private function autoSizeColumns($sheet, int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }
    }

    public function reportDeclaration(Request $request)
    {
        $request->validate([
            'declaration_id' => 'required',
            'comments' => 'nullable|string'
        ]);

        $statusOfDeclaration = StatusOfDeclaration::find($request->status_id);
        $statusOfDeclaration->report_status = 'R';
        $statusOfDeclaration->comments = $request->comments;
        $statusOfDeclaration->update();

        $token = session('api_token') ?? '';

        $payload = [
            'declaration_id' => $request->declaration_id,
            'reason'         => $request->reason,
            'comments'       => $request->comments,
            'reported_by'   => Auth::id()
        ];

        $shouldKeepInput = false;
        $flashType = 'error';
        $flashMessage = 'API Error: Failed to submit report.';

        try {
            $response = $this->apiService->withToken($token)
                ->post('institute/report-declaration', $payload);

            if (is_array($response) && !isset($response['validation_errors'])) {
                $flashType = 'success';
                $flashMessage = 'Report submitted successfully.';
            } elseif (is_array($response) && isset($response['validation_errors'])) {
                $message = $response['validation_errors']['message'] ?? 'Failed to submit report.';
                $shouldKeepInput = true;
                $flashMessage = 'API Error: ' . $message;
            } else {
                $shouldKeepInput = true;
            }
        } catch (\Exception $e) {
            $shouldKeepInput = true;
            $flashMessage = 'System Error: Could not connect to the reporting service.';
        }

        $redirect = redirect()->back();
        if ($shouldKeepInput) {
            $redirect = $redirect->withInput();
        }

        return $redirect->with($flashType, $flashMessage);
    }
}
