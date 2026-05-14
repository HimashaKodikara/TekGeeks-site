<?php

namespace App\Http\Controllers\Api\Logs;

use Exception;
use App\Models\CommonLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\APIResponseMessage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CommonLogController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'activityType' => 'nullable|max:50',
            'actionPage' => 'nullable|max:190',
            'functionName' => 'nullable|max:190',
            'declarationId' => 'nullable|max:190',
            'subjectId' => 'nullable|max:190',
            'subjectType' => 'nullable|max:190',
            'description' => 'nullable|max:190',
            'remark' => 'nullable|max:190',
            'ipAddress' => 'nullable|max:190',
            'url' => 'nullable',
            'methodType' => 'nullable|max:190',
            'data' => 'nullable',
            'nic' => 'nullable|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::Validation_Error,
                'errors' => $validator->errors(),
            ], 422);
        }

        try{

            DB::beginTransaction();

            $commonLog = new CommonLog();
            $commonLog->activity_type = $request->activityType ?? null;
            $commonLog->action_page = $request->actionPage ?? null;
            $commonLog->function = $request->functionName ?? null;
            $commonLog->declaration_id = $request->declarationId ?? null;
            $commonLog->user_id = auth('sanctum')->user()->id ?? null;
            $commonLog->subject_id = $request->subjectId ?? null;
            $commonLog->subject_type = $request->subjectType ?? null;
            $commonLog->description = $request->description ?? null;
            $commonLog->remark = $request->remark ?? null;
            $commonLog->ip_address = $request->ipAddress ?? null;
            $commonLog->url = $request->url ?? null;
            $commonLog->method = $request->methodType ?? null;
            $commonLog->data = $request->data ?? null;
            $commonLog->nic = $request->nic ?? null;
            $commonLog->save();

            DB::commit();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => 'log successfully save.',
            ], 200);

        }catch(Exception $e){
            Log::error('Log Erorr : '.$e);
            DB::rollBack();
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => 'Could save log. Please try again later.'
            ], 500);
        }
    }
}
