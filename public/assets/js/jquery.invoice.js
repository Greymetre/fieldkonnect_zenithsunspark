
function getAddressData()
{
  var pincode_id = $("select[name=pincode_id]").val();
  var base_url =$('.baseurl').data('baseurl'); 
  if(pincode_id){
    $.ajax({
      url: base_url + '/getAddressData',
      dataType: "json",
      type: "GET",
      data:{ _token: "{{csrf_token()}}", pincode_id:pincode_id},
      success: function(res){
        if(res)
        {
          $(".city").empty();
          $(".district").empty();
          $(".state").empty();
          $(".country").empty();
          $(".city").append('<option value="'+res.city_id+'">'+res.city_name+'</option>');
          $(".district").append('<option value="'+res.district_id+'">'+res.district_name+'</option>');
          $(".state").append('<option value="'+res.state_id+'">'+res.state_name+'</option>');
          $(".country").append('<option value="'+res.country_id+'">'+res.country_name+'</option>');
        }
        else
        {
          $(".city").empty();
          $(".district").empty();
          $(".state").empty();
          $(".country").empty();
        }
      }
    });
  } 
}

function sellerinfo()
{
  var customer_id = $("select[name=seller_id]").val();
  var base_url =$('.baseurl').data('baseurl'); 
  if(customer_id){
    $.ajax({
      url: base_url + '/getCustomerData',
      dataType: "json",
      type: "GET",
      data:{ _token: "{{csrf_token()}}", customer_id:customer_id},
      success: function(res){
        if(res)
        {
          $(".seller_address").empty();
          $(".sellername").empty();
          $('.seller_address').append(res.address1+'<br> '+res.address2+'<br>Phone : '+res.mobile+'<br>Email: '+res.email);
          $('.sellername').val(res.name);
        }
        else
        {
          $(".seller_address").empty();
          $(".sellername").empty();
        }
      }
    });
  }
  else
  {
    $(".buyer_address").empty(); 
    $(".sellername").empty();
  }
}

function buyerinfo()
{
  var customer_id = $("select[name=buyer_id]").val();
  var base_url =$('.baseurl').data('baseurl'); 
  if(customer_id){
    $.ajax({
      url: base_url + '/getCustomerData',
      dataType: "json",
      type: "GET",
      data:{ _token: "{{csrf_token()}}", customer_id:customer_id},
      success: function(res){
        if(res)
        {
          $(".buyer_address").empty();
          $('.buyername').empty();
          $('.addresslists').append('<option value="">Select Address</option>');
          $('.buyer_address').append(res.address1+'<br> '+res.address2+'<br>Phone : '+res.mobile+'<br>Email: '+res.email);
          $('.buyername').val(res.name);
          $.each(res.addresslists, function(key, item) 
          {
            $('.addresslists').append('<option value="' + item.address_id + '">' + item.address1 + ' ' + item.address2 + '</option>');
          });
        }
        else
        {
          $(".buyer_address").empty();
          $('.buyername').empty();
        }
      }
    });
  }
  else
  {
   $(".buyer_address").empty(); 
  } 
}


function getAddressInfo()
{
  var address_id = $("select[name=address_id]").val();
  var base_url =$('.baseurl').data('baseurl'); 
  if(address_id){
    $.ajax({
      url: base_url + '/getAddressInfo',
      dataType: "json",
      type: "GET",
      data:{ _token: "{{csrf_token()}}", address_id:address_id},
      success: function(res){
        if(res)
        {
          $(".addresstext").empty();
          $(".addresstext").val(res);
        }
        else
        {
          $(".addresstext").empty();
        }
      }
    });
  } 
}

function getOrderInfo()
{
  var order_id = $("select[name=order_id]").val();
  var base_url =$('.baseurl').data('baseurl'); 
  if(order_id){
    $.ajax({
      url: base_url + '/getOrderInfo',
      dataType: "json",
      type: "GET",
      data:{ _token: "{{csrf_token()}}", order_id:order_id},
      success: function(res){
        if(res)
        {
          $(".seller").empty();
          $(".buyer").empty();
          $(".addresslists").empty();
          $(".buyername").empty();
          $(".sellername").empty();
          $(".addresstext").empty();
          $('.seller').append('<option value="' + res.seller_id + '">' + res.seller_name + '</option>');
          $('.buyer').append('<option value="' + res.buyer_id + '">' + res.buyer_name + '</option>');
          $('.addresslists').append('<option value="' + res.address_id + '">' + res.address_name + '</option>');
          $('.buyername').val(res.buyer_name);
          $('.sellername').val(res.seller_name);
          $(".addresstext").val(res.address_name);

        }
        else
        {
          $(".seller").empty();
          $(".buyer").empty();
          $(".addresslists").empty();
          $(".buyername").empty();
          $(".sellername").empty();
          $(".addresstext").empty();
        }
      }
    });
  } 
}


