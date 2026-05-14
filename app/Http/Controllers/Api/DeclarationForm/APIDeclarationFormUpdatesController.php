<?php

namespace App\Http\Controllers\Api\DeclarationForm;

use Illuminate\Http\Request;
use App\Helpers\APIResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\DeclarationType;
use App\Models\StatusOfDeclaration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class APIDeclarationFormUpdatesController extends Controller
{
    public function updateStatusofDeclarationInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'declarant_registration_id' => 'required|integer',
            'declaration_type_id' => 'required',
            'purpose_of_declaration_id' => 'required',
            'declaration_year' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $status_of_declaration = StatusOfDeclaration::firstOrNew([
                'declarant_registration_id' => $request->declarant_registration_id,
                'purpose_of_declaration_id' => $request->purpose_of_declaration_id
            ]);

            if ($request->filled('declaration_type_id')) {
                $status_of_declaration->declaration_type_id = $request->declaration_type_id;
            }

            if ($request->filled('declaration_year')) {
                $status_of_declaration->declaration_year = $request->declaration_year;
            }

            if ($request->filled('pref_lang')) {
                $status_of_declaration->pref_lang = $request->pref_lang;
            }

            if ($request->filled('completed_date')) {
                $status_of_declaration->completed_date = $request->completed_date;
            }

            if ($request->filled('status')) {
                $status_of_declaration->status = $request->status;
            }

            if ($request->filled('is_recompleted')) {
                $status_of_declaration->is_recompleted = $request->is_recompleted;
            }

            if ($request->filled('recompleted_date')) {
                $status_of_declaration->recompleted_date = $request->recompleted_date;
            }

            if ($request->filled('pdf_path')) {
                $status_of_declaration->pdf_path = $request->pdf_path;
            }

            if ($request->filled('pdf_generated_at')) {
                $status_of_declaration->pdf_generated_at = $request->pdf_generated_at;
            }

            if ($request->filled('designation_class_id')) {
                $status_of_declaration->designation_class_id = $request->designation_class_id;
            }

            if ($request->filled('designation_id')) {
                $status_of_declaration->designation_id = $request->designation_id;
            }

            if ($request->filled('institution_id')) {
                $status_of_declaration->institution_id = $request->institution_id;
            }

            $status_of_declaration->save();

            DB::commit();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function checkStatusofDeclaration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'declarant_registration_id' => 'required|integer',
            'current_year' => 'required',
            'declaration_type' => 'required',
            'purpose_of_declaration_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $getDeclarationFormStatus = StatusOfDeclaration::join('declaration_types', 'declaration_types.id', '=', 'status_of_declarations.declaration_type_id')
                ->where('status_of_declarations.declarant_registration_id', $request->declarant_registration_id)
                ->where('status_of_declarations.declaration_year', $request->current_year)
                ->where('status_of_declarations.declaration_type_id', $request->declaration_type)
                ->when($request->purpose_of_declaration_id, function ($query) use ($request) {
                    return $query->where('status_of_declarations.purpose_of_declaration_id', $request->purpose_of_declaration_id);
                })
                ->select(
                    'status_of_declarations.*',
                    'declaration_types.type_name_en AS declaration_type'
                )
                ->orderByDesc('status_of_declarations.created_at')
                ->first();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'data' => [
                    'declaration_form_status' => $getDeclarationFormStatus
                ]
            ], 200);
        } catch (\Throwable $e) {
            Log::info($e);
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function getDeclarationsByRegistrationId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'declarant_registration_id' => 'required|integer',
            'lang' => 'required|string|max:10',
            'declaration_type' => 'nullable|integer',
            'year' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $query = StatusOfDeclaration::leftJoin('declaration_types', 'declaration_types.id', '=', 'status_of_declarations.declaration_type_id')
                ->where('status_of_declarations.declarant_registration_id', $request->declarant_registration_id)
                ->where('status_of_declarations.is_delete', 0);

            if ($request->declaration_type) {
                $query->where('status_of_declarations.declaration_type_id', $request->declaration_type);
            }

            if ($request->year) {
                $query->where('status_of_declarations.declaration_year', $request->year);
            }

            $declarations = $query->select('status_of_declarations.*', 'declaration_types.type_name_' . $request->lang . ' as type_name', 'declaration_types.max_editable_days')->orderByDesc('created_at')->get();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'data' => $declarations
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function checkActiveDeclaration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'declarant_registration_id' => 'required|integer',
            // 'declaration_type_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $record = StatusOfDeclaration::where('declarant_registration_id', $request->declarant_registration_id)
                // ->where('declaration_type_id', $request->declaration_type_id)
                ->whereIn('status', ['S', 'R'])
                ->latest()
                ->first();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'data' => [
                    'purpose_of_declaration_id' => $record->purpose_of_declaration_id ?? null,
                    'status' => $record->status ?? null
                ]
            ], 200);
        } catch (\Throwable $e) {

            Log::error($e);

            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function getDeclarationById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purpose_of_declaration_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = StatusOfDeclaration::where('status_of_declarations.purpose_of_declaration_id', $request->purpose_of_declaration_id)
                ->where('status_of_declarations.is_delete', 0)
                ->first();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'data' => $data
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function getDeclarationReferenceNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'declaration_id' => 'required|integer',
            'declaration_type' => 'required|integer',
            'declaration_year' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $statusOfDecRec = StatusOfDeclaration::where('purpose_of_declaration_id', $request->declaration_id)
                ->where('declaration_type_id', $request->declaration_type)
                ->lockForUpdate()
                ->first();

            if ($statusOfDecRec) {
                if ($statusOfDecRec->reference_number) {
                    DB::commit();
                    return response()->json([
                        'status' => APIResponseMessage::SUCCESS_STATUS,
                        'reference_number' => $statusOfDecRec->reference_number
                    ]);
                }

                $declarationTypePrefix = DeclarationType::where('id', $request->declaration_type)->value('declaration_type_prefix');
                $currentYear = $request->declaration_year;

                $getLastRefNumber = StatusOfDeclaration::where('declaration_type_id', $request->declaration_type)
                    ->where('declaration_year', $currentYear)
                    ->whereNotNull('reference_number')
                    ->orderByDesc('sequence_number')
                    ->lockForUpdate()
                    ->first();

                $nextNumber = $getLastRefNumber ? $getLastRefNumber->sequence_number + 1 : 1;

                $middleNewNumber = str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

                $newReferenceNumber = $declarationTypePrefix . $middleNewNumber . $currentYear;

                $statusOfDecRec->reference_number = $newReferenceNumber;
                $statusOfDecRec->sequence_number = $nextNumber;
                $statusOfDecRec->save();
                DB::commit();
                return response()->json([
                    'status' => APIResponseMessage::SUCCESS_STATUS,
                    'reference_number' => $newReferenceNumber
                ]);
            } else {
                return response()->json([
                    'status' => APIResponseMessage::ERROR_STATUS,
                    'message' => 'Declaration record not found.'
                ], 404);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'An unexpected server error occurred. Please try again.',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatusofTheDeclarations(Request $request)
    {
        try {

            $payload = $request->all();

            if (!isset($payload['data']) || !is_array($payload['data'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid data payload',
                    'data' => null
                ], 422);
            }

            $declarations = $payload['data'];

            foreach ($declarations as $declaration) {

                StatusOfDeclaration::where(
                    'purpose_of_declaration_id',
                    $declaration['id']
                )->update([
                    'designation_class_id' => $declaration['designation_class_id'] ?? null,
                    'designation_id'       => $declaration['designation_id'] ?? null,
                    'institution_id'       => $declaration['institution_id'] ?? null,
                ]);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Status of declarations updated successfully',
                'data'    => null
            ]);
        } catch (\Throwable $e) {

            Log::error('Error updating status of declarations', [
                'payload' => $request->all(),
                'error'   => $e->getMessage()
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update status of declarations',
                'data'    => null
            ], 500);
        }
    }

    public function getAllStatusofTheDeclarations(Request $request)
    {
        try {
            $statusOfDeclarations = StatusOfDeclaration::where('is_delete', 0)->get();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'data' => $statusOfDeclarations
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error fetching all status of declarations', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'Failed to fetch status of declarations',
                'data' => null
            ], 500);
        }
    }
}
