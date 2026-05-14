<?php

namespace App\Http\Controllers\Backend\WebPortalManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Support\Arr;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Activity as SpatieActivity;
use Throwable;

class FaqController extends Controller implements HasMiddleware
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $mainTitle;
    private $title;

    function __construct()
    {
        $this->mainTitle = 'FAQs';
        $this->title = 'FAQ';
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:faq-list|faq-create|faq-edit|faq-delete', only: ['list']),
            new Middleware('permission:faq-create', only: ['index', 'store']),
            new Middleware('permission:faq-edit', only: ['edit', 'update']),
            new Middleware('permission:faq-status-update', only: ['activation']),
            new Middleware('permission:faq-delete', only: ['destroy']),
        ];
    }

    public function list(Request $request)
    {
        $loggedUser = Auth::user();
        $loggeduserrole = $loggedUser->roles()->first();

        $mainTitle = $this->mainTitle;
        $title = $this->title;

        if ($request->ajax()) {

            $query = Faq::where('is_delete', 0)->orderBy('display_order', 'ASC');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('heading_en', function ($row) {
                    return Str::limit($row->heading_en, 50, '...');
                })
                ->editColumn('heading_si', function ($row) {
                    return Str::limit($row->heading_si, 50, '...');
                })
                ->editColumn('heading_ta', function ($row) {
                    return Str::limit($row->heading_ta, 50, '...');
                })
                ->addColumn('edit', function ($row) {
                    $edit_url = url('adminpanel/web-portal-management/faqs/edit', encrypt($row->id));
                    if($row->id != 1) {
                        return '<a href="' . $edit_url . '"><span class="btn btn-warning btn-xs btn-icon rounded-circle"><i class="fal fa-pen"></i></span></a>';
                    } else {
                        return '';
                    }
                })
                ->addColumn('activation', function ($row) {
                    $status = ($row->status == "Y") ? 'fal fa-check' : 'fal fa-backspace';
                    $iconColor = ($row->status == "Y") ? 'text-success' : 'text-danger';
                    $url = url('adminpanel/web-portal-management/faqs/change-status', encrypt($row->id));

                    if($row->id != 1) {
                        return '<a class="btn-activate '.$iconColor.'" href="' . $url . '"><i class="' . $status . '"></i></a>';
                    } else {
                        return '';
                    }
                })
                ->addColumn('visibility_status', function ($row) {
                    $status = ($row->is_show_in_portal == "Y") ? 'fal fa-eye' : 'fal fa-eye-slash';
                    $iconColor = ($row->is_show_in_portal == "Y") ? 'text-success' : 'text-danger';
                    $url = url('adminpanel/web-portal-management/faqs/visibility-status-change', encrypt($row->id));
                    if($row->id != 1) {
                    return '<a class="btn-activate '.$iconColor.'" href="' . $url . '"><i class="' . $status . '"></i></a>';
                    } else {
                        return '';
                    }
                })
                // ->addColumn('delete', 'backend.webportalmanagement.faqs.actionsBlock')

                ->rawColumns(['edit', 'activation', 'delete','visibility_status'])
                ->make(true);
        }

        return view('backend.webportalmanagement.faqs.list', compact('mainTitle','title'));
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
        $faq = Faq::find($id);

        $mainTitle = $this->mainTitle;
        $title = $this->title;

        return view('backend.webportalmanagement.faqs.edit', compact('faq','mainTitle','title'));
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

        try {

            $validated = $request->validate([
                'heading_en' => 'required|string|min:3|max:250',
                'heading_si' => 'required|string|min:3|max:250',
                'heading_ta' => 'required|string|min:3|max:250',
                'description_en' => 'required',
                'description_si' => 'required',
                'description_ta' => 'required',
                'icon_path'    => 'nullable|file|mimes:png|max:10240',
                'display_order' => 'nullable|integer|min:0',
            ], [
                'heading_en.required' => 'Please enter the heading in English.',
                'heading_si.required' => 'Please enter the heading in Sinhala.',
                'heading_ta.required' => 'Please enter the heading in Tamil.',
                'description_en.required' => 'Please enter the description in English.',
                'description_si.required' => 'Please enter the description in Sinhala.',
                'description_ta.required' => 'Please enter the description in Tamil.',
                'display_order.integer' => 'Display order must be a whole number.',
            ]);

            $faq = Faq::find($id);

            $before = Arr::only($faq->toArray(), (new Faq())->getFillable()); // old values

            $validated['display_order'] = $request->display_order;

            if ($request->hasFile('icon_path')) {


                $file = $request->file('icon_path');

                if (! $file->isValid()) {
                    return redirect()->route('faqs.edit', ['id' => encrypt($id)])->withErrors(['icon_path' => 'Upload failed.'])->withInput();
                }

                // Extra PDF magic-number check (optional)
                $handle = fopen($file->getRealPath(), 'rb');
                $signature = fread($handle, 8);
                fclose($handle);
                if ($signature !== "\x89PNG\r\n\x1A\n") {
                    return redirect()->route('faqs.edit', ['id' => encrypt($id)])->withErrors(['icon_path' => 'Invalid PNG file.'])->withInput();
                }

                // Build a safe filename
                $original = $file->getClientOriginalName();
                $timestamp = now()->format('Ymd_His');
                $sanitized = $timestamp.'_'.preg_replace('/[^A-Za-z0-9.\-_]/', '_', str_replace(' ', '_', $original));

                // Save to public disk under "faq/"
                // This creates: storage/app/public/faq/<file>
                $path = $file->storeAs('faq', $sanitized, 'public'); // returns "faq/<file>"

                // Optional safety check
                if (! Storage::disk('public')->exists($path)) {
                    return redirect()->route('faqs.edit', ['id' => encrypt($id)])->withErrors(['icon_path' => 'Could not save the file to storage.'])->withInput();
                }

                $validated['icon_path'] = $path;
            }

            $faq->update($validated);

            $after = Arr::only($faq->toArray(), (new Faq())->getFillable()); // new values

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

            $title = $this->title;

            activity()->performedOn($faq)
            ->causedBy(auth()->user())
            ->event('updated')
            ->withProperties([
                'before'        => $before,
                'after'         => $after,
                'changed'       => $changed,
            ])
            ->tap(function (SpatieActivity $activity) use ($title) {
                $activity->title = $title;   // 👈 writes to the new column
            })
            ->log("FAQ record {$faq->heading_en} updated.");

            return redirect()->route('faqs.faq-list')->with('success', 'FAQ updated successfully');

        } catch (ValidationException $e) {
            // Force redirect back to the form route (your index-as-create)
            return redirect()->route('faqs.edit', ['id' => encrypt($id)])
                            ->withErrors($e->validator)
                            ->withInput();
        } catch (Throwable $e) {
            // Force redirect back to the form route (your index-as-create)
            return redirect()->route('faqs.edit', ['id' => encrypt($id)])
                            ->withErrors($e->getMessage())
                            ->withInput();
        }
    }

    public function activation(Request $request, $id)
    {
        $decryptId = decrypt($id);
        $data = Faq::findOrFail($decryptId);

        $title = $this->title;

        if ($data->status == "Y") {
            $data->status = 'N';
            $data->save();

            activity()->performedOn($data)
            ->causedBy(auth()->user())
            ->event('status changed to deactivate')
            ->tap(function (SpatieActivity $activity) use ($title) {
                $activity->title = $title;   // 👈 writes to the new column
            })
            ->log("FAQ record {$data->heading_en} deactivated.");

            return redirect()->route('faqs.faq-list')->with('success', 'Record deactivated successfully.');
        } else {
            $data->status = "Y";
            $data->save();

            activity()->performedOn($data)
            ->causedBy(auth()->user())
            ->event('status changed to activate')
            ->tap(function (SpatieActivity $activity) use ($title) {
                $activity->title = $title;   // 👈 writes to the new column
            })
            ->log("FAQ record {$data->heading_en} re activated.");

            return redirect()->route('faqs.faq-list')->with('success', 'Record reactivated successfully.');
        }
    }

    public function checkDisplayOrderExistency(Request $request)
    {
        $data = $request->validate([
            'display_order' => 'required|integer|min:0',
            'ignore_id'     => 'nullable|string',
        ]);

        $ignoreId = null;
        if (!empty($data['ignore_id'])) {
            try {
                $ignoreId = decrypt($data['ignore_id']);
            } catch (DecryptException $e) {
                // treat as no ignore id (or return a 422)
                $ignoreId = null;
            }
        }

        $q = Faq::where('is_delete',0)->where('display_order', $data['display_order']);
        if ($ignoreId) {
            $q->where('id', '!=', (int)$ignoreId);
        }

        return response()->json(['exists' => $q->exists()]);
    }

    public function visibilityStatusChange(Request $request, $id)
    {
        $decryptId = decrypt($id);
        $data = Faq::findOrFail($decryptId);

        $title = $this->title;

        if ($data->is_show_in_portal == "Y") {
            $data->is_show_in_portal = 'N';
            $data->save();

            activity()->performedOn($data)
            ->causedBy(auth()->user())
            ->event('visibility set to hidden')
            ->tap(function (SpatieActivity $activity) use ($title) {
                $activity->title = $title;   // 👈 writes to the new column
            })
            ->log("FAQ record {$data->heading_en} has been set to hidden.");

            return redirect()->route('faqs.faq-list')->with('success', 'Record has been successfully hidden.');
        } else {
            $data->is_show_in_portal = "Y";
            $data->save();

            activity()->performedOn($data)
            ->causedBy(auth()->user())
            ->event('visibility set to visible')
            ->tap(function (SpatieActivity $activity) use ($title) {
                $activity->title = $title;   // 👈 writes to the new column
            })
            ->log("FAQ record {$data->heading_en} has been set to visible.");

            return redirect()->route('faqs.faq-list')->with('success', 'Record has been successfully made visible.');
        }
    }

}
