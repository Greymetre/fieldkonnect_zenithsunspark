<x-app-layout>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon card-header-theme">
                    <div class="card-icon">
                        <i class="material-icons">perm_identity</i>
                    </div>
                    <h4 class="card-title">{!! trans('panel.appraisal.title_singular') !!} {!! trans('panel.global.list') !!}
                        <span class="">
                            <div class="btn-group header-frm-btn">
                                <form action="{{ route('appraisal.download') }}" method="post">
                                    <div class="d-flex flex-wrap flex-row">
                                    @csrf
                                    <div class="p-2" style="width: 250px;">
                                        <select class="selectpicker" multiple name="branch_id" id="branch_id" data-style="select-with-transition" title="Select Branch">
                                            <option value="">Select Branch</option>
                                            @if(@isset($branches ))
                                            @foreach($branches as $branche)
                                            <option value="{!! $branche['id'] !!}" {{ old( 'branch_id') == $branche['id'] ? 'selected' : '' }}>{!! $branche['name'] !!}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="p-2" style="width: 250px;">
                                        <select class="selectpicker" name="executive_id" id="executive_id" data-style="select-with-transition" title="Select User">
                                            <option value="">Select User</option>
                                            @if(@isset($users ))
                                            @foreach($users as $user)
                                            <option value="{!! $user['id'] !!}" {{ old( 'executive_id') == $user['id'] ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="p-2" style="width: 250px;">
                                        <select class="selectpicker" name="division_id" id="division_id" data-style="select-with-transition" title="Select Division">
                                            <option value="">Select division</option>
                                            @if(@isset($divisions ))
                                            @foreach($divisions as $division)
                                            <option value="{!! $division['id'] !!}" {{ old( 'division_id')}}>{!! $division['name'] !!}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>


                                    <div class="p-2" style="width: 250px;">
                                        <select class="selectpicker" name="financial_year" id="financial_year" data-style="select-with-transition">
                                            <option value="">Select Financial Year</option>
                                            @foreach($sale_weightage_years as $sale_weightage_year)
                                            <option value="{{$sale_weightage_year}}">{{$sale_weightage_year}}</option>
                                            @endforeach
                                        </select>
                                    </div>



                                    <div class="p-2">
                                    <button type="submit" href="" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.appraisal.title') !!}"><i class="material-icons">cloud_download</i></button>
                                    </div>
                                    </div>
                                </form>
                                <div class="next-btn"></div>
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
                    <div class="table-responsive">
                        <table id="getattendance" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
                            <thead class="text-primary">
                                <th>{!! trans('panel.global.no') !!}</th>
                                <th>{!! trans('panel.global.action') !!}</th>
                                <th>{!! trans('panel.beat.user_name') !!}</th>
                                <th>{!! trans('panel.user.designation') !!}</th>
                                <th>{!! trans('panel.user.division') !!}</th>
                                <th>{!! trans('panel.user.branch_name') !!}</th>
                                <th>{!! trans('panel.appraisal.financial_year') !!}</th>
                                <th>{!! trans('panel.appraisal.self_grade') !!}</th>
                                <th>{!! trans('panel.global.created_at') !!}</th>
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
        $(function() {
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
                    url: "{{ route('appraisal.index') }}",
                    data: function(d) {
                        d.user_id = $('#executive_id').val(),
                            d.division_id = $('#division_id').val(),
                            d.financial_year = $('#financial_year').val()
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        "defaultContent": '',
                        className: 'td-actions text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'designation_name',
                        name: 'designation_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'division_name',
                        name: 'division_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'branch_name',
                        name: 'branch_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'financial_year',
                        name: 'financial_year',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'grade',
                        name: 'grade',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'date',
                        name: 'date',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $("#executive_id").on('change', function() {
                table.draw();
            })
            $("#division_id").on('change', function() {
                table.draw();
            })
            $("#financial_year").on('change', function() {
                table.draw();
            })
        });

        $("#branch_id").on('change', function() {
            var search_branches = $(this).val();
            $.ajax({
                url: "{{ url('appraisal/index') }}",
                data: {
                    "search_branches": search_branches
                },
                success: function(res) {
                    if (res.status == true) {
                        var select = $('#executive_id');
                        select.empty();
                        select.append('<option>Select User</option>');
                        $.each(res.users, function(k, v) {
                            select.append('<option value="' + v.id + '" >' + v.name + '</option>');
                        });
                        select.selectpicker('refresh');
                    }
                }
            });

        })
    </script>
</x-app-layout>