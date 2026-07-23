<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AppraisalController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BeatController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CouponsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\FirmTypeController;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\PincodeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SchemeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\TaskDepartmentController;
use App\Http\Controllers\TaskProjectController;
use App\Http\Controllers\TaskPriorityController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserReportingController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VisitReportController;
use App\Http\Controllers\VisitTypeController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchOprningQuantityController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ComplaintTypeController;
use App\Http\Controllers\CustomerKycController;
use App\Http\Controllers\DamageEntryController;
use App\Http\Controllers\DealerAppointmentController;
use App\Http\Controllers\DealerPortalSettingsController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\ExpensesTypeController;
use App\Http\Controllers\SalesWeightageController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EndUserController;
use App\Http\Controllers\GiftBrandController;
use App\Http\Controllers\GiftCategoryController;
use App\Http\Controllers\GiftModelController;
use App\Http\Controllers\GiftSubcategoryController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LoyaltyAppSettingController;
use App\Http\Controllers\NeftRedemptionDetailsController;
use App\Http\Controllers\TransactionHistoryController;
use App\Http\Controllers\OrderSchemeController;
use App\Http\Controllers\RedemptionController;
use App\Http\Controllers\WarrantyActivationController;
use App\Http\Controllers\SalesTargetUsersController;
use App\Http\Controllers\MobileUserLoginDetailsController;
use App\Http\Controllers\NewJoiningController;
use App\Http\Controllers\ServiceBillController;
use App\Http\Controllers\ServiceChargeProductsController;
use App\Http\Controllers\FieldKonnectAppSettings;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MarketIntelligencesFieldController;
use App\Http\Controllers\MspActivityController;
use App\Http\Controllers\PrimarySchemeController;
use App\Http\Controllers\ResignationController;
use App\Http\Controllers\SapStockController;
use App\Http\Controllers\WareHouseController;
use App\Http\Controllers\PlannedSOPController;
use App\Http\Controllers\ClaimGenerationController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadContactsController;
use App\Http\Controllers\LeadNotesController;
use App\Http\Controllers\LeadTasksController;
use App\Http\Controllers\LeadOpportunitiesController;
use App\Http\Controllers\LeadOpportunitiesStatusController;
use App\Http\Controllers\ServiceBillComplaintType;
use App\Http\Controllers\OpeningStockController;
use App\Http\Controllers\PowerBiSettingController;
use App\Http\Controllers\TaxInvoiceController;
use App\Models\DealerPortalSettings;
use App\Models\PowerBiSetting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('aboutus', function () {
    return view('aboutus');
});
Route::get('aboutus/mission', function () {
    return view('mission');
});
Route::get('aboutus/consulting', function () {
    return view('consulting');
});
Route::get('aboutus/abridgemspl', function () {
    return view('abridgemspl');
});
Route::get('aboutus/abridgeit', function () {
    return view('abridgeit');
});
Route::get('contactus', function () {
    return view('contactus');
});
Route::get('/privcay-policy', [NewJoiningController::class, 'privacyPolicy'])->name('privacyPolicy');

//New Joining without auth Route
Route::get('/new-joining-form', [NewJoiningController::class, 'create'])->name('joining-form');
Route::get('/new-joining-thanks', [NewJoiningController::class, 'thanks'])->name('joining-thanks');
Route::post('/new-joining-submit', [NewJoiningController::class, 'store'])->name('joining-form.store');

//Dealer Appointment without auth Route
Route::get('/dealer-appointment-form', [DealerAppointmentController::class, 'create'])->name('dealer-appointment-form');
Route::get('/dealer-appointment-kyc-form', [DealerAppointmentController::class, 'create_kyc'])->name('dealer-appointment-kyc-form');
Route::get('/dealer-appointment-thanks', [DealerAppointmentController::class, 'thanks'])->name('dealer-appointment-thanks');
Route::post('/dealer-appointment-submit', [DealerAppointmentController::class, 'store'])->name('dealer-appointment-form.store');
Route::post('/dealer-appointment-kyc-submit', [DealerAppointmentController::class, 'kyc_store'])->name('dealer-appointment-kyc-form.store');


Route::group(['middleware' => ['auth']], function () {
    Route::get('/dealer_account_statement', function () {
        abort_if(Gate::denies('dealer_account_statement'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('work_in_progress');
    });
    Route::get('/sales_summary_dashboard', function () {
        abort_if(Gate::denies('sales_summary_dashboard'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $url = PowerBiSetting::where('key', 'sales')->first()->value;
        return view('dashboard.sales_summary_dashboard', compact('url'));
    });
    Route::get('/employee_expense_report', function () {
        abort_if(Gate::denies('employee_expense_report_dasboard'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $url = PowerBiSetting::where('key', 'employee_expense')->first()->value;
        return view('dashboard.employee_expense_report', compact('url'));
    });
    
    //Lead Management
    Route::resource('leads', LeadController::class);
    Route::post('leads/getLeads', [LeadController::class, 'getLeads'])->name('leads.getLeads');
    Route::post('lead-upload', [LeadController::class, 'upload'])->name('leads.upload');
    Route::any('lead-template', [LeadController::class, 'template'])->name('leads.template');
    Route::post('leads/searchExistsLead', [LeadController::class, 'searchExistsLead'])->name('leads.searchExistsLead');
    Route::post('leads/storeAddress', [LeadController::class, 'storeAddress'])->name('leads.storeAddress');
    Route::post('leads/assignLead', [LeadController::class, 'assignLead'])->name('leads.assignLead');
    Route::post('leads/deleteLead', [LeadController::class, 'deleteLead'])->name('leads.deleteLead');

    Route::post('leads/uploadleadFiles', [LeadController::class, 'uploadleadFiles'])->name('leads.uploadleadFiles');
    Route::get('leads-deleteMedia', [LeadController::class, 'deleteMedia'])->name('leads-deleteMedia');
    Route::get('leads-exportLeads', [LeadController::class, 'exportLeads'])->name('leads-exportLeads');
    Route::post('leads/changeStatus', [LeadController::class, 'changeStatus'])->name('leads.changeStatus');
    Route::post('convert_lead', [LeadController::class, 'convert_lead'])->name('leads.convert');
    Route::any('lead_visit_report', [LeadController::class, 'visit_report'])->name('leads.visit.report');
    Route::any('lead_checkin-download', [LeadController::class, 'visit_report_download'])->name('leads.visit.report_download');

    //Contacts Management
    Route::resource('lead-contacts', LeadContactsController::class);
    Route::post('lead-contacts/getLeadContacts', [LeadContactsController::class, 'getLeadContacts'])->name('lead-contacts.getLeadContacts');
    Route::get('contacts-exportContacts', [LeadContactsController::class, 'exportContacts'])->name('contacts-exportContacts');
     Route::post('lead-contacts/checkboxAction', [LeadContactsController::class, 'checkboxAction'])->name('lead-contacts.checkboxAction');
     Route::post('lead-contacts-upload', [LeadContactsController::class, 'upload'])->name('lead.contacts.upload');
    Route::any('lead-contacts-template', [LeadContactsController::class, 'template'])->name('lead.contacts.template');


    // LeadNotesController Management
    Route::resource('lead-notes', LeadNotesController::class);

    //OPPORTUNITIES STATUS
    Route::resource('lead-opportunities-status', LeadOpportunitiesStatusController::class);


    Route::resource('lead-tasks', LeadTasksController::class);
    Route::get('tasks-exportTasks', [LeadTasksController::class, 'exportTasks'])->name('tasks-exportTasks');
    Route::post('lead-tasks/checkboxAction', [LeadTasksController::class, 'checkboxAction'])->name('lead-tasks.checkboxAction');
    Route::post('lead-tasks-change-status', [LeadTasksController::class, 'change_status'])->name('lead-tasks.change_status');

    
    Route::post('lead-tasks/getLeadTasks', [LeadTasksController::class, 'getLeadTasks'])->name('lead-tasks.getLeadTasks');
    
    Route::resource('lead-opportunities', LeadOpportunitiesController::class);
    Route::post('lead-opportunitiess/getLeadContacts', [LeadOpportunitiesController::class, 'getCardData'])->name('lead-opportunities.getCardData');
    Route::post('lead-opportunitiess/updateCardStatus', [LeadOpportunitiesController::class, 'updateCardStatus'])->name('lead-opportunities.updateCardStatus');
    
    Route::post('lead-opportunitiess/getsingleData', [LeadOpportunitiesController::class, 'getsingleData'])->name('lead-opportunities.getsingleData');

    //Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('/dealer_dashboard', [DashboardController::class, 'dealer_dashboard']);
    Route::post('dashboardData', [DashboardController::class, 'dashboardData']);
    Route::post('travelSummaryData', [DashboardController::class, 'travelSummaryData']);
    Route::post('visitSummaryData', [DashboardController::class, 'visitSummaryData']);
    Route::post('couponSummaryData', [DashboardController::class, 'couponSummaryData']);
    Route::post('orderSummaryData', [DashboardController::class, 'orderSummaryData']);
    Route::post('salesSummaryData', [DashboardController::class, 'salesSummaryData']);
    Route::post('activityDashboardCount', [DashboardController::class, 'activityDashboardCount']);
    Route::post('secondarySalesKpiData', [DashboardController::class, 'secondarySalesKpiData']);

    // Secondary Sales Dashboard
    Route::get('secondary_dashboard/sales', [DashboardController::class, 'secondary_dashboard_sales'])->name('secondary_dashboard.sales');
    Route::any('secondary_dashboard/sales/list', [DashboardController::class, 'secondary_dashboard_sales_list'])->name('secondary_dashboard.sales.list');

    // Primary Sales Dashboard
    Route::any('primary_sales_template', [DashboardController::class, 'primary_sales_template'])->name('primary_sales_template');
    Route::post('primary_sales/upload', [DashboardController::class, 'primary_sales_upload'])->name('primary_sales.upload');
    Route::any('primary_sales/download', [DashboardController::class, 'primary_sales_download'])->name('primary_sales.download');
    Route::any('primary_dashboard/sales/list', [DashboardController::class, 'primary_dashboard_sales_list'])->name('primary_dashboard.sales.list');
    Route::post('secondary_dashboard/total_order_value', [DashboardController::class, 'total_order_value'])->name('secondary_dashboard.total_order_value');
    Route::post('primarySalesKpiData', [DashboardController::class, 'primarySalesKpiData']);

    // Product Analysis Branch
    Route::any('reports/product_analysis_branch', [ReportController::class, 'product_analysis_branch']);
    Route::any('product_analysis_branch/download', [ReportController::class, 'product_analysis_branch_download'])->name('product_analysis_branch.download');
    Route::any('product_analysis_branch/list', [ReportController::class, 'product_analysis_branch_list'])->name('product_analysis_branch.list');

    // Product Analysis Qty
    Route::any('reports/product_analysis_qty', [ReportController::class, 'product_analysis_qty']);
    Route::any('product_analysis_qty/download', [ReportController::class, 'product_analysis_qty_download'])->name('product_analysis_qty.download');
    Route::any('product_analysis_qty/list', [ReportController::class, 'product_analysis_qty_list'])->name('product_analysis_qty.list');

    // Product Analysis Value
    Route::any('reports/product_analysis_value', [ReportController::class, 'product_analysis_value']);
    Route::any('product_analysis_value/download', [ReportController::class, 'product_analysis_value_download'])->name('product_analysis_value.download');
    Route::any('product_analysis_value/list', [ReportController::class, 'product_analysis_value_list'])->name('product_analysis_value.list');

    // Group Wise Analysis
    Route::any('reports/group_wise_analysis', [ReportController::class, 'group_wise_analysis']);
    Route::any('group_wise_analysis/download', [ReportController::class, 'group_wise_analysis_download'])->name('group_wise_analysis.download');
    Route::any('group_wise_analysis/list', [ReportController::class, 'group_wise_analysis_list'])->name('group_wise_analysis.list');

    // Per Employee Costing
    Route::any('reports/per_employee_costing', [ReportController::class, 'per_employee_costing'])->name('per_employee_costing');
    Route::any('per_employee_costing/download', [ReportController::class, 'per_employee_costing_download'])->name('per_employee_costing.download');

    // Top Dealer
    Route::any('reports/top_dealer', [ReportController::class, 'top_dealer']);
    Route::any('top_dealer/download', [ReportController::class, 'top_dealer_download'])->name('top_dealer.download');
    Route::any('top_dealer/list', [ReportController::class, 'top_dealer_list'])->name('top_dealer.list');

    // Dealer Growth
    Route::any('reports/dealer_growth', [ReportController::class, 'dealer_growth']);
    Route::any('dealer_growth/download', [ReportController::class, 'dealer_growth_download'])->name('dealer_growth.download');
    Route::any('dealer_growth/list', [ReportController::class, 'dealer_growth_list'])->name('dealer_growth.list');

    // New Dealer Sale
    Route::any('reports/new_dealer_sale', [ReportController::class, 'new_dealer_sale']);
    Route::any('new_dealer_sale/download', [ReportController::class, 'new_dealer_sale_download'])->name('new_dealer_sale.download');
    Route::any('new_dealer_sale/list', [ReportController::class, 'new_dealer_sale_list'])->name('new_dealer_sale.list');

    // User Incentive
    Route::any('reports/user_incentive', [ReportController::class, 'user_incentive']);
    Route::any('user_incentive/download', [ReportController::class, 'user_incentive_download'])->name('user_incentive.download');
    Route::any('user_incentive/list', [ReportController::class, 'user_incentive_list'])->name('user_incentive.list');

    // Customer Outstanting
    Route::any('reports/customer_outstanting_template', [ReportController::class, 'customer_outstanting_template'])->name('reports.customer_outstanting_template');
    Route::post('reports/customer_outstanting/upload', [ReportController::class, 'customer_outstanting_upload'])->name('reports.customer_outstanting.upload');
    Route::any('reports/customer_outstanting/download', [ReportController::class, 'customer_outstanting_download'])->name('reports.customer_outstanting.download');
    Route::any('reports/customer_outstanting', [ReportController::class, 'customer_outstanting'])->name('reports.customer_outstanting');

    //Marketing
    Route::get('marketings', [MarketingController::class, 'index'])->name('marketing.index');
    Route::get('marketings/create', [MarketingController::class, 'create'])->name('marketing.create');
    Route::any('marketings/download', [MarketingController::class, 'download'])->name('marketing.download');
    Route::get('marketings_template', [MarketingController::class, 'marketings_template'])->name('marketing.template');
    Route::any('marketings_upload', [MarketingController::class, 'marketings_upload'])->name('marketing.upload');
    Route::any('marketings_new_dealer', [MarketingController::class, 'marketings_new_dealer'])->name('marketing.new_dealer');
    Route::any('dealer-board-installation', [MarketingController::class, 'dealer_board_installation'])->name('marketing.dealer_board_installation');
    Route::any('dealer-welcome-kit', [MarketingController::class, 'dealer_welcome_kit'])->name('marketing.dealer_welcome_kit');
    Route::any('marketing-dealer-appointment/download', [MarketingController::class, 'new_dealer_download'])->name('marketing.new_dealer_download');

    //Customers
    Route::resource('customertype', CustomerTypeController::class);
    Route::post('customertype-active', [CustomerTypeController::class, 'active'])->name('customertype.active');
    Route::any('customertype-download', [CustomerTypeController::class, 'download'])->name('customertype.download');
    Route::resource('firmtype', FirmTypeController::class);
    Route::post('firmtype-active', [FirmTypeController::class, 'active'])->name('firmtype.active');
    Route::any('firmtype-download', [FirmTypeController::class, 'download'])->name('firmtype.download');
    Route::resource('customers', CustomerController::class);
    Route::any('customers-download', [CustomerController::class, 'download'])->name('customers.download');
    Route::any('customers-template', [CustomerController::class, 'template'])->name('customers.template');
    Route::post('customers-upload', [CustomerController::class, 'upload'])->name('customers.upload');
    Route::post('customers-active', [CustomerController::class, 'active'])->name('customers.active');
    Route::any('customers-survey', [CustomerController::class, 'survey'])->name('customers.survey');
    Route::any('survey-download', [CustomerController::class, 'surveyDownload'])->name('survey-download');
    Route::any('customer_balance', [CustomerController::class, 'customer_balance'])->name('customer_balance');
    Route::any('customer_balance/update', [CustomerController::class, 'customer_balance_update'])->name('customer_balance.update');
    Route::any('customer_balance/list', [CustomerController::class, 'customer_balance_list'])->name('customer_balance.list');

    //Ware House Routs
    Route::resource('ware_house', WareHouseController::class);

    //SAP Stock Routs
    Route::any('sap_stock', [SapStockController::class, 'index'])->name('sap_stock.index');

    //End User Routs
    Route::resource('end_user', EndUserController::class);
    Route::post('end-users-active', [EndUserController::class, 'active'])->name('end-users.active');
    Route::post('end-user/download', [EndUserController::class, 'download'])->name('end-users.download');

    //
    Route::any('distributors', [CustomerController::class, 'distributors'])->name('distributors.index');
    Route::any('distributors/create', [CustomerController::class, 'createDistributor'])->name('distributors.create');
    Route::any('distributor-download', [CustomerController::class, 'distributordownload'])->name('distributor-download');
    //Country
    Route::resource('country', CountryController::class);
    Route::post('country-active', [CountryController::class, 'active'])->name('country.active');
    Route::any('country-download', [CountryController::class, 'download'])->name('country.download');
    Route::any('country-template', [CountryController::class, 'template'])->name('country.template');
    Route::post('country-upload', [CountryController::class, 'upload'])->name('country.upload');

    //State
    Route::resource('state', StateController::class);
    Route::any('state-download', [StateController::class, 'download'])->name('state.download');
    Route::any('state-template', [StateController::class, 'template'])->name('state.template');
    Route::post('state-upload', [StateController::class, 'upload'])->name('state.upload');
    Route::post('state-active', [StateController::class, 'active'])->name('state.active');

    //District
    Route::resource('district', DistrictController::class);
    Route::any('district-download', [DistrictController::class, 'download'])->name('district.download');
    Route::any('district-template', [DistrictController::class, 'template'])->name('district.template');
    Route::post('district-upload', [DistrictController::class, 'upload'])->name('district.upload');
    Route::post('district-active', [DistrictController::class, 'active'])->name('district.active');
    //City
    Route::resource('city', CityController::class);
    Route::any('city-download', [CityController::class, 'download'])->name('city.download');
    Route::any('city-template', [CityController::class, 'template'])->name('city.template');
    Route::post('city-upload', [CityController::class, 'upload'])->name('city.upload');
    Route::post('city-active', [CityController::class, 'active'])->name('city.active');
    //Pincode
    Route::resource('pincode', PincodeController::class);
    Route::any('pincode-download', [PincodeController::class, 'download'])->name('pincode.download');
    Route::any('pincode-template', [PincodeController::class, 'template'])->name('pincode.template');
    Route::post('pincode-upload', [PincodeController::class, 'upload'])->name('pincode.upload');
    Route::post('pincode-active', [PincodeController::class, 'active'])->name('pincode.active');
    // Roles
    Route::delete('roles/destroy', [RolesController::class, 'massDestroy'])->name('roles.massDestroy');
    Route::resource('roles', RolesController::class);
    Route::any('roles-download', [RolesController::class, 'download'])->name('roles.download');
    Route::any('roles-template', [RolesController::class, 'template'])->name('roles.template');
    Route::post('roles-upload', [RolesController::class, 'upload'])->name('roles.upload');
    //braches
    Route::resource('branches', BranchController::class);
    Route::any('branch_report/download', [BranchController::class, 'branch_report_download'])->name('branch_report.download');
    //Division
    Route::resource('division', DivisionController::class);
    Route::any('division_report/download', [DivisionController::class, 'division_report_download'])->name('division_report.download');
    //Designation
    Route::resource('designation', DesignationController::class);
    //holidays
    Route::resource('holidays', HolidayController::class);

    // Users
    Route::delete('users/destroy', [UsersController::class, 'massDestroy'])->name('users.massDestroy');
    Route::resource('users', UsersController::class);
    Route::any('users-download', [UsersController::class, 'download'])->name('users.download');
    Route::any('users-template', [UsersController::class, 'template'])->name('users.template');
    Route::post('users-upload', [UsersController::class, 'upload'])->name('users.upload');
    Route::post('users-active', [UsersController::class, 'active'])->name('users.active');
    Route::any('usercity', [UsersController::class, 'userCity'])->name('users.usercity');
    Route::post('usercity-upload', [UsersController::class, 'userCityUpload'])->name('usercity.upload');
    Route::any('usercity-download', [UsersController::class, 'userCitydownload'])->name('usercity.download');
    Route::any('customer-user', [UsersController::class, 'CustomerUserView'])->name('customers.user.create');
    //Targets
    Route::resource('reportings', UserReportingController::class);
    //Sales Weightege
    Route::resource('sales_weightage', SalesWeightageController::class);
    Route::post('salesweightage/multiupdate', [SalesWeightageController::class, 'multiupdate'])->name('salesweightage.multiupdate');

    //Appraisal
    Route::get('appraisal/{id}/create', [AppraisalController::class, 'create']);
    Route::get('appraisal/index', [AppraisalController::class, 'index'])->name('appraisal.index');
    Route::any('appraisal/store', [AppraisalController::class, 'store'])->name('appraisal.store');
    Route::post('appraisal/update', [AppraisalController::class, 'update'])->name('appraisal.update');
    Route::any('appraisal-download', [AppraisalController::class, 'download'])->name('appraisal.download');
    Route::any('getappraisal', [AppraisalController::class, 'getappraisal'])->name('appraisal.getappraisal');
    Route::any('geSalesWeightages', [AppraisalController::class, 'geSalesWeightages'])->name('appraisal.geSalesWeightages');
    Route::get('appraisal/{id}/{year}/edit', [AppraisalController::class, 'edit']);

    Route::get('appraisal/{id}/{year}/viewappraisal', [AppraisalController::class, 'viewappraisal']);

    Route::get('appraisal/{id}/{year}/appraisalApprove', [AppraisalController::class, 'appraisalApprove']);

    Route::post('appraisals/updateappraisal', [AppraisalController::class, 'updateappraisal'])->name('appraisals.updateappraisal');

    Route::post('appraisals/updateapproval', [AppraisalController::class, 'updateapproval'])->name('appraisals.updateapproval');





    // Permissions
    Route::delete('permissions/destroy', [PermissionsController::class, 'massDestroy'])->name('permissions.massDestroy');
    Route::resource('permissions', PermissionsController::class);
    Route::any('permissions-download', [PermissionsController::class, 'download'])->name('permissions.download');
    Route::any('permissions-template', [PermissionsController::class, 'template'])->name('permissions.template');
    Route::post('permissions-upload', [PermissionsController::class, 'upload'])->name('permissions.upload');
    //Category Route
    Route::resource('categories', CategoryController::class);
    Route::any('categories-download', [CategoryController::class, 'download'])->name('categories.download');
    Route::any('categories-template', [CategoryController::class, 'template'])->name('categories.template');
    Route::post('categories-upload', [CategoryController::class, 'upload'])->name('categories.upload');
    Route::post('categories-active', [CategoryController::class, 'active'])->name('categories.active');
    //Sub Category
    Route::resource('subcategories', SubCategoryController::class);
    Route::any('subcategories-download', [SubCategoryController::class, 'download'])->name('subcategories.download');
    Route::any('subcategories-template', [SubCategoryController::class, 'template'])->name('subcategories.template');
    Route::post('subcategories-upload', [SubCategoryController::class, 'upload'])->name('subcategories.upload');
    Route::post('subcategories-active', [SubCategoryController::class, 'active'])->name('subcategories.active');

    // Sales Target Users
    Route::get('sales_users/target_users', [SalesTargetUsersController::class, 'sales_target_users'])->name('sales_users.target_users');
    Route::post('sales_users/target_users_upload/upload', [SalesTargetUsersController::class, 'target_users_upload'])->name('sales_users.target_users_upload.upload');
    Route::any('sales_users/target_users_download/download', [SalesTargetUsersController::class, 'target_users_download'])->name('sales_users.target_users_download.download');
    Route::post('sales_users/target_users/list', [SalesTargetUsersController::class, 'sales_target_users_list'])->name('sales_users.target_users.list');
    Route::get('sales_users/target_user/delete', [SalesTargetUsersController::class, 'sales_target_users_delete'])->name('sales_users.target_user.delete');
    Route::any('sales-target-users-template', [SalesTargetUsersController::class, 'template'])->name('sales_users.target_user.template');
    Route::get('sales-target-users/{id}', [SalesTargetUsersController::class, 'update_target_user_modal'])->name('sales-target-users');
    Route::post('sales-target-users/store', [SalesTargetUsersController::class, 'update_target_user_updte'])->name('sales-target-users.store');

    // Target achievement distributed dealer wise
    Route::get('sales_dealer/target_dealers', [SalesTargetUsersController::class, 'sales_target_dealers'])->name('sales_dealer.target_dealers');
    Route::post('sales_dealer/target_achievement/list', [SalesTargetUsersController::class, 'sales_dealers_target_achievement'])->name('sales_dealer.target_achievement');
    Route::any('sales_dealer/target_dealers/download', [SalesTargetUsersController::class, 'sales_target_dealers_download'])->name('sales_dealer.target_dealers.download');
    Route::any('sales-target-dealers-template', [SalesTargetUsersController::class, 'sales_dealers_target_template'])->name('sales_dealer.target_dealers.template');
    Route::get('sales-target-dealer/{id}', [SalesTargetUsersController::class, 'update_target_dealer_modal'])->name('sales-target-dealer');
    Route::post('sales-dealer-users/store', [SalesTargetUsersController::class, 'update_target_dealer_update'])->name('sales-target-dealer.store');
    Route::get('sales_dealer/delete', [SalesTargetUsersController::class, 'sales_target_dealer_delete'])->name('sales_dealer.delete');
    Route::post('sales_dealers/target/upload', [SalesTargetUsersController::class, 'sales_target_dealers_upload'])->name('sales_target_dealers_upload.target.upload');

    // Branch wise sales target
    Route::get('branches_sales_target', [SalesTargetUsersController::class, 'branches_sales_target'])->name('branches_sales_target');
    Route::any('branch-target-template', [SalesTargetUsersController::class, 'branch_target_template'])->name('branch-target-template');
    Route::post('branch_target_upload', [SalesTargetUsersController::class, 'branch_target_upload'])->name('branch_target_upload');
    Route::post('branch_target/list', [SalesTargetUsersController::class, 'branch_target_list'])->name('branch_target.list');
    Route::get('branch_target/delete', [SalesTargetUsersController::class, 'branch_target_delete'])->name('branch_target.delete');
    Route::any('branch_target/download', [SalesTargetUsersController::class, 'branch_target_download'])->name('branch_target.download');

    // Branch wise achievement
    Route::any('branch/achievement/template', [SalesTargetUsersController::class, 'branch_achievement_template'])->name('branch.achievement.template');
    Route::post('branch/achievement/upload', [SalesTargetUsersController::class, 'branch_achievement_upload'])->name('branch.achievement.upload');

    // Sales dealers distributors achievement
    Route::any('sales-dealers-achievement-template', [SalesTargetUsersController::class, 'sales_dealers_achievement_template'])->name('sales.dealers.achievement.template');
    Route::post('sales-dealers/achievement/upload', [SalesTargetUsersController::class, 'sales_dealers_achievement_upload'])->name('sales.dealers.achievement.upload');
    Route::get('sales/achievement/download', [SalesTargetUsersController::class, 'achievement_download'])->name('sales_users.achievement.download');

    // Sales Achievement
    Route::any('sales-achievement-template', [SalesTargetUsersController::class, 'achievement_template'])->name('sales.achievement.template');
    Route::post('sales/achievement/upload', [SalesTargetUsersController::class, 'achievement_upload'])->name('sales.achievement.upload');
    Route::get('sales/achievement/download', [SalesTargetUsersController::class, 'achievement_download'])->name('sales_users.achievement.download');

    //Brand
    Route::resource('brands', BrandController::class);
    Route::any('brands-download', [BrandController::class, 'download'])->name('brands.download');
    Route::any('brands-template', [BrandController::class, 'template'])->name('brands.template');
    Route::post('brands-upload', [BrandController::class, 'upload'])->name('brands.upload');
    Route::post('brands-active', [BrandController::class, 'active'])->name('brands.active');
    //UnitMeasure
    Route::resource('units', UnitController::class);
    Route::any('units-download', [UnitController::class, 'download'])->name('units.download');
    Route::any('units-template', [UnitController::class, 'template'])->name('units.template');
    Route::post('units-upload', [UnitController::class, 'upload'])->name('units.upload');
    Route::post('units-active', [UnitController::class, 'active'])->name('units.active');
    //Products
    Route::resource('products', ProductController::class);
    Route::any('products-download', [ProductController::class, 'download'])->name('products.download');
    Route::any('products-template', [ProductController::class, 'template'])->name('products.template');
    Route::post('products-upload', [ProductController::class, 'upload'])->name('products.upload');
    Route::post('products-active', [ProductController::class, 'active'])->name('products.active');
    // Route::any('stockinfo', [ProductController::class, 'stockInfo'])->name('products.stockinfo');
    // Route::any('stockupdate', [ProductController::class, 'stockUpdate'])->name('products.stockupdate');
    Route::any('production', [ProductController::class, 'production'])->name('products.production');
    Route::any('productionupdate', [ProductController::class, 'productionUpdate'])->name('products.productionupdate');
    Route::any('products-list', [ProductController::class, 'productList']);
    Route::any('checkProductCode', [ProductController::class, 'checkProductCode'])->name('checkProductCode');
    Route::any('dealer_product', [ProductController::class, 'dealer_product'])->name('dealer_product');
    Route::any('dealer_product_list/{category_id}', [ProductController::class, 'dealer_product_list'])->name('category.products');

    // Customer Outstanting
    Route::any('stock', [ProductController::class, 'stock'])->name('stock');
    Route::any('stock_template', [ProductController::class, 'stock_template'])->name('stock.template');
    Route::post('stock/upload', [ProductController::class, 'stock_upload'])->name('stock.upload');
    Route::any('stock/download', [ProductController::class, 'stock_download'])->name('stock.download');

    // Customer Outstanting
    Route::any('msp_activity', [MspActivityController::class, 'index'])->name('msp_activity.index');
    Route::any('msp_activity_template', [MspActivityController::class, 'msp_activity_template'])->name('msp_activity.template');
    Route::post('msp_activity/upload', [MspActivityController::class, 'msp_activity_upload'])->name('msp_activity.upload');
    Route::any('msp_activity/download', [MspActivityController::class, 'msp_activity_download'])->name('msp_activity.download');

    //Orders
    Route::resource('orders', OrderController::class);
    Route::any('orders-download', [OrderController::class, 'download'])->name('orders.download');
    Route::any('orders-template', [OrderController::class, 'template'])->name('orders.template');
    Route::post('orders-upload', [OrderController::class, 'upload'])->name('orders.upload');
    Route::post('orders-active', [OrderController::class, 'active'])->name('orders.active');
    Route::any('ordersInfo', [OrderController::class, 'ordersInfo'])->name('orders.info');
    Route::get('ordertopoint', [OrderController::class, 'ordertopoint']);
    Route::any('expected-delivery', [OrderController::class, 'expectedDelivery'])->name('orders.expecteddelivery');
    Route::any('submit-expected-delivery', [OrderController::class, 'submitExpectedDelivery'])->name('orders.submitexpecteddelivery');
    Route::any('order-dispatched/{id}', [OrderController::class, 'orderDispatched'])->name('orders.dispatched');
    Route::any('order-partially-dispatched/{id}', [OrderController::class, 'orderPartiallyDispatched'])->name('orders.partiallydispatched');
    Route::any('order-cancle/{id}', [OrderController::class, 'orderCancle'])->name('orders.orderCancle');
    Route::any('order-pendding/{id}', [OrderController::class, 'orderPendding'])->name('orders.orderPendding');
    Route::post('submit-dispatched', [OrderController::class, 'submitDispatched'])->name('orders.submitdispatched');
    Route::any('order-detail-delete', [OrderController::class, 'deleteOrderDtails'])->name('orders.deletedetails');

    Route::post('submit-fullydispatched', [OrderController::class, 'submitFullyDispatched'])->name('orders.submitFullyDispatched');


    //Targets
    Route::resource('targets', TargetController::class);
    //Secondry Sales
    Route::resource('sales', SalesController::class);
    Route::any('sales-download', [SalesController::class, 'download'])->name('sales.download');
    Route::any('sales-template', [SalesController::class, 'template'])->name('sales.template');
    Route::post('sales-upload', [SalesController::class, 'upload'])->name('sales.upload');

    Route::any('saleApproval/{sales_id}', [SalesController::class, 'saleApproval'])->name('sales.saleApproval');
    Route::post('sales-active', [SalesController::class, 'active'])->name('sales.active');
    Route::any('salesInfo', [SalesController::class, 'salesInfo'])->name('sales.info');
    //Schemes
    Route::resource('schemes', SchemeController::class);
    Route::any('schemes-download', [SchemeController::class, 'download'])->name('schemes.download');
    Route::any('schemes-template', [SchemeController::class, 'template'])->name('schemes.template');
    Route::post('schemes-upload', [SchemeController::class, 'upload'])->name('schemes.upload');
    Route::post('schemes-active', [SchemeController::class, 'active'])->name('schemes.active');
    Route::any('scheme-product-list', [SchemeController::class, 'scheme_product_list'])->name('scheme.product.ist');
    Route::get('scheme-product-pdf', [SchemeController::class, 'generatePdf'])->name('scheme.product-.pdf');
    //Redeemption Products
    Route::resource('gifts', GiftController::class);
    Route::any('gifts-download', [GiftController::class, 'download'])->name('gifts.download');
    Route::any('gifts-template', [GiftController::class, 'template'])->name('gifts.template');
    Route::post('gifts-upload', [GiftController::class, 'upload'])->name('gifts.upload');
    Route::post('gifts-active', [GiftController::class, 'active'])->name('gifts.active');
    Route::get('gifts-pdf', [GiftController::class, 'generatePdf'])->name('gifts.pdf');
    //Wallets
    Route::resource('wallets', WalletController::class);
    Route::any('wallets-download', [WalletController::class, 'download'])->name('wallets.download');
    Route::any('wallets-template', [WalletController::class, 'template'])->name('wallets.template');
    Route::post('wallets-upload', [WalletController::class, 'upload'])->name('wallets.upload');
    Route::post('wallets-active', [WalletController::class, 'active'])->name('wallets.active');
    Route::any('redeemedPoint', [WalletController::class, 'redeemedPoint'])->name('wallets.redeemedPoint');
    Route::any('walletsInfo', [WalletController::class, 'walletsInfo'])->name('wallets.info');
    //Settings
    Route::get('settings', [SettingController::class, 'index']);
    Route::any('settings-download', [SettingController::class, 'download'])->name('settings.download');
    Route::any('settings-template', [SettingController::class, 'template'])->name('settings.template');
    Route::post('settings-upload', [SettingController::class, 'upload'])->name('settings.upload');

    Route::any('settingSubmit', [SettingController::class, 'settingSubmit']);
    //Settings
    Route::resource('status', StatusController::class);
    Route::any('status-download', [StatusController::class, 'download'])->name('status.download');
    Route::any('status-template', [StatusController::class, 'template'])->name('status.template');
    Route::post('status-upload', [StatusController::class, 'upload'])->name('status.upload');
    Route::post('status-active', [StatusController::class, 'active'])->name('status.active');
    //CustmersLogin
    Route::resource('coupons', CouponsController::class);
    Route::any('coupons-download', [CouponsController::class, 'download'])->name('coupons.download');
    Route::any('coupons-template', [CouponsController::class, 'template'])->name('coupons.template');
    Route::post('coupons-upload', [CouponsController::class, 'upload'])->name('coupons.upload');

    Route::any('couponprofile', [CouponsController::class, 'couponprofile'])->name('coupons.couponprofile');
    //CustmersLogin
    Route::any('customersLogin', [CustomerController::class, 'customersLogin'])->name('customers.customersLogin');
    //Beat
    Route::resource('beats', BeatController::class);
    Route::any('beatdetail', [BeatController::class, 'beatdetail'])->name('beats.beatdetail');
    Route::any('beats-download', [BeatController::class, 'download'])->name('beats.download');
    Route::any('beats-template', [BeatController::class, 'template'])->name('beats.template');
    Route::post('beats-upload', [BeatController::class, 'upload'])->name('beats.upload');
    Route::post('add-beatusers', [BeatController::class, 'addBeatUsers'])->name('beats.add-beatusers');
    Route::post('add-beatcustomers', [BeatController::class, 'addBeatCustomer'])->name('beats.add-beatcustomers');
    Route::delete('schedule-delete/{id}', [BeatController::class, 'beatScheduleDelete']);
    Route::post('updateschedule', [BeatController::class, 'beatScheduleUpdate']);
    Route::delete('beatcustomer-delete/{id}', [BeatController::class, 'beatCustomerDelete']);
    Route::delete('beat-user-delete/{id}', [BeatController::class, 'beatUserDelete']);
    Route::any('beats-schedule/{id}', [BeatController::class, 'beatsSchedule']);
    //Current Location
    Route::any('livelocation', [BeatController::class, 'livelocation']);
    //Attendance
    Route::any('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('submitAttendances', [AttendanceController::class, 'submitAttendances'])->name('submitAttendances');
    Route::any('attendancesInfo', [AttendanceController::class, 'attendancesInfo'])->name('attendances.info');
    Route::any('attendance-download', [AttendanceController::class, 'download'])->name('attendance.download');
    Route::any('removePunchout', [AttendanceController::class, 'removePunchout'])->name('removePunchout');
    Route::any('punchoutnow', [AttendanceController::class, 'punchoutnow'])->name('punchoutnow');
    Route::delete('attendances/{id}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');

    Route::any('approveAttendance', [AttendanceController::class, 'approveAttendance'])->name('approveAttendance');
    Route::any('rejectAttendance', [AttendanceController::class, 'rejectAttendance'])->name('rejectAttendance');

    Route::any('attendancesummary-download', [AttendanceController::class, 'attendanceSummaryDownload'])->name('attendancesummary.download');



    //Checkin
    Route::any('checkin', [CheckinController::class, 'index'])->name('checkin.index');
    Route::any('checkin-download', [CheckinController::class, 'download'])->name('checkin.download');
    //Visit Type
    Route::resource('visittypes', VisitTypeController::class);
    Route::any('visittypes-download', [VisitTypeController::class, 'download'])->name('visittypes.download');
    Route::any('visittypes-template', [VisitTypeController::class, 'template'])->name('visittypes.template');
    Route::post('visittypes-upload', [VisitTypeController::class, 'upload'])->name('visittypes.upload');
    Route::post('visittypes-active', [VisitTypeController::class, 'active'])->name('visittypes.active');
    //Visit Reports
    Route::resource('visitreports', VisitReportController::class);
    Route::any('visitreports-download', [VisitReportController::class, 'download'])->name('visitreports.download');
    Route::any('visitreports-template', [VisitReportController::class, 'template'])->name('visitreports.template');
    Route::post('visitreports-upload', [VisitReportController::class, 'upload'])->name('visitreports.upload');
    Route::post('visitreports-active', [VisitReportController::class, 'active'])->name('visitreports.active');
    Route::any('mastervisitreport', [VisitReportController::class, 'masterVisitreport']);
    Route::any('master-visitreports-download', [VisitReportController::class, 'masterVisitreportsDownload']);
    //Notes
    Route::resource('notes', NotesController::class);
    Route::post('notes-active', [NotesController::class, 'active'])->name('notes.active');
    Route::any('notes-download', [NotesController::class, 'download'])->name('notes.download');
    //Tasks
    Route::resource('tasks', TasksController::class);
    Route::any('tasks-download', [TasksController::class, 'download'])->name('tasks.download');
    Route::any('tasks-template', [TasksController::class, 'template'])->name('tasks.template');
    Route::post('tasks-upload', [TasksController::class, 'upload'])->name('tasks.upload');
    Route::post('tasks-active', [TasksController::class, 'active'])->name('tasks.active');
    Route::any('tasks-completed', [TasksController::class, 'completed'])->name('tasks.completed');
    Route::any('tasks-done', [TasksController::class, 'done'])->name('tasks.done');
    Route::any('tasks-reopen', [TasksController::class, 'reopen'])->name('tasks.reopen');
    Route::any('tasksInfo', [TasksController::class, 'tasksInfo'])->name('tasks.info');
    Route::delete('/media/{media}', [TasksController::class, 'deleteMedia'])->name('media.delete');
    Route::get('/tasks/{token}/show', [TasksController::class, 'show'])->name('tasks.show');


    // Tasks Departments
    Route::resource('task-departments', TaskDepartmentController::class);
    // Tasks Projects
    Route::resource('task-projects', TaskProjectController::class);
    // Tasks Priority
    Route::resource('task-priorities', TaskPriorityController::class);

    //Payment
    Route::resource('payments', PaymentController::class);
    Route::any('payments-download', [PaymentController::class, 'download'])->name('payments.download');
    Route::any('payments-template', [PaymentController::class, 'template'])->name('payments.template');
    Route::post('payments-upload', [PaymentController::class, 'upload'])->name('payments.upload');
    Route::any('paymentsInfo', [PaymentController::class, 'paymentsInfo'])->name('payments.info');
    //Supports
    Route::resource('supports', SupportController::class);
    Route::any('supports-download', [SupportController::class, 'download'])->name('supports.download');
    Route::any('supports-template', [SupportController::class, 'template'])->name('supports.template');
    Route::post('supports-upload', [SupportController::class, 'upload'])->name('supports.upload');
    Route::any('supports-assigned', [SupportController::class, 'assigned'])->name('supports.assigned');
    Route::any('supports-response', [SupportController::class, 'response'])->name('supports.response');
    Route::any('supports-message', [SupportController::class, 'message'])->name('supports.message');
    Route::any('supports-closed', [SupportController::class, 'closed'])->name('supports.closed');
    Route::any('supports-reopend', [SupportController::class, 'reopend'])->name('supports.reopend');
    //Proposals
    Route::resource('proposals', ProposalController::class);
    Route::any('proposals-download', [ProposalController::class, 'download'])->name('proposals.download');
    Route::any('proposals-template', [ProposalController::class, 'template'])->name('proposals.template');
    Route::post('proposals-upload', [ProposalController::class, 'upload'])->name('proposals.upload');
    Route::post('proposals-active', [ProposalController::class, 'active'])->name('proposals.active');
    //Estimate
    Route::resource('estimate', EstimateController::class);
    Route::any('estimates-download', [EstimateController::class, 'download'])->name('estimates.download');
    Route::any('estimates-template', [EstimateController::class, 'template'])->name('estimates.template');
    Route::post('estimates-upload', [EstimateController::class, 'upload'])->name('estimates.upload');
    Route::post('estimates-active', [EstimateController::class, 'active'])->name('estimates.active');
    Route::any('estimates_download', [EstimateController::class, 'download'])->name('estimate.export');
    //DataSource
    Route::resource('datasources', DataSourceController::class);
    Route::any('datasources-download', [DataSourceController::class, 'download'])->name('datasources.download');
    Route::any('datasources-template', [DataSourceController::class, 'template'])->name('datasources.template');
    Route::post('datasources-upload', [DataSourceController::class, 'upload'])->name('datasources.upload');
    //Shipments
    Route::resource('shipments', ShipmentController::class);
    Route::any('shipments-download', [ShipmentController::class, 'download'])->name('shipments.download');
    Route::any('shipments-template', [ShipmentController::class, 'template'])->name('shipments.template');
    Route::post('shipments-upload', [ShipmentController::class, 'upload'])->name('shipments.upload');
    Route::post('shipments-active', [ShipmentController::class, 'active'])->name('shipments.active');
    //Courier
    Route::resource('couriers', CourierController::class);
    Route::any('couriers-download', [CourierController::class, 'download'])->name('couriers.download');
    Route::any('couriers-template', [CourierController::class, 'template'])->name('couriers.template');
    Route::post('couriers-upload', [CourierController::class, 'upload'])->name('couriers.upload');
    Route::post('couriers-active', [CourierController::class, 'active'])->name('couriers.active');
    /*============= LeaveType ====================*/
    Route::resource('leavetypes', LeaveTypeController::class);
    /*============= Leave ====================*/
    Route::resource('leaves', LeaveController::class);
    Route::post('leaves-approved', [LeaveController::class, 'approved'])->name('leaves.approved');
    Route::any('leaveapproval', [LeaveController::class, 'leaveApproval'])->name('leaves.approval');
    Route::any('leaverejected', [LeaveController::class, 'leaveRejected'])->name('leaves.rejected');
    Route::post('combo-off-leave', [LeaveController::class, 'comboOffLeave'])->name('comboOffLeave');
    /*============= Team ====================*/
    Route::resource('teams', TeamController::class);
    /*============= Holiday ====================*/
    Route::resource('holiday', HolidayController::class);
    Route::post('holiday-active', [HolidayController::class, 'active'])->name('holiday.active');
    /*============= Meeting ====================*/
    Route::resource('meeting', MeetingController::class);
    Route::post('meeting-active', [MeetingController::class, 'active'])->name('meeting.active');
    /*============= Awards ====================*/
    Route::resource('award', AwardController::class);
    Route::post('award-active', [AwardController::class, 'active'])->name('award.active');
    /*============= Training ====================*/
    Route::resource('training', TrainingController::class);
    Route::post('training-active', [TrainingController::class, 'active'])->name('training.active');
    /*============= Promotion ====================*/
    Route::resource('promotion', PromotionController::class);
    Route::post('promotion-active', [PromotionController::class, 'active'])->name('promotion.active');
    /*============= Project ====================*/
    Route::resource('project', ProjectController::class);
    Route::post('project-active', [ProjectController::class, 'active'])->name('project.active');
    /*============= Event ====================*/

    //Fields
    Route::resource('market_intelligences', MarketIntelligencesFieldController::class);
    Route::any('market_intelligences_download', [MarketIntelligencesFieldController::class, 'download'])->name('market_intelligences.download');
    Route::any('market_intelligences_show/{id}', [MarketIntelligencesFieldController::class, 'show'])->name('market-intelligences.show');
    Route::any('reports/marketIntelligence', [MarketIntelligencesFieldController::class, 'marketIntelligence'])->name('reports.marketIntelligence');

    //Fields
    Route::resource('fields', FieldController::class);
    Route::post('fields-active', [FieldController::class, 'active'])->name('fields.active');
    Route::any('contacts', [ContactController::class, 'index'])->name('contacts.index');
    /*==== Reports ==========*/
    Route::any('reports/beatadherence', [ReportController::class, 'beatadherence']);
    Route::any('reports/adherencesummary', [ReportController::class, 'adherencesummary']);
    Route::any('reports/customervisit', [ReportController::class, 'customervisit']);
    Route::any('counterVisitReportDownload', [ReportController::class, 'counterVisitReportDownload']);
    Route::any('beatAdherenceDetailDownload', [ReportController::class, 'beatAdherenceDetailDownload']);
    Route::any('reports/attendancereport', [ReportController::class, 'attendancereport']);
    Route::any('reports/reports_sale', [UsersController::class, 'reports_sale']);
    Route::any('reports/fos_rating', [UsersController::class, 'fos_rating']);
    Route::any('reports/asm_rating', [UsersController::class, 'asm_rating']);
    Route::any('reports/ch_rating', [UsersController::class, 'ch_rating']);
    Route::post('pms-form', [UsersController::class, 'pms_form']);
    Route::any('reports/primary_sales', [ReportController::class, 'primary_sales']);
    Route::any('reports/secondary_sales', [ReportController::class, 'secondary_sales']);
    Route::get('user_sales_report_download', [UsersController::class, 'user_sales_report_download']);
    Route::get('fos_rating_report_download', [UsersController::class, 'fos_rating_report_download']);
    Route::get('asm_rating_report_download', [UsersController::class, 'asm_rating_report_download']);
    Route::get('ch_rating_report_download', [UsersController::class, 'ch_rating_report_download']);
    Route::any('reports/customersreport', [ReportController::class, 'customersReport']);
    Route::any('reports/loyalty_summary_report', [ReportController::class, 'loyaltySummaryReport'])->name('loyaltySummaryReport');
    Route::any('reports/loyalty_dealer_wise_summary_report', [ReportController::class, 'loyaltyDealerWiseSummaryReport'])->name('loyaltyDealerWiseSummaryReport');
    Route::any('reports/loyalty_retailer_wise_summary_report', [ReportController::class, 'loyaltyRetailerWiseSummaryReport'])->name('loyaltyRetailerWiseSummaryReport');
    Route::any('reports/per_day_counter_visit_report', [ReportController::class, 'perDayCounterVisitReport']);
    // loyalty summary report export
    Route::any('loyalty-summary-report-download', [ReportController::class, 'loyaltySummaryReportDownload'])->name('loyalty-summary-report-download');
    // loyalty summary dealer wise report export
    Route::any('loyalty-delaer-wise-report-download', [ReportController::class, 'loyaltyDealerSummaryReportDownload'])->name('loyalty-delaer-wise-report-download');
    Route::any('loyalty-retailer-wise-report-download', [ReportController::class, 'loyaltyRetailerSummaryReportDownload'])->name('loyalty-retailer-wise-report-download');



    Route::any('reports/fieldactivity', [ReportController::class, 'fieldActivity']);
    Route::any('fieldActivityReportData', [ReportController::class, 'fieldActivityReportData']);
    Route::any('reports/tourprogramme', [ReportController::class, 'tourProgramme']);
    Route::any('tourProgrammeReportData', [ReportController::class, 'tourProgrammeReportData']);
    Route::any('reports/monthlymovement', [ReportController::class, 'monthlyMovement']);
    Route::any('monthlyMovementReportData', [ReportController::class, 'monthlyMovementReportData']);
    Route::any('reports/pointcollections', [ReportController::class, 'pointCollections']);
    Route::any('pointCollectionReportData', [ReportController::class, 'pointCollectionReportData']);
    Route::any('reports/territorycoverage', [ReportController::class, 'territoryCoverage']);
    Route::any('territoryCoverageReportData', [ReportController::class, 'territoryCoverageReportData']);
    Route::any('reports/performanceparameter', [ReportController::class, 'performanceParameter']);
    Route::any('performanceParameterReportData', [ReportController::class, 'performanceParameterReportData']);
    Route::any('reports/asmwisemechanicspoints', [ReportController::class, 'asmWiseMechanicsPoints']);
    Route::any('asmWiseMechanicsPointsReportData', [ReportController::class, 'asmWiseMechanicsPointsReportData']);
    Route::any('reports/targetvssales', [ReportController::class, 'targetVsSales']);
    Route::any('targetvsSaleReportData', [ReportController::class, 'targetvsSaleReportData']);
    Route::any('reports/surveyanalysis', [ReportController::class, 'surveyAnalysis']);
    Route::any('surveyAnalysisReportData', [ReportController::class, 'surveyAnalysisReportData']);
    Route::any('surveyAnalysis-download', [ReportController::class, 'surveyAnalysisDownload']);

    Route::any('reports/gamification', [ReportController::class, 'gamification'])->name('reports.gamification');
    Route::any('customerAnalysis-download', [ReportController::class, 'customerAnalysisDownload']);
    //Report Download
    Route::any('fieldActivity-download', [ReportController::class, 'fieldActivityDownload']);
    Route::any('tourProgramme-download', [ReportController::class, 'tourProgrammeDownload']);
    Route::any('monthlyMovement-download', [ReportController::class, 'monthlyMovementDownload']);
    Route::any('pointCollection-download', [ReportController::class, 'pointCollectionDownload']);
    Route::any('territoryCoverage-download', [ReportController::class, 'territoryCoverageDownload']);
    Route::any('performanceParameter-download', [ReportController::class, 'performanceParameterDownload']);
    Route::any('mechanicsPoints-download', [ReportController::class, 'mechanicsPointsDownload']);
    Route::any('targetAchievement-download', [ReportController::class, 'targetAchievementDownload']);
    Route::any('reports/attendancereportSummary', [ReportController::class, 'attendancereportSummary']);

    //Tours
    Route::resource('tours', TourController::class);
    Route::any('toursInfoUpdate', [TourController::class, 'update'])->name('tours.toursInfoUpdate');
    Route::any('tours-download', [TourController::class, 'download'])->name('tours.download');
    Route::any('tours-template', [TourController::class, 'template'])->name('tourss.template');
    Route::post('tours-upload', [TourController::class, 'upload'])->name('tours.upload');
    Route::post('tours-changeStatus', [TourController::class, 'changeStatus'])->name('tours.changesttus');

    // /Expenses Type
    Route::resource('expenses_type', ExpensesTypeController::class);
    Route::post('expenses-type-active', [ExpensesTypeController::class, 'changeStatus']);

    // /Expenses
    Route::resource('expenses', ExpensesController::class);
    Route::post('expenses-active', [ExpensesController::class, 'changeStatus']);
    Route::any('map-all', [ExpensesController::class, 'all_map']);
    Route::post('expenses-checked-by-reporting', [ExpensesController::class, 'changeStatus']);
    Route::post('expenses-uncheck', [ExpensesController::class, 'uncheckStatus']);
    Route::any('expenses-download', [ExpensesController::class, 'expenseDownload'])->name('expenses.download');
    Route::post('rejectExpense', [ExpensesController::class, 'rejectExpense'])->name('rejectExpense');
    Route::post('approveExpense', [ExpensesController::class, 'approveExpense'])->name('approveExpense');
    Route::post('getexpenseType', [ExpensesController::class, 'getexpenseType'])->name('getexpenseType');
    Route::post('getexpenseUserType', [ExpensesController::class, 'getexpenseUserType'])->name('getexpenseUserType');
    Route::post('getexpenseUserTypeEdit', [ExpensesController::class, 'getexpenseUserTypeEdit'])->name('getexpenseUserTypeEdit');
    Route::any('approveExpenses', [ExpensesController::class, 'approveExpenses'])->name('approveExpenses');
    Route::any('checkExpenses', [ExpensesController::class, 'checkExpenses'])->name('checkExpenses');
    Route::any('rejectExpenses', [ExpensesController::class, 'rejectExpenses'])->name('rejectExpenses');

    Route::get('deletImages', [ExpensesController::class, 'deletImages'])->name('deletImages');
    Route::get('deleteview', [ExpensesController::class, 'deleteview'])->name('deleteview');


    // departments
    Route::resource('departments', DepartmentController::class);
    Route::post('departments-active', [DepartmentController::class, 'active'])->name('departments.active');
    Route::any('department_report/download', [DepartmentController::class, 'department_report_download'])->name('department_report.download');

    //order scheme
    Route::resource('orderschemes', OrderSchemeController::class);
    Route::post('orderschemes-active', [OrderSchemeController::class, 'active'])->name('orderschemes.active');
    Route::any('orderschemes-template', [OrderSchemeController::class, 'template'])->name('orderschemes.template');
    Route::any('orderschemes-download', [OrderSchemeController::class, 'download'])->name('orderschemes.download');

    //primary scheme
    Route::resource('primary_scheme', PrimarySchemeController::class);
    Route::any('primary_scheme_template', [PrimarySchemeController::class, 'primary_scheme_template'])->name('primary_scheme.template');
    Route::post('primary_scheme-active', [PrimarySchemeController::class, 'active'])->name('primary_scheme.active');
    Route::any('primary_scheme-template', [PrimarySchemeController::class, 'template'])->name('primary_scheme.template');
    Route::any('primary_scheme-download', [PrimarySchemeController::class, 'download'])->name('primary_scheme.download');
    Route::any('primary_scheme_report', [PrimarySchemeController::class, 'primary_scheme_report'])->name('primary_scheme.report');
    Route::any('primary_scheme_report/download', [PrimarySchemeController::class, 'primary_scheme_report_download'])->name('primary_scheme.report.download');
    Route::any('primary_scheme_report/template', [PrimarySchemeController::class, 'primary_scheme_report_template'])->name('primary_scheme.report.template');



    //Services
    Route::get('services/serial_number_transaction', [ServicesController::class, 'serial_number_transaction'])->name('service.serial_number_transaction');
    Route::get('services/serial_number_transaction/delete', [ServicesController::class, 'serial_number_transaction_delete'])->name('service.serial_number_transaction.delete');
    Route::post('services/serial_number_transaction/upload', [ServicesController::class, 'serial_number_transaction_upload'])->name('service.serial_number_transaction.upload');
    Route::get('services/serial_number_transaction/download', [ServicesController::class, 'serial_number_transaction_download'])->name('service.serial_number_transaction.download');
    Route::get('services/serial_number_history/download', [ServicesController::class, 'serial_number_history_download'])->name('service.serial_number_history.download');
    Route::post('services/serial_number_transaction/list', [ServicesController::class, 'serial_number_transaction_list'])->name('service.serial_number_transaction.list');
    Route::get('services/serial_number_history', [ServicesController::class, 'serial_number_history'])->name('service.serial_number_history');
    Route::post('services/serial_number_history/list', [ServicesController::class, 'serial_number_history_list'])->name('service.serial_number_history.list');
    Route::get('services/serial_number_history/edit/{id}', [ServicesController::class, 'serial_number_history_edit'])->name('service.serial_number_history.edit');
    Route::PUT('services/serial_number_history/update', [ServicesController::class, 'serial_number_history_update'])->name('service.serial_number_history.update');

    // Transaction History
    Route::resource('transaction_history', TransactionHistoryController::class);
    Route::get('transaction_history_download', [TransactionHistoryController::class, 'download'])->name('transaction_history.download');
    Route::post('transaction_history_upload', [TransactionHistoryController::class, 'upload'])->name('transaction_history.upload');
    Route::post('transaction_history_main_upload', [TransactionHistoryController::class, 'upload_main'])->name('transaction_history_main.upload');
    Route::get('transaction_history_template', [TransactionHistoryController::class, 'template'])->name('transaction_history.template');
    Route::get('transaction_history_main_template', [TransactionHistoryController::class, 'template_main'])->name('transaction_history_main.template');
    Route::get('transaction_history_manualcreate', [TransactionHistoryController::class, 'manualcreate'])->name('transaction_history.manualcreate');
    Route::POST('transaction_history_manualstore', [TransactionHistoryController::class, 'manualstore'])->name('transaction_history.manualstore');
    Route::POST('transaction_history_manualupdate', [TransactionHistoryController::class, 'manualupdate'])->name('transaction_history.manualupdate');
    // point recalculate 
    Route::post('point-recalculate', [TransactionHistoryController::class, 'point_recalculate'])->name('transaction_history.point_recalculate');

    // Mobile User Login Sarthi Details
    Route::get('mobile_user_login', [MobileUserLoginDetailsController::class, 'mobile_user_login'])->name('mobile_user_login');

    Route::post('mobile_user/login_list/download', [MobileUserLoginDetailsController::class, 'mobile_user_login_download'])->name('mobile_user.login_list.download');

    Route::post('mobile_user_login/list', [MobileUserLoginDetailsController::class, 'mobile_user_login_list'])->name('mobile_user_login.list');

    // Mobile User Login Fildkonnect Details
    Route::get('user_app_details', [MobileUserLoginDetailsController::class, 'user_app_details'])->name('user_app_details');

    Route::post('user_app_details/login_list/download', [MobileUserLoginDetailsController::class, 'user_app_details_download'])->name('user_app_details.login_list.download');

    Route::post('user_app_details/list', [MobileUserLoginDetailsController::class, 'user_app_details_list'])->name('user_app_details.list');
    Route::post('user_app_details/multi_login', [MobileUserLoginDetailsController::class, 'user_app_details_multi_login'])->name('user_app_details.multi_login');

    //Gift Category Route
    Route::resource('gift-categories', GiftCategoryController::class);
    Route::any('gift-categories-download', [GiftCategoryController::class, 'download'])->name('gift.categories.download');
    Route::any('gift-categories-template', [GiftCategoryController::class, 'template'])->name('gift.categories.template');
    Route::post('gift-categories-upload', [GiftCategoryController::class, 'upload'])->name('gift.categories.upload');
    Route::post('gift-categories-active', [GiftCategoryController::class, 'active'])->name('gift.categories.active');

    //Gift Sub Category Route
    Route::resource('gift-subcategories', GiftSubcategoryController::class);
    Route::any('gift-subcategories-download', [GiftSubcategoryController::class, 'download'])->name('gift.subcategories.download');
    Route::any('gift-subcategories-template', [GiftSubcategoryController::class, 'template'])->name('gift.subcategories.template');
    Route::post('gift-subcategories-upload', [GiftSubcategoryController::class, 'upload'])->name('gift.subcategories.upload');
    Route::post('gift-subcategories-active', [GiftSubcategoryController::class, 'active'])->name('gift.subcategories.active');

    //Gift Model Route
    Route::resource('gift-model', GiftModelController::class);
    Route::any('gift-model-download', [GiftModelController::class, 'download'])->name('gift.model.download');
    Route::any('gift-model-template', [GiftModelController::class, 'template'])->name('gift.model.template');
    Route::post('gift-model-upload', [GiftModelController::class, 'upload'])->name('gift.model.upload');
    Route::post('gift-model-active', [GiftModelController::class, 'active'])->name('gift.model.active');

    //Gift Brand Route
    Route::resource('gift-brands', GiftBrandController::class);
    Route::any('gift-brands-download', [GiftBrandController::class, 'download'])->name('gift.brands.download');
    Route::any('gift-brands-template', [GiftBrandController::class, 'template'])->name('gift.brands.template');
    Route::post('gift-brands-upload', [GiftBrandController::class, 'upload'])->name('gift.brands.upload');
    Route::post('gift-brands-active', [GiftBrandController::class, 'active'])->name('gift.brands.active');

    // Redemption Route
    Route::resource('redemptions', RedemptionController::class);
    Route::get('redemptions_download', [RedemptionController::class, 'download'])->name('redemptions.download');
    Route::post('redemptions-upload', [RedemptionController::class, 'upload'])->name('redemptions.upload');
    Route::get('redemptions-template', [RedemptionController::class, 'template'])->name('redemptions.template');
    Route::get('redemptions-template-gift', [RedemptionController::class, 'gift_template'])->name('redemptions.gift_template');
    Route::get('redemption-change-status', [RedemptionController::class, 'changeStatus'])->name('redemptions.changeStatus');
    Route::get('redemption-gift-delivered', [RedemptionController::class, 'giftDelivered'])->name('redemptions.giftDelivered');
    Route::any('redemption-gift-catalogue', [RedemptionController::class, 'gift_catalogue'])->name('redemptions.gift-catalogue');
    Route::any('redemption-gifttable', [RedemptionController::class, 'gifttable'])->name('redemptions.gifttable');
    Route::get('neft-redemption-change-status', [NeftRedemptionDetailsController::class, 'changeStatus'])->name('neft.redemptions.changeStatus');

    // Redemption Route
    Route::resource('warranty_activation', WarrantyActivationController::class);
    Route::any('warranty-activation-download', [WarrantyActivationController::class, 'download'])->name('warranty_activation.download');
    Route::post('warranty-status-change', [WarrantyActivationController::class, 'statuschange'])->name('warranty_activation.statuschange');

    // Customer KYC Route
    Route::resource('customer-kyc', CustomerKycController::class);
    Route::any('customer-kyc-download', [CustomerKycController::class, 'download'])->name('customer-kyc.download');

    // Complaint Type Route
    Route::resource('complaint-type', ComplaintTypeController::class);
    Route::post('complaint-type-active', [ComplaintTypeController::class, 'active'])->name('complaint-type.active');

    // Complaint Route
    Route::resource('complaints', ComplaintController::class);
    Route::post('getComplaints', [ComplaintController::class, 'getComplaints'])->name('getComplaints');
    Route::post('complaint-attach-delete', [ComplaintController::class, 'deleteAttachment'])->name('deleteAttachment');
    Route::post('complaint-cancel', [ComplaintController::class, 'cancelComplaint'])->name('cancelComplaint');
    Route::post('complaint-pending', [ComplaintController::class, 'pendingComplaint'])->name('pendingComplaint');
    Route::post('complaint-open', [ComplaintController::class, 'openComplaint'])->name('openComplaint');
    Route::post('complaint-complete', [ComplaintController::class, 'completeComplaint'])->name('completeComplaint');
    Route::post('check-complaint-complete', [ComplaintController::class, 'checkCompleteComplaint'])->name('checkCompleteComplaint');
    Route::post('complaint-close', [ComplaintController::class, 'closeComplaint'])->name('closeComplaint');
    Route::any('complaint_download', [ComplaintController::class, 'complaint_download'])->name('complaint_download');
    Route::any('complaint-work-done/{complaint}', [ComplaintController::class, 'work_done'])->name('complaint_work_done');
    Route::any('complaint-work-done-submit', [ComplaintController::class, 'work_done_submit'])->name('complaint_work_done_submit');
    Route::any('complaint-assign-user', [ComplaintController::class, 'assign_user'])->name('complaint_assign_user');
    Route::any('complaint-assign-service-center', [ComplaintController::class, 'assign_service_center'])->name('complaint_assign_service_center');
    Route::post('complaint-add-note', [ComplaintController::class, 'complaint_add_notes'])->name('complaint_add_notes');

    // Leaves Route
    Route::resource('leaves', LeaveController::class);
    Route::any('approveLeave', [LeaveController::class, 'approveLeave'])->name('approveLeave');
    Route::any('rejectLeave', [LeaveController::class, 'rejectLeave'])->name('rejectLeave');

    // Resignations Route
    Route::resource('resignations', ResignationController::class);
    Route::any('resignation/download', [ResignationController::class, 'download'])->name('resignation.download');
    Route::any('update_checklist', [ResignationController::class, 'update_checklist'])->name('resignation.update_checklist');
    Route::any('resignation_status_change', [ResignationController::class, 'resignation_status_change'])->name('resignation.resignation_status_change');
    Route::any('resignation_last_working_date_change', [ResignationController::class, 'resignation_last_working_date_change'])->name('resignation.resignation_last_working_date_change');

    // Loyalty App Setting Route
    Route::resource('loyalty-app-setting', LoyaltyAppSettingController::class);

    // FieldKonnectAppSetting App Setting Route
    Route::resource('field-konnect-app-setting', FieldKonnectAppSettings::class);
    Route::resource('delar-portal-setting', DealerPortalSettingsController::class);

    // Damage Entry Route
    Route::resource('damage_entries', DamageEntryController::class);
    Route::get('damage-entries-change-status', [DamageEntryController::class, 'changeStatus'])->name('damage_entries.changeStatus');
    Route::any('damage_entries/download', [DamageEntryController::class, 'damage_entries_download'])->name('damage_entries.download');

    Route::get('logout', '\App\Http\Controllers\Auth\AuthenticatedSessionController@destroy');

    //New Joining with auth Route
    Route::get('/new-joinings', [NewJoiningController::class, 'index'])->name('new-joining');
    Route::get('/new-joinings-show/{newJoining}', [NewJoiningController::class, 'show'])->name('new-joining.show');
    Route::any('/new-joining/download', [NewJoiningController::class, 'download'])->name('new-joining.download');

    //Dealer Appointment with auth Route
    Route::get('/dealer-appointments', [DealerAppointmentController::class, 'index'])->name('dealer-appointment');
    Route::get('/dealer-appointments-show/{dealerAppointment}', [DealerAppointmentController::class, 'show'])->name('dealer-appointment.show');
    Route::get('/dealer-appointments-PDFshow/{dealerAppointment}', [DealerAppointmentController::class, 'PDFshow'])->name('dealer-appointment.PDFshow');
    Route::get('/dealer-appointments-destroy/{dealerAppointment}', [DealerAppointmentController::class, 'destroy'])->name('dealer-appointment.destroy');
    Route::any('/dealer-appointment/download', [DealerAppointmentController::class, 'download'])->name('dealer-appointment.download');
    Route::get('/dealer-appointment-edit/{dealerAppointment}', [DealerAppointmentController::class, 'edit'])->name('dealer-appointment.edit');
    Route::post('/dealer-appointment-update//{dealerAppointment}', [DealerAppointmentController::class, 'update'])->name('dealer-appointment-form.update');
    Route::any('/dealer-certificate-generate', [DealerAppointmentController::class, 'dealerCertificateGenerate'])->name('dealer-certificate.generate');

    // Damage Entry Route
    Route::any('visitors', [DashboardController::class, 'visitors'])->name('visitor');

    // Service Bill Route
    Route::resource('service_bills', ServiceBillController::class);
    Route::post('service-bill-company-claim', [ServiceBillController::class, 'company_claim'])->name('company_claim');
    Route::post('service-bill-draft', [ServiceBillController::class, 'draftBill'])->name('draftBill');
    Route::post('service-bill-approve', [ServiceBillController::class, 'approveBill'])->name('approveBill');
    Route::post('service-bill-cancel', [ServiceBillController::class, 'cancelBill'])->name('cancelBill');
    Route::post('service-bill-customer-pay', [ServiceBillController::class, 'customer_pay'])->name('customer_pay');
    Route::any('service-bill-product-remove', [ServiceBillController::class, 'remove_product'])->name('remove_product');

    // Service Charge Product Route
    // Division Route
    Route::get('service-charge/dividsions', [ServiceChargeProductsController::class, 'divisionindex'])->name('servicecharge.dividsions.index');
    Route::any('service-charge/dividsions/add', [ServiceChargeProductsController::class, 'divisionstore'])->name('servicecharge.dividsions.add');
    Route::any('service-charge/dividsions/download', [ServiceChargeProductsController::class, 'divisiondownload'])->name('servicecharge.dividsions.download');
    Route::any('service-charge/dividsions/{id}/edit', [ServiceChargeProductsController::class, 'divisionedit'])->name('servicecharge.dividsions.edit');
    Route::any('service-charge/dividsions/{id}/active', [ServiceChargeProductsController::class, 'divisionactive'])->name('servicecharge.dividsions.active');
    Route::any('service-charge/dividsions/{id}/delete', [ServiceChargeProductsController::class, 'divisiondelete'])->name('servicecharge.dividsions.delete');
    // Category Route
    Route::get('service-charge/categories', [ServiceChargeProductsController::class, 'categoryindex'])->name('servicecharge.categories.index');
    Route::any('service-charge/categories/add', [ServiceChargeProductsController::class, 'categorystore'])->name('servicecharge.categories.add');
    Route::any('service-charge/categories/{id}/edit', [ServiceChargeProductsController::class, 'categoryedit'])->name('servicecharge.categories.edit');
    Route::any('service-charge/categories/{id}/active', [ServiceChargeProductsController::class, 'categoryactive'])->name('servicecharge.categories.active');
    Route::any('service-charge/categories/{id}/delete', [ServiceChargeProductsController::class, 'categorydelete'])->name('servicecharge.categories.delete');
    Route::any('service-charge/categories/download', [ServiceChargeProductsController::class, 'categorydownload'])->name('servicecharge.categories.download');
    Route::any('service-charge/categories/upload', [ServiceChargeProductsController::class, 'categoryupload'])->name('servicecharge.categories.upload');
    // Charge Type Route
    Route::get('service-charge/chargetype', [ServiceChargeProductsController::class, 'chargetypeindex'])->name('servicecharge.chargetype.index');
    Route::any('service-charge/chargetype/add', [ServiceChargeProductsController::class, 'chargetypestore'])->name('servicecharge.chargetype.add');
    Route::any('service-charge/chargetype/download', [ServiceChargeProductsController::class, 'chargetypedownload'])->name('servicecharge.chargetype.download');
    Route::any('service-charge/chargetype/{id}/edit', [ServiceChargeProductsController::class, 'chargetypeedit'])->name('servicecharge.chargetype.edit');
    Route::any('service-charge/chargetype/{id}/active', [ServiceChargeProductsController::class, 'chargetypeactive'])->name('servicecharge.chargetype.active');
    Route::any('service-charge/chargetype/{id}/delete', [ServiceChargeProductsController::class, 'chargetypedelete'])->name('servicecharge.chargetype.delete');
    // Product Route
    Route::get('service-charge/products', [ServiceChargeProductsController::class, 'productindex'])->name('servicecharge.products.index');
    Route::any('service-charge/products/create', [ServiceChargeProductsController::class, 'productcreate'])->name('servicecharge.products.create');
    Route::any('service-charge/products/add', [ServiceChargeProductsController::class, 'productstore'])->name('servicecharge.products.add');
    Route::any('service-charge/products/update', [ServiceChargeProductsController::class, 'productstore'])->name('servicecharge.products.update');
    Route::any('service-charge/products/{id}/edit', [ServiceChargeProductsController::class, 'productedit'])->name('servicecharge.products.edit');
    Route::any('service-charge/products/{id}/active', [ServiceChargeProductsController::class, 'productactive'])->name('servicecharge.products.active');
    Route::any('service-charge/products/{id}/delete', [ServiceChargeProductsController::class, 'productdelete'])->name('servicecharge.products.delete');
    Route::any('service-charge/products/download', [ServiceChargeProductsController::class, 'productdownload'])->name('servicecharge.products.download');
    Route::any('service-charge/products/upload', [ServiceChargeProductsController::class, 'productupload'])->name('servicecharge.products.upload');

    Route::resource('planned-sop', PlannedSOPController::class);
    Route::any('planned-sop-list', [PlannedSOPController::class, 'plannedSopList'])->name('plannedSopList');
    Route::any('planned-sop-export', [PlannedSOPController::class, 'sop_download'])->name('sop_download');
    Route::any('planned-sale-sop-export', [PlannedSOPController::class, 'sale_sop_download'])->name('sale_sop_download');
    Route::any('planned-sop-import', [PlannedSOPController::class, 'sop_import'])->name('sop_import');
    Route::any('planned-sop-template', [PlannedSOPController::class, 'sop_template'])->name('sop_template');
    Route::any('planned-sop-multistatus-change', [PlannedSOPController::class, 'planned_sop_multistatus_change'])->name('planned-sop-multistatus-change');

    Route::get('planned-sop-forecast', [PlannedSOPController::class, 'plannedForCast'])->name('plannedForCast');

    // claim gerneration 
    Route::resource('claim-generation', ClaimGenerationController::class);
    Route::any('claim-generation-list', [ClaimGenerationController::class, 'getClaims'])->name('getClaims');
    Route::any('claim-generation-export', [ClaimGenerationController::class, 'ClaimGenerationExport'])->name('claim-generation.export');
    Route::any('claim-generation-list-single', [ClaimGenerationController::class, 'getClaimsSingle'])->name('getClaimsSingle');
    Route::any('claim-generation-pdf/{id}', [ClaimGenerationController::class, 'claimGenerationPdf'])->name('claim-generation.pdf');

    // service bill complaint type  
    Route::resource('service-bills-complaints-type', ServiceBillComplaintType::class);
    Route::any('service-bills-complaints-type-list', [ServiceBillComplaintType::class, 'getServiceComplaintType'])->name('getServiceComplaintType');
    Route::any('service_bill_complaint_type_download', [ServiceBillComplaintType::class, 'service_bill_complaint_type_download'])->name('service_bill_complaint_type_download');

    // opening stock routes
    Route::resource('opening-stocks', OpeningStockController::class)->only('index');
    Route::any('opening-stocks-list', [OpeningStockController::class, 'getOpeningStocks'])->name('getOpeningStocks');
    Route::any('opening-stocks-import', [OpeningStockController::class, 'openingStockImport'])->name('openingStockImport');
    Route::any('opening-stocks-export', [OpeningStockController::class, 'openingStockExport'])->name('openingStockExport');

    // Branch Opening Quantity routes
    Route::resource('opening-quantity', BranchOprningQuantityController::class)->only('index');
    Route::any('opening-quantity-list', [BranchOprningQuantityController::class, 'getOpeningquantity'])->name('getOpeningquantity');
    Route::any('opening-quantity-import', [BranchOprningQuantityController::class, 'openingStockImport'])->name('openingStockImport');
    Route::any('opening-quantity-export', [BranchOprningQuantityController::class, 'openingStockExport'])->name('openingStockExport');

    //Power BI Settings Routes
    Route::get('power_bi_setting', [PowerBiSettingController::class, 'index']);
    Route::post('power_bi_setting/store', [PowerBiSettingController::class, 'store'])->name('power_bi_setting.store');

    //Tax Invoice Routes
    Route::resource('tax_invoice', TaxInvoiceController::class);
    Route::post('add_payment_term', [TaxInvoiceController::class, 'add_payment_term'])->name('add_payment_term');
    Route::post('add_tax', [TaxInvoiceController::class, 'add_tax'])->name('add_tax');
    Route::post('add_tds', [TaxInvoiceController::class, 'add_tds'])->name('add_tds');
    Route::get('convert_to_tax_invoice/{convert_estimate}', [TaxInvoiceController::class, 'convert_to_tax_invoice'])->name('convert_to_tax_invoice');
    Route::get('invoice_setting', [TaxInvoiceController::class, 'invoice_setting'])->name('invoice_setting');
    Route::post('invoice_setting_store', [TaxInvoiceController::class, 'invoice_setting_store'])->name('invoice_settings.store');
    Route::delete('/invoice-labels/{id}', [TaxInvoiceController::class, 'destroy_label'])->name('invoice_labels.destroy');
    Route::any('invoices_download', [TaxInvoiceController::class, 'download'])->name('invoice.export');

});

Route::any('getState', [AjaxController::class, 'getState']);
Route::any('getDistrict', [AjaxController::class, 'getDistrict']);
Route::any('getCity', [AjaxController::class, 'getCity']);
Route::any('getCountry', [AjaxController::class, 'getCountry']);
Route::any('getPincode', [AjaxController::class, 'getPincode']);
Route::any('getAddressData', [AjaxController::class, 'getAddressData']);
Route::any('getAddressInfo', [AjaxController::class, 'getAddressInfo']);
Route::any('getCustomerData', [AjaxController::class, 'getCustomerData'])->name('getCustomerData');
Route::any('getCustomerDataSelect', [AjaxController::class, 'getCustomerDataSelect'])->name('getCustomerDataSelect');
Route::any('getDealerDisDataSelect', [AjaxController::class, 'getDealerDisDataSelect'])->name('getDealerDisDataSelect');
Route::any('getRetailerDataSelect', [AjaxController::class, 'getRetailerDataSelect'])->name('getRetailerDataSelect');
Route::any('getProductDataSelect', [AjaxController::class, 'getProductDataSelect'])->name('getProductDataSelect');
Route::any('getServiceCategory', [AjaxController::class, 'getServiceCategory'])->name('getServiceCategory');
Route::any('getStateDataSelect', [AjaxController::class, 'getStateDataSelect'])->name('getStateDataSelect');
Route::any('getPincodeDataSelect', [AjaxController::class, 'getPincodeDataSelect'])->name('getPincodeDataSelect');
Route::any('getCategoryData', [AjaxController::class, 'getCategoryData']);
Route::any('getPrimaryGroup', [AjaxController::class, 'getPrimaryGroup']);
Route::any('getExpensesData', [AjaxController::class, 'getExpensesData'])->name('getExpensesData');
Route::any('getSubCategoryData', [AjaxController::class, 'getSubCategoryData']);
Route::any('getGiftSubCategoryData', [AjaxController::class, 'getGiftSubCategoryData']);
Route::any('getGiftModelData', [AjaxController::class, 'getGiftModelData']);
Route::any('getProductData', [AjaxController::class, 'getProductData']);
Route::any('getProductInfo', [AjaxController::class, 'getProductInfo']);
Route::any('getUserList', [AjaxController::class, 'getUserList']);
Route::any('getUserListAppoint', [AjaxController::class, 'getUserListAppoint']);
Route::any('getUserInfo', [AjaxController::class, 'getUserInfo']);
Route::any('getRetailerlist', [AjaxController::class, 'getRetailerlist']);
Route::any('getOrderInfo', [AjaxController::class, 'getOrderInfo']);
Route::any('uniqueValidation', [AjaxController::class, 'uniqueValidation']);
Route::any('getCustomerLatLong', [AjaxController::class, 'getCustomerLatLong']);
Route::any('getUppaidInvouces', [AjaxController::class, 'getUppaidInvouces']);
Route::any('dashboardActivity', [AjaxController::class, 'dashboardActivity']);
Route::any('getUserLocationData', [AjaxController::class, 'getUserLocationData']);
Route::any('getUserActivityData', [AjaxController::class, 'getUserActivityData']);
Route::any('getCustomerActivityData', [AjaxController::class, 'getCustomerActivityData']);
Route::any('schemesdetails/remove', [AjaxController::class, 'removeSchemesdetails']);
Route::any('changeDocumnetStatus', [AjaxController::class, 'changeDocumnetStatus']);
Route::any('getBankdetailandPoints', [AjaxController::class, 'getBankdetailandPoints']);
Route::any('getProductByCoupon', [AjaxController::class, 'getProductByCoupon']);
Route::any('getTourPlanByUserAndDate', [AjaxController::class, 'getTourPlanByUserAndDate']);
Route::any('userCityList', [AjaxController::class, 'userCityList']);
Route::any('getProductInfoBySerialNo', [AjaxController::class, 'getProductInfoBySerialNo']);
Route::any('getEndUserData', [AjaxController::class, 'getEndUserData']);
Route::any('getComplaintsData', [AjaxController::class, 'getComplaintsData']);
Route::any('getComplaintsDataProduct', [AjaxController::class, 'getComplaintsDataProduct']);
Route::any('/fetchPieChartData', [AjaxController::class, 'fetchPieChartData'])->name('fetchPieChartData');
Route::any('/remove_session', [AjaxController::class, 'remove_session'])->name('remove.session');
Route::any('/getPrimaryTotal', [AjaxController::class, 'getPrimaryTotal'])->name('getPrimaryTotal');
Route::any('getServiceProduct', [AjaxController::class, 'getServiceProduct'])->name('getServiceProduct');
Route::any('getServiceProductDetails', [AjaxController::class, 'getServiceProductDetails'])->name('getServiceProductDetails');
Route::any('changeAppointmentStatus', [AjaxController::class, 'changeAppointmentStatus']);
Route::any('getWorkDoneTime', [AjaxController::class, 'getWorkDoneTime']);
Route::any('getPrimarySachme', [AjaxController::class, 'getPrimarySachme']);
Route::any('getExpenseCount', [AjaxController::class, 'getExpenseCount']);
Route::any('getLeaveBalance', [AjaxController::class, 'getLeaveBalance']);
Route::any('marketing/getCounts', [AjaxController::class, 'marketingGetCounts'])->name('marketing.getCounts');
Route::any('getProductTimeInterval', [AjaxController::class, 'getProductTimeInterval']);
Route::any('getSellerBuyer', [AjaxController::class, 'getSellerBuyer']);
Route::any('addMarketingType', [AjaxController::class, 'addMarketingType']);
Route::any('getMarketingType', [AjaxController::class, 'getMarketingType']);
Route::any('deleteMarketingType', [AjaxController::class, 'deleteMarketingType']);
Route::any('getUserByBranch', [AjaxController::class, 'getUserByBranch']);
Route::any('getPMS', [AjaxController::class, 'getPMS']);
Route::any('getPincodeSearch', [AjaxController::class, 'getPincodeSearch']);
Route::any('getAllCustomer', [AjaxController::class, 'getAllCustomer']);
Route::any('getAllPartyName', [AjaxController::class, 'getAllPartyName']);
Route::any('getProductWarrentyTime', [AjaxController::class, 'getProductWarrentyTime']);
Route::any('getServiceChargeType', [AjaxController::class, 'getServiceChargeType']);
Route::any('getServiceServiceEngiByDivision', [AjaxController::class, 'getServiceServiceEngiByDivision']);
Route::any('getCountsOfComplaints', [AjaxController::class, 'getCountsOfComplaints']);
Route::any('getSaledata', [AjaxController::class, 'getSaledata']);
Route::any('getServiceBillReason', [AjaxController::class, 'getServiceBillReason']);
Route::any('checkServiceBillComplaintType', [AjaxController::class, 'checkServiceBillComplaintType']);
Route::any('getSubCategory', [AjaxController::class, 'getSubCategory']);
Route::any('getProductInfoListBySubcategory', [AjaxController::class, 'getProductInfoListBySubcategory']);
Route::any('getFullDetailsOfProduct', [AjaxController::class, 'getFullDetailsOfProduct']);
Route::any('planned-sop-fore-total', [AjaxController::class, 'getplannedForCast'])->name('getplannedForCast');

Route::any('getCustomerAddress', [AjaxController::class, 'getCustomerAddress']);

//Clear Cache facade value:
Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function () {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function () {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function () {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function () {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function () {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

Route::get('/key-generate', function () {
    $exitCode = Artisan::call('key:generate');
    return '<h1>Cache key:generate</h1>';
});

Route::get('/taskreminder', function () {

    Artisan::call('task:reminder');
});

Route::get('/dashboard/silver', function () {
    return view('dashboard_silver');
});



require __DIR__ . '/auth.php';
