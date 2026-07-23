<x-app-layout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">{!! trans('panel.wallet.title_singular') !!}</h3>
                <div class="card-tools">
                  <div class="btn-group">
                    <a href="{{ url('wallets') }}" class="btn btn-info btn-sm">
                      <i class="fas fa-undo"></i><span style="padding-left: 10px;">{!! trans('panel.wallet.title') !!}</span>
                    </a>
                </div>
              </div>
            </div>
              <!-- /.card-header -->
             
            </div>
          </div>
        </div>
      </div>
    </section>
</x-app-layout>
