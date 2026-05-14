<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\HomeProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\HomeProjectRequest;
use Illuminate\Support\Facades\DB;
use App\Helpers\APIResponseMessage;
use Illuminate\Support\Facades\Event;
use App\Events\LoggableEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use Yajra\DataTables\Facades\DataTables;

class HomeProjectController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:home-project-list|home-project-create|home-project-edit|home-project-delete', only: ['index', 'show', 'getHomeProject']),
            new Middleware('permission:home-project-create', only: ['create', 'store']),
            new Middleware('permission:home-project-edit', only: ['edit', 'update']),
            new Middleware('permission:home-project-delete', only: ['destroy']),
        ];
    }

    public function getHomeProject()
    {
        $data = HomeProject::select('*');
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('edit', 'admin.homeproject.partials._delete')
            ->addColumn('status', 'admin.homeproject.partials._status')
            ->rawColumns(['edit', 'status'])
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.homeproject.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.homeproject.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HomeProjectRequest $request)
    {
        try{

            DB::beginTransaction();

            $homeproject = new HomeProject();
            $homeproject->name = $request->name;
            $homeproject->description = $request->description;
            $homeproject->techstack = $request->techstack;
            $homeproject->awards = $request->awards;
            $homeproject->case_study_link = $request->case_study_link;
            $homeproject->website = $request->website;
            
            if($request->hasfile('banner_image')){
               $homeproject->banner_image = $request
                ->file('banner_image'
                ->store('home-project/banner','public'));
            }

             if ($request->hasFile('content_image')) {
                $homeproject->content_image = $request
                    ->file('content_image')
                    ->store('home-projects/content', 'public');
            }

            if ($request->hasFile('company_logo')) {
                $homeproject->company_logo = $request
                    ->file('company_logo')
                    ->store('home-projects/company-logo', 'public');
            }

            $homeproject->save();

            DB::commit();

            return redirect()-> route('home-project.index')->with('success',APIResponseMessage:: CREATED);

        }catch(Exception $e){
            DB::rollBack();
            return redirect()-> route('home-project.index')->with('success',APIResponseMessage::FAIL);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $homeprojectId = decrypt($id);
        $homeproject = HomeProject::with([])->find($homeprojectId);

        return view('admin.homeproject.edit',['homeProject' => $homeproject]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HomeProject $homeProject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HomeProjectRequest $request, string $id)
    {
        try{
            DB::beginTransaction();

            $homeproject = HomeProject::find($id);

            $homeproject->name = $request->name;
            $homeproject->description = $request->description;
            $homeproject->techstack = $request->techstack;
            $homeproject->awards = $request->awards;
            $homeproject->case_study_link = $request->case_study_link;
            $homeproject->website = $request->website;
            
            if($request->hasfile('banner_image')){
               $homeproject->banner_image = $request
                ->file('banner_image'
                ->store('home-project/banner','public'));
            }

             if ($request->hasFile('content_image')) {
                $homeproject->content_image = $request
                    ->file('content_image')
                    ->store('home-projects/content', 'public');
            }

            if ($request->hasFile('company_logo')) {
                $homeproject->company_logo = $request
                    ->file('company_logo')
                    ->store('home-projects/company-logo', 'public');
            }

            $homeproject->save();

            DB::commit();

            return redirect()-> route('home-project.index')->with('success',APIResponseMessage:: UPDATED);

        }catch(Exception $e){
            DB::rollBack();
            return redirect()-> route('home-project.index')->with('error',APIResponseMessage:: FAIL);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            DB::beginTransaction();

            $homeproject = HomeProject::find($id);
            $homeproject->update(['deleted_by' => Auth::id()]);
            HomeProject::with([]) -> find($id)->delete();

            DB::commit();
            Event:: dispatch(new LoggableEvent($homeproject, 'deleted'));

            return redirect()-> route('home-project.index')->with('success',APIResponseMessage:: SUCCESS);
        }
        catch(Exception $e){
            DB::rollBack();
            return redirect()-> route('home-project.index')->with('error',APIResponseMessage:: FAIL);
        }
    }
}
