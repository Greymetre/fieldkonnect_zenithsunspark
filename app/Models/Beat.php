<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beat extends Model
{
    use HasFactory;

    protected $table = 'beats';

    protected $fillable = ['active', 'beat_name', 'description', 'region_id', 'country_id', 'state_id', 'district_id', 'city_id', 'created_by', 'created_at', 'updated_at'];

    public function message()
    {
        return [
            'beat_name.required' => 'Enter Beat Name',
        ];
    }

    public function insertrules()
    {
        return [
            'beat_name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
            'user_id' => 'required|exists:users,id',

        ];
    }
    public function updaterules($id = '')
    {
        return [
            'beat_name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function save_data($request)
    {
        try {
            if (is_array($request->district_id)) {
                $request['district_id'] = implode(',', $request->district_id);
            }
            if (is_array($request->city_id)) {
                $request['city_id'] = implode(',', $request->city_id);
            }
            $created_at = getcurentDateTime();
            if ($beat_id = Beat::insertGetId([
                'active' => 'Y',
                'beat_name' => isset($request['beat_name']) ? ucfirst($request['beat_name']) : '',
                'description' => isset($request['description']) ? $request['description'] : '',
                'country_id' => isset($request['country_id']) ? $request['country_id'] : null,
                'state_id' => isset($request['state_id']) ? $request['state_id'] : null,
                'district_id' => isset($request['district_id']) ? $request['district_id'] : null,
                'city_id' => isset($request['city_id']) ? $request['city_id'] : null,
                'region_id' => isset($request['region_id']) ? $request['region_id'] : null,
                'created_by' => isset($request['created_by']) ? $request['created_by'] : null,
                'created_at' => $created_at,
                'updated_at' => $created_at
            ])) {
                return $response = array('status' => 'success', 'message' => 'Beat Insert Successfully', 'beat_id' => $beat_id);
            }
            return $response = array('status' => 'error', 'message' => 'Error in Beat Store');
        } catch (\Exception $e) {
            return $response = array('status' => 'error', 'message' => $e->getMessage());
        }
    }

    public function update_data($request)
    {
        try {
            if (is_array($request->district_id)) {
                $request['district_id'] = implode(',', $request->district_id);
            }
            if (is_array($request->city_id)) {
                $request['city_id'] = implode(',', $request->city_id);
            }

            $created_at = getcurentDateTime();
            $beats = Beat::find($request['beat_id']);
            $beats->beat_name = isset($request['beat_name']) ? ucfirst($request['beat_name']) : '';
            $beats->description = isset($request['description']) ? $request['description'] : '';
            $beats->country_id = !empty($request['country_id']) ? $request['country_id'] : null;
            $beats->state_id = isset($request['state_id']) ? $request['state_id'] : null;
            $beats->district_id = isset($request['district_id']) ? $request['district_id'] : null;
            $beats->city_id = isset($request['city_id']) ? $request['city_id'] : null;
            $beats->region_id = isset($request['region_id']) ? $request['region_id'] : null;
            $beats->updated_at = $created_at;
            if ($beats->save()) {
                return $response = array('status' => 'success', 'message' => 'Beat Update Successfully');
            }
            return $response = array('status' => 'error', 'message' => 'Error in Beat Update');
        } catch (\Exception $e) {
            return $response = array('status' => 'error', 'message' => $e->getMessage());
        }
    }
    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id', 'name');
    }

    public function beatschedules()
    {
        return $this->hasMany('App\Models\BeatSchedule', 'beat_id', 'id')->select('id', 'beat_id', 'beat_date', 'user_id');
    }
    public function beatcustomers()
    {
        return $this->hasMany('App\Models\BeatCustomer', 'beat_id', 'id')->select('id', 'beat_id', 'customer_id');
    }

    public function beatusers()
    {
        return $this->hasMany('App\Models\BeatUser', 'beat_id', 'id')->select('id', 'beat_id', 'user_id');
    }

    public function countryname()
    {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id')->select('id', 'country_name');
    }
    public function statename()
    {
        return $this->belongsTo('App\Models\State', 'state_id', 'id')->select('id', 'state_name');
    }
}
