<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DynamicMenu;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Activity as SpatieActivity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller implements HasMiddleware
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:roles-list|roles-create|roles-edit|roles-delete', only: ['list']),
            new Middleware('permission:roles-create', only: ['index', 'store']),
            new Middleware('permission:roles-edit', only: ['edit', 'update']),
            new Middleware('permission:roles-status-update', only: ['activation']),
            new Middleware('permission:roles-delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('edit', 'backend.roles.actionsBlock')
                ->rawColumns(['edit'])
                ->make(true);
        }

        return view('backend.roles.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $permission = Permission::get();
        $dynamicMenu = DynamicMenu::where('show_menu', 1)->orderBy('fOrder', 'ASC')->get();

        return view('backend.roles.index', compact('permission', 'dynamicMenu'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|max:120|unique:roles,name',
                'permission' => 'required|array',
                'permission.*' => 'string|exists:permissions,name', // if you send names; use id if you send ids
                'user_manual' => 'nullable|file|mimes:pdf', // 35MB
            ]);

            // Optional: your lowercase duplicate check (unique rule already handles it)
            $existing = Role::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->exists();
            if ($existing) {
                return back()->withErrors(['name' => 'The role name has already been taken.'])->withInput();
            }

            $path = null;

            if ($request->hasFile('user_manual')) {
                $file = $request->file('user_manual');

                if (! $file->isValid()) {
                    return back()->withErrors(['user_manual' => 'Upload failed.'])->withInput();
                }

                // Extra PDF magic-number check (optional)
                $handle = fopen($file->getRealPath(), 'rb');
                $signature = fread($handle, 4);
                fclose($handle);
                if ($signature !== '%PDF') {
                    return back()->withErrors(['user_manual' => 'Uploaded file is not a valid PDF.'])->withInput();
                }

                // Build a safe filename
                $original = $file->getClientOriginalName();
                $timestamp = now()->format('Ymd_His');
                $sanitized = $timestamp.'_'.preg_replace('/[^A-Za-z0-9.\-_]/', '_', str_replace(' ', '_', $original));

                // Save to public disk under "usermanual/"
                // This creates: storage/app/public/usermanual/<file>
                $path = $file->storeAs('usermanual', $sanitized, 'public'); // returns "usermanual/<file>"

                // Optional safety check
                if (! Storage::disk('public')->exists($path)) {
                    return back()->withErrors(['user_manual' => 'Could not save the file to storage.'])->withInput();
                }
            }

            $role = new Role;
            $role->name = $request->name;
            $role->guard_name = 'web';
            // Store relative path ("usermanual/<file>"); easier to build URLs later
            $role->user_manual = $path;
            $role->save();

            // If you posted names:
            $role->syncPermissions($request->input('permission'));
            // If you posted IDs instead, use:
            // $perms = \Spatie\Permission\Models\Permission::whereIn('id', $request->input('permission'))->get();
            // $role->syncPermissions($perms);

            $fieldsToLog = (new Role)->getFillable();

            $title = 'Role';

            // Optional custom message (global tap adds URL/IP/model_path automatically)
            activity()->performedOn($role)
                ->causedBy(auth()->user())
                ->event('created')
                ->withProperties([
                    'attributes' => Arr::only($role->toArray(), $fieldsToLog),
                ])
                ->tap(function (SpatieActivity $activity) use ($title) {
                    $activity->title = $title;   // 👈 writes to the new column
                })
                ->log("New role {$role->name} created.");

            return redirect()->route('roles.roles-list')->with('success', 'Role created successfully');

        } catch (ValidationException $e) {
            return redirect()->route('roles.create')
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Throwable $e) {
            return redirect()->route('roles.create')
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join('role_has_permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', $id)
            ->get();

        return view('roles.show', compact('role', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */

    /*
    public function edit($id)
    {
       $role = Role::find($id);
       $permission = Permission::get();
       $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
           ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
           ->all();

       return view('roles.edit', compact('role', 'permission', 'rolePermissions'));
    }
    */

    public function edit($id)
    {
        $id = decrypt($id);
        $role = Role::find($id);
        $permission = Permission::get();
        $dynamicMenu = DynamicMenu::where('show_menu', 1)->orderBy('fOrder', 'ASC')->get();

        //         print_r($dynamicMenu);
        // die();
        $rolePermissions = DB::table('role_has_permissions')->where('role_has_permissions.role_id', $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        //  var_dump($rolePermissions); exit();
        return view('backend.roles.edit', compact('role', 'permission', 'rolePermissions', 'dynamicMenu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request)
    {
        $id = decrypt($request->id);

        try {

            $request->validate([
                'name' => 'required|max:120',
                'permission' => 'required|array',
                'permission.*' => 'string|exists:permissions,name', // if you send names; use id if you send ids
                'user_manual' => 'nullable|file|mimes:pdf|max:35840', // 35MB
            ]);

            $role = Role::find($id);

            $role->name = $request->input('name');
            $role->guard_name = 'web';

            if ($request->hasFile('user_manual')) {

                $file = $request->file('user_manual');

                if (! $file->isValid()) {
                    return redirect()->route('roles.edit', ['id' => $id])->withErrors(['user_manual' => 'Upload failed.'])->withInput();
                }

                // Extra PDF magic-number check (optional)
                $handle = fopen($file->getRealPath(), 'rb');
                $signature = fread($handle, 4);
                fclose($handle);
                if ($signature !== '%PDF') {
                    return redirect()->route('roles.edit', ['id' => $id])->withErrors(['user_manual' => 'Uploaded file is not a valid PDF.'])->withInput();
                }

                // Build a safe filename
                $original = $file->getClientOriginalName();
                $timestamp = now()->format('Ymd_His');
                $sanitized = $timestamp.'_'.preg_replace('/[^A-Za-z0-9.\-_]/', '_', str_replace(' ', '_', $original));

                // Save to public disk under "usermanual/"
                // This creates: storage/app/public/usermanual/<file>
                $path = $file->storeAs('usermanual', $sanitized, 'public'); // returns "usermanual/<file>"

                // Optional safety check
                if (! Storage::disk('public')->exists($path)) {
                    return redirect()->route('roles.edit', ['id' => $id])->withErrors(['user_manual' => 'Could not save the file to storage.'])->withInput();
                }

                $role->user_manual = $path;
            }

            $role->save();

            $role->syncPermissions($request->input('permission'));

            $fieldsToLog = (new Role)->getFillable();

            $title = 'Role';

            // Optional custom message (global tap adds URL/IP/model_path automatically)
            activity()->performedOn($role)
                ->causedBy(auth()->user())
                ->event('created')
                ->withProperties([
                    'attributes' => Arr::only($role->toArray(), $fieldsToLog),
                ])
                ->tap(function (SpatieActivity $activity) use ($title) {
                    $activity->title = $title;   // 👈 writes to the new column
                })
                ->log("Role record {$role->name} updated.");

            return redirect()->route('roles.roles-list')->with('success', 'Role updated successfully');

        } catch (ValidationException $e) {
            return redirect()->route('roles.edit', ['id' => $id])
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Throwable $e) {
            return redirect()->route('roles.edit', ['id' => $id])
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }
}
