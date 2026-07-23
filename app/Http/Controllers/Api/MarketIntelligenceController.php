<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketIntelligenceServey;
use App\Models\MarketIntelligenceServeyData;
use App\Models\MarketIntelligencesField;
use App\Models\MarketIntelligencesFielddata;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Log;

class MarketIntelligenceController extends Controller
{
    public function getFields(Request $request)
    {
        $user = $request->user();

        $field = MarketIntelligencesField::with('fieldsData:id,value,field_id')
            ->where('division_id' , $user->division_id)
            ->select('id', 'field_name', 'key', 'field_type', 'input_type')
            ->get();
        return response()->json(['status' => 'success', 'data' => $field], 200);
    }

    public function MarketIntelligenceStore(Request $request)
    {
        $user = $request->user();
        $nextId = MarketIntelligenceServey::max('id') + 1;

        $data['created_by'] = $user->id;
        $data['title'] = 'Servey-' . $nextId;
        $data['division_id'] = $user->division_id;

        $servey = MarketIntelligenceServey::create($data);
        
        if ($request->hasFile('servey_image')) {
            $file = $request->file('servey_image');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $servey->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('servey_image');
        }

        foreach (json_decode($request->data) as $key => $value) {
            if ($key != '_token' && $key != 'servey_image') {
                MarketIntelligenceServeyData::updateOrCreate(
                    ['servey_id' => $servey->id, 'key' => $key],
                    ['value' => $value]
                );
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Market Intelligence created successfully', 'data' => $servey], 200);
    }
}
