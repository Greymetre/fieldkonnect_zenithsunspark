<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'active',
        'name',
        'first_name',
        'last_name',
        'mobile',
        'email',
        'email_verified_at',
        'password',
        'password_string',
        'notification_id',
        'device_type',
        'gender',
        'profile_image',
        'latitude',
        'longitude',
        'region_id',
        'remember_token',
        'deleted_at',
        'created_at',
        'updated_at',
        'location',
        'reportingid',
        'branch_id',
        'primary_branch_id',
        'branch_show',
        'designation_id',
        'employee_codes',
        'department_id',
        'division_id',
        'warehouse_id',
        'payroll',
        'lave_balance',
        'grade',
        'blood_group',
        'personal_number',
        'sales_type',
        'customerid',
        'show_attandance_report'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_image')
            ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
            ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
            ->singleFile();

        $this->addMediaCollection('aadhar_image')
            ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
            ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
            ->singleFile();

        $this->addMediaCollection('pan_image')
            ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
            ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
            ->singleFile();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guard_name = 'users';

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id', 'name');
    }

    public function userinfo()
    {
        return $this->belongsTo('App\Models\UserDetails', 'id', 'user_id');
    }

    public function userbeats()
    {
        return $this->hasMany('App\Models\BeatUser', 'user_id', 'id')->select('beat_id', 'user_id');
    }

    public function cities()
    {
        return $this->hasMany('App\Models\UserCityAssign', 'userid', 'id')->select('city_id', 'userid');
    }

    public function reportinginfo()
    {
        return $this->belongsTo('App\Models\User', 'reportingid', 'id')->select('id', 'name');
    }

    public function getbranch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
    }

    // public function getdepartment()
    // {
    //     return $this->belongsTo('App\Models\Division', 'department_id', 'id')->select('id','division_name');
    // }

    public function getdivision()
    {
        return $this->belongsTo('App\Models\Division', 'division_id', 'id')->select('id', 'division_name');
    }

    public function user_customer()
    {
        return $this->belongsTo('App\Models\Customers', 'customerid', 'id');
    }

    public function getdepartment()
    {
        return $this->belongsTo('App\Models\Department', 'department_id', 'id')->select('id', 'name');
    }


    public function getdesignation()
    {
        return $this->belongsTo('App\Models\Designation', 'designation_id', 'id')->select('id', 'designation_name');
    }

    public function geteducation()
    {
        return $this->hasMany(UserEducation::class);
    }

    public function getpmsdetail()
    {
        return $this->hasOne(Appraisal::class, 'user_id', 'id');
    }

    public static function tree($user_id)
    {
        $all_user = User::get();
        $root_users = User::where('id', $user_id)->get();
        self::formatTree($root_users, $all_user);

        return $root_users;
    }

    private static function formatTree($root_users, $all_user)
    {
        foreach ($root_users as $root_user) {
            $root_user->children = $all_user->where('reportingid', $root_user->id)->values();
            if ($root_user->children->isNotEmpty()) {
                self::formatTree($root_user->children, $all_user);
            }
        }
    }

    public function attendance_details()
    {
        return $this->hasOne(Attendance::class, 'user_id', 'id');
    }

    public function resignation()
    {
        return $this->hasOne(Resignation::class, 'user_id', 'id');
    }

    public function all_attendance_details()
    {
        return $this->hasMany(Attendance::class);
    }

    // public function visits()
    // {
    //     return $this->hasMany(VisitReport::class, 'user_id', 'id');
    // }

    public function customers()
    {
        return $this->hasMany(Customers::class, 'created_by', 'id');
    }


    public function visits()
    {
        return $this->hasMany(CheckIn::class, 'user_id', 'id');
    }

    public function primarySales()
    {
        return $this->hasMany(PrimarySales::class, 'emp_code', 'employee_codes');
    }

    public function expenses()
    {
        return $this->hasMany(Expenses::class, 'user_id', 'id');
    }

    public function target()
    {
        return $this->hasMany(SalesTargetUsers::class, 'user_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\WareHouse', 'warehouse_id', 'id');
    }
}
