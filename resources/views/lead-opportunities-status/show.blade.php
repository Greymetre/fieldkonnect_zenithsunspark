<x-app-layout>
    <style>

        .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
            border: transparent!important;
            background: transparent!important;
            font-weight: normal!important;
            color: #000!important;
        }

        .ui-widget-header{
            background: transparent!important;
            border: transparent!important;
        }

        .image-profile {
            background: #9ff3f3;
            width: 80px;
            height: 80px;
            font-size: 45px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            text-shadow: 0px 0px 10px #fff;
            border: 6px double #FFF;
        }

        .fileinput .thumbnail {
           max-width:300px!important;
           box-shadow: unset!important;
        }

        .sdm{
            width: 77px;
            justify-content: space-between;
            opacity: 0;
        }

        .contact-person .gh:hover .sdm {
            opacity: 1;
        }

        .wer {
            width: 250px;
        }

        .deletebox {
            width: 30px;
            height: 30px;
            background: #cff2fb;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50px;
        }

        .listdata h5 p {
            color: #262A2A;
            font-weight: 500;
        }

        .activity-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: #fff;
            font-weight: bold;
        }

        a.email_s {
            border-radius: 50px;
            border-color: transparent!important;
            text-transform: capitalize;
            font-size: 16px;
            font-weight: 500;
        }

        a.email_s img {
            margin-right: 10px;
        }

        .imagedata {
            margin-left: 15px;
        }

        .imagebox img {
            border: 1px solid #3694cc;
            border-radius: 5px;
            overflow: hidden;
        }

        .imagedata p {
            font-size: 14px!important;
            font-weight: 500 !important;
            line-height: 17px;
            margin-bottom: 0px;
            font-family: 'Poppins', sans-serif;
            color: #414141;
        }

        button.btnsmall {
            border-radius: 50px !important;
            margin-left: 10px;
        }

        img.imagss {
            width: 65px;
            height: 65px;
            background: #eee;
            padding: 10px;
            border-radius: 50px;
            margin-right: 20px;
        }

        .activity-card {
            border-left: 4px solid #0d6efd;
            background-color: #f8f9fa;
        }

        .task-complete {
            text-decoration: line-through;
            color: #6c757d;
        }

        button.deletebtn {
            border: 0px;
            background: transparent;
            margin-left: 1px;
            cursor: pointer;
        }

        .per {
            justify-content: space-between;
            width: 75px;
            opacity: 0;
        }

        button.deletebtn:focus{
            outline: 0px;
        }

        button.deletebtn img {
            width: 22px;
            height: auto;
        }

        .card{
            color: #000 !important;
        }

        .new-task .card-header{
            border-bottom: 1px solid #E8E8E8!important;
            cursor: pointer;
        }

        .new-task .card-header a {
            color: #334C8B;
            font-size: 15px;
            font-weight: 500;
            line-height: 21px;
        }

        .new-task .card-header strong {
            font-size: 16px;
            color: #262A2A;
            font-weight: 600;
        }

        ul.list-task li p {
            color: #000;
            font-weight: 500;
            font-size: 14px;
            line-height: 21px;
            margin: 0px;
        }

        ul.list-task li {
            margin-bottom: 10px;
        }

        ul.list-task li p span.date {
            font-size: 12px;
            font-weight: 400;
            color: #636363;
            line-height: 21px;
        }

        .taskform .form-group label {
            display: none;
        }

        .taskform .form-control[readonly]{
            border: 1px solid #1072ae!important;
            border-radius: 10px!important; 
        }

        .taskform .form-control{
            border: 1px solid #1072ae!important;
            border-radius: 10px!important;
            height: 30px!important;
        }

        .new-task button.btn.btn-default {
            border-radius: 50px;
            font-size: 13px;
            font-weight: 500;
            height: 35px;
            line-height: 20px;
            width: 100%;
            text-align: center;
            padding: 1px 16px;
            min-width: 90px;
        }

        .new-task .card{
            padding: 0px!important;
        }

        .taskform .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #000 !important;
            text-transform: uppercase;
            font-size: 14px !important;
            height: 30px !important;
            line-height: 35px !important;
            font-weight: 500;
            text-transform: capitalize !important;
        }

        .taskform .form-control::placeholder {
            color: #000;
            font-weight: 500;
        }

        .contact-task-form .form-control::placeholder
        {
            color: #000;
            font-weight: 500;
        }


        .taskform .select2-container--default .select2-selection--single{
            border: 1px solid #1072ae!important;
            border-radius: 10px!important;
        }

        ul.list-task li h5 {
            font-size: 14px;
            font-weight: 500 !important;
            line-height: 17px;
            margin-bottom: 0px;
            font-family: 'Poppins', sans-serif;
            color: #000;
        }

        .contact-person p {
            font-size: 13px !important;
            font-weight: 400 !important;
            line-height: 21px !important;
            color: #414141;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0px;
            margin-bottom: 0px !important;
        }

        .editdelte {
            justify-content: space-around;
            width: 84px;
        }

        .contac-link {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            width: 23%;
        }

        .image-profile img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        ul.cn-list li {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
        }

        .contact-task-form .form-control {
            border: 1px solid #1072ae !important;
            border-radius: 10px !important;
            height: 30px !important;
        }

        .contact-task-form label {
            display: none;
        }

        .infomation-data h3
        {
            margin: 0px;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0px;
            line-height: 19px;
            color: #3C4858!important;
        }

        .infomation-data p {
            font-size: 14px;
            font-weight: 400;
            color: #6F6F6F;
            margin: 2px 0px;
            line-height: 21px;
        }

        .inermain {
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            align-items: flex-start;
        }

        .infomation-data ul li {
            width: 65%;
            margin-bottom: 0px;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            margin-right: 20px;
        }



        .infomation-data ul{
            margin-bottom: 0px;
        }

        .infomation-data p {
            font-size: 14px;
            font-weight: 400;
            color: #6F6F6F;
            margin: 6px 0px;
            line-height: 21px;
            margin-bottom: 0px;
        }

        .imagedata p {
            margin-bottom: 0px;
        }

        .infomation-data ul li span {
            font-size: 14px;
            font-weight: 400;
            color: #3C4858;
            line-height: 21px;
        }

        .image-profile {
            margin-right: 10px;
        }

        .infomation-data ul {
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            align-items: center;
        }

        .button-box button img {
            margin-right: 5px;
        }
        .button-box button {
            border-radius: 50px;
            height: 48px;
            background: unset !important;
            background-color: #3779B7 !important;
            border-color: #3779B7 !important;
            font-size: 16px;
            font-weight: 500;
            color: #fff !important;
            text-transform: capitalize;
        }

        .button-box button:nth-child(1) {
            margin-right: 15px;
        }

        .infomation-data ul li img {
            margin-right: 8px;
        }

        .tabsection .nav-tabs .nav-item .nav-link {
            font-size: 14px;
            color: #565E5E!important;
            font-weight: 500;
            line-height: 21px;
            letter-spacing: 0px;
            text-transform: capitalize;
            border-bottom: 2px solid transparent!important;
        }

        .tabsection .nav-tabs .nav-item .nav-link.active {
            color: #3779B7 !important;
            font-weight: 600;
            border-bottom: 2px solid #3779B7 !important;
        }

        .tabsection .nav-tabs .nav-item {
            padding-right: 35px;
        }

        .listdata h5 {
            font-size: 14px;
            line-height: 16px;
            font-weight: 500;
            letter-spacing: 0px;
            color: #262A2A;
            font-family: 'Poppins', sans-serif;
            margin: 0px;
        }

        .listdata p {
            font-size: 14px;
            line-height: 21px;
            font-weight: 400;
            letter-spacing: 0px;
            font-family: 'Poppins', sans-serif;
            color: #6F6F6F;
            margin-bottom: 0px;
        }

        .tabsection h6.date_listing {
            font-size: 13px;
            font-weight: 400;
            line-height: 14px;
            letter-spacing: 0.5px;
            color: #334C8B !important;
            text-transform: capitalize;
            background: #D7F4FF;
            border-radius: 4px;
            width: 100%;
            max-width: 109px;
            padding: 5px 5px;
        }

        .list-one {
            display: flex;
            flex-direction: row;
            justify-content: flex-start;
            align-items: flex-start;
            width: 100%;
        }

        .buttonbox button:focus {
            outline: 0px;
        }

        .buttonbox button {
            border: 0px;
            background: transparent;
        }

        .list-one .image_box {
            margin-right: 10px;
        }

        .tabsection .greenbox {
            width: 40px;
            height: 40px;
            background: #CAF4C6;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 4px;
        }

        .tabsection .main-div {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 15px;

        }

        .list-two .dropdown-menu {
            top: 0 !important;
        }

        .list-two{
            position:relative;
        }

        .list-two button {
            background: transparent;
            border: 0px;
            width: 10px;
            height: auto;
        }

        .list-two button:focus {
            outline: 0px;
        }

        .list-two .dropdown-toggle::after{
            display: none;
        }

        .list-two .card-header p.hed {
            font-size: 18px!important;
            font-weight: 600!important;
            color: #262A2A!important;
            letter-spacing: 0px!important;
            line-height: 21px!important;
        }

        .tabsection .card-header{
            border-bottom: 1px solid #E8E8E8!important;
        }

        .donebtn{
            background: #2B6CF0 !important;
            border-radius: 32px!important;
            font-size: 14px;
            font-weight: 600;
        }

        .editbox img {
            /*width: 100%;*/
        }

        .editbox {
            width: 20px;
            height: auto;
        }

        .editbox {
            width: 30px;
            height: 30px;
            background: #cff2fb;
            display: flex;
            align-items: center;
            border-radius: 50px;
            justify-content: center;
        }

        ul.list-task li:hover .editbox {
            opacity: 1;
            cursor:pointer;
        }

        .contact-person {
            width: 140px;
        }

        .contact_edit {
            opacity: 0;
            width: 30px;
            height: 30px;
            background: #d1f5fe;
            border-radius: 50px;
            text-align: center;
            align-items: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        ul.cn-list li:hover .contact_edit {
            opacity: 1;
            cursor: pointer;
        }

        ul.list-task li:hover .per {
            opacity: 1;
        }


        .boxing {
            text-align: right;
            margin-top: 10px;
        }

        .border-s {
            border: 1px solid #1072ae !important;
            border-radius: 10px !important;
            padding: 7px 7px;
        }

        select#type {
            color: #000;
        }

        .appurt .contact-person {
            width: 100%;
        }

        ul.appurt li:hover .editsource {
            opacity: 1;
        }

        .editsource {
            width: 30px;
            height: 30px;
            background: #cff2fb;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50px;
        }


        @media only screen and (min-width: 768px) and (max-width: 991px){ 

            .tabsection .nav-tabs .nav-item {
                padding-right: 20px;
            }
        }

        @media (max-width: 767px){

            .tabsection .nav-tabs .nav-item {
                padding-right: 3px;
            }

            .inermain{
                flex-direction:column;
            }

            .mpadding {
                padding: 0px !important;
            }

            .mbcss {
                display: flex;
                flex-direction: column;
            }

            .infomation-data ul {
                flex-direction: column;
                width: 100%;
            }

            .infomation-data ul li {
                width: 100%;
                margin-bottom: 0px;
                display: flex;    
            }

            .mbcss .button-box {
                display: flex;
                flex-direction: row;
            }
        }

        .shdnone {
            box-shadow: unset !important;
            background: #e3e9f6;
        }

        .forus {
            margin: 0px !important;
            box-shadow: unset !important;
            border-radius: 0px !important;
        }

        .new-task {
            background: #fff;
            box-shadow: 0 1px 4px 0 rgba(0, 0, 0, .14)!important;
            padding: 0!important;
            margin-top: 21px!important;
            border-radius: 6px!important;
            overflow: hidden;
        }

        button.btn.btn-primary.btn-sm.btn-icon-split.float-right.btnsmall {
            margin-bottom: 20px;
        }

        .forus .card-header {
            border-top: 1px solid #E8E8E8 !important;
        }
        .btn-group.kim button {
        border-radius: 50px;
        padding: 5px 10px;
        text-transform: capitalize;
        background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%) !important;
        color: #fff !important;
        border-color: transparent;
    }

    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="card shdnone mt-0 p-0">
                <div class="card-header m-0 card-header-tabs card-header-warning">
                    <div class="nav-tabs-navigation">
                        <div class="nav-tabs-wrapper new_id">
                            <h4 class="card-title ">
                            Company Overview </h4>

                        </div>
                    </div>
                </div>
                <div class="card-body shdnone">
                    @if(count($errors) > 0)
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="material-icons">close</i>
                        </button>
                        <span>
                            @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                            @endforeach
                        </span>
                    </div>
                    @endif
                    @if(session('message_success'))
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="material-icons">close</i>
                        </button>
                        <span>
                            {{ session('message_success') }}
                        </span>
                    </div>
                    @endif
                    @if(session('message_info'))
                    <div class="alert alert-info">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="material-icons">close</i>
                        </button>
                        <span>
                            {{ session('message_info') }}
                        </span>
                    </div>
                    @endif


<!--  -->
<div class="container-fluid p-4 mpadding">

<!--  -->
<button type="button" data-toggle="modal" data-target="#updateLeadModel" class="btn btn-primary btn-sm btn-icon-split float-right btnsmall">
    <span class="text">Update Leads</span>
    <button type="button" data-toggle="modal" data-target="#AddAddressModel" onclick="openAddAddressModel()" class="btn btn-primary btn-sm btn-icon-split float-right btnsmall">
        <span class="text">Add Address</span>
    </button>
</button>
<!--  -->
<!-- Header -->
<div class="card p-0 px-3 py-3">
    <div class="d-flex justify-content-between align-items-center mb-0 mbcss">
        <div class="inermain">
            <div class="image-profile">
                {{strtoupper(substr($lead->company_name, 0, 1));}}
            </div>
            <div class="infomation-data">
                <h3>{{$lead->company_name??''}} <span class="badge badge-pill badge-info">{{$lead->status_is?$lead->status_is->display_name:'Pending'}}</span> </h3>
                <!-- <div class="btn-group kim" style="width: 120px;">
                    <select name="status" id="status" class="form-control selectpicker">
                        <option value="0" {{$lead->status == 0 ? 'selected' : ''}}>Pending</option>
                        @if($status->count() > 0)
                        @foreach($status as $stat)
                        <option value="{{$stat['id']}}" {{$lead->status == $stat['id'] ? 'selected' : ''}}> {{$stat['display_name']}} </option>
                        @endforeach
                        @endif
                    </select>
                </div>  -->
                <p>{{$address_data??''}}</p>
                <ul>
                    <li>
                        @if($lead_contacts[0]->email??'')
                        <img src="{{url('/').'/'.asset('assets/img')}}/mail.svg"> <span>{{$lead_contacts[0]->email??''}}</span>
                        @endif
                    </li>
                    <li>
                        @if($lead_contacts[0]->phone_number??'')
                        <img src="{{url('/').'/'.asset('assets/img')}}/phone.svg"> <span>{{$lead_contacts[0]->phone_number??''}}</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
        <div class="button-box">
            <button class="btn btn-primary me-2" onclick="$('#frmLeadNotesAdd').show()"><img src="{{url('/').'/'.asset('assets/img')}}/paper.svg"> Note</button>
            @if($lead_contacts[0]->email??'')
            <a  href="mailto:{{$lead_contacts[0]->email??''}}" class="btn btn-outline-primary text-white email_s"><img src="{{url('/').'/'.asset('assets/img')}}/btnmail.svg">Email</a>
            @endif
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
<!-- Left Column: Tasks and Contacts -->
<div class="col-md-4 ">
    <div class="new-task">
<!-- Tasks -->
<div class="card forus mb-4">
    <div class="card-header d-flex justify-content-between">
        <strong onclick="toggleCardBody()">Tasks</strong>
        <a class="text-decoration-none" onclick="createTask()">+ Add</a>
    </div>
    <div class="card-body" id="cardBody">

<!--  -->
<form method="POST" 
action="{{ route('lead-tasks.store') }}" class="form-horizontal taskform" id="frmLeadTaskCreate" enctype="multipart/form-data" style="display:none;">
@csrf
<div class="row">
    <div class="col-md-12 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <label for="users">User<span style="color:red">*</span></label>
            <select class="select2" name="assigned_to" id="assigned_to" data-style="select-with-transition" title="Select User">
                <option value="">Select User</option>
                @if(@isset($users))
                @foreach($users as $user)
                <option value="{!! $user->id !!}">{!! $user->name !!}</option>
                @endforeach
                @endif
            </select>
            @if($errors->has('assigned_to'))
            <p class="help-block">
                <strong>{{ $errors->first('assigned_to') }}</strong>
            </p>
            @endif
        </div>
    </div>

    <div class="col-md-12 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <label for="description">Description<span style="color:red">*</span></label>
            <input type="hidden" name="lead_id" value="{{$lead->id??''}}">
            <input type="hidden" name="task_id"  id="task_id" value="">
            <input type="text"  name="description" id="description" value="{{ old('description','') }}"  class="form-control" placeholder="Description">
            @if($errors->has('description'))
            <p class="help-block">
                <strong>{{ $errors->first('description') }}</strong>
            </p>
            @endif
        </div>
    </div>
    <div class="col-md-6 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <label for="date">Date <span style="color:red">*</span></label>

            <input type="text"  name="date" id="task_date" value="{{ old('date','') }}"  class="form-control datepicker" readonly="true" placeholder="Date">
            @if($errors->has('date'))
            <p class="help-block">
                <strong>{{ $errors->first('date') }}</strong>
            </p>
            @endif
        </div>
    </div>
    <div class="col-md-6 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <label for="time">Time <span style="color:red">*</span></label>
            <input type="time"  name="time" id="time" value="{{ old('time','') }}"  class="form-control" placeholder="Time">
            @if($errors->has('time'))
            <p class="help-block">
                <strong>{{ $errors->first('time') }}</strong>
            </p>
            @endif
        </div>     
    </div>

    <div class="col-md-6 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <div class="d-flex flex-row">
                <button type="button" class="btn btn-default mr-2" onclick="$('#frmLeadTaskCreate').hide()">Cancel</button>
                <button type="submit" class="btn btn-default">Save</button>
            </div>
        </div>
    </div>

</div>
</form>

<ul class="list-group list-group-flush list-task contact-list">
    @foreach($lead_tasks as $lead_task)
    <li> 
        <div class="d-flex flex-row justify-content-between">
            <div class="wer">
                <p>{{$lead_task->description??''}}</p>
                <p><img src="{{ url('/').'/'.asset('assets/img/leaddate.svg') }}"> <span class="date">{{date("d F Y",strtotime($lead_task->date))}}</span>
                </p>
            </div>

            <div class="d-flex flex-row per">
                <div class="editbox" onclick="editTask('{{$lead_task}}')"> <img src="{{ url('/').'/'.asset('assets/img/ph_note-pencil-fill.svg') }}">
                </div>

                <div class="deletebox">
                    <form action="{{ route('lead-tasks.destroy', $lead_task->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Do you really want to delete?')">
                        @csrf
                        @method('DELETE')
                        <div type="submit" class="editbox">
                            <button class="deletebtn" type="submit"><img src="{{ url('/').'/'.asset('assets/img/deletbox.svg') }}"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </li>
    @endforeach
</ul>
</div>
<!--  -->
</div>

<!-- Contacts -->
<div class="card forus">
    <div class="card-header d-flex justify-content-between" >
        <strong onclick="toggleCardBody1()">Contacts</strong>
        <a  class="text-decoration-none" onclick="createContact()">+ Add</a>
    </div>
    <div class="card-body" id="cardBody1">

<!--  -->
<form method="POST" 
action="{{ route('lead-contacts.store') }}" class="form-horizontal contact-task-form" id="frmLeadContactsCreate" enctype="multipart/form-data" style="display:none;">
@csrf
<div class="row">
    <div class="col-md-12 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <label for="name">Contact Name <span style="color:red">*</span></label>
            <input type="hidden" name="contact_id" id="contact_id" value="">
            <input type="hidden" name="lead_id" value="{{$lead->id??''}}">
            <input type="text"  name="name" id="name" value="{{ old('name','') }}"  class="form-control" placeholder="Contact Name">
            @if($errors->has('name'))
            <p class="help-block">
                <strong>{{ $errors->first('name') }}</strong>
            </p>
            @endif
        </div>
    </div>
    <div class="col-md-12 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <label for="title">Title <span style="color:red">*</span></label>

            <input type="text"  name="title" id="title" value="{{ old('title','') }}"  class="form-control" placeholder="Title">
            @if($errors->has('title'))
            <p class="help-block">
                <strong>{{ $errors->first('title') }}</strong>
            </p>
            @endif
        </div>
    </div>
    <div class="col-md-12 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <label for="phone_number">Phone Number <span style="color:red">*</span></label>
            <input type="text"  name="phone_number" id="phone_number" value="{{ old('phone_number','') }}"  class="form-control" placeholder="Phone Number">
            @if($errors->has('phone_number'))
            <p class="help-block">
                <strong>{{ $errors->first('phone_number') }}</strong>
            </p>
            @endif
        </div>     
    </div>
    <div class="col-md-6 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <label for="contact_email">Email <span style="color:red">*</span></label>
            <input type="text"  name="contact_email" id="contact_email" value="{{ old('contact_email','') }}"  class="form-control" placeholder="Email">
            @if($errors->has('contact_email'))
            <p class="help-block">
                <strong>{{ $errors->first('contact_email') }}</strong>
            </p>
            @endif
        </div>     
    </div>
    <div class="col-md-6 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <label for="url">url <span style="color:red">*</span></label>
            <input type="text"  name="url" id="url" value="{{ old('Url','') }}"  class="form-control" placeholder="Url">
            @if($errors->has('url'))
            <p class="help-block">
                <strong>{{ $errors->first('url') }}</strong>
            </p>
            @endif
        </div>     
    </div>
    <div class="col-md-6 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <div class="d-flex flex-row mt-2">
                <button type="submit" class="btn btn-default mr-2">Save</button>
                <button type="button" class="btn btn-default" onclick="$('#frmLeadContactsCreate').hide()">Cancel</button>

            </div>
        </div>
    </div>

</div>
</form>
<!--  -->
<ul class="list-group list-group-flush list-task cn-list">
    @foreach($lead_contacts as $lead_contact)
    <li>
        <div class="contact-person">
            <h5>{{$lead_contact->name??''}}</h5>
            <p>{{$lead_contact->title??''}}</p>
        </div>
        <div class="contac-link">

<!-- <p><img src="{{ url('/').'/'.asset('assets/img/whatsup.svg') }}"></p> -->
@if($lead_contact->email)
<p><img src="{{ url('/').'/'.asset('assets/img/mail.svg') }}" alt="Mail icon" 
    title="{{$lead_contact->email}}" 
    data-toggle="tooltip" ></p>
    @endif
    @if($lead_contact->phone_number)
    <p><img src="{{ url('/').'/'.asset('assets/img/phone.svg') }}" alt="Mail icon" 
        title="{{$lead_contact->phone_number}}" 
        data-toggle="tooltip"></p>
        @endif
    </div>
    <div class="editdelte d-flex flex-row ">

        <div class="contact_edit" onclick="editContact('{{$lead_contact}}')"><img src="{{ url('/').'/'.asset('assets/img/ph_note-pencil-fill.svg') }}"></div>
        <div class="contact_edit">
            <form action="{{ route('lead-contacts.destroy', $lead_contact->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Do you really want to delete?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="deletebtn">
                    <img src="{{ url('/').'/'.asset('assets/img/deletbox.svg') }}">
                </button>
            </form>
        </div>
    </div>
</li>
@endforeach
</ul>
</div>
</div>

<!--opportunities  -->
<div class="card forus">
    <div class="card-header d-flex justify-content-between" >
        <strong onclick="toggleCardBody2()">Opportunities</strong>
        <a onclick="createOpportunity()" class="text-decoration-none">+ Add</a>
    </div>
    <div class="card-body" id="cardBody2">

<!--  -->
<form method="POST" 
action="{{ route('lead-opportunities.store') }}" class="form-horizontal taskform" id="frmLeadOpportunitiesCreate" enctype="multipart/form-data" style="display:none;" >
@csrf
<div class="row">
<div class="col-md-12 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <label for="amount">Amount</label>
            <input type="number" step="0.01" min="0"  name="amount" id="o_amount" value="{{ old('amount','0.00') }}"  class="form-control" placeholder="Amount">
            @if($errors->has('amount'))
            <p class="help-block">
                <strong>{{ $errors->first('amount') }}</strong>
            </p>
            @endif
        </div>
    </div>
    <div class="col-md-12 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <label for="users">User<span style="color:red">*</span></label>
            <select class="select2" name="assigned_to" id="o_assigned_to" data-style="select-with-transition" title="Select User">
                <option value="">Select User</option>
                @if(@isset($users))
                @foreach($users as $user)
                <option value="{!! $user->id !!}">{!! $user->name !!}</option>
                @endforeach
                @endif
            </select>
            @if($errors->has('assigned_to'))
            <p class="help-block">
                <strong>{{ $errors->first('assigned_to') }}</strong>
            </p>
            @endif
        </div>
    </div>

    <div class="col-md-12 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <label for="lead_contact_id">Contact<span style="color:red">*</span></label>
            <select class="select2" name="lead_contact_id" id="lead_contact_id" data-style="select-with-transition" title="Select Contact">
                <option value="">Select Contact</option>
                @if(@isset($lead_contacts))
                @foreach($lead_contacts as $lead_contact)
                <option value="{!! $lead_contact->id !!}">{!! $lead_contact->name !!}</option>
                @endforeach
                @endif
            </select>
            @if($errors->has('lead_contact_id'))
            <p class="help-block">
                <strong>{{ $errors->first('lead_contact_id') }}</strong>
            </p>
            @endif
        </div>
    </div>

    <div class="col-md-12 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <label for="note">Note<span style="color:red">*</span></label>
            <input type="hidden" name="lead_id" value="{{$lead->id??''}}">
            <input type="hidden" name="opportunity_id"  id="opportunity_id" value="">
            <input type="text"  name="note" id="o_note" value="{{ old('note','') }}"  class="form-control" placeholder="Note">
            @if($errors->has('note'))
            <p class="help-block">
                <strong>{{ $errors->first('note') }}</strong>
            </p>
            @endif
        </div>
    </div>
    <div class="col-md-12 pr-1 pl-1">
        <div class="col-md-12 form-group">
            <label for="confidence" class="d-block mb-0">Confidence<span style="color:red">*</span></label>

            <div class="boxing mt-0">
                <span id="confidenceValue">50%</span>
            </div>
            <div class="border-s">
                <input type="range" class="form-control-range" name="confidence" id="confidence" value="50" min="0" max="100">
            </div>

<!-- <input type="text"  name="confidence" id="confidence" value="{{ old('confidence','') }}"  class="form-control" placeholder="Confidence"> -->


@if($errors->has('confidence'))
<p class="help-block">
    <strong>{{ $errors->first('confidence') }}</strong>
</p>
@endif
</div>
</div>
<div class="col-md-12 pr-1 pl-1">
    <div class="col-md-12 form-group">
        <label for="estimated_close_date">Estimated Close Date <span style="color:red">*</span></label>

        <input type="text"  name="estimated_close_date" id="estimated_close_date" value="{{ old('estimated_close_date','') }}"  class="form-control datepicker" readonly="true" placeholder="Date">
        @if($errors->has('estimated_close_date'))
        <p class="help-block">
            <strong>{{ $errors->first('estimated_close_date') }}</strong>
        </p>
        @endif
    </div>
</div>
<div class="col-md-12 pr-1 pl-1">
    <div class="col-md-12 ">
        {!! Form::select('status', config('constants.OPPORTUNITY_STATUS'),old('status',''), array('class' => 'form-control','id'=>'status')) !!}

        @if($errors->has('status'))
        <p class="help-block">
            <strong>{{ $errors->first('status') }}</strong>
        </p>
        @endif
    </div>
</div>
<div class="col-md-6 pr-1 pl-1">
    <div class="col-md-12 form-group">
        <div class="d-flex flex-row mt-3">
            <button type="submit" class="btn btn-default mr-2">Save</button>
            <button type="button" class="btn btn-default" onclick="$('#frmLeadOpportunitiesCreate').hide()">Cancel</button>
        </div>

    </div>
</div>

</div>
</form>
<!--  -->
<ul class="list-group list-group-flush list-task cn-list appurt">
    @foreach($lead_opportunities  as $lead_opportunity)
    <li>
        <div class="contact-person">
            <div class="d-flex flex-row justify-content-between gh">
                <div class="">
                    <h5>{{$lead_opportunity->note}}</h5>
                    <p>{{$lead_opportunity->amount}}</p>
                </div>
                <div class="d-flex flex-row sdm">
                    <div class="editsource" onclick="editOpportunity('{{$lead_opportunity}}')"><img src="{{ url('/').'/'.asset ('assets/img/ph_note-pencil-fill.svg') }}">
                    </div>
                    <div class="deletebox">
                        <form action="{{ route('lead-opportunities.destroy', $lead_opportunity->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Do you really want to delete?')">
                            @csrf
                            @method('DELETE')
                            <button class="deletebtn editsource" type="submit" class="">
                                <img src="{{ url('/').'/'.asset ('assets/img/deletbox.svg') }}">
                            </button>
                        </form>
                    </div>

                </div>
            </div>


        </div>
    </li>
    @endforeach
</ul>
</div>
</div>

<!-- opportunities end -->
</div>

</div>

<!-- Right Column: Activities -->
<div class="col-md-8">

    <div class="tabsection">

<!--- new tab--->
<div class="card p-0">
<!-- Tab Navigation -->
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="activities-tab" data-toggle="tab" href="#activities" role="tab">Activities</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="notes-tab" data-toggle="tab" href="#notes" role="tab">Notes</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="files-tab" data-toggle="tab" href="#files" role="tab">Files</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="tasks-tab" data-toggle="tab" href="#tasks" role="tab">Tasks</a>
    </li>
</ul>
</div>

<div class="card p-0">
<!-- Tab Content -->
<div class="tab-content p-0 mt-0" id="myTabContent">
    <div class="tab-pane fade show active" id="activities" role="tabpanel">

        <div class="card-header">
            <p class="hed m-0">Activities</p>
        </div>
        <div class="card-body">

            <div class="mb-3">
                <form method="POST" 
                action="{{ route('lead-notes.store') }}" class="form-horizontal" id="frmLeadNotesAdd" enctype="multipart/form-data" style="display:none;">
                @method('POST')
                @csrf
                <input type="hidden" name="lead_id" value="{{$lead->id??''}}">
                <input type="hidden" name="note_id" id="note_id" >
                {!! Form::textarea('note', old('note',''), ['class' => 'form-control rounded border ckeditor-init', 'placeholder' => 'Add a note...','rows'=>8,'id'=>'note_text']) !!}
                <div class="text-end mt-2">
                    <button class="btn btn-sm btn-primary donebtn" type="submit">Done</button>
                </div>
            </form>
        </div>

        @php
        $previousDate = null;
        @endphp
        @foreach($combined as $lead_note)
        @php
        $noteDate = \Carbon\Carbon::parse($lead_note->created_at)->format('d M Y'); // e.g., 28 Feb 2024
        $noteTime = \Carbon\Carbon::parse($lead_note->created_at)->format('h:i a'); // e.g., 10:25 pm
        @endphp
        <div>
            @if($noteDate !== $previousDate)
            <h6 class="text-muted date_listing mt-3"> <img src="{{ url('/').'/'.asset('assets/img/leaddate.svg') }}"> {{ $noteDate }}</h6>
            @php
            $previousDate = $noteDate;
            @endphp
            @endif
<!-- code 28 feb 2024  -->

<!--new div--->
<div class="card mb-2 p-0">
    <div class="main-div">
        <div class="list-one">
            <div class="image_box">
                <div class="greenbox">
                    @if($lead_note->type == 'note')
                    <img src="{{url('/').'/'.asset('assets/img')}}/cup.svg">
                    @else
                    <img src="{{url('/').'/'.asset('assets/img')}}/task_logo.png" width="35">
                    @endif
                </div>
            </div>
            <div class="listdata">
                @if($lead_note->type == 'note')
                <h5>{!! $lead_note->note??''!!}</h5>
                @else
                <h5>{!! $lead_note->description??''!!}({{date('d M Y', strtotime($lead_note->date))}}, {{date('h:i A', strtotime($lead_note->time))}})</h5>
                @endif

                <p></p>

                <p class="date-list">{{$noteTime}}</p>
            </div>
        </div>
        @if($lead_note->type == 'note')
        <div class="list-two">
            <div class="dropdown">
                <button class="dropdown-toggle" type="button" id="dropdownMenuButton"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{url('/').'/'.asset('assets/img')}}/point.svg">

            </button>

            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <div class="dropdown-item"  onclick="editNote('{{$lead_note}}')">
                    <a>Edit</a>
                </div>
                <div class="dropdown-item">
                    <form action="{{ route('lead-notes.destroy', $lead_note->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Do you really want to delete?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="">
                            delete
                        </button>
                    </form>
                </div>



            </div>
        </div>

    </div>
    @endif
</div>

</div>
</div>
@endforeach
</div>
</div>
<div class="tab-pane fade" id="notes" role="tabpanel">
    <div class="card-header">
        <p class="hed m-0">Notes</p>
    </div>
    <div class="card-body">

        @php
        $previousDate1 = null;
        @endphp
        @foreach($lead_notes as $lead_note)
        @php
        $noteDate = \Carbon\Carbon::parse($lead_note->created_at)->format('d M Y'); // e.g., 28 Feb 2024
        $noteTime = \Carbon\Carbon::parse($lead_note->created_at)->format('h:i a'); // e.g., 10:25 pm
        @endphp
        <div>
            @if($noteDate !== $previousDate1)
            <h6 class="text-muted date_listing mt-3"> <img src="{{ url('/').'/'.asset('assets/img/leaddate.svg') }}"> {{ $noteDate }}</h6>
            @php
            $previousDate1 = $noteDate;
            @endphp
            @endif
<!-- code 28 feb 2024  -->

<!--new div--->
<div class="card mb-2 p-0">
    <div class="main-div">
        <div class="list-one">
            <div class="image_box">
                <div class="greenbox">
                    <img src="{{url('/').'/'.asset('assets/img')}}/cup.svg">
                </div>
            </div>
            <div class="listdata">
                <h5>{!! $lead_note->note??''!!}</h5>

                <p></p>

                <p class="date-list">{{$noteTime}}</p>
            </div>
        </div>
        <div class="list-two">
            <div class="dropdown">
                <button class="dropdown-toggle" type="button" id="dropdownMenuButton"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{url('/').'/'.asset('assets/img')}}/point.svg">

            </button>

            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <div class="dropdown-item">
                    <a onclick="editNote('{{$lead_note}}')">Edit</a>
                </div>
                <div class="dropdown-item">
                    <form action="{{ route('lead-notes.destroy', $lead_note->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Do you really want to delete?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="">
                            delete
                        </button>
                    </form>
                </div>



            </div>
        </div>

    </div>
</div>

</div>
</div>
@endforeach
</div>
</div>
<div class="tab-pane fade" id="files" role="tabpanel">

    <div class="card-header">
        <p class="hed m-0">Files</p>
    </div>
    <div class="card-body">
    @if(count($media_items) > 0)
        <ul class="">
            @foreach($media_items as $media)
            <li class="d-flex flex-row justify-content-between align-items-center mb-3">
                <a href="{{ $media->getFullUrl() }}" target="_blank">
                    <div class="imagebox">
                        <div class="d-flex flex-row align-items-center">
                            @php
                                $mime = $media->mime_type;
                            @endphp

                            @if(str_contains($mime, 'image'))
                                <img src="{{ $media->getFullUrl() }}" alt="{{ $media->name }}" width="50" height="50">
                            @elseif(str_contains($mime, 'pdf'))
                                <img src="{{ url('/').'/'.asset('assets/img/pdf-icon.jpg') }}" alt="PDF" width="40"> 
                            @elseif(str_contains($mime, 'spreadsheet') || str_contains($mime, 'excel'))
                                <img src="{{ url('/').'/'.asset('assets/img/excel-icon.png') }}" alt="Excel" width="40"> 
                            @else
                                <img src="{{ url('/').'/'.asset('images/file-icon.png') }}" alt="File" width="40"> 
                            @endif

                            <div class="imagedata">
                                <p>{{ $media->file_name }}</p>
                                <p>{{ strtoupper($media->mime_type) }} • {{ number_format($media->size / 1024, 1) }} KB</p>
                            </div>
                        </div>
                    </div>
                </a>
                <div class="buttonbox">
                    <a href="{{ route('leads-deleteMedia') }}?media_id={{ $media->id }}"
                    onclick="return confirm('Do you really want to delete?')">
                    <img src="{{ url('/').'/'.asset('assets/img/deletbox.svg') }}">
                    </a>
                </div>
            </li>
            @endforeach 
        </ul>
        @endif

        <div>

            <form method="POST" 
            action="{{ route('leads.uploadleadFiles') }}" class="form-horizontal taskform" id="frmLeadfileUpload" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12 pr-1 pl-1">
                    <input type="hidden" name="lead_id" value="{{$lead->id??''}}">
                    <div class="fileinput fileinput-new" data-provides="fileinput">

                        <label class="bmd-label-floating">Lead file</label>
                        @if ($errors->has('lead_file'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('lead_file') }}</p></div>
                        @endif

                        <div class="fileinput-new thumbnail">
                            <img src="{!! url('/').'/'.asset('assets/img/up.png') !!}" class="imagepreview7">
                            <div class="selectThumbnail">
                                <span class="btn btn-just-icon btn-round btn-file">
                                    <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                                    <span class="fileinput-exists">Change</span>
                                    <input type="file" name="lead_file" class="getimage7" accept="image/*,application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                                </span>
                                <br>
                                <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                            </div>
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>

                    </div>
                </div>

                <div class="col-md-6 pr-1 pl-1">
                    <div class="col-md-12 form-group">
                        <div class="d-flex flex-row">
                            <button type="submit" class="btn btn-default">Save</button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
</div>
<div class="tab-pane fade" id="tasks" role="tabpanel">

    <div class="card-header">
        <p class="hed m-0"> Task</p>
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush list-task contact-list">
            @foreach($lead_tasks as $lead_task)
            <li> 
                <div class="d-flex flex-row justify-content-between">
                    <div class="">
                        <p>{{$lead_task->description??''}}</p>
                        <p><img src="{{ url('/').'/'.asset('assets/img/leaddate.svg') }}"> <span class="date">{{date("d F Y",strtotime($lead_task->date))}}</span>
                        </p>
                    </div>
                    <div class="d-flex flex-row">
                        <div class="editbox" onclick="editTask('{{$lead_task}}')"> <img src="{{ url('/').'/'.asset('assets/img/ph_note-pencil-fill.svg') }}"></div>
                        <div class="deletebox editbox">
                            <form action="{{ route('lead-tasks.destroy', $lead_task->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Do you really want to delete?')">
                                @csrf
                                @method('DELETE')
                                <button class="deletebtn" type="submit" class="">
                                    <img src="{{ url('/').'/'.asset('assets/img/deletbox.svg') }}">

                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>
</div>
</div>

<!--- end new tab---->
</div>


</div>
</div>
</div>
<!--  -->



</div>
</div>
</div>
</div>


<div class="modal fade" id="updateLeadModel" role="dialog">
    <div class="modal-dialog">

<!-- Modal content-->
<form method="POST" 
action="{{ route('leads.update',$lead) }}" class="form-horizontal" id="frmLeadsUpdate" enctype="multipart/form-data">
@method('PUT')
@csrf
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Lead</h4>
    </div>


    <div class="modal-body">
        <div class="row">
            <div class="col-md-12 pr-1 pl-1">
                <div class="col-md-12 form-group">
                    <label for="company_name">Name <span style="color:red">*</span></label>
                    <input type="text"  name="company_name" id="company_name" value="{{ old('company_name',$lead->company_name??'') }}"  class="form-control" placeholder="Name">
                    @if($errors->has('company_name'))
                    <p class="help-block">
                        <strong>{{ $errors->first('company_name') }}</strong>
                    </p>
                    @endif
                </div>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">Canel</button>
        <button type="submit" class="btn btn-default">Save</button>
    </div>
</div>
</form>
</div>
</div>

<div class="modal fade" id="AddAddressModel" role="dialog">
    <div class="modal-dialog">

<!-- Modal content-->
<form method="POST" 
action="{{ route('leads.storeAddress') }}" class="form-horizontal" id="frmLeadsAddress" enctype="multipart/form-data">
@method('POST')
@csrf
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Address</h4>
    </div>


    <div class="modal-body">
        <div class="row">
            <div class="col-md-12 pr-1 pl-1">
                <div class="col-md-12 form-group">
                    <label for="address1">Address <span style="color:red">*</span></label>
                    <input type="hidden" name="lead_id" value="{{$lead->id??''}}">
                    <input type="hidden" name="address_id" value="{{$address->id??''}}">
                    <input type="text" name="address1" class="form-control" value="{!! old( 'address1',$address->address1??'' ) !!}" maxlength="200" required>
                    @if ($errors->has('address1'))
                    <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('address1') }}</p></div>
                    @endif
                </div>
                <div class="col-md-12 form-group">
                    <label for="address2">Address2</label>
                    <input type="text" name="address2" class="form-control" value="{!! old( 'address2',$address->address2??'' ) !!}" maxlength="200" >
                    @if ($errors->has('address2'))
                    <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('address2') }}</p></div>
                    @endif
                </div>
                <div class="col-md-12 form-group">
                    <label for="country">Country <span style="color:red">*</span></label>
                    <select class="form-control select2 country" name="country_id" onchange="getStateList()" style="width: 100%;" id="country_id">
                        <option value="">Select {!! trans('panel.global.country') !!}</option>
                        @if(@isset($countries ))
                        @foreach($countries as $country)
                        <option value="{!! $country['id'] !!}" @if(isset($address) && $address->country_id==$country['id']) selected @endif >{!! $country['country_name'] !!}</option>
                        @endforeach
                        @endif
                    </select>
                    @if ($errors->has('country_id'))
                    <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('country_id') }}</p></div>
                    @endif
                </div>
                <div class="col-md-12 form-group">
                    <label for="state_id">State <span style="color:red">*</span></label>
                    <select class="form-control select2 state" name="state_id" id="state_id" onchange="getDistrictList()" style="width: 100%;">
                        @if(isset($address) && $address->state_id)
                        <option value="{!!  $address->state_id !!}">{!! $address->statename->state_name??'' !!}</option>
                        @else
                        <option value="">Select {!! trans('panel.global.state') !!}</option>
                        @endif
                    </select>
                    @if ($errors->has('state_id'))
                    <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('state_id') }}</p></div>
                    @endif
                </div>
                <div class="col-md-12 form-group">
                    <label for="district_id">District </label>
                    <select class="form-control select2 district" name="district_id" id="district_id" onchange="getCityList()" style="width: 100%;">
                        @if(isset($address) && $address->district_id)
                        <option value="{!!  $address->district_id !!}">{!! $address->cityname->city_name??'' !!}</option>
                        @else
                        <option value="">Select {!! trans('panel.global.district') !!}</option>
                        @endif

                    </select>
                    @if ($errors->has('district_id'))
                    <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('district_id') }}</p></div>
                    @endif
                </div>
                <div class="col-md-12 form-group">
                    <label for="city_id">City <span style="color:red">*</span></label>
                    <select class="form-control select2 city" name="city_id" id="city_id" onchange="getPincodeList()" style="width: 100%;">
                        @if(isset($address) && $address->city_id)
                        <option value="{!!  $address->city_id !!}">{!! $address->cityname->city_name??'' !!}</option>
                        @else
                        <option value="">Select {!! trans('panel.global.city') !!}</option>
                        @endif
                    </select>
                    @if ($errors->has('city_id'))
                    <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('city_id') }}</p></div>
                    @endif
                </div>
                <div class="col-md-12 form-group">
                    <label for="pincode_id">pincode <span style="color:red">*</span></label>
                    <select class="form-control pincode select2" name="pincode_id" id="pincode_id" onchange="getAddressData()" style="width: 100%;">
                        <option value="">Select {!! trans('panel.global.pincode') !!}</option>
                        @if(@isset($pincodes ))
                        @foreach($pincodes as $pincode)
                        <option value="{!! $pincode['id'] !!}" @if(isset($address) && $address->pincode_id==$pincode['id']) selected @endif >{!! $pincode['pincode'] !!}</option>
                        @endforeach
                        @endif
                    </select>
                    @if ($errors->has('pincode_id'))
                    <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('pincode_id') }}</p></div>
                    @endif
                </div>

            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">Canel</button>
        <button type="submit" class="btn btn-default">Save</button>
    </div>
</div>
</form>
</div>
</div>

<script>
    const rangeInput = document.getElementById("confidence");
    const confidenceValue = document.getElementById("confidenceValue");

    rangeInput.addEventListener("input", function () {
        confidenceValue.textContent = this.value + "%";
    });
</script>


<script>
    function toggleCardBody() {
        const body = document.getElementById("cardBody");
        body.style.display = (body.style.display === "none") ? "block" : "none";
    }

    function toggleCardBody1() {
        const body = document.getElementById("cardBody1");
        body.style.display = (body.style.display === "none") ? "block" : "none";
    }

    function toggleCardBody2() {
        const body = document.getElementById("cardBody2");
        body.style.display = (body.style.display === "none") ? "block" : "none";
    }



</script>

<script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
<!-- Load jQuery and moment.js -->

<script type="text/javascript" src="{{ url('/').'/'.asset('vendor/ckeditor/js/ckeditor.js') }}"></script>
<script>

    jQuery(document).ready(function(){

        
        $('#task_date').on('focusout', function() {
          $(this).css('z-index', '');
        }); 
        $('#estimated_close_date').on('focusout', function() {
          $(this).css('z-index', '');
        });

        jQuery('#frmLeadContactsCreate').validate({
            rules: {
                name: {
                    required: true
                },
                title: {
                    required: true
                },
                phone_number: {
                    required: true,
                    number: true
                },
                contact_email: {
//required: true,
                    email:true
                },
// url: {
//     required: true
// },             
            }

        });

        jQuery('#frmLeadTaskCreate').validate({
            rules: {
                assigned_to: {
                    required: true
                },
                description: {
                    required: true
                },
                date: {
                    required: true
                },
                // time: {
                //     required: true
                // }           
            }

        });


        jQuery('#frmLeadsUpdate').validate({
            rules: {
                company_name: {
                    required: true
                },
            }
        });

        jQuery('#frmLeadNotesAdd').validate({
            ignore: [],
            rules: {
                note: {
                    required: true
                },
            }
        });

        jQuery('#frmLeadOpportunitiesCreate').validate({
            rules: {
                assigned_to: {
                    required: true
                },
                lead_contact_id: {
                    required: true
                },
                note: {
                    required: true
                },
amount: {
    required: true,
    number:true
},
// type: {
//     required: true
// },
                confidence: {
                    required: true,
                    number:true,
                    max: 100,
                    min: 0
                },
                estimated_close_date: {
                    required: true
                }, 
                status: {
                    required: true
                },
            }
        });

        jQuery('#frmLeadsAddress').validate({
            rules: {
                address1: {
                    required: true
                },
// address2: {
//     required: true
// },
                country_id: {
                    required: true
                },
                state_id: {
                    required: true
                },
// district_id: {
//     required: true
// },
                city_id: {
                    required: true
                },
                pincode_id: {
                    required: true
                },
            }
        });




// 1. Initialize CKEditor for all matching elements
        document.querySelectorAll('.ckeditor-init').forEach(function (item) {
            var editor = CKEDITOR.replace(item, {
                customConfig: 'config.js',
                toolbar: 'Basic',
                height: '15em'
            });

// 2. Update the textarea content on change (already good)
            editor.on('change', function () {
                this.updateElement();
            });
        });


    });

    function createTask(){
        $('#task_id').val('');
        $('#frmLeadTaskCreate :input:not(:button, [type="hidden"])').val('');
        $('#frmLeadTaskCreate #assigned_to').change();
        $('#frmLeadTaskCreate').show();
    }
    function editTask(task_data){

        if (typeof task_data === "string") {
            task_data = JSON.parse(task_data);
        }

        $('#frmLeadTaskCreate').show();
//console.log(task_data);
        $('#frmLeadTaskCreate #assigned_to').val(task_data.assigned_to);
        $('#frmLeadTaskCreate #assigned_to').change();
        $('#frmLeadTaskCreate #description').val(task_data.description);
        $('#frmLeadTaskCreate #task_date').val(task_data.date);
        $('#frmLeadTaskCreate #time').val(task_data.time);
        $('#frmLeadTaskCreate #task_id').val(task_data.id);


    }

    function createContact(){
        $('#contact_id').val('');
        $('#frmLeadContactsCreate :input:not(:button, [type="hidden"])').val('');
        $('#frmLeadContactsCreate').show();
    }

    function editContact(contact_data){

        if (typeof contact_data === "string") {
            contact_data = JSON.parse(contact_data);
        }

        $('#frmLeadContactsCreate').show();
        $('#contact_id').val(contact_data.id);
        $('#frmLeadContactsCreate #name').val(contact_data.name);
        $('#frmLeadContactsCreate #title').val(contact_data.title);
        $('#frmLeadContactsCreate #phone_number').val(contact_data.phone_number);
        $('#frmLeadContactsCreate #contact_email').val(contact_data.email);
        $('#frmLeadContactsCreate #url').val(contact_data.url);
    }


    function createOpportunity(){
        $('#opportunity_id').val('');
        $('#frmLeadOpportunitiesCreate :input:not(:button, [type="hidden"])').val('');
        $('#frmLeadOpportunitiesCreate #o_amount').val(0);
        $('#frmLeadOpportunitiesCreate').show();
        $('#frmLeadOpportunitiesCreate #o_assigned_to').change();
        $('#frmLeadOpportunitiesCreate #lead_contact_id').change();
        var firstVal = $('#frmLeadOpportunitiesCreate #status option:first').val();
        $('#frmLeadOpportunitiesCreate #status').val(firstVal).change();
//var firstVal = $('#frmLeadOpportunitiesCreate #type option:first').val();
//$('#frmLeadOpportunitiesCreate #type').val(firstVal).change();

    }

    function editOpportunity(opportunity_data){

        if (typeof opportunity_data === "string") {
            opportunity_data = JSON.parse(opportunity_data);
        }
        console.log(opportunity_data);

        $('#opportunity_id').val(opportunity_data.id);
        $('#frmLeadOpportunitiesCreate #o_assigned_to').val(opportunity_data.assigned_to);
        $('#frmLeadOpportunitiesCreate #lead_contact_id').val(opportunity_data.lead_contact_id);
        $('#frmLeadOpportunitiesCreate #o_assigned_to').change();
        $('#frmLeadOpportunitiesCreate #lead_contact_id').change();
        $('#frmLeadOpportunitiesCreate #o_note').val(opportunity_data.note);
$('#frmLeadOpportunitiesCreate #o_amount').val(opportunity_data.amount);
        $('#frmLeadOpportunitiesCreate #confidence').val(opportunity_data.confidence);
        $('#frmLeadOpportunitiesCreate #confidenceValue').html(opportunity_data.confidence+"%");
        $('#frmLeadOpportunitiesCreate #estimated_close_date').val(opportunity_data.estimated_close_date);
        $('#frmLeadOpportunitiesCreate #status').val(opportunity_data.status);
        $('#frmLeadOpportunitiesCreate #status').change();
//$('#frmLeadOpportunitiesCreate #type').val(opportunity_data.type);
// $('#frmLeadOpportunitiesCreate #type').change();
        $('#frmLeadOpportunitiesCreate').show();


    }

    function editNote(note){

        if (typeof note === "string") {
            note = JSON.parse(note);
        }

        $('#frmLeadNotesAdd').show();
        $('#note_id').val(note.id);
        $('#note_text').val(note.note);

// Set value in CKEditor
        if (CKEDITOR.instances['note_text']) {
            CKEDITOR.instances['note_text'].setData(note.note);
        } else {
            $('#note_text').val(note.note);
        }
    }

    function openAddAddressModel(){

        $('#country_id').select2({
            dropdownParent: $('#AddAddressModel')
        });
        $('#state_id').select2({
            dropdownParent: $('#AddAddressModel')
        });
        $('#district_id').select2({
            dropdownParent: $('#AddAddressModel')
        });
        $('#city_id').select2({
            dropdownParent: $('#AddAddressModel')
        });
        $('#pincode_id').select2({
            dropdownParent: $('#AddAddressModel')
        });
    }

//     $('#status').on('change', function () {
//     alert(this.value);
//     var status = this.value;
//     Swal.fire({
//         title: 'Are you sure?',
//         text: "You won't be able to revert this!",
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonColor: '#3085d6',
//         cancelButtonColor: '#d33',
//         confirmButtonText: 'Yes, change it!'
//     }).then((result) => {
//         if (result.value) {
//             $.ajax({
//                 url: "{{ route('leads.changeStatus') }}",
//                 method: 'POST',
//                 data: {
//                     lead_id: '{{$lead->id}}',
//                     status: status,
//                     _token: '{{ csrf_token() }}'
//                 },
//                 success: function (res) {
//                     if (res.status == 'success') {
//                         Swal.fire({
//                             icon: 'success',
//                             title: res.message,
//                             showConfirmButton: true,
//                             timer: 2000
//                         })
//                     }else{
//                         Swal.fire({
//                             icon: 'error',
//                             title: res.message,
//                             showConfirmButton: true,
//                             timer: 2000
//                         })
//                     }
//                 }
//             });
//         }
//     });
// });


</script>
</x-app-layout>
