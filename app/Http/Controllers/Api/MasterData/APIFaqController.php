<?php

namespace App\Http\Controllers\Api\MasterData;

use Illuminate\Http\Request;
use App\Helpers\APIResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\Faq;
use App\Models\PublicAuthority;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class APIFaqController extends Controller
{
    public function getFaqList(Request $request)
    {
        $validityToken = '0alw9cHvIs1EmeJeoj2YbMi4V3YFzEktc34MSEYiGRsxhDV0Asy0MFSG9vWdrXBdYdIrSDCAGYsgb5e8Jl3L9EmyUrHdEu2TSL98HyCVvjBa07GOLr';

        if ($request->input('validityToken') === $validityToken) {
            $faqs = Faq::where('status', 'Y')
                ->where('is_show_in_portal', 'Y')
                ->where('is_delete', 0)
                ->orderBy('display_order', 'ASC')
                ->get();
            $baseUrl = $request->getSchemeAndHttpHost();
            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => APIResponseMessage::DATAFETCHED,
                'data' => [
                    'faqs' => $faqs,
                    'base_url' => $baseUrl.'/storage',
                ],
            ], 200);
        } else {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::UNAUTHORIZED,
            ], 401);
        }
    }

    public function getPublicAuthorityList(Request $request)
    {
        $validityToken = '0alw9cHvIs1EmeJeoj2YbMi4V3YFzEktc34MSEYiGRsxhDV0Asy0MFSG9vWdrXBdYdIrSDCAGYsgb5e8Jl3L9EmyUrHdEu2TSL98HyCVvjBa07GOLr';
        if ($request->input('validityToken') === $validityToken) {
            $publicAuthorities = PublicAuthority::where('status', 'Y')
                ->where('is_delete', 0)
                ->get();
            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => APIResponseMessage::DATAFETCHED,
                'data' => [
                    'public_authorities' => $publicAuthorities
                ],
            ], 200);
        } else {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::UNAUTHORIZED,
            ], 401);
        }
    }

    public function getDesignationList(Request $request)
    {
        $validityToken = '0alw9cHvIs1EmeJeoj2YbMi4V3YFzEktc34MSEYiGRsxhDV0Asy0MFSG9vWdrXBdYdIrSDCAGYsgb5e8Jl3L9EmyUrHdEu2TSL98HyCVvjBa07GOLr';
        $authorityId = $request->authority_id;
        if ($request->input('validityToken') === $validityToken) {
            $designations = Designation::where('status', 'Y')
                ->where('is_delete', 0)
                ->when($authorityId, function ($q) use ($authorityId) {
                    return $q->where('authority_id', $authorityId);
                })
                ->get();
            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => APIResponseMessage::DATAFETCHED,
                'data' => [
                    'designations' => $designations
                ],
            ], 200);
        } else {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::UNAUTHORIZED,
            ], 401);
        }
    }

    public function checkDeclarationEligibility(Request $request)
    {
        $validityToken = '0alw9cHvIs1EmeJeoj2YbMi4V3YFzEktc34MSEYiGRsxhDV0Asy0MFSG9vWdrXBdYdIrSDCAGYsgb5e8Jl3L9EmyUrHdEu2TSL98HyCVvjBa07GOLr';
        $authorityId = $request->authority_id;
        $designationId = $request->designation_id;
        if ($request->input('validityToken') === $validityToken) {
            $checkEligibility = Designation::where('status', 'Y')
                ->where('is_delete', 0)
                ->where('authority_id', $authorityId)
                ->where('id', $designationId)
                ->orderBy('designation_name_en', 'ASC')
                ->exists();

            return response()->json([
                'status' => APIResponseMessage::SUCCESS_STATUS,
                'message' => APIResponseMessage::DATAFETCHED,
                'data' => [
                    'exists' => $checkEligibility
                ],
            ], 200);
        } else {
            return response()->json([
                'status' => APIResponseMessage::ERROR_STATUS,
                'message' => APIResponseMessage::UNAUTHORIZED,
            ], 401);
        }
    }

}
