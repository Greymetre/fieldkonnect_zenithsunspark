<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [ 'active','ranking' ,'category_name', 'category_image','created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];

    public function message()
    {
        return [
            'name.required' => 'Enter Category Name',
        ];
    }

    public function insertrules()
    {
        return [
            'category_name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
            'category_image' => 'required',
        ];
    }
    public function updaterules($id ='')
    {
        return [
            'category_name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
        ];
    }

    public function save_data($request)
    {
        try
        {
            
            $created_at = getcurentDateTime();
            if( $category_id = Category::insertGetId([
                'active' => 'Y',
                'category_name' => isset($request['category_name'])? ucfirst($request['category_name']):'',
                'category_image' => isset($request['category_image'])? $request['category_image']:'',
                'created_at' => $created_at ,
                'updated_at' => $created_at
            ]) )
            {
                return $response = array('status' => 'success', 'message' => 'Category Insert Successfully','category_id' => $category_id);
            }
            return $response = array('status' => 'error', 'message' => 'Error in Category Store');
        }
        catch(\Exception $e)
        {
            return $response = array('status' => 'error', 'message' => $e->getMessage());
        }
    }

    public function update_data($request)
    {
        try
        {
            
            $created_at = getcurentDateTime();
            $categories = Category::find($request['category_id']);
            $categories->category_name = isset($request['category_name'])? $request['category_name'] :'';
            if(!empty($request['category_image']))
            {
                $categories->category_image = isset($request['category_image'])? $request['category_image'] :null;
            }
            $categories->updated_at = $created_at;
            if($categories->save())
            {
                return $response = array('status' => 'success', 'message' => 'Category Update Successfully');
            } 
            return $response = array('status' => 'error', 'message' => 'Error in Category Update');
        }
        catch(\Exception $e)
        {
            return $response = array('status' => 'error', 'message' => $e->getMessage());
        }
    }
    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
}
