<x-app-layout>
   <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
         {{ __('Dashboard') }}
      </h2>
   </x-slot>
   @if(auth()->user()->hasRole('Customer Dealer'))
   <div class="nav-wrapper position-relative end-0">
      <ul class="nav nav-pills nav-pills-warning nav-pills-icons justify-content-center" id="tabs" role="tablist">
         <li class="nav-item">
            <a class="nav-link active show" data-toggle="tab" href="#sliderTab" role="tablist">
               <i class="material-icons">tune</i> Slider
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#salesTab" role="tablist">
               <i class="material-icons">check_box_outline_blank</i> Sales
            </a>
         </li>
      </ul>
   </div>
   <div class="tab-content tab-space tab-subcategories">
      <div class="tab-pane active show" id="sliderTab">
         @if($dealer_poster_setting->slider == 'Y')
         <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
               @if($dealer_poster_setting->exists && $dealer_poster_setting->getMedia('dealer_portal_slider_image')->count() > 0 && Storage::disk('s3')->exists($dealer_poster_setting->getMedia('dealer_portal_slider_image')[0]->getPath()))
               @foreach($dealer_poster_setting->getMedia('dealer_portal_slider_image') as $k => $media)
               <li data-target="#carouselExampleIndicators" data-slide-to="{{$k}}" class="{{$k==0?'active':''}}"></li>
               @endforeach
               @endif
            </ol>
            <div class="carousel-inner">
               @if($dealer_poster_setting->exists && $dealer_poster_setting->getMedia('dealer_portal_slider_image')->count() > 0 && Storage::disk('s3')->exists($dealer_poster_setting->getMedia('dealer_portal_slider_image')[0]->getPath()))
               @foreach($dealer_poster_setting->getMedia('dealer_portal_slider_image') as $k => $media)
               <div class="carousel-item {{$k==0?'active':''}}">
                  <img class="d-block w-100" src="{{ $media->getFullUrl() }}" alt="{{$media->name}}">
               </div>
               @endforeach
               @endif
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
               <span class="carousel-control-prev-icon" aria-hidden="true"></span>
               <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
               <span class="carousel-control-next-icon" aria-hidden="true"></span>
               <span class="sr-only">Next</span>
            </a>
         </div>
         @endif
      </div>
      <div class="tab-pane" id="salesTab">
         <div class="row">
            <!-- Current Month Sales -->
            <div class="col-md-4 mb-4">
               <div class="card shadow-sm border-left-primary h-100 py-2">
                  <div class="card-body d-flex justify-content-between align-items-center">
                     <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                           Current Month Sales
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                           ₹ {{ number_format($salesSummary['month'], 2) }} Lakh
                        </div>
                     </div>
                     <div class="icon text-primary">
                        <i class="fas fa-calendar fa-2x"></i>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Current Quarter Sales -->
            <div class="col-md-4 mb-4">
               <div class="card shadow-sm border-left-success h-100 py-2">
                  <div class="card-body d-flex justify-content-between align-items-center">
                     <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                           Current Quarter Sales
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                           ₹ {{ number_format($salesSummary['quarter'], 2) }} Lakh
                        </div>
                     </div>
                     <div class="icon text-success">
                        <i class="fas fa-chart-line fa-2x"></i>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Current Financial Year Sales -->
            <div class="col-md-4 mb-4">
               <div class="card shadow-sm border-left-warning h-100 py-2">
                  <div class="card-body d-flex justify-content-between align-items-center">
                     <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                           Financial Year Sales
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                           ₹ {{ number_format($salesSummary['financial_year'], 2) }} Lakh
                        </div>
                     </div>
                     <div class="icon text-warning">
                        <i class="fas fa-rupee-sign fa-2x"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   @endif
</x-app-layout>