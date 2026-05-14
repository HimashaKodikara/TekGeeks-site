<?php

namespace App\Http\Controllers\Backend\InstitutePortal;

use App\Http\Controllers\Controller;
use App\Models\DeclarantRegistration;
use App\Models\InstituteNicUpload;
use App\Models\InstituteNicUploadDetail;
use App\Models\MonetaryInstituteDetail;
use App\Models\PublicAuthority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class AdminInstituteDashboardPortalController extends Controller
{
    public function index()
    {
        return view('backend.institute-portal.admin_institute_dashboard_portal.index');
    }

    public function designationDetails(Request $request, $id)
    {
        $instituteId = decrypt($id);
        $isAdmin     = true;

        $declarationYears = DB::table('status_of_declarations as sod')
            ->where('sod.institution_id', $instituteId)
            ->where('sod.is_delete', 0)
            ->whereNotNull('sod.declaration_year')
            ->distinct()
            ->orderBy('sod.declaration_year', 'desc')
            ->pluck('sod.declaration_year');

        $uploads = InstituteNicUpload::with(['uploader', 'foundDetails', 'notFoundDetails'])
            ->where('institute_id', $instituteId)
            ->latest()
            ->get();

        $declarantIds = DB::table('status_of_declarations')
            ->where('institution_id', $instituteId)
            ->where('is_delete', 0)
            ->distinct()
            ->pluck('declarant_registration_id');

        $registrations = DeclarantRegistration::with([
                            'declarationStatuses' => function ($query) use ($instituteId) {
                                $query->where('institution_id', $instituteId)->where('is_delete', 0);
                            },
                            'declarationStatuses.declarationType',
                            'declarationStatuses.designation',
                            'personalInfo',
                        ])
                        ->whereIn('id', $declarantIds)
                        ->get();

        $groupedData = $registrations->groupBy(function ($item) {
            return $item->declarationStatuses->first()?->designation?->designation_name_en ?? 'Unassigned Designation';
        });

        $routePrefix = 'admin-institute';

        return view('backend.institute-portal.designation-dashboard', compact(
            'groupedData', 'id', 'isAdmin', 'declarationYears', 'routePrefix', 'uploads'
        ));
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

    public function downloadNicReport(Request $request)
    {
        $results = session('nic_check_results');

        if (empty($results['rows'])) {
            return back()->withErrors(['excel_file' => 'No upload results found. Please upload a file first.']);
        }

        $type     = $request->query('type', 'all');
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

            $notFoundSheet = $spreadsheet->createSheet();
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

    public function downloadUploadReport(Request $request, $uploadId)
    {
        $upload = InstituteNicUpload::with(['foundDetails', 'notFoundDetails', 'details'])->findOrFail($uploadId);

        $type     = $request->query('type', 'all');
        $year     = $upload->declaration_year ?? 'N/A';

        $spreadsheet = new Spreadsheet();

        if ($type === 'not_found') {
            $sheet   = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Not Found Users');
            $headers = ['#', 'NIC', 'Name (from file)', 'Designation (from file)', 'Institution (from file)', 'Email (from file)'];
            $this->writeSheetHeaders($sheet, $headers, 'DC3545');

            $row = 2;
            foreach ($upload->notFoundDetails as $i => $r) {
                $sheet->fromArray([
                    $i + 1,
                    $r->nic,
                    $r->uploaded_name ?? '',
                    $r->uploaded_designation ?? '',
                    $r->uploaded_institution_name ?? '',
                    $r->uploaded_email ?? '',
                ], null, "A{$row}");
                $row++;
            }
            $this->autoSizeColumns($sheet, count($headers));
            $filename = 'not_found_users_year' . $year . '_' . now()->format('Ymd_His') . '.xlsx';

        } elseif ($type === 'found') {
            $sheet   = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Found Users');
            $headers = ['#', 'NIC', 'Name (from file)', 'System Name', 'Email (system)', 'Designation (system)', 'Name Match', 'Email Match'];
            $this->writeSheetHeaders($sheet, $headers, '198754');

            $row = 2;
            foreach ($upload->foundDetails as $i => $r) {
                $sheet->fromArray([
                    $i + 1, $r->nic, $r->uploaded_name ?? '',
                    $r->system_name ?? '', $r->email ?? '', $r->designation ?? '',
                    is_null($r->name_match)  ? 'No name provided' : ($r->name_match  ? 'Match' : 'Mismatch'),
                    is_null($r->email_match) ? 'No email'         : ($r->email_match ? 'Match' : 'Mismatch'),
                ], null, "A{$row}");
                if ($r->name_match === false) {
                    $sheet->getStyle("A{$row}:H{$row}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FFF3CD');
                }
                $row++;
            }
            $this->autoSizeColumns($sheet, count($headers));
            $filename = 'found_users_year' . $year . '_' . now()->format('Ymd_His') . '.xlsx';

        } else {
            $foundSheet   = $spreadsheet->getActiveSheet();
            $foundSheet->setTitle('Found Users');
            $foundHeaders = ['#', 'NIC', 'Name (from file)', 'System Name', 'Email (system)', 'Designation (system)', 'Name Match', 'Email Match'];
            $this->writeSheetHeaders($foundSheet, $foundHeaders, '198754');

            $row = 2;
            foreach ($upload->foundDetails as $i => $r) {
                $foundSheet->fromArray([
                    $i + 1, $r->nic, $r->uploaded_name ?? '',
                    $r->system_name ?? '', $r->email ?? '', $r->designation ?? '',
                    is_null($r->name_match)  ? 'No name provided' : ($r->name_match  ? 'Match' : 'Mismatch'),
                    is_null($r->email_match) ? 'No email'         : ($r->email_match ? 'Match' : 'Mismatch'),
                ], null, "A{$row}");
                if ($r->name_match === false) {
                    $foundSheet->getStyle("A{$row}:H{$row}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FFF3CD');
                }
                $row++;
            }
            $this->autoSizeColumns($foundSheet, count($foundHeaders));

            $notFoundSheet = $spreadsheet->createSheet();
            $notFoundSheet->setTitle('Not Found Users');
            $notFoundHeaders = ['#', 'NIC', 'Name (from file)', 'Designation (from file)', 'Institution (from file)', 'Email (from file)'];
            $this->writeSheetHeaders($notFoundSheet, $notFoundHeaders, 'DC3545');

            $row = 2;
            foreach ($upload->notFoundDetails as $i => $r) {
                $notFoundSheet->fromArray([
                    $i + 1, $r->nic, $r->uploaded_name ?? '',
                    $r->uploaded_designation ?? '', $r->uploaded_institution_name ?? '', $r->uploaded_email ?? '',
                ], null, "A{$row}");
                $row++;
            }
            $this->autoSizeColumns($notFoundSheet, count($notFoundHeaders));

            $summarySheet = $spreadsheet->createSheet();
            $summarySheet->setTitle('Summary');
            $summarySheet->fromArray([['Metric', 'Count']], null, 'A1');
            $summarySheet->fromArray([
                ['Total Uploaded',  $upload->total_count],
                ['Found in System', $upload->found_count],
                ['Not Found',       $upload->not_found_count],
                ['Declaration Year', $year],
                ['Generated At',    now()->format('Y-m-d H:i:s')],
            ], null, 'A2');
            $summarySheet->getStyle('A1:B1')->getFont()->setBold(true);
            $this->autoSizeColumns($summarySheet, 2);

            $filename = 'nic_check_report_year' . $year . '_' . now()->format('Ymd_His') . '.xlsx';
        }

        $spreadsheet->setActiveSheetIndex(0);
        $tempPath = tempnam(sys_get_temp_dir(), 'nic_report_');

        (new XlsxWriter($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function getAjaxInstituteManagement()
    {
        $statsSub = DB::table('declarant_registrations as dr')
            ->join('status_of_declarations as sod', function ($j) {
                $j->on('sod.declarant_registration_id', '=', 'dr.id')
                ->where('sod.institution_id', 1)
                  ->where('sod.is_delete', 0);
            })
            ->selectRaw("
                dr.designation_id,
                sod.institution_id,
                COUNT(DISTINCT CASE WHEN sod.declaration_type_id = 2     AND sod.status IN ('S','E') THEN dr.id END) as annual_started,
                COUNT(DISTINCT CASE WHEN sod.declaration_type_id = 2     AND sod.status IN ('C','R') THEN dr.id END) as annual_completed,
                COUNT(DISTINCT CASE WHEN sod.declaration_type_id != 2    AND sod.status IN ('S','E') THEN dr.id END) as other_started,
                COUNT(DISTINCT CASE WHEN sod.declaration_type_id != 2    AND sod.status IN ('C','R') THEN dr.id END) as other_completed
            ")
            ->groupBy('dr.designation_id', 'sod.institution_id');

        $query = DB::table('monetary_institute_details as mid')
            ->join('public_authorities as pa', fn($j) => $j->on('pa.id', '=', 'mid.public_authority_id')->where('pa.is_delete', 0))
            ->leftJoin('designation_classes as dc', 'dc.id', '=', 'mid.designation_class_id')
            ->leftJoin('monetary_institutes as mi', 'mi.id', '=', 'mid.monetary_institute_id')
            ->leftJoinSub($statsSub, 'stats', function ($j) {
                $j->on('stats.institution_id', '=', 'mid.public_authority_id')
                  ->on('stats.designation_id', '=', 'mid.designation_id');
            })
            ->selectRaw("
                mid.monetary_institute_id,
                mid.designation_class_id,
                COALESCE(mi.monetary_institute_name, 'N/A') as monetary_institute_name,
                COALESCE(dc.name_en, 'Unclassified') as designation_class_name,
                COALESCE(SUM(COALESCE(stats.annual_started,  0)), 0) as annual_started,
                COALESCE(SUM(COALESCE(stats.annual_completed,0)), 0) as annual_completed,
                COALESCE(SUM(COALESCE(stats.other_started,   0)), 0) as other_started,
                COALESCE(SUM(COALESCE(stats.other_completed, 0)), 0) as other_completed
            ")
            ->groupBy('mid.monetary_institute_id', 'mid.designation_class_id', 'mi.monetary_institute_name', 'dc.name_en');

        return DataTables::of($query)
            ->addIndexColumn()
            ->filterColumn('monetary_institute_name', fn($q, $k) => $q->where('mi.monetary_institute_name', 'ILIKE', "%{$k}%"))
            ->filterColumn('designation_class_name', fn($q, $k) => $q->where('dc.name_en', 'ILIKE', "%{$k}%"))
            ->addColumn('annual_declarations', fn($row) =>
                '<span class="badge px-2 py-1 m-1" style="background:#f6c23e;color:#333;">Started: ' . $row->annual_started . '</span>'
                . '<span class="badge px-2 py-1 m-1" style="background:#1cc88a;color:#fff;">Completed: ' . $row->annual_completed . '</span>'
            )
            ->addColumn('other_declarations', fn($row) =>
                '<span class="badge px-2 py-1 m-1" style="background:#f6c23e;color:#333;">Started: ' . $row->other_started . '</span>'
                . '<span class="badge px-2 py-1 m-1" style="background:#1cc88a;color:#fff;">Completed: ' . $row->other_completed . '</span>'
            )
            ->addColumn('action', function ($row) {
                $key   = encrypt($row->monetary_institute_id . '|' . ($row->designation_class_id ?? ''));
                $route = route('admin-institute.view-institute-management', $key);
                return '<a href="' . $route . '" class="btn btn-sm btn-outline-primary btn-icon rounded-circle waves-effect waves-themed" title="View Details">
                            <i class="fal fa-eye"></i>
                        </a>';
            })
            ->rawColumns(['annual_declarations', 'other_declarations', 'action'])
            ->toJson();
    }

    public function viewInstituteManagement($id)
    {
        [$monetaryInstituteId, $designationClassId] = explode('|', decrypt($id));
        $designationClassId = $designationClassId !== '' ? (int) $designationClassId : null;

        $monetaryInstitute = DB::table('monetary_institutes')->find($monetaryInstituteId);
        $designationClass  = $designationClassId ? DB::table('designation_classes')->find($designationClassId) : null;

        $declarations = DB::table('status_of_declarations as sod')
            ->join('declarant_registrations as dr', 'dr.id', '=', 'sod.declarant_registration_id')
            ->leftJoin('declaration_types as dt', 'dt.id', '=', 'sod.declaration_type_id')
            ->leftJoin('designations as dsg', 'dsg.id', '=', 'sod.designation_id')
            ->where('sod.institution_id', 1)
            ->where(function ($q) use ($designationClassId) {
                $designationClassId
                    ? $q->where('sod.designation_class_id', $designationClassId)
                    : $q->whereNull('sod.designation_class_id');
            })
            ->where('sod.is_delete', 0)
            ->whereNotNull('sod.declaration_type_id')
            ->select(
                'dsg.designation_name_en as designation_name',
                'dr.surname',
                'dr.other_names',
                'dr.nic',
                'dr.email',
                'dt.type_name_en as declaration_type',
                'sod.status',
                'sod.completed_date'
            )
            ->get();

        return view('backend.institute-portal.admin_institute_dashboard_portal.monetary-institute-view', compact(
            'monetaryInstitute', 'designationClass', 'declarations', 'id'
        ));
    }

    public function publicAuthorities()
    {
        return view('backend.institute-portal.admin_institute_dashboard_portal.public-authorities');
    }

    public function getAjaxPublicAuthorities()
    {
        $query = PublicAuthority::query()
            ->where('is_delete', 0)
            ->select(['id', 'name_en', 'name_si', 'name_ta', 'status']);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('status_badge', fn($row) =>
                $row->status === 'Y'
                    ? '<span class="badge bg-success rounded-pill px-3">Active</span>'
                    : '<span class="badge bg-danger rounded-pill px-3">Inactive</span>'
            )
            ->addColumn('action', function ($row) {
                $route = route('admin-institute.designation-details', encrypt($row->id));
                return '<a href="' . $route . '" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="fal fa-arrow-right me-1"></i> View
                        </a>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->toJson();
    }

    public function getAjaxDesignationsUser()
    {
        $uploadSub = DB::table('institute_nic_uploads')
            ->selectRaw('
                institute_id,
                COUNT(*)             as upload_count,
                SUM(found_count)     as total_found,
                SUM(not_found_count) as total_not_found,
                MAX(created_at)      as last_uploaded_at
            ')
            ->groupBy('institute_id');

        $inner = DB::table('public_authorities as pa')
            ->where('pa.is_delete', 0)
            ->leftJoin('status_of_declarations as sod', fn($j) => $j
                ->on('sod.institution_id', '=', 'pa.id')
                ->where('sod.is_delete', '=', 0)
            )
            ->leftJoinSub($uploadSub, 'uploads', fn($j) => $j->on('uploads.institute_id', '=', 'pa.id'))
            ->selectRaw("
                pa.id as public_authority_id,
                pa.name_en as institute_name,
                COUNT(DISTINCT sod.declarant_registration_id) as total_count,
                COUNT(CASE WHEN sod.declaration_type_id = 2 AND sod.status IN ('S', 'E') THEN 1 END) as annual_started_count,
                COUNT(CASE WHEN sod.declaration_type_id = 2 AND sod.status IN ('C', 'R') THEN 1 END) as annual_completed_count,
                COUNT(CASE WHEN sod.declaration_type_id != 2 AND sod.status IN ('S', 'E') THEN 1 END) as other_started_count,
                COUNT(CASE WHEN sod.declaration_type_id != 2 AND sod.status IN ('C', 'R') THEN 1 END) as other_completed_count,
                COALESCE(MAX(uploads.upload_count), 0)    as upload_count,
                COALESCE(MAX(uploads.total_found), 0)     as total_found,
                COALESCE(MAX(uploads.total_not_found), 0) as total_not_found,
                MAX(uploads.last_uploaded_at)             as last_uploaded_at
            ")
            ->groupBy('pa.id', 'pa.name_en')
            ->orderBy('pa.id');

        $data = DB::table(DB::raw("({$inner->toSql()}) as institutes"))
            ->mergeBindings($inner)
            ->selectRaw('*');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('publicAuthorityName', fn($row) => $row->institute_name)
            ->filterColumn('publicAuthorityName', function ($query, $keyword) {
                $query->where('institute_name', 'ILIKE', "%{$keyword}%");
            })
            ->orderColumn('publicAuthorityName', 'institute_name $1')
            ->addColumn('instituteCount', fn($row) =>
                '<span class="badge text-dark badge-secondary">' . $row->total_count . ' Total Registrants</span>'
            )
            ->addColumn('annualStatus', fn($row) =>
                '<span class="badge px-2 py-1 m-1" style="background:#f6c23e;color:#333;">Started: ' . ($row->annual_started_count ?? 0) . '</span>'
                . '<span class="badge px-2 py-1 m-1" style="background:#1cc88a;color:#fff;">Completed: ' . ($row->annual_completed_count ?? 0) . '</span>'
            )
            ->addColumn('otherStatus', fn($row) =>
                '<span class="badge px-2 py-1 m-1" style="background:#f6c23e;color:#333;">Started: ' . ($row->other_started_count ?? 0) . '</span>'
                . '<span class="badge px-2 py-1 m-1" style="background:#1cc88a;color:#fff;">Completed: ' . ($row->other_completed_count ?? 0) . '</span>'
            )
            ->addColumn('uploadCount', function ($row) {
                $count    = (int)($row->upload_count    ?? 0);
                $found    = (int)($row->total_found     ?? 0);
                $notFound = (int)($row->total_not_found ?? 0);

                if ($count === 0) {
                    return '<span class="badge px-2 py-1" style="background:#eaecf4;color:#6e7d8c;">No Uploads</span>';
                }

                $lastDate = $row->last_uploaded_at
                    ? '<small class="d-block text-muted mt-1">'
                      . \Carbon\Carbon::parse($row->last_uploaded_at)->format('Y-m-d H:i')
                      . '</small>'
                    : '';

                return '<span class="badge px-2 py-1 d-block mb-1" style="background:#4e73df;color:#fff;">'
                     . $count . ' Upload' . ($count > 1 ? 's' : '') . '</span>'
                     . '<span class="badge px-2 py-1 me-1" style="background:#1cc88a;color:#fff;"><i class="fal fa-check me-1"></i>' . $found . ' Found</span>'
                     . '<span class="badge px-2 py-1" style="background:#e74a3b;color:#fff;"><i class="fal fa-times me-1"></i>' . $notFound . ' Not Found</span>'
                     . $lastDate;
            })
            ->addColumn('edit', function ($row) {
                $route = route('admin-institute.designation-details', encrypt($row->public_authority_id));
                return '<a href="' . $route . '" class="btn btn-sm btn-outline-primary btn-icon rounded-circle waves-effect waves-themed">
                            <i class="fal fa-arrow-right"></i>
                        </a>';
            })
            ->rawColumns(['publicAuthorityName', 'instituteCount', 'annualStatus', 'otherStatus', 'uploadCount', 'edit'])
            ->toJson();
    }
}
