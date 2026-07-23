<x-app-layout>
  <style>
    .swal2-container.swal2-center.swal2-fade.swal2-shown {
      z-index: 9999;
    }

    #invoice_logo {
      cursor: pointer;
    }

    #invoice_esign {
      cursor: pointer;
    }
  </style>
  <section class="invoice_main">
    @if (count($errors) > 0)
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <div class="container-fluid">
      <div class="card p-4">
        <form action="{{ route('invoice_settings.store') }}" method="post" enctype="multipart/form-data">
          @csrf
          <h5><i class="fa fa-file-text-o"></i> Invoice Settings</h5>

          <div class="form-row">
            <!-- Invoice Logo -->
            <div class="form-group col-md-3">
              <label for="invoice_logo">Invoice Logo</label>
              <input type="file"
                class="form-control-file"
                id="invoice_logo"
                name="invoice_logo"
                accept="image/*"
                onchange="previewImage(event, 'logo_preview')">

              <div class="mt-2">
                @if($invoice_setting && $invoice_setting->getFirstMediaUrl('invoice_logo'))
                <img id="logo_preview" src="{{ $invoice_setting->getFirstMediaUrl('invoice_logo') }}"
                  alt="Logo Preview" style="max-width: 150px;" class="img-thumbnail">
                @else
                <img id="logo_preview" src="#" alt="Logo Preview" style="max-width: 150px; display: none;" class="img-thumbnail">
                @endif
              </div>
            </div>

            <div class="form-group col-md-4"></div>

            <!-- Invoice E-Sign -->
            <div class="form-group col-md-3">
              <label for="invoice_esign">Invoice E-Sign</label>
              <input type="file"
                class="form-control-file"
                id="invoice_esign"
                name="invoice_esign"
                accept="image/*"
                onchange="previewImage(event, 'esign_preview')">

              <div class="mt-2">
                @if($invoice_setting && $invoice_setting->getFirstMediaUrl('invoice_esign'))
                <img id="esign_preview" src="{{ $invoice_setting->getFirstMediaUrl('invoice_esign') }}"
                  alt="E-Sign Preview" style="max-width: 150px;" class="img-thumbnail">
                @else
                <img id="esign_preview" src="#" alt="E-Sign Preview" style="max-width: 150px; display: none;" class="img-thumbnail">
                @endif
              </div>
            </div>

            <div class="form-group col-md-2"></div>

          </div>

          <hr>

          <h5><i class="fa fa-map-marker"></i> Company Address</h5>
          <div id="address_wrapper">
            <div class="form-row">
              <div class="form-group col-md-4">
                <label>Company Name</label>
                <input type="text" name="company_name" class="form-control"
                  value="{{ $invoice_setting->company_name ?? '' }}" placeholder="Enter Company Name">
              </div>

              <div class="form-group col-md-8">
                <label>Address</label>
                <textarea name="company_address" class="form-control" rows="2"
                  placeholder="Enter Address">{{ $invoice_setting->address->address1 ?? '' }}</textarea>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-3">
                <label>State</label>
                <select class="form-control select2 " name="state_id" id="state_id" onchange="getDistrictList()" style="width: 100%;">
                  <option value="">Select {!! trans('panel.global.state') !!}</option>
                  @if($states && count($states) > 0)
                  @foreach($states as $state)
                  <option value="{!! $state->id !!}" {{ old('state_id', isset($invoice_setting) && $invoice_setting->address ? $invoice_setting->address->state_id : '') == $state->id ? 'selected' : '' }}>{!! $state->state_name !!}</option>
                  @endforeach
                  @endif
                </select>
              </div>
              <div class="form-group col-md-3">
                <label>District</label>
                <select class="form-control select2 district" name="district_id" id="district_id" onchange="getCityList()" style="width: 100%;">
                  @if(isset($invoice_setting) && $invoice_setting->address && $invoice_setting->address->district_id)
                  <option value="{!!  $invoice_setting->address->district_id !!}" selected>{!! $invoice_setting->address->districtname->district_name ?? '' !!}</option>
                  @else
                  <option value="">Select {!! trans('panel.global.district') !!}</option>
                  @endif
                </select>
              </div>
              <div class="form-group col-md-3">
                <label>City</label>
                <select class="form-control select2 city" name="city_id" id="city_id" onchange="getPincodeList()" style="width: 100%;">
                  @if(isset($invoice_setting) && $invoice_setting->address && $invoice_setting->address->city_id)
                  <option value="{!!  $invoice_setting->address->city_id !!}" selected>{!! $invoice_setting->address->cityname->city_name??'' !!}</option>
                  @else
                  <option value="">Select {!! trans('panel.global.city') !!}</option>
                  @endif
                </select>
              </div>
              <div class="form-group col-md-3">
                <label>Pincode</label>
                <select class="form-control pincode select2" name="pincode_id" id="pincode_id" onchange="getAddressData()" style="width: 100%;">
                  @if(isset($invoice_setting) && $invoice_setting->address && $invoice_setting->address->pincode_id)
                  <option value="{!!  $invoice_setting->address->pincode_id !!}" selected>{!! $invoice_setting->address->pincodename->pincode !!}</option>
                  @endif
                  <option value="">Select {!! trans('panel.global.pincode') !!}</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-3">
                <label>GST Number</label>
                <input type="text" name="gst_number" class="form-control"
                  value="{{ $invoice_setting->gst_number ?? '' }}" placeholder="Enter GST Number">
              </div>
              <div class="form-group col-md-3">
                <label>PAN Number</label>
                <input type="text" name="pan_number" class="form-control"
                  value="{{ $invoice_setting->pan_number ?? '' }}" placeholder="Enter PAN Number">
              </div>
            </div>
          </div>
          <hr>

          <h5><i class="fa fa-tags"></i> Invoice Labels</h5>
          <div id="labels_wrapper">
            @if($invoice_setting && $invoice_setting->labels->count())
            @foreach($invoice_setting->labels as $index => $label)
            <div class="form-row label-row mb-3">
              <div class="form-group col-md-3">
                <label>Label Name</label>
                <input type="hidden" name="labels[{{ $index }}][id]" value="{{ $label->id }}">
                <input type="text" class="form-control"
                  name="labels[{{ $index }}][name]"
                  value="{{ $label->name }}" placeholder="Enter Label Name">
              </div>

              <div class="form-group col-md-3">
                <label>Page</label>
                <select name="labels[{{ $index }}][page]" class="form-control select2">
                  @for($p=2; $p<=5; $p++)
                    <option value="{{ $p }}" {{ $label->page == $p ? 'selected' : '' }}>Page {{ $p }}</option>
                    @endfor
                </select>
              </div>

              <div class="form-group col-md-3">
                <label>Page Heading</label>
                <input type="text" name="labels[{{$index}}][page_heading]" class="form-control" placeholder="Enter Page Heading" value="{{ $label->page_heading }}">
              </div>

              <div class="form-group col-md-2">
                <label>Label Icon</label>
                <input type="file" class="form-control-file"
                  name="labels[{{ $index }}][icon]"
                  accept=".png,.jpg,.jpeg"
                  onchange="previewLabelIcon(event, {{ $index }})">
                <div class="mt-2">
                  @if($label->getFirstMediaUrl('label_icon'))
                  <img id="label_icon_preview_{{ $index }}"
                    src="{{ $label->getFirstMediaUrl('label_icon') }}"
                    style="max-width: 60px;" class="img-thumbnail">
                  @else
                  <img id="label_icon_preview_{{ $index }}"
                    src="#" alt="Icon Preview"
                    style="max-width: 60px; display:none;" class="img-thumbnail">
                  @endif
                </div>
              </div>

              <div class="form-group col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-block" onclick="removeLabelRow(this, {{ $label->id }})">
                  <i class="fa fa-trash"></i>
                </button>
              </div>
            </div>
            @endforeach
            @else
            {{-- Empty row when first time --}}
            <div class="form-row label-row mb-3">
              <div class="form-group col-md-3">
                <label>Label Name</label>
                <input type="text" class="form-control" name="labels[0][name]" placeholder="Enter Label Name">
              </div>

              <div class="form-group col-md-3">
                <label>Page</label>
                <select name="labels[0][page]" class="form-control select2">
                  <option value="2">Page 2</option>
                  <option value="3">Page 3</option>
                  <option value="4">Page 4</option>
                  <option value="5">Page 5</option>
                </select>
              </div>

              <div class="form-group col-md-3">
                <label>Page Heading</label>
                <input type="text" name="labels[0][page_heading]" class="form-control" placeholder="Enter Page Heading">
              </div>

              <div class="form-group col-md-2">
                <label>Label Icon</label>
                <input type="file" class="form-control-file" name="labels[0][icon]" accept=".png,.jpg,.jpeg" onchange="previewLabelIcon(event, 0)">
                <div class="mt-2">
                  <img id="label_icon_preview_0" src="#" alt="Icon Preview" style="max-width: 60px; display:none;" class="img-thumbnail">
                </div>
              </div>

              <div class="form-group col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-block" onclick="removeLabelRow(this)">
                  <i class="fa fa-trash"></i>
                </button>
              </div>
            </div>
            @endif
          </div>

          <div class="form-group col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-success btn-block" onclick="addLabelRow()">
              <i class="fa fa-plus"></i>
            </button>
          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Settings</button>
          </div>
        </form>
      </div>
    </div>
  </section>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script>
    let labelIndex = {{ $invoice_setting && $invoice_setting->labels-> count() ? $invoice_setting->labels-> count() : 1 }};

    function previewImage(event, previewId) {
      const input = event.target;
      const preview = document.getElementById(previewId);

      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    function previewLabelIcon(event, index) {
      const input = event.target;
      const preview = document.getElementById(`label_icon_preview_${index}`);
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    function addLabelRow() {
      const wrapper = document.getElementById('labels_wrapper');
      const newRow = document.createElement('div');
      newRow.classList.add('form-row', 'label-row', 'mb-3');

      newRow.innerHTML = `
          <div class="form-group col-md-3">
              <label>Label Name</label>
              <input type="text" class="form-control" name="labels[${labelIndex}][name]" placeholder="Enter Label Name">
          </div>

          <div class="form-group col-md-3">
              <label>Page</label>
              <select name="labels[${labelIndex}][page]" class="form-control select2">
                  <option value="2">Page 2</option>
                  <option value="3">Page 3</option>
                  <option value="4">Page 4</option>
                  <option value="5">Page 5</option>
              </select>
          </div>

          <div class="form-group col-md-3">
              <label>Page Heading</label>
              <input type="text" name="labels[${labelIndex}][page_heading]" class="form-control" placeholder="Enter Page Heading">
          </div>

          <div class="form-group col-md-2">
              <label>Label Icon</label>
              <input type="file" class="form-control-file" name="labels[${labelIndex}][icon]" accept=".png,.jpg,.jpeg" onchange="previewLabelIcon(event, ${labelIndex})">
              <div class="mt-2">
                  <img id="label_icon_preview_${labelIndex}" src="#" alt="Icon Preview" style="max-width: 60px; display:none;" class="img-thumbnail">
              </div>
          </div>

          <div class="form-group col-md-1 d-flex align-items-end">
              <button type="button" class="btn btn-danger btn-block" onclick="removeLabelRow(this)">
                  <i class="fa fa-trash"></i>
              </button>
          </div>
      `;
      wrapper.appendChild(newRow);
      $('.select2').select2();
      labelIndex++;
    }

    function removeLabelRow(button, labelId) {
      if (labelId !== undefined && labelId !== null) {
        Swal.fire({
          title: "Are you sure?",
          text: "This label will be permanently deleted.",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.value) {
            $.ajax({
              url: "{{ route('invoice_labels.destroy', ':id') }}".replace(':id', labelId),
              type: "DELETE",
              data: {
                _token: "{{ csrf_token() }}"
              },
              success: function(response) {
                Swal.fire("Deleted!", response.message, "success");
                button.closest('.label-row').remove();
              },
              error: function(xhr) {
                Swal.fire("Error!", "Something went wrong while deleting.", "error");
              }
            });
          }
        });
      } else {
        button.closest('.label-row').remove();
      }
    }
  </script>
</x-app-layout>