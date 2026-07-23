<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-tabs card-header-warning">
          <div class="nav-tabs-navigation">
            <div class="nav-tabs-wrapper">
              <h4 class="card-title ">Marketing Master Upload
                @if(auth()->user()->can(['marketing_master_access']))
                <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('marketings') }}">
                      <i class="material-icons">next_plan</i> Marketing Master
                      <div class="ripple-container"></div>
                    </a>
                  </li>
                </ul>
                @endif
              </h4>
            </div>
          </div>
        </div>
        <div class="card-body">
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
          <form action="{{ URL::to('marketings_upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="google_drivelink">Google DriveLink <span class="text-danger"> *</span></label>
                  <input type="text" name="google_drivelink" class="form-control" required>
                  @if ($errors->has('google_drivelink'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('google_drivelink') }}</p>
                  </div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="count_of_participant">Count Of Participants <span class="text-danger"> *</span> <small>(the total number of people or groups of people that are involved in an event.)</small></label>
                  <input type="number" name="count_of_participant" class="form-control" required>
                  @if ($errors->has('count_of_participant'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('count_of_participant') }}</p>
                  </div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                    <span class="btn btn-just-icon btn-theme btn-file">
                      <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                      <span class="fileinput-exists">Change</span>
                      <input type="hidden">
                      <input title="Please select a file for upload data" type="file" title="Select file for upload data" name="import_file" style="flex-wrap: nowrap;" required accept=".xls,.xlsx" />
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <div class="input-group-append">
                    <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Marketing Master">
                      <i class="material-icons">cloud_upload</i>
                      <div class="ripple-container"></div>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/validation_loyalty.js') }}"></script>
</x-app-layout>