<?php

namespace App\Http\Controllers\Api\Declarantportal;

use App\Models\DeclarantFormContent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\DeclarantFormPage;
use App\Models\District;

class APIFormContentsController extends Controller
{
    /**
     * Get declarant form contents by page_id and language
     */
    public function getDeclarantFormContents(Request $request)
    {
        // Validate input
        $request->validate([
            'page_url' => 'required|string',
            'declaration_type_id' => 'nullable|integer',
            'lang'    => 'nullable|string|in:en,si,ta'
        ]);

        $pageUrl = $request->input('page_url');
        $declarationTypeId = $request->input('declaration_type_id');
        $lang   = $request->input('lang', 'en');


        $page = DeclarantFormPage::where('page_route', $pageUrl)
            ->where('status', 'Y')
            ->where('is_delete', 0)
            ->first();

        if (!$page) {
            return response()->json([
                'status' => 'error',
                'message' => 'Page not found or inactive.'
            ], 404);
        }

        // Fetch active and not-deleted contents
        $contents = DeclarantFormContent::where('page_id', $page->id)
            ->where('declaration_type_id', $declarationTypeId)
            ->where('status', 'Y')
            ->where('is_delete', 0)
            ->first();

        if (!$contents) {
            return response()->json([
                'status' => 'error',
                'message' => 'No contents found for this page.'
            ], 404);
        }

        $data = [
            'question' => $contents->{'question_' . $lang} ?? $contents->question_en,
            'content'  => $contents->{'content_' . $lang} ?? $contents->content_en,
        ];

        return response()->json([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    public function getDeclarantFormPage(Request $request)
    {
        // Validate input
        $request->validate([
            'page_url' => 'required|string',
            'lang'    => 'nullable|string|in:en,si,ta'
        ]);

        $pageUrl = $request->input('page_url');
        $lang   = $request->input('lang', 'en');

        $page = DeclarantFormPage::where('page_route', $pageUrl)
            ->where('status', 'Y')
            ->where('is_delete', 0)
            ->first();

        if (!$page) {
            return response()->json([
                'status' => 'error',
                'message' => 'Page not found or inactive.'
            ], 404);
        }

        $data = [
            'page_name' => $page->{'page_name_' . $lang} ?? $page->page_name_en,
            'order' => $page->order
        ];

        return response()->json([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    public function getProvinceName(Request $request)
    {
        $request->validate([
            'districtID' => 'required|integer',
            'lang'       => 'required|string|in:en,si,ta',
        ]);

        $province = District::with('province')
            ->where('id', $request->districtID)
            ->where('status', 'Y')
            ->where('is_delete', '0')
            ->first();

        if (!$province) {
            return response()->json([
                'status' => false,
                'message' => 'District not found.',
                'data' => null,
            ], 404);
        }
        $lang = $request->lang;
        $provinceNameField = 'province_name_' . $lang;

        return response()->json([
            'status'  => 'success',
            'message' => 'Province name retrieved successfully.',
            'data'    => [
                'province_id'   => $province->province->id,
                'province_name' => $province->province->{$provinceNameField},
            ],
        ]);
    }

    public function getAllDeclarantFormPages(Request $request)
    {
        $request->validate([
            'lang' => 'nullable|string|in:en,si,ta'
        ]);

        $lang = $request->input('lang', 'en');

        $data = DeclarantFormPage::where('status', 'Y')
            ->where('is_delete', 0)
            ->select("page_name_{$lang} as page_name", 'id as page_id', 'page_route')
            ->orderBy('order', 'asc')
            ->whereNotIn('id', [1, 2, 3])
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No pages found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
