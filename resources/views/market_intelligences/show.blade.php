<x-app-layout>
  <style>
    .image-container {
      width: 280px;
      height: 200px;
      border-radius: 5%;
      overflow: hidden;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #f0f0f0;
      text-align: center;
      position: relative;
      margin: 10px auto;
    }

    .circle-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 5%;
    }

    .no-image {
      width: 100%;
      height: 100%;
      border-radius: 5%;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #ddd;
      color: #555;
      font-size: 18px;
      font-weight: bold;
    }

    .view-image-btn {
      display: block;
      position: relative;
      width: 100%;
      height: 100%;
      border-radius: 10%;
      text-align: center;
    }

    .btn-overlay {
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(0, 0, 0, 0.6);
      color: #fff;
      padding: 6px 10px;
      border-radius: 15px;
      font-size: 16px;
      display: none;
    }

    .view-image-btn:hover .btn-overlay {
      display: block;
    }

    .details-container {
      text-align: center;
      padding: 10px;
      border-radius: 8px;
    }

    .details-container h5 {
      margin: 5px 0;
      font-size: 14px;
    }
  </style>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12 main-content1">
        @if(Session::has('success'))
        <div class="alert alert-success" id="hide_div">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{!! session('success') !!}</strong>
        </div>
        @endif

        @if(Session::has('danger'))
        <div class="alert alert-danger" id="hide_danger">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{!! session('danger') !!}</strong>
        </div>
        @endif


        <div class="row">
          <div class="col-md-12">
            <div class="card mt-0 p-0">
              <div class="card-header m-0 card-header-tabs card-header-warning">
                <div class="d-flex justify-content-between align-items-center">
                  <h4 class="card-title m-0">Market Intelligence View</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="alert" style="display: none;" id="hide_check">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <strong class="message"></strong>
        </div>
        
        <hr>
        <div class="row">
          <div class="col-md-12">
            <div class="card mt-0 p-0">
              <div class="details-container row">
                <div class="col-3 left-details">
                  <h5 class="text-dark"><strong>Created At: {!! date('d-M-Y', strtotime($id->created_at)) !!}</strong></h5>
                </div>
                <div class="col-3 left-details">
                  <h5 class="text-dark"><strong>Created By: {!! $id->createdbyname->name !!}</strong></h5>
                </div>
                <div class="col-3 right-details">
                  <h5 class="text-dark"><strong>State: {!! $id->data->state !!}</strong></h5>
                </div>
                <div class="col-3 right-details">
                  <h5 class="text-dark"><strong>Division: {!! $id->division->division_name !!}</strong></h5>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="mt-0 p-0">
              <div id="image-div" class="image-container">
                @php
                $media = $id->getMedia('servey_image')->first();
                @endphp
                @if ($media)
                <a href="{{ $media->getFullUrl() }}" target="_blank" class="view-image-btn">
                  <img class="circle-image" src="{{ $media->getFullUrl() }}" alt="Survey Image">
                  <span class="btn-overlay">View Image</span>
                </a>
                @else
                <div class="no-image">No Image</div>
                @endif
              </div>
            </div>
          </div>
          @php
          $chunks = $fields->split(2);
          @endphp

          @foreach($chunks as $chunk)
          <div class="col-md-4">
            <div class="card mt-0 p-0">
              <table class="table table-striped table-hover">
                @foreach($chunk as $key => $value)
                <tr>
                  <th class="text-secondary text-center">{!! $value->field_name !!}</th>
                  <th>:</th>
                  @php
                  $dataValue = $id->data->where('key', $value->key)->first();
                  @endphp
                  @if($dataValue)
                  <td class="text-bold text-center">{!! $dataValue->value !!}</td>
                  @else
                  <td class="text-bold text-center">-</td>
                  @endif
                </tr>
                @endforeach
              </table>
            </div>
          </div>

          @endforeach

        </div>
        <hr>
      </div>

    </div>


    <!-- end model for status -->

    <!-- Custom styles for this page -->
    <link rel="stylesheet" href="{{ url('/').'/'.asset('lightboxx/css/lightbox.min.css') }}">

    <script src="{{ url('/').'/'.asset('lightboxx/js/lightbox-plus-jquery.min.js') }}"></script>

    <!-- for checked -->
  </section>
  <!-- /.content -->
</x-app-layout>