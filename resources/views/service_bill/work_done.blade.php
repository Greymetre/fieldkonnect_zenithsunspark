<x-app-layout>
  <style>
    
  </style>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">

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

        <div class="alert" style="display: none;" id="hide_check">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <strong class="message"></strong>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-6">
                <h5 class="card-title pb-1">Complaint > {!! $complaint['complaint_number'] !!}</h5>
              </div>

            </div>
            <hr>
            <div class="invoice">
              <div class="row">
                <div class="col-12">
                  <h4>
                    <h5><b>Work Done</b></h5>
                  </h4>
                  <hr>
                </div>
              </div>
              <form action="{{route('complaint_work_done_submit')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                  <div class="col-md-6">
                    <input type="hidden" id="complaint_id" name="complaint_id" value="{{$complaint['id']}}">
                    <div class="form-group">
                      <h6>Action Done By ASC</h6>
                      <select name="done_by" id="done_by" class="form-control select2">
                        <option value="" disabled selected>Action Done By ASC...</option>
                        <option value="Repairing">Repairing</option>
                        <option value="Replacement">Replacement</option>
                        <option value="Telephonic Complaint Resolve">Telephonic Complaint Resolve</option>
                        <option value="Complaint Cancelltion">Complaint Cancelltion</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <h6>Service Center Remark</h6>
                      <textarea name="remark" id="remark" placeholder="Service Center Remark" class="form-control"></textarea>
                    </div>
                  </div>
                </div>
                <div class="row">
                    <h6>Attechment</h6>
                    <input class="form-control form-control-lg" name="work_done_attach[]" id="work_done_attach" multiple type="file" accept="application/pdf, image/*">
                </div>
                <input type="submit" value="UPDATE" class="btn btn-success btn-lg">
              </form>
            </div>
          </div>
        </div>

  </section>

</x-app-layout>