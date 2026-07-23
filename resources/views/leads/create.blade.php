<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{isset($lead) ? 'UPDATE' : 'ADD'}} NEW LEAD
            <span class="pull-right">
              <div class="btn-group">
                @if(auth()->user()->can(['customer_access']))
                <a href="{{ url('leads') }}" class="btn btn-just-icon btn-theme" title="Leads"><i class="material-icons">arrow_circle_left</i></a>
                @endif
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
          <form method="POST" action="{{ isset($lead) ? route('leads.update', $lead->id) : route('leads.store') }}" id="frmLeadsCreate" enctype="multipart/form-data" class="w-100">
            @method(isset($lead) ? 'PUT' : 'POST')
            @csrf

            <div class="modal-content lead-modal">
              {{-- Header --}}
              <!-- <div class="modal-header border-0 pb-0">
            <h5 class="modal-title font-weight-bold text-uppercase mb-0">ADD NEW LEAD</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div> -->

              {{-- Body --}}
              <div class="modal-body pt-3">
                <div class="form-row">
                  {{-- Lead Type --}}
                  <div class="form-group col-6 mb-2">
                    <select name="status" id="status" class="custom-select" required>
                      <option value="" disabled selected>Lead Type</option>
                      @foreach($status as $opt)
                      <option value="{{ $opt->id }}" {{ old('status', isset($lead) ? $lead->status : 0)==$opt->id ? 'selected' : '' }}>{{ $opt->display_name }}</option>
                      @endforeach
                    </select>
                    @error('status') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>

                  {{-- Firm Name --}}
                  <div class="form-group col-6 mb-2">
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name', isset($lead) ? $lead->company_name : '') }}"
                      class="form-control form-control-lg" placeholder="Firm Name" required>
                    @error('company_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                </div>
                <div class="form-row">
                  {{-- Customer Name --}}
                  <div class="form-group col-6 mb-2">
                    <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name', isset($lead) ? $lead->contacts?->first()->name : '') }}"
                      class="form-control form-control-lg" placeholder="Customer Name" required>
                    @error('contact_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>

                  {{-- Mobile --}}
                  <div class="form-group col-6 mb-2">
                    <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number', isset($lead) ? $lead->contacts?->first()->phone_number : '') }}"
                      class="form-control form-control-lg" placeholder="Mobile Number" maxlength="15">
                    @error('phone_number') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                </div>
                <div class="form-row">
                  {{-- Email --}}
                  <div class="form-group col-6 mb-2">
                    <input type="email" name="email" id="email" value="{{ old('email', isset($lead) ? $lead->contacts?->first()->email : '') }}"
                      class="form-control form-control-lg" placeholder="Email Id">
                    @error('email') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>

                  {{-- Address --}}
                  <div class="form-group col-6 mb-2">
                    <input type="text" name="address" id="address" value="{{ old('address', isset($lead) ? $lead->address?->address1 : '') }}"
                      class="form-control form-control-lg" placeholder="Address">
                    @error('address') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                </div>

                {{-- Pin / City --}}
                <div class="form-row">
                  <div class="form-group col-3 mb-2">
                    <select class="form-control select2 " name="state_id" id="state_id" onchange="getDistrictList()" style="width: 100%;">
                      <option value="">Select {!! trans('panel.global.state') !!}</option>
                      @if($states && count($states) > 0)
                      @foreach($states as $state)
                      <option value="{!! $state->id !!}" {{ old('state_id', isset($lead) && $lead->address ? $lead->address->state_id : '') == $state->id ? 'selected' : '' }}>{!! $state->state_name !!}</option>
                      @endforeach
                      @endif
                    </select>
                    @error('state_alt') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                  <div class="form-group col-3 mb-2">
                    <select class="form-control select2 district" name="district_id" id="district_id" onchange="getCityList()" style="width: 100%;">
                      @if(isset($lead) && $lead->address && $lead->address->district_id)
                      <option value="{!!  $lead->address->district_id !!}" selected>{!! $lead->address->districtname->district_name ?? '' !!}</option>
                      @else
                      <option value="">Select {!! trans('panel.global.district') !!}</option>
                      @endif
                    </select>
                    @error('state') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                  <div class="form-group col-3 mb-2">
                    <select class="form-control select2 city" name="city_id" id="city_id" onchange="getPincodeList()" style="width: 100%;">
                      @if(isset($lead) && $lead->address && $lead->address->city_id)
                      <option value="{!!  $lead->address->city_id !!}" selected>{!! $lead->address->cityname->city_name??'' !!}</option>
                      @else
                      <option value="">Select {!! trans('panel.global.city') !!}</option>
                      @endif
                    </select>
                    @error('city') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                  <div class="form-group col-3 mb-2">
                  <select class="form-control pincode select2" name="pincode_id" id="pincode_id" onchange="getAddressData()" style="width: 100%;">
                    @if(isset($lead) && $lead->address && $lead->address->pincode_id)
                    <option value="{!!  $lead->address->pincode_id !!}" selected>{!! $lead->address->pincodename->pincode !!}</option>
                    @endif
                    <option value="">Select {!! trans('panel.global.pincode') !!}</option>
                  </select>
                  @error('pin_code') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>
                </div>

                {{-- Other / Lead Source --}}
                <div class="form-row">
                  <div class="form-group col-6 mb-2">
                    <input type="text" name="other" id="other" value="{{ old('other', isset($lead) && isset($lead->others) ? (count(json_decode($lead->others, true)) > 0 ? array_values(json_decode($lead->others, true))[0] : '') : '') }}"
                      class="form-control form-control-lg" placeholder="Other">
                    @error('other') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                  <div class="form-group col-6 mb-2">
                    <select name="lead_source" id="lead_source" class="custom-select" required>
                      <option value="" disabled selected>Lead Source</option>
                      @foreach($lead_sources as $src)
                      <option value="{{ $src }}" {{ old('lead_source', isset($lead) ? $lead->lead_source : '')==$src ? 'selected' : '' }}>{{ $src }}</option>
                      @endforeach
                    </select>
                    @error('lead_source') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                </div>

                {{-- Assigned To --}}
                <div class="form-row">
                  <div class="form-group col-6 mb-2">
                    <input type="text" name="company_url" id="company_url" value="{{ old('company_url', isset($lead) ? $lead->company_url : '') }}" class="form-control form-control-lg" placeholder="Website">
                    @error('company_url') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                  <div class="form-group col-6 mb-2">
                    <select name="assign_to" id="assign_to" class="custom-select" required>
                      <option value="" disabled selected>Assigned To</option>
                      @foreach($users as $user)
                      <option value="{{ $user->id }}" {{ old('assign_to', isset($lead) ? $lead->assign_to : '')==$user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                      @endforeach
                    </select>
                    @error('assign_to') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                </div>

                {{-- Note --}}
                <div class="form-group mb-2">
                  <textarea name="note" id="note note_text" rows="3" class="form-control rounded border ckeditor-init" placeholder="Note">{{ old('note', isset($lead) ? $lead->notes->first()?->note : '') }}</textarea>
                  @error('note') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>
              </div>

              {{-- Footer --}}
              <div class="modal-footer border-0">
                <button type="submit" class="btn btn-info btn-block lead-submit">{{ isset($lead) ? 'Update' : 'Create' }}</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script type="text/javascript" src="{{ url('/').'/'.asset('vendor/ckeditor/js/ckeditor.js') }}"></script>
  <script>
    document.querySelectorAll('.ckeditor-init').forEach(function(item) {
      var editor = CKEDITOR.replace(item, {
        customConfig: 'config.js',
        toolbar: 'Basic',
        height: '15em'
      });

      editor.on('change', function() {
        this.updateElement();
      });
    });

    document.addEventListener('DOMContentLoaded', function() {
      var pincodeSelect = document.getElementById('pincode_id');
      if (pincodeSelect && pincodeSelect.value) {
        getAddressData();
      }
    });
  </script>

</x-app-layout>