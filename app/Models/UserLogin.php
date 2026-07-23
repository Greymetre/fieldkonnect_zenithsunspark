<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    use HasFactory;

    protected $table = 'user_logins';

    protected $fillable = [ 'active', 'user_id', 'entry_from', 'provider', 'mobile', 'login_at', 'logout_at', 'deleted_at', 'created_at', 'updated_at'];

    public function save_data($data)
    {
        try
        {
            
            $created_at = getcurentDateTime();
            if( UserLogin::insert([
                'active' => 'Y',
                'user_id' => isset($data['id'])? $data['id']:'',
                'entry_from' => isset($data['entry_from'])? $data['entry_from']:'web',
                'provider' => isset($data['provider'])? $data['provider']:'users',
                'mobile' => isset($data['mobile'])? $data['mobile']:null,
                'login_at' => $created_at ,
                'created_at' => $created_at
            ]) )
            {
                return $response = array('status' => 'success', 'message' => 'UserLogin Insert Successfully');
            }
            return $response = array('status' => 'error', 'message' => 'Error in UserLogin Store');
        }
        catch(\Exception $e)
        {
            return $response = array('status' => 'error', 'message' => $e->getMessage());
        }
    }

    public function logout($user)
    {
        try
        {
            

            $logout = UserLogin::where('user_id',$user['id'])
                                ->where('provider',$user['provider'])
                                ->latest()->first();
            $logout->logout_at = getcurentDateTime();
            $logout->updated_at = getcurentDateTime();
            if($logout->save())
            {
                return $response = array('status' => 'success', 'message' => 'User Logout Successfully');
            }
            return $response = array('status' => 'error', 'message' => 'Error in User Logout');
        }
        catch(\Exception $e)
        {
            return $response = array('status' => 'error', 'message' => $e->getMessage());
        }
    }

    public function customers()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->select('id','name', 'first_name', 'last_name');
    }
}
