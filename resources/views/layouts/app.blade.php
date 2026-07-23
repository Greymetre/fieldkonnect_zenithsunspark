<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <title>{{ config('app.name', 'Blueneba') }}</title>
   <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
   <!-- CSS Files -->
   <link href="{{ asset('assets/css/materialdashboard2.css?v=' . now()->timestamp) }}" rel="stylesheet" />
   <link href="{{ asset('assets/css/newdesign.css?v=' . now()->timestamp) }}" rel="stylesheet" />
   <link href="{{ asset('assets/css/custom1.css?v=' . now()->timestamp) }}" rel="stylesheet" />
   <!-- CSS Just for demo purpose, don't include it in your project -->
   <link href="{{ asset('assets/demo/demo.css?v=' . now()->timestamp) }}" rel="stylesheet" />
   <!-- <link href="{{ asset('assets/css/jquery-ui.css') }}" rel="stylesheet" /> -->
   <link href="{{ asset('assets/css/responsive.bootstrap4.css?v=' . now()->timestamp) }}" rel="stylesheet" />
   <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.css?v=' . now()->timestamp) }}">
   <link href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" rel="stylesheet">
   <script src="{{ asset('assets/js/core/jquery.min.js') }}"></script>
   <script src="{{ asset('assets/js/core/jquery-ui.js') }}"></script>
   <script src="{{ asset('assets/js/plugins/moment.min.js') }}"></script>
   <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
   <meta http-equiv="Cache-Control" content="no-store" />
   <style>
      .iconimg {
         background: #fff;
         padding: 0px;
         height: 100%;
         min-height: 50px;
         object-fit: contain;
         margin-top: 10px;
      }

      .main-panel>.navbar {
         background: #FFFFFF;
         padding: 0 !important;
      }

      /* Google Font Import - Poppins */
      @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

      * {
         margin: 0;
         padding: 0;
         box-sizing: border-box;
         font-family: 'Poppins', sans-serif;
      }

      :root {
         /* ===== Colors ===== */
         --body-color: #E4E9F7;
         --sidebar-color: #FFF;
         --primary-color: #3860a4;
         --primary-color-light: #F6F5FF;
         --toggle-color: #DDD;
         --text-color: #707070;

         /* ====== Transition ====== */
         /*  --tran-03: all 0.2s ease;
      --tran-03: all 0.3s ease;
      --tran-04: all 0.3s ease;
      --tran-05: all 0.3s ease;*/

         --tran-03: unset;
         --tran-03: unset;
         --tran-04: unset;
         --tran-05: unset;
      }

      body {
         min-height: 100vh;
         background-color: var(--body-color);
         transition: var(--tran-05);
      }

      ::selection {
         background-color: us color: #fff;
      }

      .sidebar li.nav-link a {
         /* background-color: var(--primary-color); */
         color: #707070;
         font-weight: 500;
      }


      .sidebar li.nav-link:hover a {
         /* background-color: var(--primary-color); */
         color: #fff;
      }

      .sidebar li.nav-link ul.navd a {
         background: transparent;
         color: #fff;
         font-weight: 500;
         font-size: 16px;
      }

      body.dark {
         --body-color: #18191a;
         --sidebar-color: #242526;
         --primary-color: #3a3b3c;
         --primary-color-light: #3a3b3c;
         --toggle-color: #fff;
         --text-color: #ccc;
      }

      /* ===== Sidebar ===== */
      .sidebar {
         position: fixed;
         top: 0;
         left: 0;
         height: 100%;
         width: 250px;
         padding: 0;
         background: var(--sidebar-color);
         transition: var(--tran-05);
         z-index: 100;
         background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
      }

      .sidebar.close {
         width: 88px;
      }

      .sidebar li.nav-link a:hover>a {
         color: #fff;
      }

      /* ===== Reusable code - Here ===== */
      .sidebar li.nav-link {
         /* height: 50px;*/
         list-style: none;
         /* display: flex; */
         align-items: center;
         margin-top: 10px;
         margin-bottom: 10px;
         padding: 0;
      }

      .sidebar header .image,
      .sidebar .icon {
         min-width: 50px;
         border-radius: 6px;
      }

      .sidebar .icon {
         min-width: 50px;
         border-radius: 6px;
         height: 100%;
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 20px;
      }

      .sidebar .text,
      .sidebar .icon {
         /* color: #fff;*/
         /* transition: var(--tran-03);*/
      }


      .sidebar .text {
         font-size: 17px;
         font-weight: 400;
         white-space: nowrap;
         opacity: 1;
         color: #fff;
         padding-left: 8px;

      }

      .sidebar .bottom-content i {
         color: #fff;
      }

      .sidebar.close .text {
         opacity: 0;
      }

      /* =========================== */

      .sidebar header {
         position: relative;
      }

      .sidebar header .image-text {
         display: flex;
         align-items: center;
      }

      .sidebar header .logo-text {
         display: flex;
         flex-direction: column;
      }

      header .image-text .name {
         margin-top: 2px;
         font-size: 18px;
         font-weight: 600;
         color: #fff;
         white-space: normal;
      }

      header .image-text .profession {
         font-size: 16px;
         margin-top: -2px;
         display: block;
      }

      .sidebar header .image {
         display: flex;
         align-items: center;
         justify-content: center;
      }

      .sidebar header .image img {

         width: 40px;
         border-radius: 32px;
         height: 40px;
         object-fit: cover;
      }

      .sidebar header .toggle {
         position: absolute;
         top: 135%;
         right: -10px;
         transform: translateY(-50%) rotate(180deg);
         height: 25px;
         width: 25px;
         /* background-color: var(--primary-color); */
         background: #fff;
         color: #3860a4;
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 22px;
         cursor: pointer;
         transition: var(--tran-05);
      }

      body.dark .sidebar header .toggle {
         color: var(--text-color);
      }

      .sidebar.close .toggle {
         transform: translateY(-50%) rotate(0deg);
      }

      /*  .sidebar .menu {
      margin-top: 20px;
    }*/

      .sidebar li.search-box {
         border-radius: 6px;
         background-color: var(--primary-color-light);
         cursor: pointer;
         transition: var(--tran-05);
      }

      .sidebar li.search-box input {
         height: 100%;
         width: 100%;
         outline: none;
         border: none;
         background-color: var(--primary-color-light);
         color: var(--text-color);
         border-radius: 6px;
         font-size: 17px;
         font-weight: 500;
         transition: var(--tran-05);
      }

      .sidebar li.nav-link a {
         list-style: none;
         height: 100%;
         background-color: transparent;
         display: flex;
         align-items: center;
         height: 100%;
         width: 100%;
         border-radius: 6px;
         text-decoration: none;
         transition: var(--tran-03);
         /*   border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;*/
         padding: 8px 0px;
         position: relative;
      }


      .sidebar li.nav-link ul li a:after {
         position: unset;
         display: none
      }

      .sidebar li.nav-link a:after {
         position: absolute;
         content: "";
         width: 10px;
         height: 10px;
         background-color: transparent;
         border: 3px solid #fff;
         right: 15px;
         top: 15px;
         border-top: 0px;
         border-left: 0px;
         transform: rotate(45deg);
      }

      .sidebar li.nav-link a.no-after:after {
         display: none !important;
      }

      .sidebar li.nav-link a.active:after {
         position: absolute;
         content: "";
         width: 10px;
         height: 10px;
         background-color: #fff;
         border: 3px solid #3860a4;
         right: 15px;
         top: 15px;
         border-top: 0px;
         border-left: 0px;
         transform: rotate(45deg);
      }  

      .sidebar li.nav-link a:hover {
         /* background-color: var(--primary-color);
*/
         color: #fff;
         background: linear-gradient(90deg, #3860a4 0%, #1b4e6c 100%);
      }

      .sidebar li.nav-link ul li a:hover .icon,
      .sidebar li.nav-link a:hover .text,
      .sidebar li.nav-link ul li a:hover span {
         /* color: var(--primary-color);*/
         color: #fff;
      }

      .sidebar li.nav-link ul li a:hover {
         background: #3860a4a3;
      }

      body.dark .sidebar li.nav-link a:hover .icon,
      body.dark .sidebar li.nav-link a:hover .text {
         color: var(--text-color);
      }



      .sidebar li.nav-link.active ul li.nav-link-btn.active .icon {
         color: #fff;
         font-weight: 500;
      }

      .sidebar .menu-bar {
         height: calc(100% - 162px);
         display: flex;
         flex-direction: column;
         justify-content: space-between;
         overflow-y: visible;
         margin-top: 20px;
         overflow-x: hidden;
      }

      .sidebar li.nav-link.active ul li .icon {
         color: #fff;
      }


      .sidebar .menu-bar::-webkit-scrollbar {
         width: 2px;
         background-color: transparent;
      }

      .sidebar .menu-bar::-webkit-scrollbar-thumb {
         border-radius: 2px;
         -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
         /*  background-color: var(--primary-color);*/
         background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
      }

      /* .menu-bar::-webkit-scrollbar {
      display: none;
    }
*/
      .sidebar .menu-bar .mode {
         border-radius: 6px;
         background-color: var(--primary-color-light);
         position: relative;
         transition: var(--tran-05);
      }

      .menu-bar .mode .sun-moon {
         height: 50px;
         width: 60px;
      }

      .mode .sun-moon i {
         position: absolute;
      }

      .mode .sun-moon i.sun {
         opacity: 0;
      }

      body.dark .mode .sun-moon i.sun {
         opacity: 1;
      }

      body.dark .mode .sun-moon i.moon {
         opacity: 0;
      }

      .menu-bar .bottom-content .toggle-switch {
         position: absolute;
         right: 0;
         height: 100%;
         min-width: 60px;
         display: flex;
         align-items: center;
         justify-content: center;
         border-radius: 6px;
         cursor: pointer;
      }

      .toggle-switch .switch {
         position: relative;
         height: 22px;
         width: 40px;
         border-radius: 25px;
         background-color: var(--toggle-color);
         transition: var(--tran-05);
      }

      .switch::before {
         content: '';
         position: absolute;
         height: 15px;
         width: 15px;
         border-radius: 50%;
         top: 50%;
         left: 5px;
         transform: translateY(-50%);
         background-color: var(--sidebar-color);
         transition: var(--tran-04);
      }

      body.dark .switch::before {
         left: 20px;
      }

      ..main-panel {
         position: absolute;
         top: 0;
         top: 0;
         left: 250px;
         height: 100vh;
         width: calc(100% - 250px);
         background-color: var(--body-color);
         transition: var(--tran-05);
      }

      .home .text {
         font-size: 30px;
         font-weight: 500;
         color: var(--text-color);
         padding: 12px 60px;
      }

      .sidebar.close~..main-panel {
         left: 78px;
         height: 100vh;
         width: calc(100% - 78px);
      }

      body.dark .home .text {
         color: var(--text-color);
      }

      .logo-main.desktop img {
         width: 75%;
         padding: 10px 5px;
      }
      .logo-main.mobile img {
         width: 68%;
      }


      .sidebar li.nav-link.active .text,
      .sidebar li.nav-link.active .icon {
         color: #fff;
      }

      /* li.nav-link a {
    color: #707070;
    font-weight: 500;
} */


      .sidebar li.nav-link.active a {
         background-color: var(--primary-color);
         color: #ffff;
         font-weight: 500;
      }

      ul.navd {
         background: transparent;
         height: 100%;
         z-index: 9;
         position: relative;
         padding: 10px 6px;
         border-bottom-left-radius: 6px;
         border-bottom-right-radius: 6px;
         /* box-shadow: 0 16px 38px -12px rgba(0, 0, 0, 0.56), 0 4px 25px 0px rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(0, 0, 0, 0.2);*/

      }

      .sidebar li.nav-link a.collapsed {
         color: #fff;
         font-weight: 500;
         background: transparent;
      }


      .sidebar li.nav-link a {
         /* background-color: var(--primary-color);
    color: #ffff;
    font-weight: 500;*/
         background-color: transparent;
         color: #fff;
         font-weight: 600;
      }


      .sidebar li.nav-link a.collapsed:hover,
      .sidebar li.nav-link.active a.collapsed {
         background: linear-gradient(90deg, #3860a4 0%, #1b4e6c 100%);
         color: #ffff;
         font-weight: 500;
      }


      .sidebar li.nav-link.active a {
         background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
      }


      .sidebar li.nav-link.active ul.navd li.nav-link-btn.active a {
         background: #3860a4a3;
         color: #fff;
         font-weight: 500;

      }

      ul.navd li.nav-link-btn {
         margin: 0px 0;
      }

      .sidebar.close li.nav-link a:after {

         width: 5px;
         height: 5px;
      }

      /* .sidebar.close~.main-panel {
    left: 78px;
    height: 100vh;
    width: calc(100% - 78px);
}
.main-panel {

    position: absolute;
    top: 0;
    top: 0;
    left: 250px;
    height: 100vh;
    width: calc(100% - 250px);
    background-color: var(--body-color);
    transition: var(--tran-05);
} */

      body .main-panel {
         position: absolute;
         top: 0;
         float: unset;
         left: 250px;
         height: unset !important;
         width: calc(100% - 250px);
         max-height: unset !important;
      }


      body .sidebar.close~.main-panel {
         left: 87px;
         height: 100vh;
         width: calc(100% - 87px);
      }

      .sidebar {
         z-index: 9999;
      }

      li {
         list-style: none;
      }

      /*ul.navd li.nav-link-btn.active, ul.navd li.nav-link-btn:hover {
    background: #00000094;
    border-radius: 8px;
}
*/
      nav.sidebar.close {
         opacity: 1;
      }


      nav.sidebar.close .icon {
         min-width: 60px;
      }

      nav.sidebar.close .menu-links span {
         display: none;
      }

      nav.sidebar.close li.nav-link a:after {
         right: 5px;
         top: 12px;
      }



      nav.sidebar.close .menu-bar {
         height: calc(100% - 106px);
         overflow-y: visible;
         margin-top: 20px;
         overflow-x: visible;
      }

      nav.sidebar.close .bottom-content {
         text-align: center;
      }


      nav.sidebar.close .bottom-content span.text.nav-text {
         display: none;
      }



      nav.sidebar.close ul.navd {
         padding: 6px 0px;

      }



      nav.sidebar .bottom-content li a {
         background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
         color: #fff;
         padding: 10px 18px;
         border-radius: 10px;
         display: flex;
         align-items: center;
         justify-content: center;
      }

      nav.sidebar .mobile {
         display: none;
      }

      nav.sidebar.close .mobile {
         display: block;
      }

      /*  .logo-main.mobile img {
      width: 39px;
      height: 100%;
      object-fit: contain;
    }*/

      nav.sidebar.close .desktop {
         display: none;
      }

      nav.sidebar.close li.nav-link a.hoveradd,
      nav.sidebar.close li.nav-link ul a.hoveradd2 {
         position: relative;
      }


      /*tablet css*/

      @media (max-width: 996px) {
         body nav.sidebar {
            display: block !important;
         }

         body .main-panel {
            position: relative;
            top: 0;
            float: unset;
            left: 0;
            width: calc(100% - 0px);

         }
      }

      nav.sidebar.close li.nav-link .d-none.mobile_hide {
         color: #fff;
         font-weight: 300;
         font-size: 13px;
         padding: 10px 10px;
         border-radius: 12px;
         position: absolute;
         right: -290%;
         top: 6%;
         z-index: 9999999;
         border-top-left-radius: 0;
         border-bottom-left-radius: 0;
         /*    transition: var(--tran-05);*/
         width: 100%;
         min-width: 161px;
         font-style: normal;
         font-family: 'Poppins', sans-serif;
         background: linear-gradient(90deg, #3860a4 0%, #1b4e6c 100%);
         letter-spacing: 1px;
      }

      body nav.sidebar.close li.nav-link a.hoveradd:hover .d-none.mobile_hide,
      body nav.sidebar.close li.nav-link ul li a.hoveradd2:hover .d-none.mobile_hide {
         display: block !important;
      }
   </style>
   </style>
   <!-- Scripts -->
</head>

<body class="">
   <!-- Loader -->
   <div class="loader-container" id="loader">
      <div class="loader"></div>
   </div>
   <div class="wrapper">
      <nav class="sidebar">
         <header>
            <div class="logo">
               <a href="{{ url('customers') }}" class="simple-text logo-normal">
                  <!-- GAJRA GEARS -->
                  <div class="logo-main desktop">
                     <img src="{{ asset('assets/img/brand_logo_new.png') }}" class="rounded" alt="...">
                  </div>
                  <div class="logo-main mobile">
                     <img src="{{ asset('assets/img/mobillogo.svg') }}" class="rounded" alt="...">
                  </div>
               </a>
            </div>
            {{--<div class="image-text mt-2">
               <span class="image">
                  <img
                     src="{!! (count(Auth::user()->getMedia('profile_image')) > 0 ? Auth::user()->getMedia('profile_image')[0]->getFullUrl() : asset('assets/img/profileuser.png?')) !!}"
                     alt="">
               </span>
               <div class="text logo-text">
                  <span class="name"> {!! Auth::user()->name !!}</span>
               </div>
            </div>--}}
            <i class='bx bx-chevron-right toggle'></i>
         </header>
         <div class="menu-bar">
            <div class="menu">
               <ul class="menu-links">
                  @if(auth()->user()->can(['dashboard_access']))
                  {{--<li class="nav-link hide_icon {{ request()->is('dealer_dashboard') ? 'active' : '' }}">
                  <a class="collapsed hoveradd" href="{{ url('dashboard') }}">
                     <i class="material-icons icon">dashboard</i>
                     <span>{!! trans('panel.sidemenu.dashboard') !!}</span>
                     <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.dashboard') !!}</div>
                  </a>
                  </li>--}}

                  <li class="nav-link {{ request()->is('sales_summary_dashboard*') || request()->is('dealer_dashboard') ? 'active' : '' }}">
                     <a class="collapsed hoveradd" data-toggle="collapse" href="#dashMenu" aria-expanded="false">
                        <i class="material-icons icon">diversity_3</i>
                        <span> {!! trans('panel.sidemenu.dashboard') !!}
                        </span>
                        <div class="d-none mobile_hide">{!! trans('panel.sidemenu.dashboard') !!}</div>
                     </a>
                     <div class="collapse" id="dashMenu" style="">
                        <ul class="navd">
                           @if(auth()->user()->can(['dealer_dashboard']))
                           <li class="nav-link-btn {{ request()->is('dealer_dashboard*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('dealer_dashboard') }}">
                                 <i class="material-icons icon">transcribe</i>
                                 <span>Dealer Dashboard</span>
                                 <div class="d-none mobile_hide">Dealer Dashboard</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can(['sales_summary_dashboard']))
                           <li class="nav-link-btn {{ request()->is('sales_summary_dashboard*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('sales_summary_dashboard') }}">
                                 <i class="material-icons icon">transcribe</i>
                                 <span>Sales</span>
                                 <div class="d-none mobile_hide">Sales</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can(['employee_expense_report_dasboard']))
                           <li class="nav-link-btn {{ request()->is('employee_expense_report*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('employee_expense_report') }}">
                                 <i class="material-icons icon">transcribe</i>
                                 <span>Employee Related Expenses</span>
                                 <div class="d-none mobile_hide">Employee Related Expenses</div>
                              </a>
                           </li>
                           @endif
                        </ul>
                     </div>
                  </li>
                  @endif
                  @if(auth()->user()->can(['lead_management_access']))
                  <li class="nav-link {{ request()->is('leads*') || request()->is('contacts*') ? 'active' : '' }}">
                     <a class="collapsed hoveradd" data-toggle="collapse" href="#leadManagementMenu" aria-expanded="false">
                        <i class="material-icons icon">leaderboard</i>
                        <span> Lead Management
                        </span>
                        <div class="d-none mobile_hide">Lead Management</div>
                     </a>
                     <div class="collapse" id="leadManagementMenu" style="">
                        <ul class="navd">
                           @if(auth()->user()->can(['lead_access']))
                           <li class="nav-link-btn {{ request()->is('leads*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('leads') }}">
                                 <i class="material-icons icon">diamond</i>
                                 <span>Leads</span>
                                 <div class="d-none mobile_hide">Leads</div>
                              </a>
                           </li>

                            <li class="nav-link-btn {{ request()->is('lead-contacts*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('lead-contacts') }}">
                                 <i class="material-icons icon">diamond</i>
                                 <span>Contacts</span>
                                 <div class="d-none mobile_hide">Contacts</div>
                              </a>
                           </li>
                           <li class="nav-link-btn {{ request()->is('lead-opportunities*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('lead-opportunities') }}">
                                 <i class="material-icons icon">diamond</i>
                                 <span>Opportunities</span>
                                 <div class="d-none mobile_hide">Opportunities</div>
                              </a>
                           </li>
                           @if(auth()->user()->can(['opportunities_status_access']))
                           <li class="nav-link-btn {{ request()->is('lead-opportunities-status*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('lead-opportunities-status') }}">
                                 <i class="material-icons icon">diamond</i>
                                 <span>Opportunities Status</span>
                                 <div class="d-none mobile_hide">Opportunities Status</div>
                              </a>
                           </li>
                           @endif
                           <li class="nav-link-btn {{ request()->is('lead-tasks*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('lead-tasks') }}">
                                 <i class="material-icons icon">diamond</i>
                                 <span>Tasks</span>
                                 <div class="d-none mobile_hide">Tasks</div>
                              </a>
                           </li>
                           @if(auth()->user()->can(['lead_visit_access']))
                           <li class="nav-link-btn {{ request()->is('lead_visit_report*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('lead_visit_report') }}">
                                 <i class="material-icons icon">diamond</i>
                                 <span>Visit Report</span>
                                 <div class="d-none mobile_hide">Visit Report</div>
                              </a>
                           </li>
                           @endif
                           @endif
                        </ul>
                     </div>
                  </li>
                  @endif
                  @if(auth()->user()->can(['customer_access']))
                  <li class="nav-link {{ request()->is('customers*') || request()->is('customertype*') || request()->is('firmtype*') || request()->is('customersLogin*') || request()->is('customers-survey*') || request()->is('fields*') ? 'active' : '' }}">
                     <a class="collapsed hoveradd" data-toggle="collapse" href="#customerMenu" aria-expanded="false">
                        <i class="material-icons icon">diversity_3</i>
                        <span> {!! trans('panel.sidemenu.customers_master') !!}
                        </span>
                        <div class="d-none mobile_hide">{!! trans('panel.sidemenu.customers_master') !!}</div>
                     </a>
                     <div class="collapse" id="customerMenu" style="">
                        <ul class="navd">
                           @if(auth()->user()->can(['customer_access']))
                           <li class="nav-link-btn {{ request()->is('customers*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('customers') }}">
                                 <i class="material-icons icon">transcribe</i>
                                 <span>{!! trans('panel.sidemenu.customers') !!}</span>
                                 <div class="d-none mobile_hide">{!! trans('panel.sidemenu.customers') !!}</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can(['customer_balance_confirmation_upload']))
                           <li class="nav-link-btn {{ request()->is('customer_balance*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('customer_balance') }}">
                                 <i class="material-icons icon">balance</i>
                                 <span>Upload Balance Confirmation</span>
                                 <div class="d-none mobile_hide">Upload Balance Confirmation</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can(['distributor_access']))
                           <!-- <li class="nav-item {{ request()->is('distributors*') ? 'active' : '' }}">
                                 <a class="nav-link" href="{{ url('distributors') }}">
                                   <i class="material-icons">store</i>
                                   <p>{!! trans('panel.sidemenu.distributors') !!}</p>
                                 </a>
                                 </li> -->
                           @endif
                           @if(auth()->user()->can('customertype_access'))
                           <li class="nav-link-btn {{ request()->is('customertype*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('customertype') }}">
                                 <i class="material-icons icon">library_books</i>
                                 <span>{!! trans('panel.sidemenu.customertype') !!}</span>
                                 <div class="d-none mobile_hide">{!! trans('panel.sidemenu.customertype') !!}</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('firmtype_access'))
                           <li class="nav-link-btn {{ request()->is('firmtype*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('firmtype') }}">
                                 <i class="material-icons icon">bubble_chart</i>
                                 <span>{!! trans('panel.sidemenu.firmtype') !!}</span>
                                 <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.firmtype') !!}</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('customer_login'))
                           <li class="nav-link-btn {{ request()->is('customersLogin*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('customersLogin') }}">
                                 <i class="material-icons icon">login</i>
                                 <span>{!! trans('panel.sidemenu.customersLogin') !!}</span>
                                 <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.customersLogin') !!}</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('survey_access'))
                           <li class="nav-link-btn {{ request()->is('customers-survey*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('customers-survey') }}">
                                 <i class="material-icons icon">sentiment_satisfied</i>
                                 <span>Customers Survey</span>
                                 <div class="d-none mobile_hide"> Customers Survey</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('field_access'))
                           <li class="nav-link-btn {{ request()->is('fields*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('fields') }}">
                                 <i class="material-icons icon">text_rotation_angleup</i>
                                 <span>Survey Field</span>
                                 <div class="d-none mobile_hide"> Survey Field</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('market_intelligence_access'))
                           <li class="nav-link-btn {{ request()->is('market_intelligences*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('market_intelligences') }}">
                                 <i class="material-icons icon">key</i>
                                 <span>Market Intelligence</span>
                                 <div class="d-none mobile_hide"> Market Intelligence</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('country_access'))
                           <li class="nav-link-btn add_icon {{ request()->is('country*') || request()->is('state*') || request()->is('district*') || request()->is('city*') || request()->is('pincode*') ? 'active' : '' }}">
                              <a class="collapsed hoveradd " data-toggle="collapse" href="#addressMenu" aria-expanded="false">
                                 <i class="material-icons icon">contact_mail</i>
                                 <span> {!! trans('panel.sidemenu.address_master') !!}
                                 </span>
                                 <div class="d-none mobile_hide">{!! trans('panel.sidemenu.address_master') !!}</div>
                              </a>
                              <div class="collapse" id="addressMenu" style="">
                                 <ul class="navd">
                                    @if(auth()->user()->can('country_access'))
                                    <li class="nav-link-btn {{ request()->is('country*') ? 'active' : '' }}">
                                       <a class="hoveradd2" href="{{ url('country') }}">
                                          <i class="material-icons icon">flag_circle</i>
                                          <span>{!! trans('panel.sidemenu.address_country') !!}</span>
                                          <div class="d-none mobile_hide">{!! trans('panel.sidemenu.address_country') !!}</div>
                                       </a>
                                    </li>
                                    @endif
                                    @if(auth()->user()->can('state_access'))
                                    <li class="nav-link-btn {{ request()->is('state*') ? 'active' : '' }}">
                                       <a class="hoveradd2" href="{{ url('state') }}">
                                          <i class="material-icons icon">location_city</i>
                                          <span>{!! trans('panel.sidemenu.address_state') !!}</span>
                                          <div class="d-none mobile_hide">{!! trans('panel.sidemenu.address_state') !!}</div>
                                       </a>
                                    </li>
                                    @endif
                                    @if(auth()->user()->can('district_access'))
                                    <li class="nav-link-btn {{ request()->is('district*') ? 'active' : '' }}">
                                       <a class="hoveradd2" href="{{ url('district') }}">
                                          <i class="material-icons icon">balcony</i>
                                          <span>{!! trans('panel.sidemenu.address_district') !!}</span>
                                          <div class="d-none mobile_hide">{!! trans('panel.sidemenu.address_district') !!}</div>
                                       </a>
                                    </li>
                                    @endif
                                    @if(auth()->user()->can('city_access'))
                                    <li class="nav-link-btn {{ request()->is('city') ? 'active' : '' }}">
                                       <a class="hoveradd2" href="{{ url('city') }}">
                                          <i class="material-icons icon">apartment</i>
                                          <span>{!! trans('panel.sidemenu.address_city') !!}</span>
                                          <div class="d-none mobile_hide">{!! trans('panel.sidemenu.address_city') !!}</div>
                                       </a>
                                    </li>
                                    @endif
                                    @if(auth()->user()->can('pincode_access'))
                                    <li class="nav-link-btn {{ request()->is('pincode*') ? 'active' : '' }}">
                                       <a class="hoveradd2" href="{{ url('pincode') }}">
                                          <i class="material-icons icon">cabin</i>
                                          <span>{!! trans('panel.sidemenu.address_pincode') !!}</span>
                                          <div class="d-none mobile_hide">{!! trans('panel.sidemenu.address_pincode') !!}</div>
                                       </a>
                                    </li>
                                    @endif
                                 </ul>
                              </div>
                           </li>
                           @endif
                           @if(auth()->user()->can('dealer_appointment'))
                           <li class="nav-link-btn {{ request()->is('dealer-appointment*') ? 'active' : '' }}">
                              <a class="nav-link hoveradd2" href="{{ url('dealer-appointments') }}">
                                 <i class="material-icons icon">pending_actions</i>
                                 <span>Dealer / Distributor Appointment</span>
                                 <div class="d-none mobile_hide"> Dealer / Distributor Appointment</div>
                              </a>
                           </li>
                           @endif
                        </ul>
                     </div>
                  </li>
                  @endif
                  @if(auth()->user()->can(['expenses_type']))
                  <!-- <li class="nav-item {{ request()->is('expenses_type') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('expenses_type') }}">
                          <i class="material-icons">dashboard</i>
                          <p>{!! trans('panel.sidemenu.expenses_type') !!}</p>
                        </a>
                        </li> -->
                  @endif
                  @if(auth()->user()->can('product_access'))
                  <li class="nav-link {{ request()->is('categories*') || request()->is('subcategories*') || request()->is('brands*') || request()->is('products*') || request()->is('units*') || request()->is('production*') ? 'active' : '' }}">
                     <a class="collapsed hoveradd" data-toggle="collapse" href="#productMenu" aria-expanded="false">
                        <i class="material-icons icon">conveyor_belt</i>
                        <span> {!! trans('panel.sidemenu.product_master') !!}
                        </span>
                        <div class="d-none mobile_hide">{!! trans('panel.sidemenu.product_master') !!}</div>
                     </a>
                     <div class="collapse" id="productMenu" style="">
                        <ul class="navd">
                           @if(auth()->user()->can('category_access'))
                           <li class="nav-link-btn {{ request()->is('categories*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('categories') }}">
                                 <i class="material-icons icon">category</i>
                                 <span>{!! trans('panel.sidemenu.categories') !!}</span>
                                 <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.categories') !!}</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('subcategory_access'))
                           <li class="nav-link-btn {{ request()->is('subcategories*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('subcategories') }}">
                                 <i class="material-icons icon">subtitles</i>
                                 <span>{!! trans('panel.sidemenu.subcategories') !!}</span>
                                 <div class="d-none mobile_hide">{!! trans('panel.sidemenu.subcategories') !!}</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('brand_access'))
                           <li class="nav-link-btn {{ request()->is('brands*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('brands') }}">
                                 <i class="material-icons icon">branding_watermark</i>
                                 <span>{!! trans('panel.sidemenu.brands') !!}</span>
                                 <div class="d-none mobile_hide">{!! trans('panel.sidemenu.brands') !!}</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('product_access'))
                           <li class="nav-link-btn {{ request()->is('products*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('products') }}">
                                 <i class="material-icons icon">widgets</i>
                                 <span>{!! trans('panel.sidemenu.products') !!}</span>
                                 <div class="d-none mobile_hide">{!! trans('panel.sidemenu.products') !!}</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('gift_access'))
                           <!-- <li class="nav-item {{ request()->is('gifts*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('gifts') }}">
                                   <i class="material-icons">donut_large</i>
                                   <p>{!! trans('panel.sidemenu.gifts') !!}</p>
                                 </a>
                                 </li> -->
                           @endif
                           @if(auth()->user()->can('unit_access'))
                           <li class="nav-link-btn {{ request()->is('units*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('units') }}">
                                 <i class="material-icons icon">apartment</i>
                                 <span>{!! trans('panel.sidemenu.units') !!}</span>
                                 <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.units') !!}</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('stock_access'))
                           <li class="nav-link-btn {{ request()->is('stock*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('stock') }}">
                                 <i class="material-icons icon">donut_small</i>
                                 <span>Stock</span>
                                 <div class="d-none mobile_hide"> Stock</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('sap_stock_access'))
                           <li class="nav-link-btn {{ request()->is('sap_stock*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('sap_stock') }}">
                                 <i class="material-icons icon">donut_small</i>
                                 <span>SAP Stock</span>
                                 <div class="d-none mobile_hide"> SAP Stock</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('opening_stock_view'))
                           <li class="nav-link-btn {{ request()->is('opening-stocks*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('opening-stocks') }}">
                                 <i class="material-icons icon">donut_small</i>
                                 <span>Opening Stock</span>
                                 <div class="d-none mobile_hide"> Opening Stock</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('branch_opening_qty_view'))
                           <li class="nav-link-btn {{ request()->is('opening-quantity*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('opening-quantity') }}">
                                 <i class="material-icons icon">donut_small</i>
                                 <span>Branch Opening Quantity</span>
                                 <div class="d-none mobile_hide"> Branch Opening Quantity</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('ware_house_access'))
                           <li class="nav-link-btn {{ request()->is('ware_house*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('ware_house') }}">
                                 <i class="material-icons icon">warehouse</i>
                                 <span>Ware House</span>
                                 <div class="d-none mobile_hide"> Ware House</div>
                              </a>
                           </li>
                           @endif
                        </ul>
                     </div>
                  </li>
                  @endif

                  @if(auth()->user()->can('forecast_access'))
                  <li class="nav-link {{ request()->is('planned-sop-forecast*') || request()->is('planned-sop*') ? 'active' : '' }}">
                     <a class="collapsed hoveradd" data-toggle="collapse" href="#forecastMenu" aria-expanded="false">
                        <i class="material-icons icon">online_prediction</i>
                        <span> Forecast </span>
                        <div class="d-none mobile_hide"> Forecast</div>
                     </a>
                     <div class="collapse" id="forecastMenu" style="">
                        <ul class="navd">
                           <li class="nav-link-btn {{ request()->is('planned-sop*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('planned-sop') }}">
                                 <i class="material-icons icon">warehouse</i>
                                 <span>Planned S&OP</span>
                                 <div class="d-none mobile_hide"> Planned S&OP</div>
                              </a>
                           </li>
                           @if(auth()->user()->can('planned_forecast'))
                           <li class="nav-link-btn {{ request()->is('planned-sop-forecast*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('planned-sop-forecast') }}">
                                 <i class="material-icons icon">warehouse</i>
                                 <span>S&OP Forecast</span>
                                 <div class="d-none mobile_hide"> S&OP Forecast</div>
                              </a>
                           </li>
                           @endif
                        </ul>
                     </div>
                  </li>
                  @endif

                  @if(auth()->user()->can('dealer_product_access'))
                  <li class="nav-link hide_icon {{ request()->is('dealer_product*') ? 'active' : '' }}">
                     <a class="hoveradd" href="{{ url('dealer_product') }}">
                        <i class="material-icons icon">flaky</i>
                        <span>{!! trans('panel.sidemenu.product_master') !!}</span>
                        <div class="d-none mobile_hide">{!! trans('panel.sidemenu.product_master') !!}</div>
                     </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('target_users_access'))
                  <li class="nav-link {{ request()->is('sales_users*') || request()->is('sales_dealer*') ? 'active' : '' }}">
                     <a class="collapsed hoveradd" data-toggle="collapse" href="#salesUserMenu" aria-expanded="false">
                        <i class="material-icons icon">real_estate_agent</i>
                        <span> {!! trans('panel.sidemenu.sales_users') !!} </span>
                        <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.sales_users') !!}</div>
                     </a>
                     <div class="collapse" id="salesUserMenu" style="">
                        <ul class="navd">
                           @if(auth()->user()->can('target_users_access_sales'))
                           <li class="nav-link-btn {{ request()->is('sales_users*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('sales_users/target_users') }}">
                                 <i class="material-icons icon">emoji_events</i>
                                 <span> {!! trans('panel.sales_users.title') !!}</span>
                                 <div class="d-none mobile_hide"> {!! trans('panel.sales_users.title') !!}</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('sales_target_dealers_access'))
                           <li class="nav-link-btn {{ request()->is('sales_dealer*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('sales_dealer/target_dealers') }}">
                                 <i class="material-icons icon">school</i>
                                 <span> Dealer distributor target vs achievement</span>
                                 <div class="d-none mobile_hide">Dealer distributor target vs achievement</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('branch_wise_sales_target_access'))
                           <li class="nav-link-btn {{ request()->is('branches_sales_target') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('branches_sales_target') }}">
                                 <i class="material-icons icon">holiday_village</i>
                                 <span> Branch Wise Sales Target</span>
                                 <div class="d-none mobile_hide"> Branch Wise Sales Target</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('primary_scheme'))
                           <li class="nav-item-btn {{ request()->is('primary_scheme') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('primary_scheme') }}">
                                 <i class="material-icons icon">holiday_village</i>
                                 <span> Primary Scheme</span>
                                 <div class="d-none mobile_hide"> Primary Scheme</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('primary_scheme_report'))
                           <li class="nav-item-btn {{ request()->is('primary_scheme_report') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('primary_scheme_report') }}">
                                 <i class="material-icons icon">holiday_village</i>
                                 <span> Primary Scheme Report</span>
                                 <div class="d-none mobile_hide"> Primary Scheme Report</div>
                              </a>
                           </li>
                           @endif
                        </ul>
                     </div>
                  </li>
                  @endif
                  @if(auth()->user()->can('tasks_access'))
                  <li class="nav-link {{ request()->is('tasks*') ? 'active' : '' }}">
                     <a class="hoveradd no-after" href="{{ url('tasks') }}">
                        <i class="material-icons icon">check_circle</i>
                        <span>{!! trans('panel.sidemenu.task') !!} Managment</span>
                        <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.task') !!} Managment</div>
                     </a>
                  </li>
                  @endif
                  @if(auth()->user()->can('hr_access'))
                  <li class="nav-link {{ request()->is('reports/attendancereport*') || request()->is('reports/attendancereportSummary*') || request()->is('holidays*') || request()->is('leaves*') || request()->is('appraisal*') || request()->is('sales_weightage*') || request()->is('users*') || request()->is('targets*') || request()->is('livelocation*') || request()->is('permissions*') || request()->is('tours*') || request()->is('usercity*') || request()->is('new-joinings*') ? 'active' : '' }}">
                     <a class="collapsed hoveradd" data-toggle="collapse" href="#hr" aria-expanded="false">
                        <i class="material-icons icon">family_restroom</i>
                        <span> {!! trans('panel.sidemenu.hr') !!}
                        </span>
                        <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.hr') !!}</div>
                     </a>
                     <div class="collapse" id="hr">
                        <ul class="navd">
                           @if(auth()->user()->can('attendance_report'))
                           <li class="nav-link-btn {{ request()->is('reports/attendancereport') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('reports/attendancereport') }}">
                                 <i class="material-icons icon">report</i>
                                 <span>Attendance Detail Report</span>
                                 <div class="d-none mobile_hide"> Attendance Detail Report</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('attendance_summary_report'))
                           <li class="nav-link-btn {{ request()->is('reports/attendancereportSummary') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('reports/attendancereportSummary') }}">
                                 <i class="material-icons icon">summarize</i>
                                 <span>Attendance Summary Report</span>
                                 <div class="d-none mobile_hide">Attendance Summary Report</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('holiday_access'))
                           <li class="nav-link-btn {{ request()->is('holidays*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('holidays') }}">
                                 <i class="material-icons icon">holiday_village</i>
                                 <span>Holidays</span>
                                 <div class="d-none mobile_hide"> Holidays</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('leave_access'))
                           <li class="nav-link-btn {{ request()->is('leaves*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('leaves') }}">
                                 <i class="material-icons icon">energy_savings_leaf</i>
                                 <span>Leaves</span>
                                 <div class="d-none mobile_hide"> Leaves</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('resignation_access'))
                           <li class="nav-link-btn {{ request()->is('resignations*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('resignations') }}">
                                 <i class="material-icons icon">outgoing_mail</i>
                                 <span>Resignation</span>
                                 <div class="d-none mobile_hide"> Resignation</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('appraisal_pms'))
                           <li class="nav-link-btn {{ request()->is('appraisal*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('appraisal/index') }}">
                                 <i class="material-icons icon">verified_user</i>
                                 <span>Appraisal(PMS)</span>
                                 <div class="d-none mobile_hide"> Appraisal(PMS)</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('sales_weightage'))
                           <li class="nav-link-btn {{ request()->is('sales_weightage*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('sales_weightage') }}">
                                 <i class="material-icons icon">checkroom</i>
                                 <span>{!! trans('panel.sales_weightage.title') !!}</span>
                                 <div class="d-none mobile_hide"> {!! trans('panel.sales_weightage.title') !!}</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('branch'))
                           <li class="nav-link-btn {{ request()->is('branch*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('branches') }}">
                                 <i class="material-icons icon">meeting_room</i>
                                 <span>Branch</span>
                                 <div class="d-none mobile_hide"> Branch</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('division'))
                           <li class="nav-link-btn {{ request()->is('division*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('division') }}">
                                 <i class="material-icons icon">safety_divider</i>
                                 <span>Division</span>
                                 <div class="d-none mobile_hide"> Division</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('designation'))
                           <li class="nav-link-btn {{ request()->is('designation*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('designation') }}">
                                 <i class="material-icons icon">shopping_bag</i>
                                 <span>Designation</span>
                                 <div class="d-none mobile_hide"> Designation</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('departments'))
                           <li class="nav-link-btn {{ request()->is('departments*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('departments') }}">
                                 <i class="material-icons icon">local_fire_department</i>
                                 <span>Departments</span>
                                 <div class="d-none mobile_hide"> Departments</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('user_access'))
                           <li class="nav-link-btn add_icon {{ request()->is('users*') || request()->is('targets*') || request()->is('livelocation*') || request()->is('roles*') || request()->is('permissions*') || request()->is('tours*') || request()->is('usercity*') || request()->is('new-joinings*') ? 'active' : '' }}">
                              <a class="hoveradd" data-toggle="collapse" href="#userMenu" aria-expanded="false">
                                 <i class="material-icons icon">badge</i>
                                 <span> {!! trans('panel.sidemenu.users_master') !!}
                                 </span>
                                 <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.users_master') !!}</div>
                              </a>
                              <div class="collapse" id="userMenu" style="">
                                 <ul class="navd">
                                    <!--                 @if(auth()->user()->can('appraisal_pms'))
                                          <li class="nav-item {{ request()->is('appraisal/create') ? 'active' : '' }}">
                                            <a class="hoveradd2" href="{{ url('appraisal/index') }}">
                                              <i class="material-icons">verified_user</i>
                                              <p>Appraisal(PMS)</p>
                                            </a>
                                          </li>
                                          @endif
                                          @if(auth()->user()->can('sales_weightage'))
                                          <li class="nav-item {{ request()->is('sales_weightage') ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ url('sales_weightage') }}">
                                              <i class="material-icons">check</i>
                                              <p>{!! trans('panel.sales_weightage.title') !!}</p>
                                            </a>
                                          </li>
                                          @endif -->
                                    @if(auth()->user()->can('new_joining_access'))
                                    <li class="nav-link-btn {{ request()->is('new-joinings*') ? 'active' : '' }}">
                                       <a class="hoveradd2" href="{{ url('new-joinings') }}">
                                          <i class="material-icons icon">verified_user</i>
                                          <span>New Joining</span>
                                          <div class="d-none mobile_hide"> New Joining</div>
                                       </a>
                                    </li>
                                    @endif
                                    @if(auth()->user()->can('user_access'))
                                    <li class="nav-link-btn {{ request()->is('users*') ? 'active' : '' }}">
                                       <a class="hoveradd2" href="{{ url('users') }}">
                                          <i class="material-icons icon">assignment_ind</i>
                                          <span>{!! trans('panel.sidemenu.users') !!}</span>
                                          <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.users') !!}</div>
                                       </a>
                                    </li>
                                    @endif
                                    @if(auth()->user()->can('user_app_details_access'))
                                    <li class="nav-link-btn {{ request()->is('user_app_details*') ? 'active' : '' }}">
                                       <a class="hoveradd2" href="{{ url('user_app_details') }}">
                                          <i class="material-icons icon">details</i>
                                          <span>User App details</span>
                                          <div class="d-none mobile_hide"> User App details</div>
                                       </a>
                                    </li>
                                    @endif
                                    @if(auth()->user()->can('target_access'))
                                    <li class="nav-link-btn {{ request()->is('targets*') ? 'active' : '' }}">
                                       <a class="hoveradd2" href="{{ url('targets') }}">
                                          <i class="material-icons icon">loupe</i>
                                          <span>User Target</span>
                                          <div class="d-none mobile_hide">User Target</div>
                                       </a>
                                    </li>
                                    @endif
                                    @if(auth()->user()->can('user_location'))
                                    <li class="nav-link-btn {{ request()->is('livelocation*') ? 'active' : '' }}">
                                       <a class="hoveradd2" href="{{ url('livelocation') }}">
                                          <i class="material-icons icon">share_location</i>
                                          <span>User Live Location</span>
                                          <div class="d-none mobile_hide"> User Live Location</div>
                                       </a>
                                    </li>
                                    @endif
                                    @if(auth()->user()->can('tours'))
                                    <li class="nav-link-btn {{ request()->is('tours*') ? 'active' : '' }}">
                                       <a class="nav-link" href="{{ url('tours') }}">
                                          <i class="material-icons icon">tour</i>
                                          <span>Tours</span>
                                          <div class="d-none mobile_hide">Tours</div>
                                       </a>
                                    </li>
                                    @endif
                                    @if(auth()->user()->can('city_assigned'))
                                    <li class="nav-link-btn {{ request()->is('usercity*') ? 'active' : '' }}">
                                       <a class="hoveradd2" href="{{ url('usercity') }}">
                                          <i class="material-icons icon">location_city</i>
                                          <span>City Assigned</span>
                                          <div class="d-none mobile_hide">City Assigned</div>
                                       </a>
                                    </li>
                                    @endif
                                 </ul>
                              </div>
                           </li>
                           @endif
                        </ul>
                     </div>
                  </li>
                  @endif
                  @if(auth()->user()->can(['account_access']))
                  <li class="nav-link {{ request()->is('expenses*') || request()->is('tax_invoice*') || request()->is('expenses_type*') || request()->is('estimate*') ? 'active' : '' }}">
                     <a class="collapsed hoveradd" data-toggle="collapse" href="#accountMenu" aria-expanded="false">
                        <i class="material-icons icon">attribution</i>
                        <span> {!! trans('panel.sidemenu.account') !!}
                        </span>
                        <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.account') !!}</div>
                     </a>
                     <div class="collapse" id="accountMenu" style="">
                        <ul class="navd">
                           @if(auth()->user()->can(['expenses_type']))
                           <li class="nav-link-btn {{ request()->is('expenses_type*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('expenses_type') }}">
                                 <i class="material-icons icon">dashboard</i>
                                 <span>{!! trans('panel.sidemenu.expenses_type') !!}</span>
                                 <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.expenses_type') !!}</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('expense_access'))
                           <li class="nav-link-btn add_icon {{ request()->is('expenses') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('expenses') }}">
                                 <i class="material-icons icon">outlet</i>
                                 <span>Expense</span>
                                 <div class="d-none mobile_hide"> Expense</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('customer_outstanting'))
                           <li class="nav-link-btn {{ request()->is('reports/customer_outstanting*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('reports/customer_outstanting') }}">
                                 <i class="material-icons icon">nature_people</i>
                                 <span>Dealer Outstanding</span>
                                 <div class="d-none mobile_hide"> Dealer Outstanding</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('dealer_account_statement'))
                           <li class="nav-link-btn {{ request()->is('dealer_account_statement*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('dealer_account_statement') }}">
                                 <i class="material-icons icon">request_page</i>
                                 <span>Dealer Account Statement</span>
                                 <div class="d-none mobile_hide"> Dealer Account Statement</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('estimate_access'))
                           <li class="nav-link-btn {{ request()->is('estimate*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('estimate') }}">
                                 <i class="material-icons icon">request_quote</i>
                                 <span>Estimate</span>
                                 <div class="d-none mobile_hide"> Estimate</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('invoice_access'))
                           <li class="nav-link-btn {{ request()->is('tax_invoice*') ? 'active' : '' }}">
                              <a class="hoveradd2" href="{{ url('tax_invoice') }}">
                                 <i class="material-icons icon">receipt_long</i>
                                 <span>Invoice</span>
                                 <div class="d-none mobile_hide"> Invoice</div>
                              </a>
                           </li>
                           @endif
                           @if(auth()->user()->can('payments_access'))
                           <li class="nav-link-btn add_icon">
                              <a class="hoveradd" data-toggle="collapse" href="#paymentManu" aria-expanded="false">
                                 <i class="material-icons icon">paid</i>
                                 <span>Payments</span>
                                 <div class="d-none mobile_hide"> Payments</div>
                              </a>
                           <li class="nav-link-btn">
                              <div class="collapse" id="paymentManu" style="">
                                 <ul class="navd">
                                    @if(auth()->user()->can('payments_create'))
                                    <li class="nav-link-btn {{ request()->is('payments*') ? 'active' : '' }}">
                                       <a class="hoveradd2" href="{{ url('payments/create') }}">
                                          <i class="material-icons icon">currency_exchange</i>
                                          <span>Payment Recieved</span>
                                          <div class="d-none mobile_hide"> Payment Recieved</div>
                                       </a>
                                    </li>
                                    @endif
                                    <li class="nav-link-btn {{ request()->is('payments*') ? 'active' : '' }}">
                                       <a class="hoveradd2" href="{{ url('payments') }}">
                                          <i class="material-icons icon">currency_rupee</i>
                                          <span>Payments</span>
                                          <div class="d-none mobile_hide"> Payments</div>
                                       </a>
                                    </li>
                                 </ul>
                              </div>
                           </li>
                  </li>
                  @endif
               </ul>
            </div>
            </li>
            @endif
            @if(auth()->user()->can('services_access'))
            <li class="nav-link {{ request()->is('services*') || request()->is('warranty_activation*') || request()->is('complaint-type*') || request()->is('complaints*') || request()->is('service-charge*') || request()->is('service_bills*') || request()->is('end_user*') ? 'active' : '' }}">
               <a class="collapsed hoveradd" data-toggle="collapse" href="#serviceMenu" aria-expanded="false">
                  <i class="material-icons icon">design_services</i>
                  <span> {!! trans('panel.sidemenu.services') !!}
                  </span>
                  <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.services') !!}</div>
               </a>
               <div class="collapse" id="serviceMenu" style="">
                  <ul class="navd">
                     @if(auth()->user()->can('serial_number_transaction'))
                     <li class="nav-link-btn {{ request()->is('services/serial_number_transaction*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('services/serial_number_transaction') }}">
                           <i class="material-icons icon">receipt_long</i>
                           <span>{!! trans('panel.sidemenu.serial_number_transaction') !!}</span>
                           <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.serial_number_transaction') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('serial_number_history'))
                     <li class="nav-link-btn {{ request()->is('services/serial_number_history*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('services/serial_number_history') }}">
                           <i class="material-icons icon">history</i>
                           <span>{!! trans('panel.sidemenu.serial_number_history') !!}</span>
                           <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.serial_number_history') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('complaint_type_access'))
                     <li class="nav-link-btn {{ request()->is('complaint-type*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('complaint-type') }}">
                           <i class="material-icons icon">mark_email_read</i>
                           <span>{!! trans('panel.sidemenu.complaint_type') !!}</span>
                           <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.complaint_type') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('complaint_access'))
                     <li class="nav-link-btn {{ request()->is('complaints*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('complaints') }}">
                           <i class="material-icons icon">checklist</i>
                           <span>{!! trans('panel.sidemenu.complaint') !!}</span>
                           <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.complaint') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('service_bill_access'))
                     <li class="nav-link-btn {{ request()->is('service_bills*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('service_bills') }}">
                           <i class="material-icons icon">account_balance_wallet</i>
                           <span>Service Bill</span>
                           <div class="d-none mobile_hide"> Service Bill</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('service_bill_type_access'))
                     <li class="nav-link-btn {{ request()->is('service-bills-complaints-type*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('service-bills-complaints-type') }}">
                           <i class="material-icons icon">account_balance_wallet</i>
                           <span>Service Bills Complaints Type</span>
                           <div class="d-none mobile_hide"> Service Bills Complaints Type</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('claim_generation_access'))
                     <li class="nav-link-btn {{ request()->is('claim-generation*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('claim-generation') }}">
                           <i class="material-icons icon">account_balance_wallet</i>
                           <span>Claim Generation</span>
                           <div class="d-none mobile_hide">Claim Generation</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('services_product_access'))
                     <li class="nav-link-btn add_icon {{ request()->is('service-charge*') ? 'active' : '' }}">
                        <a class="collapsed hoveradd" data-toggle="collapse" href="#serviceProductMenu" aria-expanded="false">
                           <i class="material-icons icon">home_repair_service</i>
                           <sapn> Service Charge Products</span>
                              <div class="d-none mobile_hide"> Service Charge Products</div>
                        </a>
                        <div class="collapse" id="serviceProductMenu" style="">
                           <ul class="navd">
                              @if(auth()->user()->can('services_product_division'))
                              <li class="nav-link-btn {{ request()->is('service-charge/dividsions*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('service-charge/dividsions') }}">
                                    <i class="material-icons icon">receipt_long</i>
                                    <span>Division</span>
                                    <div class="d-none mobile_hide"> Division</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('services_product_category'))
                              <li class="nav-link-btn {{ request()->is('service-charge/categories*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('service-charge/categories') }}">
                                    <i class="material-icons icon">category</i>
                                    <span>Categories</span>
                                    <div class="d-none mobile_hide"> Categories</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('services_product_products'))
                              <li class="nav-link-btn {{ request()->is('service-charge/products*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('service-charge/products') }}">
                                    <i class="material-icons icon">storefront</i>
                                    <span>Products</span>
                                    <div class="d-none mobile_hide"> Products</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('services_product_chargetype'))
                              <li class="nav-link-btn {{ request()->is('service-charge/chargetype*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('service-charge/chargetype') }}">
                                    <i class="material-icons icon">power</i>
                                    <span>Charge Type</span>
                                    <div class="d-none mobile_hide"> Charge Type</div>
                                 </a>
                              </li>
                              @endif
                           </ul>
                        </div>
                     </li>
                     @endif
                     @if(auth()->user()->can('warranty_activation_access'))
                     <li class="nav-link-btn {{ request()->is('warranty_activation*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('warranty_activation') }}">
                           <i class="material-icons icon">history</i>
                           <span>{!! trans('panel.sidemenu.warranty_activation') !!}</span>
                           <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.warranty_activation') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('end_user_access'))
                     <li class="nav-link-btn {{ request()->is('end_user*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('end_user') }}">
                           <i class="material-icons icon">group</i>
                           <span>End Users</span>
                           <div class="d-none mobile_hide"> End Users</div>
                        </a>
                     </li>
                     @endif
                  </ul>
               </div>
            </li>
            @endif
            <!--           @if(auth()->user()->can('order_access')) 
                        <li class="nav-item {{ request()->is('orders*') ? 'active' : '' }}">
                          <a class="nav-link" href="{{ url('orders') }}">
                            <i class="material-icons">shopping_bag</i>
                            <p>{!! trans('panel.sidemenu.orders') !!}</p>
                          </a>
                        </li>
                        @endif -->
            @if(auth()->user()->can('order_access'))
            <li class="nav-link {{ request()->is('orders*') || request()->is('orderschemes*') || request()->is('sales') ? 'active' : '' }}">
               <a class="collapsed hoveradd" data-toggle="collapse" href="#orderMenu" aria-expanded="false">
                  <i class="material-icons icon">star</i>
                  <span>{!! trans('panel.sidemenu.orders') !!}</span>
                  <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.orders') !!}</div>
               </a>
               <div class="collapse" id="orderMenu" style="">
                  <ul class="navd">
                     @if(auth()->user()->can('order_access'))
                     <li class="nav-link-btn {{ request()->is('orders*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('orders') }}">
                           <i class="material-icons icon">shopping_bag</i>
                           <span>{!! trans('panel.sidemenu.orders') !!}</span>
                           <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.orders') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('orderscheme'))
                     <li class="nav-link-btn {{ request()->is('orderschemes*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('orderschemes') }}">
                           <i class="material-icons icon">flaky</i>
                           <span>Order Schemes</span>
                           <div class="d-none mobile_hide"> Order Schemes</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('sale_access'))
                     <li class="nav-link-btn {{ request()->is('sales') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('sales') }}">
                           <i class="material-icons icon">shopping_cart</i>
                           <span>{!! trans('panel.sidemenu.order_dispatch') !!}</span>
                           <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.order_dispatch') !!}</div>
                        </a>
                     </li>
                     @endif
                  </ul>
               </div>
            </li>
            @endif
            @if(auth()->user()->can('marketing_access'))
            <li class="nav-link {{ request()->is('marketings*') ? 'active' : '' }}">
               <a class="collapsed hoveradd" data-toggle="collapse" href="#marketingMenu" aria-expanded="false">
                  <i class="material-icons icon">local_convenience_store</i>
                  <span>Marketing</span>
                  <div class="d-none mobile_hide"> Marketing</div>
               </a>
               <div class="collapse" id="marketingMenu" style="">
                  <ul class="navd">
                     @if(auth()->user()->can('marketing_master_access'))
                     <li class="nav-link-btn {{ request()->is('marketings*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('marketings') }}">
                           <i class="material-icons icon">add_business</i>
                           <span>Marketing Master</span>
                           <div class="d-none mobile_hide"> Marketing Master</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('marketing_new_dealer_access'))
                     <li class="nav-link-btn {{ request()->is('marketings_new_dealer*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('marketings_new_dealer') }}">
                           <i class="material-icons icon">flaky</i>
                           <span>New Dealer/Distributor</span>
                           <div class="d-none mobile_hide"> New Dealer/Distributor</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('msp_activity_access'))
                     <li class="nav-link-btn {{ request()->is('msp_activity*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('msp_activity') }}">
                           <i class="material-icons icon">celebration</i>
                           <span>MSP Activity</span>
                           <div class="d-none mobile_hide"> MSP Activity</div>
                        </a>
                     </li>
                     @endif
                  </ul>
               </div>
            </li>
            @endif
            @if(auth()->user()->can('wallet_access'))
            <!-- <li class="nav-item ">
                        <a class="collapsed hoveradd" data-toggle="collapse" href="#walletMenu" aria-expanded="false">
                          <i class="material-icons">account_balance_wallet</i>
                          <p> {!! trans('panel.sidemenu.wallet_master') !!}
                        
                          </p>
                        </a>
                        <div class="collapse" id="walletMenu" style="">
                          <ul class="nav">
                            @if(auth()->user()->can('wallet_access'))
                            <li class="nav-item ">
                              <a class="nav-link" href="{{ url('wallets') }}">
                                <i class="material-icons">account_balance_wallet</i>
                                <p>{!! trans('panel.sidemenu.wallet') !!}</p>
                              </a>
                            </li>
                            @endif
                            @if(auth()->user()->can('redeemedpoint_access'))
                            <li class="nav-item ">
                              <a class="nav-link" href="{{ url('redeemedPoint') }}">
                                <i class="material-icons">redeem</i>
                                <p>{!! trans('panel.sidemenu.redeemedPoint') !!}</p>
                              </a>
                            </li>
                            @endif
                          </ul>
                        </div>
                        </li> -->
            @endif
            @if(auth()->user()->can('scheme_access'))
            <li class="nav-link {{ request()->is('schemes*') || request()->is('transaction_history*') || request()->is('gifts*') || request()->is('gift-categories*') || request()->is('gift-subcategories*') || request()->is('gift-model*') || request()->is('gift-brands*') || request()->is('redemptions*') || request()->is('damage_entries*') || request()->is('mobile_user_login*') || request()->is('customer-kyc*') ? 'active' : '' }}">
               <a class="collapsed hoveradd" data-toggle="collapse" href="#schemesMenu" aria-expanded="false">
                  <i class="material-icons icon">card_membership</i>
                  <span> Loyalty Engine </span>
                  <div class="d-none mobile_hide"> Loyalty Engine </div>
               </a>
               <div class="collapse" id="schemesMenu">
                  <ul class="navd">
                     @if(auth()->user()->can('scheme_access_list'))
                     <li class="nav-link-btn {{ request()->is('schemes*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('schemes') }}">
                           <i class="material-icons icon">create</i>
                           <span>Loyalty {!! trans('panel.sidemenu.scheme_master') !!}</span>
                           <div class="d-none mobile_hide"> Loyalty {!! trans('panel.sidemenu.scheme_master') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('transaction_history_access'))
                     <li class="nav-link-btn {{ request()->is('transaction_history*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('transaction_history') }}">
                           <i class="material-icons icon">history</i>
                           <span>Transaction Coupon History</span>
                           <div class="d-none mobile_hide"> Transaction Coupon History</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('loyalty_mobile_app_users_access'))
                     <li class="nav-link-btn {{ request()->is('mobile_user_login') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('mobile_user_login') }}">
                           <i class="material-icons icon">developer_mode</i>
                           <span>Mobile App Users</span>
                           <div class="d-none mobile_hide"> Mobile App Users</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('damage_entry_access'))
                     <li class="nav-link-btn {{ request()->is('damage_entries*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('damage_entries') }}">
                           <i class="material-icons icon">insert_page_break</i>
                           <span>Damage QR Entries</span>
                           <div class="d-none mobile_hide"> Damage QR Entries</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('redemption_access'))
                     <li class="nav-link-btn {{ request()->is('redemptions*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('redemptions') }}">
                           <i class="material-icons icon">mp</i>
                           <span>Redemption</span>
                           <div class="d-none mobile_hide"> Redemption</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('gift_access'))
                     <li class="nav-link-btn {{ request()->is('gifts*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('gifts') }}">
                           <i class="material-icons icon">model_training</i>
                           <span>Gift Catalogue</span>
                           <div class="d-none mobile_hide"> Gift Catalogue</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('gift_category_access'))
                     <li class="nav-link-btn {{ request()->is('gift-categories*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('gift-categories') }}">
                           <i class="material-icons icon">redeem</i>
                           <span>Gift Categories</span>
                           <div class="d-none mobile_hide"> Gift Categories</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('gift_subcategory_access'))
                     <li class="nav-link-btn {{ request()->is('gift-subcategories*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('gift-subcategories') }}">
                           <i class="material-icons icon">redeem</i>
                           <span>Gift Sub Categories</span>
                           <div class="d-none mobile_hide"> Gift Sub Categories</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('gift_model_access'))
                     <li class="nav-link-btn {{ request()->is('gift-model*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('gift-model') }}">
                           <i class="material-icons icon">redeem</i>
                           <span>Gift Model</span>
                           <div class="d-none mobile_hide"> Gift Model</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('gift_brand_access'))
                     <li class="nav-link-btn {{ request()->is('gift-brands*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('gift-brands') }}">
                           <i class="material-icons icon">redeem</i>
                           <span>Gift Brand</span>
                           <div class="d-none mobile_hide"> Gift Brand</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('customer_kyc_access'))
                     <li class="nav-link-btn {{ request()->is('customer-kyc*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('customer-kyc') }}">
                           <i class="material-icons icon">verified</i>
                           <span>Customer KYC</span>
                           <div class="d-none mobile_hide"> Customer KYC</div>
                        </a>
                     </li>
                     @endif
                  </ul>
               </div>
            </li>
            @endif
            @if(auth()->user()->can('status_access'))
            <li class="nav-link {{request()->is('loyalty-app-setting*') || request()->is('roles*') || request()->is('power_bi_setting*') ? 'active' : '' }}">
               <a class="collapsed hoveradd" data-toggle="collapse" href="#settingMenu" aria-expanded="false">
                  <i class="material-icons icon">settings</i>
                  <span> {!! trans('panel.sidemenu.setting_master') !!}
                  </span>
                  <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.setting_master') !!}</div>
               </a>
               <div class="collapse" id="settingMenu" style="">
                  <ul class="navd">
                     @if(auth()->user()->can('setting_access'))
                     <!-- <li class="nav-item ">
                                 <a class="nav-link" href="{{ url('settings') }}">
                                   <i class="material-icons">settings</i>
                                   <p>{!! trans('panel.sidemenu.setting') !!}</p>
                                 </a>
                                 </li> -->
                     @endif
                     @if(auth()->user()->can('power_bi_setting_access'))
                     <li class="nav-link-btn {{request()->is('power_bi_setting*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('power_bi_setting') }}">
                           <i class="material-icons icon">analytics</i>
                           <span>Power BI {!! trans('panel.sidemenu.setting') !!}</span>
                           <div class="d-none mobile_hide">Power BI {!! trans('panel.sidemenu.setting') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('invoice_setting_access'))
                     <li class="nav-link-btn {{request()->is('invoice_setting*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('invoice_setting') }}">
                           <i class="material-icons icon">settings</i>
                           <span>Invoice {!! trans('panel.sidemenu.setting') !!}</span>
                           <div class="d-none mobile_hide">Invoice {!! trans('panel.sidemenu.setting') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('loyalty_app_setting_access'))
                     <li class="nav-link-btn {{request()->is('loyalty-app-setting*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('loyalty-app-setting') }}">
                           <i class="material-icons icon">manage_accounts</i>
                           <span>Loyalty App {!! trans('panel.sidemenu.setting') !!}</span>
                           <div class="d-none mobile_hide">Loyalty App {!! trans('panel.sidemenu.setting') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('loyalty_app_setting_access'))
                     <li class="nav-link-btn {{request()->is('field-konnect-app-setting*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('field-konnect-app-setting') }}">
                           <i class="material-icons icon">admin_panel_settings</i>
                           <span>FieldKonnect App {!! trans('panel.sidemenu.setting') !!}</span>
                           <div class="d-none mobile_hide"> FieldKonnect App {!! trans('panel.sidemenu.setting') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('dealer_portal_setting_access'))
                     <li class="nav-link-btn {{request()->is('delar-portal-setting*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('delar-portal-setting') }}">
                           <i class="material-icons  icon">settings_applications</i>
                           <span>Dealer portal {!! trans('panel.sidemenu.setting') !!}</span>
                           <div class="d-none mobile_hide">Dealer portal {!! trans('panel.sidemenu.setting') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('status_access'))
                     <li class="nav-link-btn {{ request()->is('status*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('status') }}">
                           <i class="material-icons icon">format_paint</i>
                           <span>{!! trans('panel.sidemenu.status') !!}</span>
                           <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.status') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('role_access'))
                     <li class="nav-link-btn {{ request()->is('roles*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('roles') }}">
                           <i class="material-icons icon">vertical_shades_closed</i>
                           <span>{!! trans('panel.sidemenu.roles') !!}</span>
                           <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.roles') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('permission_access'))
                     <li class="nav-link-btn {{ request()->is('permissions*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('permissions') }}">
                           <i class="material-icons icon">workspace_premium</i>
                           <span>{!! trans('panel.sidemenu.permissions') !!}</span>
                           <div class="d-none mobile_hide">{!! trans('panel.sidemenu.permissions') !!}</div>
                        </a>
                     </li>
                     @endif
                  </ul>
               </div>
            </li>
            @endif
            @if(auth()->user()->can('supports_access'))
            <li class="nav-link">
               <a class="collapsed hoveradd" data-toggle="collapse" href="#supportMenu" aria-expanded="false">
                  <i class="material-icons icon">houseboat</i>
                  <span> {!! trans('panel.sidemenu.support_master') !!}
                  </span>
                  <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.support_master') !!}</div>
               </a>
               <div class="collapse" id="supportMenu" style="">
                  <ul class="navd">
                     @if(auth()->user()->can('supports_access'))
                     <li class="nav-link-btn ">
                        <a class="hoveradd2" href="{{ url('supports') }}">
                           <i class="material-icons icon">kitesurfing</i>
                           <span>{!! trans('panel.sidemenu.support') !!}</span>
                           <div class="d-none mobile_hide">{!! trans('panel.sidemenu.support') !!}</div>
                        </a>
                     </li>
                     @endif
                  </ul>
               </div>
            </li>
            @endif
            @if(auth()->user()->can('visitreport_access') || auth()->user()->can('beat_access'))
            <li class="nav-link {{ request()->is('beats*') || request()->is('beatdetail*') ? 'active' : '' }}">
               <a class="collapsed hoveradd" data-toggle="collapse" href="#beatMenu" aria-expanded="false">
                  <i class="material-icons icon">houseboat</i>
                  <span> Beats
                  </span>
                  <div class="d-none mobile_hide"> Beats</div>
               </a>
               <div class="collapse" id="beatMenu" style="">
                  <ul class="navd">
                     @if(auth()->user()->can('beat_access'))
                     <li class="nav-link-btn {{ request()->is('beats*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('beats') }}">
                           <i class="material-icons icon">kitesurfing</i>
                           <span>{!! trans('panel.sidemenu.beats') !!}</span>
                           <div class="d-none mobile_hide">{!! trans('panel.sidemenu.beats') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('beatdetail_access'))
                     <li class="nav-link-btn {{ request()->is('beatdetail*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('beatdetail') }}">
                           <i class="material-icons icon">waves</i>
                           <span>{!! trans('panel.sidemenu.beatdetail') !!}</span>
                           <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.beatdetail') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('checkin_access'))
                     <li class="nav-link-btn {{ request()->is('checkin*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('checkin') }}">
                           <i class="material-icons icon">assignment_turned_in</i>
                           <span>{!! trans('panel.sidemenu.checkin') !!}</span>
                           <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.checkin') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('visitreport_access'))
                     <li class="nav-link-btn {{ request()->is('visitreports*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('visitreports') }}">
                           <i class="material-icons icon">summarize</i>
                           <span>{!! trans('panel.sidemenu.visitreport') !!}</span>
                           <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.visitreport') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('visittype_access'))
                     <li class="nav-link-btn {{ request()->is('visittypes*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('visittypes') }}">
                           <i class="material-icons icon">border_color</i>
                           <span>{!! trans('panel.sidemenu.visittype') !!}</span>
                           <div class="d-none mobile_hide"> {!! trans('panel.sidemenu.visittype') !!}</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('visitreport_access'))
                     <li class="nav-link-btn">
                        <a class="hoveradd2" href="{{ url('mastervisitreport') }}">
                           <i class="material-icons icon">store</i>
                           <span>Master VisitReport</span>
                           <div class="d-none mobile_hide"> Master VisitReport</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('adherence_report'))
                     <li class="nav-link-btn {{ request()->is('reports/beatadherence*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('reports/beatadherence') }}">
                           <i class="material-icons icon">vrpano</i>
                           <span>Beat Adherence </span>
                           <div class="d-none mobile_hide"> Beat Adherence </div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('summary_report'))
                     <li class="nav-link-btn {{ request()->is('reports/adherencesummary*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('reports/adherencesummary') }}">
                           <i class="material-icons icon">summarize</i>
                           <span>Adherence Summary</span>
                           <div class="d-none mobile_hide"> Adherence Summary</div>
                        </a>
                     </li>
                     @endif
                  </ul>
               </div>
            </li>
            @endif
            @if(auth()->user()->can('reports'))
            <li class="nav-link {{ request()->is('reports*') ? 'active' : '' }}">
               <a class="collapsed hoveradd" data-toggle="collapse" href="#tasksMenu" aria-expanded="false">
                  <i class="material-icons icon">airplay</i>
                  <span> Reports
                  </span>
                  <div class="d-none mobile_hide"> Reports</div>
               </a>
               <div class="collapse" id="tasksMenu" style="">
                  <ul class="navd">
                     <!--                 @if(auth()->user()->can('attendance_report'))
                                 <li class="nav-item {{ request()->is('reports/attendancereport*') ? 'active' : '' }}">
                                   <a class="nav-link" href="{{ url('reports/attendancereport') }}">
                                     <i class="material-icons">check_circle</i>
                                     <p>Attendance Report</p>
                                   </a>
                                 </li>
                                 @endif -->
                     @if(auth()->user()->can('reports_sale'))
                     <li class="nav-link-btn add_icon {{ request()->is('reports/reports_sale*') || request()->is('reports/fos_rating*') || request()->is('reports/primary_sales*') || request()->is('reports/secondary_sales*') || request()->is('reports/product_analysis_qty*') || request()->is('reports/product_analysis_branch*') || request()->is('reports/product_analysis_value*') || request()->is('reports/group_wise_analysis*') || request()->is('reports/asm_rating*') || request()->is('reports/ch_rating*') ? 'active' : '' }}">
                        <a class="hoveradd" data-toggle="collapse" href="#salesReportsMenu" aria-expanded="false">
                           <i class="material-icons icon">point_of_sale</i>
                           <span>Sales</span>
                           <div class="d-none mobile_hide"> Sales</div>
                        </a>
                     <li class="nav-link-btn ">
                        <div class="collapse" id="salesReportsMenu" style="">
                           <ul class="navd">
                              @if(auth()->user()->can('user_working_report'))
                              <li class="nav-link-btn {{ request()->is('reports/reports_sale*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/reports_sale') }}">
                                    <i class="material-icons icon">hub</i>
                                    <span>User working report</span>
                                    <div class="d-none mobile_hide"> User working report</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('fos_rating_report'))
                              <li class="nav-link-btn {{ request()->is('reports/fos_rating*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/fos_rating') }}">
                                    <i class="material-icons icon">trending_down</i>
                                    <span>FOS Rating Report</span>
                                    <div class="d-none mobile_hide"> FOS Rating Report</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('asm_rating_report'))
                              <li class="nav-link-btn {{ request()->is('reports/asm_rating*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/asm_rating') }}">
                                    <i class="material-icons icon">trending_down</i>
                                    <span>Rating Report</span>
                                    <div class="d-none mobile_hide">Rating Report</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('ch_rating_report'))
                              <!-- <li class="nav-link-btn {{ request()->is('reports/ch_rating*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/ch_rating') }}">
                                    <i class="material-icons icon">trending_down</i>
                                    <span>CH/BM Rating Report</span>
                                    <div class="d-none mobile_hide"> CH/BM Rating Report</div>
                                 </a>
                              </li> -->
                              @endif
                              @if(auth()->user()->can('dashboard_primary_sales_access'))
                              <li class="nav-link-btn {{ request()->is('reports/primary_sales*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/primary_sales') }}">
                                    <i class="material-icons icon">stay_primary_landscape</i>
                                    <span>Primary Sales</span>
                                    <div class="d-none mobile_hide"> Primary Sales</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('dashboard_secondary_sales_access'))
                              <li class="nav-link-btn {{ request()->is('reports/secondary_sales*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/secondary_sales') }}">
                                    <i class="material-icons icon">cases</i>
                                    <span>Secondary Sales </span>
                                    <div class="d-none mobile_hide"> Secondary Sales </div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('product_analysis_branch_access'))
                              <li class="nav-link-btn {{ request()->is('reports/product_analysis_branch*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/product_analysis_branch') }}">
                                    <i class="material-icons icon">store</i>
                                    <span>Product Analysis Branch Wise</span>
                                    <div class="d-none mobile_hide"> Product Analysis Branch Wise</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('product_analysis_qty_access'))
                              <li class="nav-link-btn {{ request()->is('reports/product_analysis_qty*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/product_analysis_qty') }}">
                                    <i class="material-icons icon">insert_chart</i>
                                    <span>Product Analysis Qty</span>
                                    <div class="d-none mobile_hide"> Product Analysis Qty</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('product_analysis_value_access'))
                              <li class="nav-link-btn {{ request()->is('reports/product_analysis_value*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/product_analysis_value') }}">
                                    <i class="material-icons icon">add_chart</i>
                                    <span>Product Analysis Value</span>
                                    <div class="d-none mobile_hide"> Product Analysis Value</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('group_wise_analysis_access'))
                              <li class="nav-link-btn {{ request()->is('reports/group_wise_analysis*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/group_wise_analysis') }}">
                                    <i class="material-icons icon">query_stats</i>
                                    <span>Group Wise Analysis</span>
                                    <div class="d-none mobile_hide"> Group Wise Analysis</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('per_employee_costing_access'))
                              <li class="nav-link-btn {{ request()->is('reports/per_employee_costing*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/per_employee_costing') }}">
                                    <i class="material-icons icon">temple_buddhist</i>
                                    <span>Per Employee Costing</span>
                                    <div class="d-none mobile_hide"> Per Employee Costing</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('top_dealer_access'))
                              <li class="nav-link-btn {{ request()->is('reports/top_dealer*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/top_dealer') }}">
                                    <i class="material-icons icon">lightbulb_circle</i>
                                    <span>Top Dealer</span>
                                    <div class="d-none mobile_hide"> Top Dealer</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('dealer_growth_access'))
                              <li class="nav-link-btn {{ request()->is('reports/dealer_growth*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/dealer_growth') }}">
                                    <i class="material-icons icon">diversity_2</i>
                                    <span>Dealer Growth</span>
                                    <div class="d-none mobile_hide"> Dealer Growth</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('new_dealer_sale_access'))
                              <li class="nav-link-btn {{ request()->is('reports/new_dealer_sale*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/new_dealer_sale') }}">
                                    <i class="material-icons icon">expand</i>
                                    <span>New Dealer Sale</span>
                                    <div class="d-none mobile_hide"> New Dealer Sale</div>
                                 </a>
                              </li>
                              @endif
                              @if(auth()->user()->can('user_incentive_access'))
                              <li class="nav-link-btn {{ request()->is('reports/user_incentive*') ? 'active' : '' }}">
                                 <a class="hoveradd2" href="{{ url('reports/user_incentive') }}">
                                    <i class="material-icons icon">biotech</i>
                                    <span>User Incentive</span>
                                    <div class="d-none mobile_hide"> User Incentive</div>
                                 </a>
                              </li>
                              @endif
                           </ul>
                        </div>
                     </li>
            </li>
            @endif
            @if(auth()->user()->can('daily_visit_report'))
            <li class="nav-link-btn add_icon" style="display:none;">
               <a class="hoveradd" href="{{ url('reports/per_day_counter_visit_report') }}">
                  <i class="material-icons icon">store</i>
                  <span>Perday Counter Visit Report</span>
                  <div class="d-none mobile_hide"> Perday Counter Visit Report</div>
               </a>
            </li>
            @endif
            @if(auth()->user()->can('fielda_ctivity_report'))
            <li class="nav-link-btn  " style="display:none;">
               <a class="hoveradd2" href="{{ url('reports/fieldactivity') }}">
                  <i class="material-icons icon">store</i>
                  <span>Field Activity Report</span>
                  <div class="d-none mobile_hide"> Field Activity Report</div>
               </a>
            </li>
            @endif
            @if(auth()->user()->can('tour_programme_report'))
            <li class="nav-link-btn " style="display:none;">
               <a class="hoveradd2" href="{{ url('reports/tourprogramme') }}">
                  <i class="material-icons icon">store</i>
                  <span>Tour Programme Report</span>
                  <div class="d-none mobile_hide"> Tour Programme Report</div>
               </a>
            </li>
            @endif
            @if(auth()->user()->can('monthly_movement_report'))
            <li class="nav-link-btn " style="display:none;">
               <a class="hoveradd2" href="{{ url('reports/monthlymovement') }}">
                  <i class="material-icons icon">store</i>
                  <span>Monthly Movement Report</span>
                  <div class="d-none mobile_hide"> Monthly Movement Report</div>
               </a>
            </li>
            @endif
            @if(auth()->user()->can('point_collections_report'))
            <li class="nav-link-btn " style="display:none;">
               <a class="hoveradd2" href="{{ url('reports/pointcollections') }}">
                  <i class="material-icons icon">store</i>
                  <span>Point Collections Report</span>
                  <div class="d-none mobile_hide"> Point Collections Report</div>
               </a>
            </li>
            @endif
            @if(auth()->user()->can('territory_coverage_report'))
            <li class="nav-link-btn" style="display:none;">
               <a class="hoveradd2" href="{{ url('reports/territorycoverage') }}">
                  <i class="material-icons icon">store</i>
                  <span>Territory Coverage Report</span>
                  <div class="d-none mobile_hide"> Territory Coverage Report</div>
               </a>
            </li>
            @endif
            @if(auth()->user()->can('performance_parameter_report'))
            <li class="nav-link-btn" style="display:none;">
               <a class="hoveradd2" href="{{ url('reports/performanceparameter') }}">
                  <i class="material-icons icon">store</i>
                  <span>Performance Parameter</span>
                  <div class="d-none mobile_hide"> Performance Parameter</div>
               </a>
            </li>
            @endif
            @if(auth()->user()->can('mechanics_points_report'))
            <li class="nav-link-btn " style="display:none;">
               <a class="hoveradd2" href="{{ url('reports/asmwisemechanicspoints') }}">
                  <i class="material-icons icon">store</i>
                  <span>Mechanics Points Report</span>
                  <div class="d-none mobile_hide"> Mechanics Points Report</div>
               </a>
            </li>
            @endif
            @if(auth()->user()->can('targetvs_sales_report'))
            <li class="nav-link-btn " style="display:none;">
               <a class="hoveradd2" href="{{ url('reports/targetvssales') }}">
                  <i class="material-icons icon">store</i>
                  <span>Target Vs Sales Report</span>
                  <div class="d-none mobile_hide"> Target Vs Sales Report</div>
               </a>
            </li>
            @endif
            @if(auth()->user()->can('survey_analysis_report'))
            <li class="nav-link-btn " style="display:none;">
               <a class="hoveradd2" href="{{ url('reports/surveyanalysis') }}">
                  <i class="material-icons icon">store</i>
                  <span>Survey Analysis Report</span>
                  <div class="d-none mobile_hide"> Survey Analysis Report</div>
               </a>
            </li>
            @endif
            @if(auth()->user()->can('customers_report_access'))
            <li class="nav-link-btn add_icon">
               <a class="hoveradd" data-toggle="collapse" href="#customerReportsMenu" aria-expanded="false">
                  <i class="material-icons icon">support_agent</i>
                  <span>Customers</span>
                  <div class="d-none mobile_hide"> Customers</div>
               </a>
            <li class="nav-link-btn">
               <div class="collapse" id="customerReportsMenu" style="">
                  <ul class="navd">
                     @if(auth()->user()->can('visit_report'))
                     <li class="nav-link-btn {{ request()->is('reports/customervisit*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('reports/customervisit') }}">
                           <i class="material-icons icon">dashboard_customize</i>
                           <span>Customer Visit</span>
                           <div class="d-none mobile_hide"> Customer Visit</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('customers_report'))
                     <li class="nav-link-btn ">
                        <a class="hoveradd2" href="{{ url('reports/customersreport') }}">
                           <i class="material-icons icon">contact_emergency</i>
                           <span>Customer Master</span>
                           <div class="d-none mobile_hide">Customer Master</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('calling_report'))
                     <li class="nav-link-btn ">
                        <a class="hoveradd2" href="{{ url('notes') }}">
                           <i class="material-icons icon">dialpad</i>
                           <span>Calling Report</span>
                           <div class="d-none mobile_hide"> Calling Report</div>
                        </a>
                     </li>
                     @endif
                     <li class="nav-link-btn {{ request()->is('reports/marketIntelligence*') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('reports/marketIntelligence') }}">
                           <i class="material-icons icon">nature_people</i>
                           <span> Market Intelligence</span>
                           <div class="d-none mobile_hide"> Market Intelligence</div>
                        </a>
                     </li>
                  </ul>
               </div>
            </li>
            </li>
            @endif
            @if(auth()->user()->can('loyalty_report_access'))
            <li class="nav-link-btn add_icon ">
               <a class="hoveradd" data-toggle="collapse" href="#loyaltyMenu" aria-expanded="false">
                  <i class="material-icons icon">loyalty</i>
                  <span>Loyalty</span>
                  <div class="d-none mobile_hide"> Loyalty</div>
               </a>
            <li class="nav-link-btn">
               <div class="collapse" id="loyaltyMenu" style="">
                  <ul class="navd">
                     @if(auth()->user()->can('loyalty_summary_report'))
                     <li class="nav-link-btn {{ request()->is('reports/loyalty_summary_report') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('reports/loyalty_summary_report') }}">
                           <i class="material-icons icon">card_membership</i>
                           <span>Loyalty Summary Report</span>
                           <div class="d-none mobile_hide"> Loyalty Summary Report</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('loyalty_dealer_wise_summary_report'))
                     <li class="nav-link-btn {{ request()->is('reports/loyalty_dealer_wise_summary_report') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('reports/loyalty_dealer_wise_summary_report') }}">
                           <i class="material-icons icon">sign_language</i>
                           <span>Loyalty Dealer Wise Summary Report</span>
                           <div class="d-none mobile_hide"> Loyalty Dealer Wise Summary Report</div>
                        </a>
                     </li>
                     @endif
                     @if(auth()->user()->can('loyalty_retailer_wise_summary_report'))
                     <li class="nav-link-btn {{ request()->is('reports/loyalty_retailer_wise_summary_report') ? 'active' : '' }}">
                        <a class="hoveradd2" href="{{ url('reports/loyalty_retailer_wise_summary_report') }}">
                           <i class="material-icons icon">point_of_sale</i>
                           <span>Retailer Wise Loyalty Summary Report</span>
                           <div class="d-none mobile_hide"> Retailer Wise Loyalty Summary Report</div>
                        </a>
                     </li>
                     @endif
                  </ul>
               </div>
            </li>
            </li>
            @endif
            </ul>
         </div>
         </li>
         @endif
         </ul>
   </div>
   <!--   <div class="bottom-content">
                  <li class="">
                    <a href="#">
                      <i class='bx bx-log-out'></i>
                      <span class="text nav-text">Logout</span>
                    </a>
                  </li>
                  
                  </div> -->
   </div>
   </nav>
   <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
         <div class="container-fluid" style="background: transparent; !important">
            <div class="new_demo">
            <img class="rounded ml-2 iconimg" alt="Zenith Sun Spark" src="{!! asset('assets/img/zenithsunspark.png') !!}?" width="75">
            <!-- <img class="rounded ml-2  iconimg" src="{!! asset('assets/img/silver.png') !!}" width="100"> -->
         </div>
            <!-- <img src="{!! asset('assets/img/logo.png') !!}" width="50"> -->
            <div class="navbar-wrapper">
               <div class="navbar-minimize">
                  <!--               <button id="minimizeSidebar" class="btn btn-just-icon btn-white btn-fab btn-round">
                           <i class="material-icons text_align-center visible-on-sidebar-regular">more_vert</i>
                           <i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">view_list</i>
                           <div class="ripple-container"></div></button> -->
               </div>
            </div>
            <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
               <span class="sr-only">Toggle navigation</span>
               <span class="navbar-toggler-icon icon-bar"></span>
               <span class="navbar-toggler-icon icon-bar"></span>
               <span class="navbar-toggler-icon icon-bar"></span>
            </button>
            @auth
            <div class="collapse navbar-collapse justify-content-end">
               <ul class="navbar-nav">
                  <!-- <li class="nav-item dropdown">
                           <a class="nav-link" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                             <i class="material-icons">notifications</i>
                             <span class="notification">5</span>
                             <p class="d-lg-none d-md-block">
                               Some Actions
                             </p>
                           </a>
                           <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                             @if(auth()->user()->can('visitor_log_access'))
                             <a href="{{route('visitor')}}" class="btn btn-info btn-sm float-right">Visitor Logs</a>
                             @endif
                             <a class="dropdown-item" href="#">Mike John responded to your email</a>
                             <a class="dropdown-item" href="#">You have 5 new tasks</a>
                             <a class="dropdown-item" href="#">You're now friend with Andrew</a>
                             <a class="dropdown-item" href="#">Another Notification</a>
                             <a class="dropdown-item" href="#">Another One</a>
                           </div>
                           </li> -->
                  <li class="nav-item dropdown d-flex" style="align-items: center;">
                     <p class="m-0" style="font-weight: bold;">{{ auth()->user()->name }}</p>
                     <a class="nav-link" href="javascript:;" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @if (auth()->user()->getMedia('profile_image')->count() > 0 && Storage::disk('s3')->exists(auth()->user()->getMedia('profile_image')[0]->getPath()))
                        <img src="{{ auth()->user()->getMedia('profile_image')[0]->getFullUrl() }}" border="0" width="40" class="rounded-circle" />
                        @else
                        <i class="material-icons">person</i>
                        @endif
                        <p class="d-lg-none d-md-block">
                           Account
                        </p>
                     </a>
                     @auth
                     <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                        <!-- <a class="dropdown-item" href="{{ url('change-password') }}">Change Password</a> -->
                        <!-- <div class="dropdown-divider"></div> -->
                        <a class="dropdown-item" href="{{ url('logout') }}">Log out</a>
                     </div>
                     @endauth
                  </li>
               </ul>
            </div>
            @endauth
         </div>
      </nav>
      <!-- End Navbar -->
      <div class="content">
         <div class="container-fluid">
            {{ $slot }}
         </div>
      </div>
      <footer class="footer">
         <div class="baseurl" data-baseurl="{{ url('/')}}"></div>
         <div class="token" data-token="{{ csrf_token() }}"></div>
         <div class="container-fluid">
            <nav class="float-left">
            </nav>
            <div class="copyright float-right">
            </div>
         </div>
      </footer>
   </div>
   </div>
   <div class="modal fade bd-example-modal-lg" id="previewimageInModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
         <div class="modal-content card">
            <div class="card-header">
               <span class="pull-right">
                  <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
               </span>
            </div>
            <div class="modal-body"> <img class="modal-content" id="img01"> </div>
         </div>
      </div>
   </div>
   <script src="{{ asset('assets/js/core/jquery.validate.js') }}"></script>
   <!-- Bootstrap -->
   <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
   <!-- overlayScrollbars -->
   <script src="{{ asset('assets/js/core/bootstrap-material-design.min.js') }}"></script>
   <!-- DataTables -->
   <script src="{{ asset('assets/js/plugins/jquery.dataTables.min.js') }}"></script>
   <script src="{{ asset('assets/js/plugins/dataTables.responsive.min.js') }}"></script>
   <script src="{{ asset('assets/js/plugins/bootstrap-tagsinput.js') }}"></script>
   <!-- OPTIONAL SCRIPTS -->
   <!-- Select2 -->
   <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
   <script src="{{ asset('assets/js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
   <script src="{{ asset('assets/js/plugins/sweetalert2.js') }}"></script>
   <script src="{{ asset('assets/js/plugins/jquery.validate.min.js') }}"></script>
   <!-- jquery-validation -->
   <script src="{{ asset('assets/js/plugins/jquery.bootstrap-wizard.js') }}"></script>
   <script src="{{ asset('assets/js/plugins/bootstrap-selectpicker.js') }}"></script>
   <script src="{{ asset('assets/js/plugins/bootstrap-datetimepicker.min.js') }}"></script>
   <!-- OPTIONAL SCRIPTS -->
   <script src="{{ asset('assets/js/plugins/chartist.min.js') }}"></script>
   <script src="{{ asset('assets/js/plugins/bootstrap-notify.js') }}"></script>
   <script src="{{ asset('assets/js/material-dashboard.js?v=2.1.2') }}"></script>
   <script src="{{ asset('assets/demo/demo.js') }}"></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" integrity="sha512-hievggED+/IcfxhYRSr4Auo1jbiOczpqpLZwfTVL/6hFACdbI3WQ8S9NCX50gsM9QVE+zLk/8wb9TlgriFbX+Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js" integrity="sha512-F636MAkMAhtTplahL9F6KmTfxTmYcAcjcCkyu0f0voT3N/6vzAuJ4Num55a0gEJ+hRLHhdz3vDvZpf6kqgEa5w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
   <script>
      $(function() {
         //Initialize Select2 Elements
         $('.select2').select2()

         $('#toggle-one').bootstrapToggle();
         $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
         });
         $(".datepicker").datepicker({
            createButton: false,
            displayClose: true,
            closeOnSelect: false,
            selectMultiple: true,
            dateFormat: 'yy-mm-dd',
            beforeShow: function(input) {
               $(input).css({
                  "position": "relative",
                  "z-index": 999999
               });
            },
            onClose: function() {
               $('.ui-datepicker').css({
                  'z-index': 0
               });
            }
         });


         //Initialize Select2 Elements
         $('.select2bs4').select2({
            theme: 'bootstrap4'
         })
         $('.timepicker').datetimepicker({
            format: 'HH:mm'
         });
      })
      $('body').on('click', '.imageDisplayModel', function() {
         var imgPath = $(this).attr("src");
         var modal = document.getElementById('previewimageInModel');
         $('#previewimageInModel').modal('show');
         document.getElementById("img01").src = imgPath;
      });

      document.addEventListener("DOMContentLoaded", function() {
         document.getElementById("loader").style.display = "block";
         document.querySelector(".content").style.display = "none";
      });

      // Hide loader and show content when the page is fully loaded
      window.addEventListener("load", function() {
         document.getElementById("loader").style.display = "none";
         document.querySelector(".content").style.display = "block";
      });
   </script>
   <script>
      const body = document.querySelector('body'),
         sidebar = body.querySelector('nav'),
         toggle = body.querySelector(".toggle"),
         // searchBtn = body.querySelector(".search-box"),
         modeSwitch = body.querySelector(".toggle-switch"),
         modeText = body.querySelector(".mode-text");
      toggle.addEventListener("click", () => {
         sidebar.classList.toggle("close");
      })
      // searchBtn.addEventListener("click", () => {
      //   sidebar.classList.remove("close");
      // })
      // modeSwitch.addEventListener("click", () => {
      //   body.classList.toggle("dark");

      //   if (body.classList.contains("dark")) {
      //     modeText.innerText = "Light mode";
      //   } else {
      //     modeText.innerText = "Dark mode";

      //   }
      // });
   </script>
   <!-- Google tag (gtag.js) -->
   <script async src="https://www.googletagmanager.com/gtag/js?id=G-8D1DXGE6Z6"></script>
   <script>
      window.dataLayer = window.dataLayer || [];

      function gtag() {
         dataLayer.push(arguments);
      }
      gtag('js', new Date());

      gtag('config', 'G-8D1DXGE6Z6');
   </script>
</body>
@yield('script')

</html>