
<x-app-layout>

  <style>

        .jm{
            position: relative;
        }

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

        select.ese {
            border-radius: 50px;
            padding: 4px 10px;
            font-size: 16px;
            text-align: center;
        }

        input#o_amount {
            text-indent: 8px;
        }

        html body .modal .form-control {
            border: 1px solid #3860a4 !important;
            border-radius: 45px !important;
            padding: 0px 20px;
        }

        .modal .rangedata span {
            color: unset!important;
        }

        .modal .select2-container--default .select2-selection--single{
            border:1px solid #3860a4!important;
        }


        .rangedata span {
            color: #000;
        }

        .dollerdata {
            text-indent: 25px;
            border: 0px;
            border-bottom: 1px solid #eee;
        }

        .dollerdata::placeholder {
            color: #000;
        }

        span.doller {
            position: absolute;
            top: 6px;
            left: 10px;
            color: #394857;
            font-weight: 600;
            font-size: 19px;
        }

        .onetime {
            border-radius: 50px !important;
            padding: 0px 15px !important;
            background: #eee;
        }
        .bell p {
            color: #1F5489;
            font-size: 16px;
            font-weight: 400;
            line-height: 14px;
            letter-spacing: 0px;
            margin-bottom: 0px !important;
            border: 1px solid #E2E2E2 !important;
            border-radius: 50px;
            padding: 12px 0px;
            text-align: center;
        }

        .well {
            width: 75%;
        }

        .border-bn {
            border: 1px solid #E8E8E8 !important;
        }

        .bell {
            width: 30%;
            text-align: right;
        }

        .bmd-form-group label,label{
            color: #000;
            font-weight: 500;
        }

        html body .modal .form-control {
            color: #000;
            font-weight: 500;
        }

        html body .modal .form-control::placeholder {
            color: #000 !important;
            font-weight: 500 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #000 !important;
            font-weight: 500 !important;
        }

        span#confidenceValue {
            color: #000 !important;
            font-weight: 500;
        }

        h4.modal-title {
            color: #000;
            font-weight: bold;
        }

        @media only screen and (min-width: 992px) and (max-width: 1024px){

            .board {
                display: flex;
                flex-wrap: wrap;
            }

            .column.p-0 {
                width: 100%;
                min-width: 46%!important;
            }

            .column_drag {
                min-height: auto!important;
            }


        }

        @media only screen and (min-width: 768px) and (max-width: 991px){

            .well {
                width: 100%;
                text-align: center;
                display: flex;
                justify-content: center;
            }

            .board {
                display: flex;
                flex-wrap: wrap;
            }

            .column.p-0 {
                width: 100%;
                min-width: 46%!important;
            }

            .bell{
                width:100%;
            }

            .well
            {
                width: 100%;
                text-align: center;
                display: flex;
                justify-content: center;
            }

            .column_drag {
                min-height: auto!important;
            }
        }

        @media (max-width: 767px){

            .bell {
                width: 100%;
                text-align: center;
            }

            .well {
                width: 100%;
                text-align: center;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .rangedata {
                margin-top: 50px !important;
            }

            #frmFilter button.btn {
                margin-top: 5px;
            }

            form#frmFilter {
                width: 100%;
                display: flex;
                justify-content: center;
                contain: content;
            }
        }
    </style>

<div class="container-fluid mt-4">
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
  <div class="card bg-white p-0">
    <div class="card-header border-bn d-flex flex-row justify-content-between align-items-center">
          <div class="well">
          {!! Form::open(['method' => 'POST', 'class' => 'form-inline', 'id' => 'frmFilter']) !!}
         
          <div class="form-group mr-sm-2 col-md-3 pb-0">    
              <select class="select2" name="assigned_to" id="assigned_to" data-style="select-with-transition" title="Select User">
                 <option value="">Select User</option>
                @if(@isset($users))
                @foreach($users as $user)
                 <option value="{!! $user->id !!}">{!! $user->name !!}</option>
                @endforeach
                @endif
              </select>        
          </div>   
          <!-- <button type="submit" class="btn btn-responsive btn-primary mr-sm-2 mb-2">{{ __('Filter') }}</button> -->
          <!-- <a href="{{route('lead-opportunities.index')}}"  class="btn btn-responsive btn-danger mb-2">{{ __('Reset') }}</a> -->
          {!! Form::close() !!}
      </div>
      <div class="bell">
        <p>Total Annualised Value ₹<span id="total_annualised_value">0</span></p>
      </div>
    </div>
    <div class="card-body">
      <div id="load_card_data">
      </div>
    </div>
</div>
</div>

<!--  -->
  <div class="modal fade" id="addOpportunityModel" role="dialog">
    <div class="modal-dialog">

      
    
      <!-- Modal content-->
          <form method="POST" 
                       action="{{ route('lead-opportunities.store') }}" class="form-horizontal taskform" id="frmLeadOpportunitiesCreate" enctype="multipart/form-data">
            @csrf
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Opportunity</h4>
        </div>
          <div class="modal-body">
          

                <div class="form-row">
                  <div class="form-group col-md-12">


                    <div class="input-group">
                      <input type="number" step="0.01" min="0" name="amount" id="o_amount" class="form-control" aria-label="Text input with dropdown button">
                       <span class="doller"> &#8377;</span>
                       &nbsp;&nbsp;
                      {{-- <div class="input-group-append">
                         {!! Form::select('type', config('constants.OPPORTUNITY_TYPES'),old('type',''), array('class' => 'ese','id'=>'type')) !!}
                      </div> --}}
                    </div>

                   
                  </div>
                 
                  <div class="form-group col-md-12 mt-2 rangedata">
                    <label class="d-flex justify-content-between">
                      <span>Confidence</span>
                      
                    </label>
                   

                     <span id="confidenceValue">0%</span>
                    <input type="range" class="form-control-range" name="confidence" id="confidence" value="50" min="0" max="100">
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
                         <input type="hidden" name="lead_id" id="lead_id" >
                         <input type="hidden" name="opportunity_id"  id="opportunity_id">
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
                         <label for="status">Status<span style="color:red">*</span></label>
                         
                           {!! Form::select('status', $opportunity_status,old('status',''), array('class' => 'form-control','id'=>'status')) !!}

                         @if($errors->has('status'))
                         <p class="help-block">
                             <strong>{{ $errors->first('status') }}</strong>
                         </p>
                         @endif
                     </div>
                   </div>

              </div>  

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">Cancel</button>
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
<!--  -->
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <!-- Load jQuery and moment.js -->



<script>
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $(document).ready(function(){
     getCardData();
     //jQuery('#frmFilter').submit(function(){
     jQuery('#assigned_to').on('change', function() {
        getCardData();
       // return false;
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

  });

    function getCardData(){
      var assigned_to = jQuery('#frmFilter [name=assigned_to]').val();
      $.post("{{ route('lead-opportunities.getCardData') }}", {assigned_to: assigned_to }, function(response){
         $('#load_card_data').html(response.view);
         $('#total_annualised_value').html(response.total_annualised_value);
      }); 

    }

    function getOpportunitydata(id){
       $.post("{{ route('lead-opportunities.getsingleData') }}", {id: id }, function(response){
        if(response.status){
          
          $('#opportunity_id').val(response.data.id);
          $('#lead_id').val(response.data.id);

          $('#frmLeadOpportunitiesCreate #o_assigned_to').val(response.data.assigned_to);
          $('#frmLeadOpportunitiesCreate #lead_contact_id').val(response.data.lead_contact_id);
          $('#frmLeadOpportunitiesCreate #o_assigned_to').change();
          $('#frmLeadOpportunitiesCreate #lead_contact_id').change();
          $('#frmLeadOpportunitiesCreate #o_note').val(response.data.note);
          $('#frmLeadOpportunitiesCreate #o_amount').val(response.data.amount);
          $('#frmLeadOpportunitiesCreate #confidence').val(response.data.confidence);
          $('#frmLeadOpportunitiesCreate #confidenceValue').html(response.data.confidence+'%');
          $('#frmLeadOpportunitiesCreate #estimated_close_date').val(response.data.estimated_close_date);
          $('#frmLeadOpportunitiesCreate #status').val(response.data.status);
          $('#frmLeadOpportunitiesCreate #status').change();
          //$('#frmLeadOpportunitiesCreate #type').val(response.data.type);
          //$('#frmLeadOpportunitiesCreate #type').change();
          //
          $('#addOpportunityModel').modal('show');

           $('#o_assigned_to').select2({
              dropdownParent: $('#addOpportunityModel')
            });
           $('#lead_contact_id').select2({
              dropdownParent: $('#addOpportunityModel')
            });
        }
      }); 

      
    }
    


     
</script>

</x-app-layout>