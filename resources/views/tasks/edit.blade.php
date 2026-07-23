   <x-app-layout>
   <style>
        .radio-container {
          display: flex;
          margin-bottom: 1rem;
        }
        .radio-container label {
            margin-right: 20px;
        }
        .select-section {
          display: none;
          margin-top:-8px !important;
        }
        #files {
           display: none;
        }
        #cke_notifications_area_descriptions{
            z-index: -1;
        }
        textarea.form-control {
            resize: vertical; /* vertical only */
            /* or use: resize: both; */
            min-height: 50px;
        }

        button.deleteImgBtn {
            border: 2px solid red;
            border-radius: 50px;
            width: 18px;
            height: 18px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            background-color: red !important;
            color: #fff !important;
        }

        .form-check{
          top: -4px;
          right: 19px;
          border: 1px solid #eee;
          outline: 0px;
          border-radius: 50px !important;
        }

        .allone {
          height: 100px !important;
          object-fit: cover;
        }
        .form-control:disabled, .form-control[readonly] {
            border:none !important;
            border-radius: 0 !important;

        }

   </style>

   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header card-header-warning card-header-icon">
               <div class="row">
                  <div class="col-6">
                     <div class="card-icon">
                        <i class="material-icons">category</i>
                     </div>
                     <h4 class="card-title"> {!! trans('panel.task.create_title') !!}</h4>
                  </div>
                  <div class="col-6 text-right">
                     <a href="{{ url('tasks') }}" class="btn btn-sm btn-info">{!! trans('panel.task.title') !!}</a>
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
               {!! Form::model($task,[
               'route' => $task->exists ? ['tasks.update', $task->id] : 'tasks.store',
               'method' => $task->exists ? 'PUT' : 'POST',
               'id' => 'storeTaskData',
               'files'=>true
               ]) !!}
               <input type="hidden" name="action" value="create">
               <div class="row">

                  <!-- task department start -->

                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">
                           {!! trans('panel.task.task_department') !!}<span class="text-danger"> *</span>
                        </label>
                        <div class="form-group bmd-form-group">
                           <select class="form-control select2" name="task_department_id" id="task_department_id" required style="width: 100%;" >
                              <option value="">Select {!! trans('panel.task.task_department') !!}</option>
                              @foreach($departments as $department)
                                 <option value="{{ $department->id }}" {{ old('task_department_id', $task['task_department_id'] ?? '') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                 </option>
                              @endforeach

                              <!-- Special options -->
                              <option value="__add__">➕ Add Department</option>
                              <<!-- option value="__edit__">✏️ Edit Selected</option>
                              <option value="__delete__">🗑️ Delete Selected</option> -->
                           </select>
                        </div>
                        @if ($errors->has('task_department_id'))
                           <div class="error">
                              <p class="text-danger">{{ $errors->first('task_department_id') }}</p>
                           </div>
                        @endif
                     </div>
                  </div>

                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.task.task_title') !!} <span class="text-danger"> *</span></label>
                           <div class="form-group has-default bmd-form-group" >
                              <input type="text" name="title" id="title" class="form-control" value="{!! old( 'title', $task['title']) !!}" maxlength="200" required >
                              @if ($errors->has('title'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('title') }}</p>
                              </div>
                              @endif
                        </div>
                     </div>
                  </div>

                  <!-- radio button section start -->
                     <div class="col-md-4">
                        <div class="input_section">
                           <label class="col-form-label">
                              <div class="radio-container1">
                                  <label><input type="radio" name="task_type" value="adhoc" {{ $task->task_type=='adhoc'?'checked':''}} > Adhoc</label>
                                  <label><input type="radio" name="task_type" value="customer" {{ $task->task_type=='customer'?'checked':''}} > Customer</label>
                                  <label><input type="radio" name="task_type" value="project" {{ $task->task_type=='project'?'checked':''}} > Project</label>
                                  <label><input type="radio" name="task_type" value="lead" {{ $task->task_type=='lead'?'checked':''}} > Lead</label>
                              </div>
                           </label>
                           <!-- customers -->
                           <div class="form-group bmd-form-group select-section" id="customer-section">
                              <select class="form-control select2" name="customer_id" id="customer_id" style="width: 100%;" required >
                                 <option value="">Select Customer</option>
                                 @if(@isset($customers ))
                                 @foreach($customers as $customer)
                                 <option value="{!! $customer['id'] !!}" {{ old( 'customer_id' , (!empty($task['customer_id']))?($task['customer_id']):('') ) == $customer['id'] ? 'selected' : '' }}>{!! $customer['id'].' '.$customer['name'] !!}</option>
                                 @endforeach
                                 @endif
                              </select>
                           </div>

                           <!-- projects -->
                           <div class="form-group bmd-form-group select-section" id="project-section">
                              <select class="form-control select2" name="task_project_id" id="task_project_id" required style="width: 100%;" >
                                 <option value="">Select {!! trans('panel.task.project_name') !!}</option>
                                 @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('task_project_id', $task['task_project_id'] ?? '') == $project->id ? 'selected' : '' }}>
                                       {{ $project->name }}
                                    </option>
                                 @endforeach

                                 <!-- Special options -->
                                 <option value="_add_">➕ Add Project</option>
                                 <<!-- option value="__edit__">✏️ Edit Selected</option>
                                 <option value="__delete__">🗑️ Delete Selected</option> -->
                              </select>
                           </div>
                           @if ($errors->has('task_project_id'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('task_project_id') }}</p>
                              </div>
                           @endif

                           <!-- Lead -->
                           <div class="form-group bmd-form-group select-section" id="lead-section">
                              <select class="form-control select2" name="lead_id" id="lead_id" style="width: 100%;" required > 
                                 <option value="">Select Lead</option>
                                 @if(@isset($leads ))
                                    @foreach($leads as $lead)
                                       <option value="{!! $lead['id'] !!}"   {{ old('lead_id', $task['lead_id'] ?? '') == $lead->id ? 'selected' : '' }} >{!! $lead['company_name'] !!}</option>
                                    @endforeach
                                 @endif
                              </select>
                           </div>

                        </div>
                     </div>
                     
                  <!-- radio button section end -->
                  <!-- assigned to -->
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.task.assigned_to') !!}<span class="text-danger"> *</span></label>
                           <div class="form-group bmd-form-group">
                               <select class="form-control select2" name="assigned_to[]" id="assigned_to" style="width: 100%;" multiple required {{ ($task['task_status']=='Completed') && !(auth()->user()->hasRole('superadmin')) ?'disabled':'' }}>
                                   @if(isset($users))
                                       @foreach($users as $user)
                                           <option value="{{ $user['id'] }}"
                                              @if(collect(old('assigned_to', $assignedUserIds ?? []))->contains($user['id'])) selected @endif>
                                              {{ $user['name'] }}
                                          </option>

                                       @endforeach
                                   @endif
                               </select>
                           </div>

                           @if ($errors->has('users'))
                           <div class="error">
                              <p class="text-danger">{{ $errors->first('users') }}</p>
                           </div>
                           @endif
                     </div>
                  </div> 

                  <!-- priority start-->
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">
                           {!! trans('panel.task.priority') !!}<span class="text-danger"> *</span>
                        </label>
                        <div class="form-group bmd-form-group">
                           <select class="form-control select2" name="task_priority_id" id="task_priority_id" required style="width: 100%;" >
                              <option value="">Select {!! trans('panel.task.priority') !!}</option>
                              @foreach($priorities as $priority)
                                 <option value="{{ $priority->id }}" {{ old('task_priority_id', $task['task_priority_id'] ?? '') == $priority->id ? 'selected' : '' }}>
                                    {{ $priority->name }}
                                 </option>
                              @endforeach

                              <!-- Special options -->
                              <option value="__add__">➕ Add Priority</option>
                              <<!-- option value="__edit__">✏️ Edit Selected</option>
                              <option value="__delete__">🗑️ Delete Selected</option> -->
                           </select>
                        </div>
                        @if ($errors->has('task_priority_id'))
                           <div class="error">
                              <p class="text-danger">{{ $errors->first('task_priority_id') }}</p>
                           </div>
                        @endif
                     </div>
                  </div>

                  

                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">Due Date & Time<span class="text-danger"> *</span></label>
                           <div class="form-group bmd-form-group">
                              <input type="text" name="due_datetime" id="datetime" class="form-control datetimepicker" value="{!! old( 'due_datetime', $task['due_datetime']) !!}" >
                           </div>
                           @if ($errors->has('datetime'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('datetime') }}</p>
                           </div>
                           @endif
                     </div>
                  </div>
               </div>
               <hr class="my-3">
               <h4 class="section-heading mb-3  h4 mt-0 text-center">{!! trans('panel.task.descriptions') !!}</h4>
               <div class="row">
                  <div class="col-md-12">
                     <div class="form-group has-default bmd1-form-group">
                        <textarea class="ckeditor1 form-control" rows="8" cols="30" name="descriptions" id="descriptions"  required>{!! old( 'descriptions', $task['descriptions']) !!}</textarea>
                        @if ($errors->has('descriptions'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('descriptions') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>   
                   
                  <!-- show images start-->
                    

                <div class="row mb-3" id="media-preview-area">
                    {{-- Existing Media --}}
                    @if(count($task->getMedia('task_admin_files')))
                        @foreach($task->getMedia('task_admin_files') as $media)
                            @php
                                $extension = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));
                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp']);
                            @endphp

                            <div class="col-md-2 text-center position-relative mb-3 existing-media">
                                @if($isImage)
                                    <img src="{{ $media->getFullUrl() }}" class="img-thumbnail allone" style="width: 100%;">
                                @else
                                    <div class="border p-2 bg-light rounded allone">
                                        <i class="bi bi-file-earmark-text fs-1"></i><br>
                                        <a href="{{ $media->getFullUrl() }}" target="_blank">{{ $media->file_name }}</a>
                                    </div>
                                 @endif
                                 
                                <div class="form-check position-absolute top-0 end-0 deleteMedia" data-media-id="{{ $media->id }}">
                                    <input type="checkbox" name="delete_media[]" value="{{ $media->id }}" class="form-check-input d-none">
                                    <button type="button" class="form-check-label text-danger bg-white px-1 deleteImgBtn" title="Delete">&minus;</button>
                                </div>
                                      

                            </div>
                        @endforeach
                    @endif    
                </div>

                <!-- assigned user attachments -->
                <div class="row mb-3" id="media-preview-area">
                    {{-- Existing Media --}}
                    @if(count($task->getMedia('task_assigned_user_files')))
                        @foreach($task->getMedia('task_assigned_user_files') as $media)
                            @php
                                $extension = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));
                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp']);
                            @endphp

                            <div class="col-md-2 text-center position-relative mb-3 existing-media">
                                @if($isImage)
                                    <img src="{{ $media->getFullUrl() }}" class="img-thumbnail allone" style="width: 100%;">
                                @else
                                    <div class="border p-2 bg-light rounded allone">
                                        <i class="bi bi-file-earmark-text fs-1"></i><br>
                                        <a href="{{ $media->getFullUrl() }}" target="_blank">{{ $media->file_name }}</a>
                                    </div>
                                 @endif
                                
                                   <div class="form-check position-absolute top-0 end-0 deleteMedia" data-media-id="{{ $media->id }}">
                                       <input type="checkbox" name="delete_media[]" value="{{ $media->id }}" class="form-check-input d-none">
                                       <button type="button" class="form-check-label text-danger bg-white px-1 deleteImgBtn" title="Delete">&minus;</button>
                                   </div>

                            </div>
                        @endforeach
                    @endif    
                </div>


                  <!-- show images end -->
                  @if( $task['task_status']!='Completed' ||  auth()->user()->hasRole('superadmin'))
                     <div class="row">
                        <div class="col-md-12">
                            <div class="attachment-wrapper text-right">
                                <!-- Attachment Button -->
                                <label for="files" class="btn btn-theme" id="attachmentLabel">
                                    📎 Attachment (CSV, Excel, JPEG, PNG, PDF)
                                </label>
                                <input type="file" name="files[]" id="files" multiple accept=".csv,.xls,.xlsx,.jpeg,.jpg,.png,.pdf" required>

                                <!-- Submit Button -->
                                {{ Form::submit('Update', ['class' => 'btn btn-theme']) }}
                            </div>
                        </div>
                     </div>
                  @endif   
            </div>
        
            {{ Form::close() }} 
         </div>
      </div>
   </div>
   </div>

   <!-- All modals -->
      <!-- Edit Department Modal -->
      <div class="modal fade" id="editDepartmentModal" tabindex="-1">
         <div class="modal-dialog">
            <form id="editDepartmentForm">
               @csrf
               <input type="hidden" id="editDepartmentId" name="id">
               <div class="modal-content">
                  <div class="modal-header"><h5 class="modal-title">Edit Department</h5></div>
                  <div class="modal-body">
                     <input name="name" id="editDepartmentName" class="form-control" required>
                  </div>
                  <div class="modal-footer">
                     <button type="submit" class="btn btn-primary">Update</button>
                  </div>
               </div>
            </form>
         </div>
      </div>

   <!-- task department end -->   


   <!-- Add Priority Modal -->
      <div class="modal fade" id="addPriorityModal" tabindex="-1">
         <div class="modal-dialog">
            <form id="addPriorityForm">
               @csrf
               <div class="modal-content">
                  <div class="modal-header"><h5 class="modal-title">Add Priority</h5></div>
                  <div class="modal-body">
                     <input name="name" id="priority_name" class="form-control" placeholder="Priority Name" required>
                  </div>
                  <div class="modal-footer">
                     <button type="button" id="addPriorityBtn" class="btn btn-primary mx-1">Save</button>
                     <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                  </div>
               </div>
            </form>
         </div>
      </div>

      <!-- Edit Priority Modal -->
      <div class="modal fade" id="editPriorityModal" tabindex="-1">
         <div class="modal-dialog">
            <form id="editPriorityForm">
               @csrf
               <input type="hidden" id="editPriorityId" name="id">
               <div class="modal-content">
                  <div class="modal-header"><h5 class="modal-title">Edit Priority</h5></div>
                  <div class="modal-body">
                     <input name="name" id="editPriorityName" class="form-control" required>
                  </div>
                  <div class="modal-footer">
                     <button type="submit" class="btn btn-primary">Update</button>
                  </div>
               </div>
            </form>
         </div>
      </div>

      <!-- Add Department Modal -->
      <div class="modal fade" id="addDepartmentModal" tabindex="-1">
         <div class="modal-dialog">
            <form id="addDepartmentForm"> 
               @csrf
               <div class="modal-content">
                  <div class="modal-header"><h5 class="modal-title">Add Department</h5></div>
                  <div class="modal-body">
                     <input name="name" id="department_name" class="form-control" placeholder="Department Name" required>
                  </div>
                  <div class="modal-footer">
                     <button type="button" id="addDepartmentBtn" class="btn btn-primary mx-1">Save</button>
                     <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                  </div>
               </div>
            </form>
         </div>
      </div>

      <!-- Add Project Modal  -->
         <div class="modal fade" id="addProjectModal" tabindex="-1">
             <div class="modal-dialog">
                 <form id="addProjectForm">
                     <div class="modal-content">
                         <div class="modal-header"><h5 class="modal-title">Add Project</h5></div>
                         <div class="modal-body">
                             <input name="name" id="project_name" class="form-control" placeholder="Project Name" required>
                         </div>
                         <div class="modal-footer">
                             <button type="button" id="addProjectBtn" class="btn btn-primary mx-1">Save</button>
                             <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                         </div>
                     </div>
                 </form>
             </div>
         </div>
               

   <!-- End All Modals -->

   <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
   <script src="{{ url('/').'/'.asset('assets/js/jquery.tasks.js') }}"></script>

   <script>
   $.ajaxSetup({
      headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
   });
   </script>

   <script type="text/javascript">

      // task new js
      let csrfToken = $('meta[name="csrf-token"]').attr('content');
      $(document).ready(function () {
         $('#task_department_id').select2({
            width: '100%',
            templateResult: function (data) {
               if (data.id === '__add__') return $('<span style="color:green;">' + data.text + '</span>');
               if (data.id === '__edit__') return $('<span style="color:orange;">' + data.text + '</span>');
               if (data.id === '__delete__') return $('<span style="color:red;">' + data.text + '</span>');
               return data.text;
            }
         });

         $('#task_department_id').on('select2:select', function (e) {
            const selectedVal = e.params.data.id;

            if (selectedVal === '__add__') {
               $(this).val(null).trigger('change');
               $('#addDepartmentModal').modal('show');
               return;
            }

            if (selectedVal === '__edit__') {
               const actualId = getActualDepartmentId();
               if (actualId) {
                  openEditModal(actualId);
               } else {
                  alert('Select a department to edit.');
               }
               $(this).val(null).trigger('change');
               return;
            }

            if (selectedVal === '__delete__') {
               const actualId = getActualDepartmentId();
               if (actualId) {
                  deleteDepartment(actualId);
               } else {
                  alert('Select a department to delete.');
               }
               $(this).val(null).trigger('change');
               return;
            }
         });

         function getActualDepartmentId() {
            const selected = $('#task_department_id').select2('data').find(
               d => !['__add__', '__edit__', '__delete__'].includes(d.id)
            );
            return selected ? selected.id : null;
         }

         function openEditModal(id) {
            const name = $('#task_department_id option[value="' + id + '"]').text();
            $('#editDepartmentId').val(id);
            $('#editDepartmentName').val(name);
            $('#editDepartmentModal').modal('show');
         }

         function deleteDepartment(id) {
            if (!confirm("Are you sure to delete this department?")) return;
            $.ajax({
               url: `/departments/${id}`,
               type: 'DELETE',
               data: { _token: '{{ csrf_token() }}' },
               success: function () {
                  $('#task_department_id option[value="' + id + '"]').remove();
                  $('#task_department_id').val(null).trigger('change');
               }
            });
         }

         // Add department AJAX
         // Make sure CSRF token is available in your HTML <head>
         

         $('#addDepartmentBtn').on('click', function (e) {
             e.preventDefault();

             $.ajax({
                 url: '{{ route("task-departments.store") }}', // your route
                 method: 'POST',
                 data: {
                     name: $('#department_name').val(),
                 },
                 headers: {
                     'X-CSRF-TOKEN': csrfToken
                 },
                 success: function (response) {
                     if(response.status){
                        $('#addDepartmentModal').modal('hide');

                        // Clear input field
                        $('#department_name').val('');

                        // Add new option just before the last option ("Add Department")
                        $('#task_department_id')
                            .find('option:last')
                            .before(`<option value="${response.data.id}" selected>${response.data.name}</option>`);
                        // Set the newly added option as selected
                        $('#task_department_id').val(response.data.id).trigger('change');
                     }   
                 },
                 error: function (xhr) {
                     console.error(xhr.responseText);
                     alert('Failed to add department. Please try again.');
                 }
             });
         });



         // Edit department AJAX
         $('#editDepartmentForm').on('submit', function (e) {
            e.preventDefault();
            const id = $('#editDepartmentId').val();
            const name = $('#editDepartmentName').val();
            $.ajax({
               url: `/departments/${id}`,
               type: 'PUT',
               data: {
                  _token: '{{ csrf_token() }}',
                  name: name
               },
               success: function (data) {
                  $(`#task_department_id option[value="${id}"]`).text(data.name).prop('selected', true);
                  $('#editDepartmentModal').modal('hide');
               }
            });
         });
      });

      // radio button js
      
      $(document).ready(function () {
         function toggleSections(selected) {
              $('.select-section').hide(); // Hide all sections
              if (selected === 'customer') {
                  $('#customer-section').show();
              } else if (selected === 'project') {
                  $('#project-section').show();
              } else if (selected === 'lead') {
                  $('#lead-section').show();
              }
          }

          // On change of radio button
          $('input[name="task_type"]').on('change', function () {
              toggleSections($(this).val());
          });

          // 👇 Trigger toggle on page load based on pre-filled value
          const selectedTaskType = $('input[name="task_type"]:checked').val();
          toggleSections(selectedTaskType);
      });



      $('#task_project_id').on('select2:select', function (e) {
         const selectedVal = e.params.data.id;
         if (selectedVal === '_add_') {
            $(this).val(null).trigger('change');
            $('#addProjectModal').modal('show');
            return;
         }

         // if (selectedVal === '__edit__') {
         //    const actualId = getActualDepartmentId();
         //    if (actualId) {
         //       openEditModal(actualId);
         //    } else {
         //       alert('Select a department to edit.');
         //    }
         //    $(this).val(null).trigger('change');
         //    return;
         // }

         // if (selectedVal === '__delete__') {
         //    const actualId = getActualDepartmentId();
         //    if (actualId) {
         //       deleteDepartment(actualId);
         //    } else {
         //       alert('Select a department to delete.');
         //    }
         //    $(this).val(null).trigger('change');
         //    return;
         // }
      });

      $('#addProjectBtn').on('click', function (e) {
         e.preventDefault();

         $.ajax({
              url: '{{ route("task-projects.store") }}', // your route
              method: 'POST',
              data: {
                  name: $('#project_name').val(),
              },
              headers: {
                  'X-CSRF-TOKEN': csrfToken
              },
              success: function (response) {
                  if(response.status){
                     $('#addProjectModal').modal('hide');

                     // Clear input field
                     $('#project_name').val('');

                     // Add new option just before the last option ("Add Department")
                     $('#task_project_id')
                         .find('option:last')
                         .before(`<option value="${response.data.id}" selected>${response.data.name}</option>`);
                     // Set the newly added option as selected
                     $('#task_project_id').val(response.data.id).trigger('change');
                  }   
              },
              error: function (xhr) {
                  console.error(xhr.responseText);
                  alert('Failed to add project. Please try again.');
              }
         });
      });

      // for priority dropdown

      $(document).ready(function () {
      $('#task_priority_id').select2({
         width: '100%',
         templateResult: function (data) {
            if (data.id === '__add__') return $('<span style="color:green;">' + data.text + '</span>');
            if (data.id === '__edit__') return $('<span style="color:orange;">' + data.text + '</span>');
            if (data.id === '__delete__') return $('<span style="color:red;">' + data.text + '</span>');
            return data.text;
         }
      });

      $('#task_priority_id').on('select2:select', function (e) {
         const selectedVal = e.params.data.id;
         if (selectedVal === '__add__') {
            $(this).val(null).trigger('change');
            $('#addPriorityModal').modal('show');
            return;
         }

         if (selectedVal === '__edit__') {
            const actualId = getActualPriorityId();
            if (actualId) {
               openPriorityEditModal(actualId);
            } else {
               alert('Select a priority to edit.');
            }
            $(this).val(null).trigger('change');
            return;
         }

         if (selectedVal === '__delete__') {
            const actualId = getActualPriorityId();
            if (actualId) {
               deletePriority(actualId);
            } else {
               alert('Select a priority to delete.');
            }
            $(this).val(null).trigger('change');
            return;
         }
      });

      function getActualPriorityId() {
         const selected = $('#task_priority_id').select2('data').find(
            d => !['__add__', '__edit__', '__delete__'].includes(d.id)
         );
         return selected ? selected.id : null;
      }

      function openPriorityEditModal(id) {
         const name = $('#task_priority_id option[value="' + id + '"]').text();
         $('#editPriorityId').val(id);
         $('#editPriorityName').val(name);
         $('#editPriorityModal').modal('show');
      }

      function deletePriority(id) {
         if (!confirm("Are you sure to delete this priority?")) return;
         $.ajax({
            url: `/priorities/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function () {
               $('#task_priority_id option[value="' + id + '"]').remove();
               $('#task_priority_id').val(null).trigger('change');
            }
         });
      }

      // Add Priority AJAX
      $('#addPriorityBtn').on('click', function (e) {
         e.preventDefault();
         $.ajax({
            url: '{{ route("task-priorities.store") }}',
            method: 'POST',
            data: {
               name: $('#priority_name').val(),
            },
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
               if (response.status) {
                  $('#addPriorityModal').modal('hide');
                  $('#priority_name').val('');
                  $('#task_priority_id')
                     .find('option:last')
                     .before(`<option value="${response.data.id}" selected>${response.data.name}</option>`);
                  $('#task_priority_id').val(response.data.id).trigger('change');
               }
            },
            error: function () {
               alert('Failed to add priority.');
            }
         });
      });

      // Edit Priority AJAX
      $('#editPriorityForm').on('submit', function (e) {
         e.preventDefault();
         const id = $('#editPriorityId').val();
         const name = $('#editPriorityName').val();

         $.ajax({
            url: `/priorities/${id}`,
            type: 'PUT',
            data: {
               _token: '{{ csrf_token() }}',
               name: name
            },
            success: function (data) {
               $(`#task_priority_id option[value="${id}"]`).text(data.name).prop('selected', true);
               $('#editPriorityModal').modal('hide');
            }
         });
      });
   });

   // delete media
    $(document).ready(function () {
        $(document).on('click', '.deleteMedia', function () {
            const $container = $(this).closest('.deleteMedia');
            const mediaId = $container.data('media-id');

            if (!confirm("Are you sure you want to delete this file?")) {
                return;
            }

            $.ajax({
                url: `/media/${mediaId}`, // adjust route if needed
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.status) {
                        // Remove the parent preview block
                        $container.closest('.col-md-2').remove();
                    } else {
                        alert(response.message || "Deletion failed.");
                    }
                },
                error: function () {
                    alert("Error deleting the file. Please try again.");
                }
            });
        });
    });
    
    $('#files').on('change', function () {
        const fileCount = this.files.length;
        if (fileCount > 0) {
            $('#attachmentLabel').text(`📎 ${fileCount} file${fileCount > 1 ? 's' : ''} selected`);
        } else {
            $('#attachmentLabel').text('📎 Attachment (CSV, Excel, JPEG, PNG, PDF)');
        }
    });



   </script>

   </x-app-layout>