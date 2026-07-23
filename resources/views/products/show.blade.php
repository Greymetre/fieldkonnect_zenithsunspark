<x-app-layout>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<h5 class="mb-4">Product Details</h5>
				<div class="row">
					<div class="col-xl4 col-lg-4 text-center"> <img class="w-100 border-radius-lg shadow-lg mx-auto imageDisplayModel" src="{!! isset($products['product_image']) ? asset('/uploads/'.$products['product_image']) :'' !!}" alt="Product Image">
					</div>
					<div class="col-lg-8 mx-auto">
              <ul class="list-group">
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">GG No:</strong> &nbsp; {!! $products['product_no'] !!} </li> 
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">OE Part No:</strong> &nbsp; {!! isset($products['part_no']) ? $products['part_no'] : '' !!}</li>
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">{!! trans('panel.product.fields.description') !!}:</strong> &nbsp; {!! isset($products['description']) ? $products['description'] : '' !!}</li>
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Specification:</strong> &nbsp; {!! $products['specification'] !!} </li> 
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">phase:</strong> &nbsp; {!! $products['phase'] !!} </li> 
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Segment:</strong> &nbsp; {!! isset($products['brands']['brand_name']) ? $products['brands']['brand_name'] : '' !!}</li>
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Model:</strong> &nbsp; {!! isset($products['model_no']) ? $products['model_no'] : '' !!}</li>
              </ul>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <hr class="horizontal gray-light my-4">
            </div>
            <div class="col-lg-7 mx-auto">
               <ul class="list-group">
                <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Name:</strong> &nbsp; {!! isset($products['product_name']) ? $products['product_name'] : '' !!}</li>
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Category:</strong> &nbsp; {!! isset($products['categories']['category_name']) ? $products['categories']['category_name'] : '' !!}</li>
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Subcategory:</strong> &nbsp; {!! isset($products['subcategories']['subcategory_name']) ? $products['subcategories']['subcategory_name'] : '' !!}</li>
              <!--   <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">{!! trans('panel.product.fields.display_name') !!}:</strong> &nbsp; {!! isset($products['display_name']) ? $products['display_name'] : '' !!}</li> -->
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">{!! trans('panel.product.fields.unit_name') !!}:</strong> &nbsp; {!! isset($products['unitmeasures']['unit_name']) ? $products['unitmeasures']['unit_name'] :'' !!}</li>
                <!-- <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">HSN Code:</strong> &nbsp; {!! isset($products['productdetails'][0]['hsn_code']) ? $products['productdetails'][0]['hsn_code'] :'' !!}</li>
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">EAN Code:</strong> &nbsp; {!! isset($products['productdetails'][0]['ean_code']) ? $products['productdetails'][0]['ean_code'] :'' !!}</li> -->
              </ul>
					   </div>
             <div class="col-lg-5 mx-auto">
               <ul class="list-group"> 
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">MRP:</strong> &nbsp; {!! isset($products['productdetails'][0]['mrp']) ? $products['productdetails'][0]['mrp'] :'' !!}</li>
<!--                 <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Price:</strong> &nbsp; {!! isset($products['productdetails'][0]['price']) ? $products['productdetails'][0]['price'] :'' !!}</li> -->
<!--                 <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">{!! trans('panel.product.fields.discount') !!}:</strong> &nbsp; {!! isset($products['productdetails'][0]['discount']) ? $products['productdetails'][0]['discount'] :'' !!}</li> -->
<!--                 <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">{!! trans('panel.product.fields.max_discount') !!}:</strong> &nbsp; {!! isset($products['productdetails'][0]['max_discount']) ? $products['productdetails'][0]['max_discount'] :'' !!}</li> -->
<!--                 <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">{!! trans('panel.product.fields.selling_price') !!}:</strong> &nbsp; {!! isset($products['productdetails'][0]['selling_price']) ? $products['productdetails'][0]['selling_price'] :'' !!}</li> -->
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">{!! trans('panel.product.fields.gst') !!}:</strong> &nbsp; {!! isset($products['productdetails'][0]['gst']) ? $products['productdetails'][0]['gst'] :'' !!}</li>
              </ul>
          </div>
				</div>
			</div>
		</div>
	</div>
</div>

</x-app-layout>
