<x-app-layout>
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header card-header-icon card-header-theme">
               <div class="card-icon">
                  <i class="material-icons">perm_identity</i>
               </div>
               <h4 class="card-title ">{!! trans('panel.category.title_singular') !!} {!! trans('panel.global.list') !!}
               </h4>
            </div>
            <div class="card-body">
              <div class="main">
               <ul class="cards">
               @foreach($categories as $category)
               @php
               $category_image = !empty($category->category_image) ? 
                (file_exists(public_path('uploads/'.$category->category_image)) ? 
                    url('public/uploads/'.$category->category_image) : 
                    asset('assets/img/nophoto20.png')) 
                : asset('assets/img/nophoto20.png');

               @endphp
                 <li class="cards_item">
                     <div class="card2">
                      <a href="{{route('category.products', $category->id)}}">
                        <div class="card_image"><img src="{{ $category_image }}" alt="{{$category->category_name}}">
                           <!-- <span class="note">{{date('d M Y',strtotime($category->created_at))}}</span> -->
                        </div>
                        <div class="card_content">
                           <h2 class="card_title">{{$category->category_name}}</h2>
                           <div class="card_text">

                              <button class="view_btn"> View <i class="material-icons">arrow_forward</i></button>
                           </div>
                        </div>
                      </a>
                     </div>
                  </li>
               @endforeach
            </div>
              </ul>
            </div>
            </div>
            <!-- ----------------------- -->
         </div>
      </div>
   </div>
   <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
</x-app-layout>