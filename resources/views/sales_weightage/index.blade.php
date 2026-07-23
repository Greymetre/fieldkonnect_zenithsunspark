<x-app-layout>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon card-header-theme">
                    <div class="card-icon">
                        <i class="material-icons">perm_identity</i>
                    </div>
                    <h4 class="card-title">{!! trans('panel.sales_weightage.title_singular') !!} {!! trans('panel.global.list') !!}
                        <span class="">
                            <div class="btn-group header-frm-btn">
                            <div class="next-btn">
                                <a href="{{route('sales_weightage.create')}}" class="btn btn-just-icon btn-theme create" title="Add Appraisal">
                                    <i class="material-icons">add_circle</i>
                                </a>
                            </div>
                            </div>
                        </span>
                    </h4>
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
                    @if(session()->has('message'))
                        <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="material-icons">close</i>
                        </button>
                            {{ session()->get('message') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table id="getattendance" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
                            <thead class=" text-primary">
                                <th>{!! trans('panel.global.no') !!}</th>
                                <th>{!! trans('panel.global.actions') !!}</th>
                                <th>{!! trans('panel.sales_weightage.financial_year') !!}</th>
                                <th>{!! trans('panel.sales_weightage.display_names') !!}</th>
                                <th>{!! trans('panel.sales_weightage.display_division') !!}</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('public/assets/js/jquery.custom.js') }}"></script>
<script src="{{ asset('public/assets/js/validation_products.js') }}"></script>
    <script type="text/javascript">
         $(function () {
            $.ajaxSetup({
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var table = $('#getattendance').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [
                    [0, 'desc']
                ],
                //"dom": 'Bfrtip',
                ajax: {
                    url: "{{ route('sales_weightage.index') }}",
                    data: function (d) {
                            d.user_id = $('#executive_id').val()
                        }
                    },
                columns: [
                    {data: 'DT_RowIndex',name: 'DT_RowIndex',orderable: false,searchable: false},
                    {data: 'action',name: 'action',orderable: false,searchable: false},
                    {data: 'financial_year',name: 'financial_year'},
                    {data: 'display_name',name: 'display_name'},
                    {data: 'devision',name: 'devision'},
                    // {data: 'weightage',name: 'weightage'},
                ]
            });
            $(document).on("click", ".delete", function(){
                var id = $(this).attr('value');
                var url = "{{ route('sales_weightage.destroy', ['sales_weightage' => ':id'] )}}";
                url = url.replace(':id', id);
    
                Swal.fire({
                    title: "Are you sure ?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Delete it",
                    confirmButtonColor: "#c93737",
                }).then((result) => {
                    console.log(result);
                    if (result.value == true) {
                        $.ajax({
                            url: url,
                            method:"DELETE",
                            data: {
                                "id": id
                            },
                            success: function(res) {
                                if(res.status){
                                    table.draw();
                                    Swal.fire("Deleted!", "", "success");
                                }else{
                                    Swal.fire("Somthing went wroung", "", "info");
                                }
                            }
                        });
                    } else if (result.dismiss == 'cancel') {
                        Swal.fire("Your data is safe", "", "info");
                    }
                });
            });
        });


    </script>
</x-app-layout>