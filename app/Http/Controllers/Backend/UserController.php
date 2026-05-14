<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Illuminate\Support\Arr;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:users-list|users-create|users-edit|users-delete', only: ['list']),
            new Middleware('permission:users-create', only: ['index', 'store']),
            new Middleware('permission:users-edit', only: ['edit', 'update']),
            new Middleware('permission:users-status-update', only: ['activation']),
            new Middleware('permission:users-delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $roles = Role::pluck('name', 'name')->all();

        return view('backend.users.index', compact('roles'));
    }


    public function list(Request $request)
    {
        $loggedUser = Auth::user();
        $loggeduserrole = $loggedUser->roles()->first();

        if ($request->ajax()) {

            $query = User::with('roles');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('institute_user_or_not', function ($row) {
                    if ($row->designation_id) {
                        return '<span class="badge" style="background-color: #28a745; color: white; padding: 5px 10px; border-radius: 4px;">Institute User</span>';
                    } else {
                        return '<span class="badge" style="background-color: #6c757d; color: white; padding: 5px 10px; border-radius: 4px;">Data Management User</span>';
                    }
                })
                ->editColumn('name', function ($row) {
                    return Str::limit($row->name, 50, '...');
                })
                ->editColumn('email', function ($row) {
                    return Str::limit($row->email, 50, '...');
                })
                ->addColumn('edit', function ($row) {
                    $edit_url = url('adminpanel/users/edit', encrypt($row->id));
                    return '<a href="' . $edit_url . '"><span class="btn btn-warning btn-xs btn-icon rounded-circle"><i class="fal fa-pen"></i></span></a>';
                })
                ->addColumn('role', function ($row) {
                    return implode(', ', $row->getRoleNames()->toArray());
                })
                ->addColumn('activation', function ($row) {
                    $status = ($row->status == "Y") ? 'fal fa-check' : 'fal fa-backspace';
                    $iconColor = ($row->status == "Y") ? 'text-success' : 'text-danger';
                    $url = url('adminpanel/users/change-status', $row->id);
                    if (!in_array($row->email, ["superadmin@tekgeeks.net"])) {
                        return '<a class="btn-activate '.$iconColor.'" href="' . $url . '"><i class="' . $status . '"></i></a>';
                    }
                    return '';
                })
                ->addColumn('block', 'backend.users.actionsBlock')
                ->filterColumn('role', function ($query, $keyword) {
                    $query->whereHas('roles', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['edit', 'role', 'activation', 'block', 'institute_user_or_not'])
                ->make(true);
        }

        return view('backend.users.list');
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     $roles = Role::pluck('name', 'name')->all();
    //     return view('backend.users.create', compact('roles'));
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'nic' => 'required|unique:users,nic',
                'password' => 'required|same:password_confirmation',
                'roles' => 'required',
            ]);


                // $input = $request->all();
            $validated['password'] = Hash::make($validated['password']);
            $validated['contact_number'] = $request->contact_number;

            $user = User::create($validated);
            // $user->assignRole($request->input('roles'));

            $user->assignRole($validated['roles']);

            // \LogActivity::addToLog('New user ' . $request->name . ' added(' . $user->id . ').');

            $fieldsToLog = (new User())->getFillable();

            $title = "User";

            // Optional custom message (global tap adds URL/IP/model_path automatically)
            activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->event('created')
            ->withProperties([
                'attributes' => Arr::only($user->toArray(), $fieldsToLog),
                'role_assigned' => $validated['roles'],
            ])
            ->tap(function (SpatieActivity $activity) use ($title) {
                $activity->title = $title;   // 👈 writes to the new column
            })
            ->log("New user {$user->name} created.");

            return redirect()->route('users.users-list')->with('success', 'Record saved successfully.');

        } catch (ValidationException $e) {
            // Force redirect back to the form route (your index-as-create)
            return redirect()->route('users.create')
                            ->withErrors($e->validator)
                            ->withInput();
        } catch (Exception $e) {
            // Force redirect back to the form route (your index-as-create)
            return redirect()->route('users.create')
                            ->withErrors($e->getMessage())
                            ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $id = decrypt($id);
        $user = User::find($id);

        $roles = Role::pluck('name', 'name')->all();

        $userRole = $user->roles->pluck('name')->first();

        return view('backend.users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = decrypt($request->id);

        // $this->validate($request, [
        //     'name' => 'required',
        //     'email' => 'required|email|unique:users,email,' . $id,
        //     'password' => 'same:confirm_password',
        //     'roles' => 'required',
        //     'institution_id' => 'required',
        //     'institution_type_id' => 'required',
        // ]);
        try {

            $validated = $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id,
                'nic' => 'required|unique:users,nic,' . $id,
                'password' => 'same:confirm_password',
                'roles' => 'required',
            ]);

            $validated['contact_number'] = $request->contact_number;

            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                $validated = Arr::except($validated, array('password'));
            }

            $user = User::find($id);

            $before = Arr::only($user->toArray(), (new User())->getFillable()); // old values

            $user->update($validated);

            DB::table('model_has_roles')->where('model_id', $id)->delete();

            $user->assignRole($validated['roles']);

            $after = Arr::only($user->toArray(), (new User())->getFillable()); // new values

            // Calculate changed fields
            $changed = [];
            foreach ($after as $key => $value) {
                if (($before[$key] ?? null) !== $value) {
                    $changed[$key] = [
                        'old' => $before[$key] ?? null,
                        'new' => $value,
                    ];
                }
            }

            $title = "User";

            activity()->performedOn($user)
            ->causedBy(auth()->user())
            ->event('updated')
            ->withProperties([
                'before'        => $before,
                'after'         => $after,
                'changed'       => $changed,
                'role_assigned' => $validated['roles'],
            ])
            ->tap(function (SpatieActivity $activity) use ($title) {
                $activity->title = $title;   // 👈 writes to the new column
            })
            ->log("User record {$user->name} updated.");

            return redirect()->route('users.users-list')->with('success', 'User updated successfully');

        } catch (ValidationException $e) {
            // Force redirect back to the form route (your index-as-create)
            return redirect()->route('users.edit', ['id' => encrypt($id)])
                            ->withErrors($e->validator)
                            ->withInput();
        } catch (Exception $e) {
            // Force redirect back to the form route (your index-as-create)
            return redirect()->route('users.edit', ['id' => encrypt($id)])
                            ->withErrors($e->getMessage())
                            ->withInput();
        }
    }

    public function activation(Request $request, $id)
    {
        $data = User::findOrFail($id);

        $title = "User";

        if ($data->status == "Y") {
            $data->status = 'N';
            $data->save();

            activity()->performedOn($data)
            ->causedBy(auth()->user())
            ->event('status changed')
            ->tap(function (SpatieActivity $activity) use ($title) {
                $activity->title = $title;   // 👈 writes to the new column
            })
            ->log("User record {$data->email} deactivated.");

            return redirect()->route('users.users-list')->with('success', 'Record deactivated successfully.');
        } else {
            $data->status = "Y";
            $data->save();

            activity()->performedOn($data)
            ->causedBy(auth()->user())
            ->event('status changed')
            ->tap(function (SpatieActivity $activity) use ($title) {
                $activity->title = $title;   // 👈 writes to the new column
            })
            ->log("User record {$data->email} re activated.");

            return redirect()->route('users.users-list')->with('success', 'Record  re activated successfully.');
        }
    }

    public function checkEmailAvailability(Request $request)
    {
        $query = User::where('email', $request->email);

        // Exclude current user if an id is sent
        if ($request->filled('id')) {
            try {
                $query->where('id', '!=', decrypt($request->id));
            } catch (\Exception $e) {
                // if id isn't encrypted or fails to decrypt, just skip
            }
        }

        return response()->json([
            'exists' => $query->exists(),
        ]);
    }

    public function checkNICAvailability(Request $request)
    {
        $query = User::whereRaw('LOWER(nic) = ?', [strtolower($request->nic)]);

        if ($request->filled('id')) {
            try {
                $query->where('id', '!=', decrypt($request->id));
            } catch (\Exception $e) {
                //
            }
        }

        return response()->json([
            'exists' => $query->exists(),
        ]);
    }

    public function getRoles(Request $request)
    {
        $loggedUser = Auth::user();
        $loggeduserrole = $loggedUser->roles()->first();

        $rolesQuery = Role::where('name', '!=', 'Admin');
        if ($loggeduserrole->name != "Admin" || $request->institution_cat_id == 2) {
            $rolesQuery->where('name', '!=', 'Commission User');
        }

        $roles = $rolesQuery->pluck('name', 'name')->all();

        return response()->json($roles);
    }
}
