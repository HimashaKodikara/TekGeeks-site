<?php

namespace App\Http\Controllers\Api\Sync;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicAuthoritiesUserSyncController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validation (Keep your secret check as is)
        if ($request->header('X-Main-Store-Secret') !== config('services.backend_support_api.api_secret')) {
            return response()->json(['error' => 'Forbidden: Invalid Sync Secret'], 403);
        }

        $request->validate([
            'table' => 'required|string',
            'data'  => 'required|array',
        ]);

        $data = $request->data;
        $isDeleteRequest = (isset($data['is_delete']) && $data['is_delete'] == 1);

        
        try {
            $result = DB::transaction(function () use ($data, $isDeleteRequest) {
                
                // Find user by NIC or Email
                $user = null;
                if (!empty($data['nic'])) {
                    $user = User::where('nic', $data['nic'])->lockForUpdate()->first();
                }
                if (!$user) {
                    $user = User::where('email', $data['email'])->lockForUpdate()->first();
                }

                if ($isDeleteRequest) {
                    if ($user) {
                        $user->delete();
                        return ['id' => $user->id, 'action' => 'deleted'];
                    }
                    return ['id' => null, 'action' => 'skipped (not found)'];
                }

                $payload = [
                    'designation_id' => $data['designation_id'] ?? null,
                    'name'           => trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
                    'email'          => $data['email'],
                    'nic'            => $data['nic'] ?? null,
                    'contact_number' => $data['phone'] ?? null,
                    'country_code'   => $data['country_code'] ?? null,
                    'status'         => $data['status'] ?? 'Y',
                    'updated_by'     => $data['updated_by'] ?? 1,
                    'is_delete'      => 0,
                ];

                Log::info("Received User Sync Request", ['data' => $payload]);

                if (!empty($data['password'])) {
                    $payload['password'] = $data['password'];
                }

                if ($user) {
                    $user->update($payload);
                    $action = 'updated';
                } else {
                    $payload['created_by'] = $data['created_by'] ?? 1;
                    $user = User::create($payload);
                    $action = 'created';
                }

                if (method_exists($user, 'syncRoles')) {
                    $user->syncRoles([3]);
                }

                return ['id' => $user->id, 'action' => $action];
            });

            return response()->json([
                'success' => true,
                'message' => "Sync successful: " . $result['action'],
                'id'      => $result['id']
            ], 200);

        } catch (\Exception $e) {
            Log::error("User Sync Error: " . $e->getMessage());
            return response()->json(['error' => 'Sync failed', 'debug' => $e->getMessage()], 500);
        }
    }
}
