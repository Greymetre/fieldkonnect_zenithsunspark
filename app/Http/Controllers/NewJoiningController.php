<?php

namespace App\Http\Controllers;

use App\DataTables\NewJoiningDataTable;
use App\Exports\NewJoiningExport;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\NewJoining;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;
use Excel;

class NewJoiningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(NewJoiningDataTable $dataTable)
    {
        abort_if(Gate::denies('new_joining_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return $dataTable->render('new_joining.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $branchs = Branch::where('active', 'Y')->get();
        $departments = Department::where('active', 'Y')->get();
        $designations = Designation::where('active', 'Y')->get();
        return view('new_joining.form', compact('branchs', 'departments', 'designations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $language = [
            'english' => $request->english,
            'hindi' => $request->hindi,
            'gujarati' => $request->gujarati,
            'other' => $request->other
        ];
        $request['language'] = json_encode($language);
        $request['occupy'] = implode(',', $request->occupy);
        $new_joining = NewJoining::create($request->all());

        if ($request->hasFile('adhar_images')) {
            $files = $request->file('adhar_images');
            foreach ($files as $file) {
                $customname = time() . '_adhar_images' . '.' . $file->getClientOriginalExtension();
                $new_joining->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('adhar_images');
            }
        }

        if ($request->hasFile('pan_images')) {
            $file = $request->file('pan_images');
            $customname = time() . '_pan_images' . '.' . $file->getClientOriginalExtension();
            $new_joining->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('pan_images');
        }

        if ($request->hasFile('passport_images')) {
            $file = $request->file('passport_images');
            $customname = time() . '_passport_images' . '.' . $file->getClientOriginalExtension();
            $new_joining->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('passport_images');
        }

        if ($request->hasFile('ssc_images')) {
            $file = $request->file('ssc_images');
            $customname = time() . '_ssc_images' . '.' . $file->getClientOriginalExtension();
            $new_joining->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('ssc_images');
        }

        if ($request->hasFile('hsc_images')) {
            $file = $request->file('hsc_images');
            $customname = time() . '_hsc_images' . '.' . $file->getClientOriginalExtension();
            $new_joining->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('hsc_images');
        }

        if ($request->hasFile('graduation_images')) {
            $file = $request->file('graduation_images');
            $customname = time() . '_graduation_images' . '.' . $file->getClientOriginalExtension();
            $new_joining->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('graduation_images');
        }

        if ($request->hasFile('birth_images')) {
            $file = $request->file('birth_images');
            $customname = time() . '_birth_images' . '.' . $file->getClientOriginalExtension();
            $new_joining->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('birth_images');
        }

        if ($request->hasFile('relieving_images')) {
            $file = $request->file('relieving_images');
            $customname = time() . '_relieving_images' . '.' . $file->getClientOriginalExtension();
            $new_joining->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('relieving_images');
        }

        if ($request->hasFile('last_salray_images')) {
            $file = $request->file('last_salray_images');
            $customname = time() . '_last_salray_images' . '.' . $file->getClientOriginalExtension();
            $new_joining->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('last_salray_images');
        }

        if ($request->hasFile('bank_images')) {
            $file = $request->file('bank_images');
            $customname = time() . '_bank_images' . '.' . $file->getClientOriginalExtension();
            $new_joining->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('bank_images');
        }

        if ($request->hasFile('offer_images')) {
            $file = $request->file('offer_images');
            $customname = time() . '_offer_images' . '.' . $file->getClientOriginalExtension();
            $new_joining->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('offer_images');
        }

        return redirect(route('joining-thanks'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NewJoining  $newJoining
     * @return \Illuminate\Http\Response
     */
    public function show(NewJoining $newJoining)
    {
        return view('new_joining.show', compact('newJoining'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NewJoining  $newJoining
     * @return \Illuminate\Http\Response
     */
    public function edit(NewJoining $newJoining)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NewJoining  $newJoining
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NewJoining $newJoining)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NewJoining  $newJoining
     * @return \Illuminate\Http\Response
     */
    public function destroy(NewJoining $newJoining)
    {
        //
    }

    public function thanks(Request $request)
    {
        return view('new_joining.thanks');
    }

    public function download(Request $request)
    {
        abort_if(Gate::denies('new_joining_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        // return $request;
        return Excel::download(new NewJoiningExport($request), 'NewJoining.xlsx');
    }

    // privacy-policy
     public function privacyPolicy(Request $request){
        return view('privacy-policy');
     }
}
