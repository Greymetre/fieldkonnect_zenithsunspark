<?php

namespace App\Http\Controllers;

use App\Models\Support;
use Illuminate\Http\Request;
use App\Http\Requests\SupportRequest;
use App\Models\Priority;
use App\Models\SupportAssign;
use App\Models\Notes;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\DataTables\SupportDataTable;
use App\Imports\SupportImport;
use App\Exports\SupportExport;
use App\Exports\SupportTemplate;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        
        $this->supports = new Support();
    }


    // public function index(SupportDataTable $dataTable)
    // {
    //     abort_if(Gate::denies('supports_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    //     return $dataTable->render('supports.index');
    // }
    public function index(Request $request)
    {
        abort_if(Gate::denies('supports_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $data = Support::latest();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('created_at', function($data)
                    {
                        return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                    })
                    ->addColumn('action', function ($query) {
                          $btn = '';
                          if(auth()->user()->can(['supports_edit']))
                          {
                            $btn = $btn.'<a href="'.url("supports/".encrypt($query->id).'/edit') .'" class="btn btn-info btn-sm" title="'.trans('panel.global.show').' '.trans('panel.support.title_singular').'">
                                            <i class="material-icons">edit</i>
                                        </a>';
                          }
                          if(auth()->user()->can(['supports_show']))
                          {
                            $btn = $btn.'<a href="'.url("supports/".encrypt($query->id)).'" class="btn btn-warning btn-sm" title="'.trans('panel.global.show').' '.trans('panel.support.title_singular').'">
                                            <i class="material-icons">visibility</i>
                                        </a>';
                          }
                          if(auth()->user()->can(['supports_delete']))
                          {
                            $btn = $btn.' <a href="" class="btn btn-danger btn-sm delete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.support.title_singular').'">
                                        <i class="material-icons">clear</i>
                                      </a>';
                          }
                          return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                        '.$btn.'
                                    </div>';
                    })
                    ->filter(function ($query) use ($request) {
                                if($request['status'] == 'Open')
                                {
                                    $query->whereNull('assigned_to');
                                }
                                if($request['status'] == 'in_progress')
                                {
                                    $query->whereNotNull('assigned_to');
                                    $query->where('isanswered','=',0);
                                }
                                if($request['status'] == 'Answered')
                                {
                                    $query->where('isanswered','=',1);
                                    $query->whereNull('closed_at');
                                }
                                if($request['status'] == 'Hold')
                                {
                                    $query->whereNotNull('lock_at');
                                }
                                if($request['status'] == 'Closed')
                                {
                                    $query->whereNotNull('closed_at');
                                }
                                if(!empty($request['search'])){
                                    $search = $request['search'] ;
                                    $query->where('full_name', 'like', "%{$search}%");
                                    $query->Orwhere('subject', 'like', "%{$search}%");
                                }
                            })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('supports.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         abort_if(Gate::denies('supports_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $users = User::select('id','name')->get();
        $priorities = Priority::select('id','priority_name')->orderBy('priority_name','asc')->get();
        return view('supports.create',compact('users','priorities'))->with('supports',$this->supports);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupportRequest $request)
    {
        //abort_if(Gate::denies('supports_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request['active'] = 'Y';
        if($suport = Support::create($request->except(['_token','associated'])))
        {
            $request['support_id'] = $suport['id'];
            if(!empty($request['associated']))
            {
                foreach ($request['associated'] as $key => $row) {
                    SupportAssign::insert([
                        'active'        => isset($request['active']) ? $request['active'] :'Y',
                        'support_id'    => $suport['id'],
                        'supportrole' => 'Associated',
                        'user_id'       => $row,
                        'created_at'    => getcurentDateTime()
                    ]);
                }
                foreach ($request['dependency'] as $key => $rows) {
                    SupportAssign::insert([
                        'active'        => isset($request['active']) ? $request['active'] :'Y',
                        'support_id'    => $suport['id'],
                        'supportrole' => 'Dependency',
                        'user_id'       => $rows,
                        'created_at'    => getcurentDateTime()
                    ]);
                }
            }
          return Redirect::to('supports')->with('message_success', 'Support Store Successfully');
        }
        return redirect()->back()->with('message_danger', 'Error in Support Store')->withInput();
    }

    public function storeMessage($request)
    {
        return Notes::insert([
            'active'      => isset($request['active']) ? $request['active'] :'Y',
            'support_id'  => isset($request['support_id']) ? $request['support_id'] : null,
            'user_id'     => isset($request['user_id']) ? $request['user_id'] : null,
            'note'        => isset($request['message']) ? $request['message'] : '', 
            'is_replay'        => isset($request['is_replay']) ? $request['is_replay'] : '', 
            'created_at'  => getcurentDateTime()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Support  $support
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(Gate::denies('supports_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $support = Support::with('associatedUsers','messages')->find($id);
        $users = User::select('id','name')->get();
        return view('supports.show',compact('support','users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Support  $support
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('supports_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $supports = Support::with('associatedUsers')->find($id);
        $users = User::select('id','name')->get();
        $priorities = Priority::select('id','priority_name')->orderBy('priority_name','asc')->get();
        return view('supports.create',compact('users','priorities'))->with('supports',$supports);
    }

    public function assigned(Request $request)
    {
        if(Support::where('id',$request['support_id'])->update([
            'assigned_to'   => $request['assigned_to'],
            'assigned_at'    => getcurentDateTime()
        ]) )
        {
            $support = Support::where('id',$request['support_id'])->first();
            Mail::send('emails.supports.assigne', ['support' => $support ], function ($message) use($support) {
                  $message->to($support['users']['email'])->subject('Tickets Assigned!');
              });
            return redirect()->back()->with('message_success', 'Tasks Update Successfully');
        }
        return redirect()->back()->with('message_danger', 'Error in Tasks Update')->withInput();
    }

    public function response(Request $request)
    {
        if(Support::where('id',$request['support_id'])->update([
            'last_response_at'    => getcurentDateTime()
        ]) )
        {
            $support = Support::where('id',$request['support_id'])->first();
            $request['user_id'] = $support['assigned_to'];
            $request['is_replay'] = 'Yes';
            if($request['message'])
            {
                $this->storeMessage($request);
            }
            Mail::send('emails.supports.response', ['support' => $support ], function ($message) use($support) {
                  $message->to($support['users']['email'])->subject('Tickets Response!');
              });
            return redirect()->back()->with('message_success', 'Tasks Update Successfully');
        }
        return redirect()->back()->with('message_danger', 'Error in Tasks Update')->withInput();
    }

    public function message(Request $request)
    {
        if(Support::where('id',$request['support_id'])->update([
            'last_message_at'    => getcurentDateTime()
        ]) )
        {
            $support = Support::where('id',$request['support_id'])->first();
            $request['user_id'] = $support['user_id'];
            $request['is_replay'] = 'No';
            if($request['message'])
            {
                $this->storeMessage($request);
            }
            $support = Support::where('id',$request['support_id'])->first();
            Mail::send('emails.supports.message', ['support' => $support,'message' => $request['message'] ], function ($message) use($support) {
                  $message->to($support['users']['email'])->subject('Message Sent!');
              });
            return redirect()->back()->with('message_success', 'Tasks Update Successfully');
        }
        return redirect()->back()->with('message_danger', 'Error in Tasks Update')->withInput();
    }

    public function closed(Request $request)
    {
        if(Support::where('id',$request['support_id'])->update([
            'closed_at'    => getcurentDateTime()
        ]) )
        {
            $support = Support::where('id',$request['support_id'])->first();
            Mail::send('emails.supports.closed', ['support' => $support,'message' => $request['message'] ], function ($message) use($support) {
                  $message->to($support['users']['email'])->subject('Message Sent!');
              });
            return redirect()->back()->with('message_success', 'Tasks Update Successfully');
        }
        return redirect()->back()->with('message_danger', 'Error in Tasks Update')->withInput();
    }

    public function reopend(Request $request)
    {
        if(Support::where('id',$request['support_id'])->update([
            'last_message_at'    => getcurentDateTime()
        ]) )
        {
            $support = Support::where('id',$request['support_id'])->first();
            Mail::send('emails.supports.reopend', ['support' => $support,'message' => $request['message'] ], function ($message) use($support) {
                  $message->to($support['users']['email'])->subject('Message Sent!');
              });
            return redirect()->back()->with('message_success', 'Tasks Update Successfully');
        }
        return redirect()->back()->with('message_danger', 'Error in Tasks Update')->withInput();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Support  $support
     * @return \Illuminate\Http\Response
     */
    public function update(SupportRequest $request, $id)
    {
        abort_if(Gate::denies('supports_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if(Support::where('id',$id)->update($request->except(['_token','id','image','_method','action','associated','active'])) )
        {
            $request['support_id'] = $id;
            if(!empty($request['associated']))
            {
                foreach ($request['associated'] as $key => $row) {
                    SupportAssign::updateOrCreate(['support_id'    => $request['support_id'],'user_id'   => $row ],[
                        'active'        => isset($request['active']) ? $request['active'] :'Y',
                        'support_id'    => $request['support_id'],
                        'supportrole' => 'Associated',
                        'user_id'       => $row,
                        'created_at'    => getcurentDateTime()
                    ]);
                }
                foreach ($request['dependency'] as $key => $rows) {
                    SupportAssign::insert([
                        'active'        => isset($request['active']) ? $request['active'] :'Y',
                        'support_id'    => $suport['id'],
                        'supportrole' => 'Dependency',
                        'user_id'       => $rows,
                        'created_at'    => getcurentDateTime()
                    ]);
                }
                SupportAssign::where('support_id', $request['support_id'])->where('supportrole','=','Associated')->whereNotIn('user_id',$request['associated'])->delete();
                SupportAssign::where('support_id', $request['support_id'])->where('supportrole','=','Dependency')->whereNotIn('user_id',$request['dependency'])->delete();
            }
            return Redirect::to('supports')->with('message_success', 'Tasks Update Successfully');
        }
         return redirect()->back()->with('message_danger', 'Error in Tasks Update')->withInput(); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Support  $support
     * @return \Illuminate\Http\Response
     */
    public function destroy(Support $support)
    {
       abort_if(Gate::denies('supports_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    public function upload(Request $request) 
    {
        abort_if(Gate::denies('supports_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new SupportImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
        abort_if(Gate::denies('supports_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SupportExport, 'supports.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('supports_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SupportTemplate, 'supports.xlsx');
    }
}
