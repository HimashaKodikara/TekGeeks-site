<?php

namespace App\Http\Controllers\Api\MasterData;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class SyncController extends Controller
{
    public function universalSync(Request $request)
    {
        if ($request->header('X-Main-Store-Secret') !== config('services.backend_support_api.api_secret')) {
            return response()->json(['error' => 'Forbidden: Invalid Sync Secret'], 403);
        }

        try {
            $tableName = $request->table;
            $data = $request->data;

            $allowedTables = [
                'districts',
                'cities',
                'countries',
                'currencies',
                'declaration_types',
                'public_authorities',
                'designations',
                'visa_types',
                'acquisition_modes',
                'vehicle_types',
                'bank_account_types',
                'virtual_asset_types',
                'income_types',
                'expense_types',
                'interest_types',
                'provinces',
                'districts',
                'immovable_asset_types',
                'commercialsable_intangible_assets_types',
                'monetary_institutes',
                'monetary_institute_details'
            ];

            if (!in_array($tableName, $allowedTables)) {
                return response()->json(['error' => 'Unauthorized table'], 403);
            }

            if (isset($data['deleted']) && $data['deleted'] === true) {
                DB::table($tableName)->where('id', $data['id'])->delete();
                return response()->json(['status' => 'deleted']);
            }

            $existingColumns = Schema::getColumnListing($tableName);
            $filteredData = array_intersect_key($data, array_flip($existingColumns));

            DB::table($tableName)->updateOrInsert(
                ['id' => $filteredData['id']],
                $filteredData
            );

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error("Sync Failed: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
