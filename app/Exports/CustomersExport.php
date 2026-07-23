<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\UserActivity;
use App\Models\Branch;
use App\Models\User;
use App\Models\Division;
use App\Models\Designation;
use App\Models\EmployeeDetail;
use App\Models\ParentDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class CustomersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $filters = [];
    protected $division_users = [];

    public function __construct($request)
    {
        $this->filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'customertype' => $request->input('customertype'),
            'branch_id' => $request->input('branch_id'),
            'division_id' => $request->input('division_id'),
            'state_id' => $request->input('state_id'),
            'city_id' => $request->input('city_id'),
            'active' => $request->input('active'),
            'executive_id' => $request->input('executive_id'),
        ];

        $this->pumpD = ['PUMPFOS', 'PUMPTM', 'PUMPASM', 'PUMPRM', 'PUMPBM', 'PUMPCH'];
        $this->fanD = ['FAN&A/TM/ASM/MM', 'FAN&A/BM/MM', 'FAN/RM', 'FAN/CH/GM/SH'];
        $this->agriD = ['AGRIGM/CH/ZM/RM/SH', 'AGRIMANAGER'];
        $this->solarD = ['SOLAR/MANAGER', 'SOLAR/BM/GM/ZM'];
        $this->serviceD = ['Service Eng'];
        $this->tenderD = ['TENDER/MANAGER'];

        $this->userids = getUsersReportingToAuth();

        if (!empty($this->filters['division_id'])) {
            $this->division_users = User::where('division_id', $this->filters['division_id'])->pluck('id')->toArray();
        }
    }

    public function collection()
    {
        $query = Customers::with([
            'customertypes',
            'firmtypes',
            'createdbyname',
            'getemployeedetail.employee_detail.getdesignation',
            'getemployeedetail.employee_detail.getbranch',
            'getemployeedetail.employee_detail.getdivision',
            'getparentdetail.parent_detail',
            'customeraddress.pincodename',
            'customeraddress.cityname',
            'customeraddress.districtname',
            'customeraddress.statename',
        ])->where(function ($query) {
            if (!empty($this->filters['executive_id'])) {
                $query->where(function ($q)  {
                    $q->where('executive_id', $this->filters['executive_id'])
                      ->orWhere('created_by', $this->filters['executive_id']);
                });
            }

            if (!Auth::user()->hasRole(['superadmin', 'Admin'])) {
                $query->where(function ($query) {
                    $query->whereIn('executive_id', $this->userids)
                        ->orWhereIn('created_by', $this->userids);
                });
            }

            if (!empty($this->division_users)) {
                $query->where(function ($query) {
                    $common = array_intersect($this->division_users, $this->userids);
                    $query->whereIn('executive_id', $common)
                        ->orWhereIn('created_by', $common);
                });
            }

            if (!empty($this->filters['active'])) {
                $query->where('active', $this->filters['active']);
            }
            if (!empty($this->filters['start_date'])) {
                $query->whereDate('created_at', '>=', $this->filters['start_date']);
            }

            if (!empty($this->filters['end_date'])) {
                $query->whereDate('created_at', '<=', $this->filters['end_date']);
            }

            if (!empty($this->filters['customertype'])) {
                $query->where('customertype', $this->filters['customertype']);
            }

            if (!empty($this->filters['branch_id'])) {
                $branch_users = User::whereIn('branch_id', $this->filters['branch_id'])->pluck('id');
                $query->whereIn('executive_id', $branch_users);
            }

            if (!empty($this->filters['state_id'])) {
                $query->whereHas('customeraddress', function ($q) {
                    $q->where('state_id', $this->filters['state_id']);
                });
            }

            if (!empty($this->filters['city_id'])) {
                $query->whereHas('customeraddress', function ($q) {
                    $q->where('city_id', $this->filters['city_id']);
                });
            }
        });

        // Limit for performance
        return $query->limit(5000)->latest()->get();
    }

    public function headings(): array
    {
        $headings = [
            'Created Date',
            'Customer ID',
            'Customer Code',
            'Status',
            'Customer Type',
            'Created By',
            'Firm Name',
            'Parent Customer',
            'First Name',
            'Last Name',
            'Mobile',
            'Contact Number 2',
            'Email',
            'Address',
            'Gmap Address',
            'Pin Code',
            'Zip Code',
            'Market Place',
            'City',
            'District',
            'State',
            'Grade',
            'Visit Status',
            'GSTIN No',
            'Aadhar No',
            'PAN No',
            'Other No',
            'Shop Image',
            'Employee Code',
            'Employee Name',
            'Designation',
            'Branch Name',
            'Division',
            'Latitude',
            'Longitude',
            'Employee ID',
            'Parent ID',
            'Pincode ID',
            'City ID',
            'District ID',
            'State ID',
            'Customer Type ID',
            'Working Status',
            'Creation Date',
            'Sap Code'
        ];

        if (!empty($this->division_users)) {
            if ($this->filters['division_id'] == '10') {
                $headings = array_merge($headings, $this->pumpD);
            } else if ($this->filters['division_id'] == '3') {
                $headings = array_merge($headings, $this->fanD);
            } else if ($this->filters['division_id'] == '4') {
                $headings = array_merge($headings, $this->agriD);
            } else if ($this->filters['division_id'] == '5') {
                $headings = array_merge($headings, $this->solarD);
            } else if ($this->filters['division_id'] == '9') {
                $headings = array_merge($headings, $this->serviceD);
            } else if ($this->filters['division_id'] == '7') {
                $headings = array_merge($headings, $this->tenderD);
            }
        }


        return $headings;
    }

    public function map($data): array
    {
        // Process related data
        $employeeDetails = $data->getemployeedetail->filter(function ($item) {
            return empty($this->division_users) || in_array($item->user_id, $this->division_users);
        });

        $parentDetails = $data->getparentdetail;

        // Map employee details
        $employeeNames = $employeeDetails->pluck('employee_detail.name')->implode(',');
        $employeeIds = $employeeDetails->pluck('user_id')->implode(',');
        $designations = $employeeDetails->pluck('employee_detail.getdesignation.designation_name')->implode(',');
        $branches = $employeeDetails->pluck('employee_detail.getbranch.branch_name')->implode(',');
        $divisions = $employeeDetails->pluck('employee_detail.getdivision.division_name')->implode(',');
        $employeeCodes = $employeeDetails->pluck('employee_detail.employee_codes')->implode(',');

        // Map parent details
        $parentNames = $parentDetails->pluck('parent_detail.name')->implode(',');
        $parentIds = $parentDetails->pluck('parent_id')->implode(',');

        // Return mapped data
        $response = [
            optional($data->created_at)->format('d-m-Y'),
            $data->id,
            $data->customer_code,
            $data->active,
            optional($data->customertypes)->customertype_name,
            optional($data->createdbyname)->name ?? 'Self',
            $data->name,
            $parentNames,
            $data->first_name,
            $data->last_name,
            $data->mobile,
            $data->contact_number,
            $data->email,
            optional($data->customeraddress)->address1,
            optional(UserActivity::where('customerid', $data->id)->first())->address,
            optional(optional($data->customeraddress)->pincodename)->pincode, // Proper nesting
            optional($data->customeraddress)->zipcode,
            optional($data->customeraddress)->landmark,
            optional(optional($data->customeraddress)->cityname)->city_name, // Proper nesting
            optional(optional($data->customeraddress)->districtname)->district_name, // Proper nesting
            optional(optional($data->customeraddress)->statename)->state_name, // Proper nesting
            optional($data->customerdetails)->grade,
            optional($data->customerdetails)->visit_status,
            optional($data->customerdetails)->gstin_no,
            optional($data->customerdetails)->aadhar_no,
            optional($data->customerdetails)->pan_no,
            optional($data->customerdetails)->otherid_no,
            $data->profile_image,
            $employeeCodes,
            $employeeNames,
            $designations,
            $branches,
            $divisions,
            $data->latitude,
            $data->longitude,
            $employeeIds,
            $parentIds,
            optional($data->customeraddress)->pincode_id,
            optional($data->customeraddress)->city_id,
            optional($data->customeraddress)->district_id,
            optional($data->customeraddress)->state_id,
            $data->customertype,
            $data->working_status,
            $data['creation_date'],
            $data['sap_code'],
        ];


        if (!empty($this->division_users)) {
            if ($this->filters['division_id'] == '10') {
                foreach ($this->pumpD as $key => $value) {
                    $names = [];
                    foreach ($data->getemployeedetail as $item) {
                        $roleNames = $item->employee_detail?->roles?->pluck('name')->unique()->toArray() ?? [];
                        if (in_array($value, $roleNames)) {
                            $names[] = $item->employee_detail->name;
                        }
                    }
                    if (!empty($names)) {
                        $response[] = implode(', ', $names);
                    } else {
                        $response[] = '-';
                    }
                }
            } else if ($this->filters['division_id'] == '3') {
                foreach ($this->fanD as $key => $value) {
                    $names = [];
                    foreach ($data->getemployeedetail as $item) {
                        $roleNames = $item->employee_detail?->roles?->pluck('name')->unique()->toArray() ?? [];
                        if (in_array($value, $roleNames)) {
                            $names[] = $item->employee_detail->name;
                        }
                    }
                    if (!empty($names)) {
                        $response[] = implode(', ', $names);
                    } else {
                        $response[] = '-';
                    }
                }
            } else if ($this->filters['division_id'] == '4') {
                foreach ($this->agriD as $key => $value) {
                    $names = [];
                    foreach ($data->getemployeedetail as $item) {
                        $roleNames = $item->employee_detail?->roles?->pluck('name')->unique()->toArray() ?? [];
                        if (in_array($value, $roleNames)) {
                            $names[] = $item->employee_detail->name;
                        }
                    }
                    if (!empty($names)) {
                        $response[] = implode(', ', $names);
                    } else {
                        $response[] = '-';
                    }
                }
            } else if ($this->filters['division_id'] == '5') {
                foreach ($this->solarD as $key => $value) {
                    $names = [];
                    foreach ($data->getemployeedetail as $item) {
                        $roleNames = $item->employee_detail?->roles?->pluck('name')->unique()->toArray() ?? [];
                        if (in_array($value, $roleNames)) {
                            $names[] = $item->employee_detail->name;
                        }
                    }
                    if (!empty($names)) {
                        $response[] = implode(', ', $names);
                    } else {
                        $response[] = '-';
                    }
                }
            } else if ($this->filters['division_id'] == '9') {
                foreach ($this->serviceD as $key => $value) {
                    $names = [];
                    foreach ($data->getemployeedetail as $item) {
                        $roleNames = $item->employee_detail?->roles?->pluck('name')->unique()->toArray() ?? [];
                        if (in_array($value, $roleNames)) {
                            $names[] = $item->employee_detail->name;
                        }
                    }
                    if (!empty($names)) {
                        $response[] = implode(', ', $names);
                    } else {
                        $response[] = '-';
                    }
                }
            } else if ($this->filters['division_id'] == '7') {
                foreach ($this->tenderD as $key => $value) {
                    $names = [];
                    foreach ($data->getemployeedetail as $item) {
                        $roleNames = $item->employee_detail?->roles?->pluck('name')->unique()->toArray() ?? [];
                        if (in_array($value, $roleNames)) {
                            $names[] = $item->employee_detail->name;
                        }
                    }
                    if (!empty($names)) {
                        $response[] = implode(', ', $names);
                    } else {
                        $response[] = '-';
                    }
                }
            }
        }


        return $response;
    }
}
