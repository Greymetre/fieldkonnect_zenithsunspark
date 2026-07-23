<?php

namespace App\Http\Controllers;

use App\Models\TourProgramme;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\TourProgrammeDataTable;
use App\Models\TourDetail;
use App\Models\User; 
use App\Models\City;
use App\Imports\TourImport;
use App\Exports\TourExport;
use App\Models\Division;

class TourController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->tours = new TourProgramme();
        
    }
    
    // public function index(TourProgrammeDataTable $dataTable)
    // {
    //     //abort_if(Gate::denies('tour_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    //     $userids = getUsersReportingToAuth();
    //     $users = User::where(function($query) use($userids){
    //                             if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
    //                             {
    //                                 $query->whereIn('id',$userids);
    //                             }
    //                         })->select('id','name')->orderBy('id','desc')->get();
    //     return $dataTable->render('tours.index',compact('users'));
    // }


        public function index(Request $request)
    {
        ////abort_if(Gate::denies('customer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $search_branches = $request->input('search_branches');
        $all_reporting_user_ids = getUsersReportingToAuth();
        $all_user_branches = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $branches = array();
        $all_branch = array();
        $divisions = Division::where('active', 'Y')->get();
        $bkey = 0;
        foreach ($all_user_branches as $k => $val) {
            if($val->getbranch){
                if(!in_array($val->getbranch->id, $all_branch)){
                    array_push($all_branch, $val->getbranch->id);
                    $branches[$bkey]['id'] = $val->getbranch->id;
                    $branches[$bkey]['name'] = $val->getbranch->branch_name;
                    $bkey++;
                }
            }
        }
        if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
            $all_reporting_user_ids = User::whereIn('id', $all_reporting_user_ids)->whereIn('branch_id', $search_branches)->pluck('id')->toArray();
        }
        $all_user_details = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $all_users = array();
        foreach ($all_user_details as $k => $val) {
            $users[$k]['id'] = $val->id;
            $users[$k]['name'] = $val->name;
        
        }
        if($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
            if ($request->ajax()) {
                $response = ["users"=>$users, "status"=>true];
                return response()->json($response);
            }
        }


        $userids = getUsersReportingToAuth();
        $userids = getUsersReportingToAuth();
        $users = User::where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->whereDoesntHave('roles', function ($query) {
                                $query->where('id', 29);
                            })->select('id','name')->orderBy('id','desc')->get();

    
        if ($request->ajax()) {
             // $data = TourProgramme::with('customertypes','firmtypes','createdbyname')
               $data = TourProgramme::with('userinfo')->where(function ($query) use ($request , $all_reporting_user_ids) {
                            if(!empty($request['executive_id']))
                            {
                                $query->where('userid', $request['executive_id']);
                            }
                            if(!empty($request['division_id']))
                            {
                                $userIds = User::where('division_id', $request['division_id'])->pluck('id');
                                $query->whereIn('userid', $userIds);
                            }

                            if(!empty($request['start_date']) && !empty($request['end_date']))
                            {
                              $query->whereBetween('date',[$request['start_date'],$request['end_date']]); 
                            }

                          
                            if(!empty($request['search']) && is_array($request['search']) == false){
                                $search = $request['search'] ;
                                $query->where(function($query) use($search) {
                                    $query->where('town', 'like', "%{$search}%")
                                    ->Orwhere('objectives', 'like', "%{$search}%")
                                    ->Orwhere('type', 'like', "%{$search}%");
                                });
                            }
                            if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                            {
                                $query->whereIn('userid',$all_reporting_user_ids);
                            }
                        })->orderBy(DB::raw('YEAR(date)'), 'DESC')->orderBy(DB::raw('DATE(date)'), 'ASC');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('checkbox', function ($item) {
                        return '<input type="checkbox" id="manual_entry_'.$item->id.'" class="manual_entry_cb checked_all" value="'.$item->id.'" />';
                        })
                        ->editColumn('created_at', function($data)
                        {
                            return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                        })
                        ->addColumn('stauts', function ($query) {
                            if($query->status == '0'){
                                $btn = ' <button type="button" data-status="0" class="btn btn-warning btn-just-icon btn-sm change_status" value="'.$query->id.'" title="Change Status(Pending)">
                                 <i class="material-icons">pending</i>
                                </button>';
                            }elseif($query->status == '1'){
                                 $btn = ' <button type="button" data-status="1" class="btn btn-success btn-just-icon btn-sm change_status" value="'.$query->id.'" title="Change Status(Approved)">
                                 <i class="material-icons">approval</i>
                                 </button>';
                            }else{
                                 $btn = ' <button type="button" data-status="2" class="btn btn-danger btn-just-icon btn-sm change_status" value="'.$query->id.'" title="Change Status(Rejected)">
                                 <i class="material-icons">circle</i>
                                 </button>';
                            }

                            return $btn;
                        })
                        ->addColumn('action', function ($query) {
                              $btn = '';
                              $activebtn ='';
                              // if(auth()->user()->can(['tour_edit']))
                              // {

                               $btn = $btn.'<a href"javascript:void(0)" class="btn btn-info btn-just-icon btn-sm edit" id="'.encrypt($query->id).'" title="'.trans('panel.global.edit').' '.trans('panel.category.title_singular').'">
                               <i class="material-icons">edit</i>
                                </a>';


                              // }
                              // if(auth()->user()->can(['customer_show']))
                              // {
                              //   $btn = $btn.'<a href="'.url("customers/".encrypt($query->id)).'" class="btn btn-theme btn-just-icon btn-sm" title="'.trans('panel.global.show').' '.trans('panel.customers.title_singular').'">
                              //                   <i class="material-icons">visibility</i>
                              //               </a>';
                              // }
                              // if(auth()->user()->can(['tour_delete']))
                              // {

                                $btn = $btn.' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="Delete Tour Plan">
                                <i class="material-icons">clear</i>
                               </a>';

                            //    if($query->status == '0'){
                            //        $btn = $btn.' <button type="button" data-status="0" class="btn btn-warning btn-just-icon btn-sm change_status" value="'.$query->id.'" title="Change Status(Pending)">
                            //         <i class="material-icons">pending</i>
                            //        </button>';
                            //    }elseif($query->status == '1'){
                            //         $btn = $btn.' <button type="button" data-status="1" class="btn btn-success btn-just-icon btn-sm change_status" value="'.$query->id.'" title="Change Status(Approved)">
                            //         <i class="material-icons">approval</i>
                            //         </button>';
                            //    }else{
                            //         $btn = $btn.' <button type="button" data-status="2" class="btn btn-danger btn-just-icon btn-sm change_status" value="'.$query->id.'" title="Change Status(Rejected)">
                            //         <i class="material-icons">circle</i>
                            //         </button>';
                            //    }


                              //}
                               
                              // if(auth()->user()->can(['customer_active']))
                              // {
                              //   $active = ($query->active == 'Y') ? 'checked="" value="'.$query->active.'"' : 'value="'.$query->active.'"';
                              //   $activebtn = '<div class="togglebutton">
                              //               <label>
                              //                 <input type="checkbox"'.$active.' id="'.$query->id.'" class="customerActive">
                              //                 <span class="toggle"></span>
                              //               </label>
                              //             </div>';
                              // }

                              return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            '.$btn.'
                                        </div>'.$activebtn;
                        })
                
                        ->rawColumns(['action', 'stauts','checkbox'])
                    ->make(true);
        }
      
       // return $dataTable->render('tours.index',compact('users','branches'));
        return view('tours.index', compact('users','branches', 'divisions'));
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userids = getUsersReportingToAuth();
        $users = User::where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->whereDoesntHave('roles', function ($query) {
                                $query->where('id', 29);
                            })->select('id','name')->orderBy('id','desc')->get();
        return view('tours.create',compact('users'))->with('tours',$this->tours);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach($request->detail as $datas){
            $validator = Validator::make($datas, [
                'date' => 'required',
                'userid' => 'required',
                'town' => 'required',
                'objectives' => 'required',
            ]); 
            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }
        }
        try
        { 
            $permission = !empty($request['id']) ? 'tour_edit' : 'tour_create' ;
            //abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if(!empty($request['detail']))
            {
                foreach ($request['detail'] as $key => $value) {
                    $tours = TourProgramme::updateOrCreate(
                        [
                            'date' => isset($value['date']) ? $value['date'] : null, 
                            'userid' => isset($value['userid']) ? $value['userid'] : null,
                        ],
                        [
                            'date' => isset($value['date']) ? $value['date'] : null, 
                            'userid' => isset($value['userid']) ? $value['userid'] : null,
                            'town' => isset($value['town']) ? $value['town'] : '',
                            'objectives' => isset($value['objectives']) ? $value['objectives'] : '',
                        ]
                    );
                    $towns = explode(',', $value['town']);
                    foreach ($towns as $key => $town) {
                        $cityid = City::where('city_name','=',$town)->pluck('id')->first();

                        $visited = TourDetail::whereHas('tourinfo',function($query) use($value){
                                                $query->where('userid','=',$value['userid']);
                                            })
                                            ->where('visited_cityid','=',$cityid)
                                            ->whereNotNull('visited_date')
                                            ->select('visited_date')
                                            ->latest()
                                            ->first();                
                        $lastvisited = (isset($cityid) && !empty($visited)) ? $visited['visited_date'] : null;
                        TourDetail::create([
                            'tourid' => isset($tours->id) ? $tours->id : null,
                            'city_id' => isset($cityid) ? $cityid : null, 
                            'last_visited' => isset($lastvisited) ? $lastvisited : null,
                        ]); 
                    }
                }
                return Redirect::to('tours')->with('message_success', 'TourProgramme Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();  
        }        
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function update(Request $request)
    {
        abort_if(Gate::denies('tasks_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            TourProgramme::where('id',$request['id'])->update([
                'date' => isset($request['date']) ? $request['date'] : null, 
                'userid' => isset($request['userid']) ? $request['userid'] : null,
                'town' => isset($request['town']) ? $request['town'] : '',
                'objectives' => isset($request['objectives']) ? $request['objectives'] : '',
            ]);
            $towns = explode(',', $request['town']);
            foreach ($towns as $key => $town) {
                $cityid = City::where('city_name','=',$town)->pluck('id')->first();
                $visited = TourDetail::whereHas('tourinfo',function($query) use($request){
                                        $query->where('userid','=',$request['userid']);
                                    })
                                    ->where('visited_cityid','=',$cityid)
                                    ->whereNotNull('visited_date')
                                    ->select('visited_date')
                                    ->latest()
                                    ->first();                
                $lastvisited = (isset($cityid) && !empty($visited)) ? $visited['visited_date'] : null;
                TourDetail::updateOrCreate(['tourid' => $request['id'], 'city_id' => $cityid],[
                    'tourid' => isset($request['id']) ? $request['id'] : null,
                    'city_id' => isset($cityid) ? $cityid : null, 
                    'last_visited' => isset($lastvisited) ? $lastvisited : null,
                ]); 
            }
        return redirect()->back()->with('message_danger', 'Error in Tour Update')->withInput(); 
    }

    public function show($id)
    {
        $id = decrypt($id);
        $tours = TourProgramme::find($id);
        return response()->json($tours);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //abort_if(Gate::denies('tour_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $tours = TourProgramme::find($id);
        return response()->json($tours);
    }

    public function destroy($id)
    {
        //abort_if(Gate::denies('tour_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $user = TourProgramme::find($id);
        // if($user->delete())
        // {
        //     return response()->json(['status' => 'success','message' => 'TourProgramme deleted successfully!']);
        // }
        // return response()->json(['status' => 'error','message' => 'Error in TourProgramme Delete!']);

        $user = TourProgramme::find($id);
      if(!empty($user)){
        TourDetail::where('tourid',$id)->delete();
         $user->delete();
         return response()->json(['status' => 'success','message' => 'TourProgramme deleted successfully!']);
       }

        return response()->json(['status' => 'error','message' => 'Error in TourProgramme Delete!']);
    }

    public function upload(Request $request) 
    {
      //abort_if(Gate::denies('tour_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new TourImport,request()->file('import_file'));
        return back();
    }
    public function download(Request $request)
    {
      //abort_if(Gate::denies('tour_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TourExport($request), 'tours.xlsx');
    }
    public function template()
    {
      //abort_if(Gate::denies('tour_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TourExport, 'tours.xlsx');
    }


    public function changeStatus(Request $request){
        // $tour = TourProgramme::find($request->id);
        // if($tour){
        //     $tour->status = $request->status;
        //     $tour->save();
        //     return response()->json(["status"=>"success"]);
        // }else{
        //     return response()->json(["status"=>false]);
        // }

        if($request->id){
        $tours = TourProgramme::whereIn('id',$request->id)->get();
        if($tours){  
            foreach($tours as $tour){
              $tour->update(['status'=>$request->status]);    
            }
            return response()->json(["status"=>"success"]);
        }else{
            return response()->json(["status"=>false]);
        }   

        }else{
           return response()->json(["status"=>false]);
        }


    }
}
