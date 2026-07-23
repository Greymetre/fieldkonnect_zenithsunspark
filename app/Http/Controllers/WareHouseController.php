<?php

namespace App\Http\Controllers;

use App\DataTables\WareHouseDataTable;
use App\Models\WareHouse;
use Illuminate\Http\Request;
use Gate;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class WareHouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(WareHouseDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('ware_house_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('ware_house.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        try
        { 
            $permission = !empty($request['id']) ? 'ware_house_edit' : 'ware_house_create' ;
            abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if(!empty($request['id']))
            {
                $status = WareHouse::where('id',$request['id'])->update($request->except(['_token','id']));
            }
            else
            {
                $status = WareHouse::create($request->except(['_token']));
            } 
            if($status)
            {
              return Redirect::to('ware_house')->with('message_success', 'Ware House Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();  
        }        
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WareHouse  $wareHouse
     * @return \Illuminate\Http\Response
     */
    public function show(WareHouse $wareHouse)
    {
        return response()->json($wareHouse);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WareHouse  $wareHouse
     * @return \Illuminate\Http\Response
     */
    public function edit(WareHouse $wareHouse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WareHouse  $wareHouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WareHouse $wareHouse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WareHouse  $wareHouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(WareHouse $wareHouse)
    {
        if($wareHouse->delete())
        {
            return response()->json(['status' => 'success','message' => 'Ware House deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Ware House Delete!']);
    }
}
