<?php

namespace App\Http\Controllers;

use App\Models\Notes;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\NotesDataTable;
use App\Http\Requests\NotesRequest;
use App\Exports\NotesExport;

class NotesController extends Controller
{
    public function index(NotesDataTable $dataTable)
    {
        //abort_if(Gate::denies('notes_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('notes.index');
    }

    public function store(Request $request)
    {
        try
        { 
            // $permission = !empty($request['id']) ? 'notes_edit' : '    notes_create' ;
            // abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $status = '';
            if(!empty($request['id']))
            {
                $status = Notes::where('id',$request['id'])->update($request->except(['_token','id']));
            }
            else
            {
                $request['active'] = 'Y';
                $request['user_id'] = Auth::user()->id;
                $status = Notes::create($request->except(['_token']));
            } 
            if($status)
            {
              return redirect()->back()->with('message_success', 'Data Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput(); 
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        //abort_if(Gate::denies('notes_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        //$id = decrypt($id);
        $note = Notes::find($id);
        return response()->json($note);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('notes_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $note = Notes::find($id);
        if($note->delete())
        {
            return response()->json(['status' => 'success','message' => 'Notes deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Notes Delete!']);
    }
    
    public function active(Request $request)
    {
        //abort_if(Gate::denies('notes_active'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if(Notes::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Notes '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function download()
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new NotesExport, 'notes.xlsx');
    }
}
