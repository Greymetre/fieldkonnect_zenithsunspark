function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('.imagepreview1').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$(".getimage1").change(function () {
  readURL(this);
});

function readURL2(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('.imagepreview2').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$(".getimage2").change(function () {
  readURL2(this);
});

function readURL3(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('.imagepreview3').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$(".getimage3").change(function () {
  readURL3(this);
});

function readURL4(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('.imagepreview4').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$(".getimage4").change(function () {
  readURL4(this);
});

function readURL5(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('.imagepreview5').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$(".getimage5").change(function () {
  readURL5(this);
});

function readURL6(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('.imagepreview6').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$(".getimage6").change(function () {
  readURL0(this);
});

function readURL7(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('.imagepreview7').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$(".getimage7").change(function () {
  readURL7(this);
});


function readURL8(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('.imagepreview8').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$(".getimage8").change(function () {
  readURL8(this);
});


/*==================== Address Functions =================*/
function getShippingStateList() {
  var country_id = $("select[name=country_id]").val();
  var base_url = $('.baseurl').data('baseurl');
  if (country_id) {
    $.ajax({
      url: base_url + '/getState',
      dataType: "json",
      type: "GET",
      data: { _token: "{{csrf_token()}}", country_id: country_id },
      success: function (res) {
        $(".shipping_state").empty();
        $(".shipping_district").empty();
        $(".shipping_city").empty();
        $(".shipping_pincode").empty();
        if (res) {
          $(".shipping_state").append('<option value="">Select State</option>');
          $.each(res, function (key, item) {
            $('.shipping_state').append('<option value="' + item.id + '">' + item.state_name + '</option>');
          });
        }
      }
    });
  }
}
function getShippingDistrictList() {
  var state_id = $("select[name=shipping_state_id]").val();
  var base_url = $('.baseurl').data('baseurl');
  if (state_id) {
    $.ajax({
      url: base_url + '/getDistrict',
      dataType: "json",
      type: "GET",
      data: { _token: "{{csrf_token()}}", state_id: state_id },
      success: function (res) {
        $(".shipping_district").empty();
        $(".shipping_city").empty();
        $(".shipping_pincode").empty();
        if (res) {
          $(".shipping_district").append('<option value="">Select District</option>');
          $.each(res, function (key, item) {
            $('.shipping_district').append('<option value="' + item.id + '">' + item.district_name + '</option>');
          });
        }
      }
    });
  }
}
function getShippingCityList() {
  var district_id = $("select[name=shipping_district_id]").val();
  var base_url = $('.baseurl').data('baseurl');
  if (district_id) {
    $.ajax({
      url: base_url + '/getCity',
      dataType: "json",
      type: "GET",
      data: { _token: "{{csrf_token()}}", district_id: district_id },
      success: function (res) {
        $(".shipping_city").empty();
        $(".shipping_pincode").empty();
        if (res) {
          $(".shipping_city").append('<option value="">Select City</option>');
          $.each(res, function (key, item) {
            $('.shipping_city').append('<option value="' + item.id + '">' + item.city_name + '</option>');
          });
        }
      }
    });
  }
}
function getShippingPincodeList() {
  var city_id = $("select[name=shipping_city_id]").val();
  var base_url = $('.baseurl').data('baseurl');
  if (city_id) {
    $.ajax({
      url: base_url + '/getPincode',
      dataType: "json",
      type: "GET",
      data: { _token: "{{csrf_token()}}", city_id: city_id },
      success: function (res) {
        $(".shipping_pincode").empty();
        if (res) {
          $(".shipping_pincode").append('<option value="">Select Pincode</option>');
          $.each(res, function (key, item) {
            $('.shipping_pincode').append('<option value="' + item.id + '">' + item.pincode + '</option>');
          });
        }
      }
    });
  }
}
function getShippingAddressData() {
  var pincode_id = $("select[name=shipping_pincode_id]").val();
  var base_url = $('.baseurl').data('baseurl');
  var token = $("meta[name='csrf-token']").attr("content");
  if (pincode_id) {
    $.ajax({
      url: base_url + '/getAddressData',
      dataType: "json",
      type: "POST",
      data: { _token: token, pincode_id: pincode_id },
      success: function (res) {
        if (res) {
          if($(".shipping_city").val() == ""){
            $(".shipping_city").empty();
            $(".shipping_city").append('<option value="' + res.city_id + '">' + res.city_name + '</option>');            
          }
          if($(".shipping_district").val() == ""){
            $(".shipping_district").empty();
            $(".shipping_district").append('<option value="' + res.district_id + '">' + res.district_name + '</option>');
          }
          if($(".shipping_state").val() == ""){
            $(".shipping_state").empty();
            $(".shipping_state").append('<option value="' + res.state_id + '">' + res.state_name + '</option>');
          }
          if($(".shipping_country").val() == ""){
            $(".shipping_country").empty();
            $(".shipping_country").append('<option value="' + res.country_id + '">' + res.country_name + '</option>');
          }
          // $(".shipping_city").empty();
          // $(".shipping_district").empty();
          // $(".shipping_state").empty();
          // $(".shipping_country").empty();
          // $(".shipping_district").append('<option value="' + res.district_id + '">' + res.district_name + '</option>');
          // $(".shipping_state").append('<option value="' + res.state_id + '">' + res.state_name + '</option>');
          // $(".shipping_country").append('<option value="' + res.country_id + '">' + res.country_name + '</option>');
        }
        else {
          $(".shipping_city").empty();
          $(".shipping_district").empty();
          $(".shipping_state").empty();
          $(".shipping_country").empty();
        }
      }
    });
  }
}


function getStateList() {
  var country_id = $("select[name=country_id]").val();
  var base_url = $('.baseurl').data('baseurl');
  if (country_id) {
    $.ajax({
      url: base_url + '/getState',
      dataType: "json",
      type: "GET",
      data: { _token: "{{csrf_token()}}", country_id: country_id },
      success: function (res) {
        $(".state").empty();
        $(".district").empty();
        $(".city").empty();
        $(".pincode").empty();
        if (res) {
          $(".state").append('<option value="">Select State</option>');
          $.each(res, function (key, item) {
            $('.state').append('<option value="' + item.id + '">' + item.state_name + '</option>');
          });
        }
      }
    });
  }
}
function getDistrictList() {
  var state_id = $("select[name=state_id]").val();
  var base_url = $('.baseurl').data('baseurl');
  if (state_id) {
    $.ajax({
      url: base_url + '/getDistrict',
      dataType: "json",
      type: "GET",
      data: { _token: "{{csrf_token()}}", state_id: state_id },
      success: function (res) {
        $(".district").empty();
        $(".city").empty();
        $(".pincode").empty();
        if (res) {
          $(".district").append('<option value="">Select District</option>');
          $.each(res, function (key, item) {
            $('.district').append('<option value="' + item.id + '">' + item.district_name + '</option>');
          });
        }
      }
    });
  }
}
function getCityList() {
  var district_id = $("select[name=district_id]").val();
  var base_url = $('.baseurl').data('baseurl');
  if (district_id) {
    $.ajax({
      url: base_url + '/getCity',
      dataType: "json",
      type: "GET",
      data: { _token: "{{csrf_token()}}", district_id: district_id },
      success: function (res) {
        $(".city").empty();
        $(".pincode").empty();
        if (res) {
          $(".city").append('<option value="">Select City</option>');
          $.each(res, function (key, item) {
            $('.city').append('<option value="' + item.id + '">' + item.city_name + '</option>');
          });
        }
      }
    });
  }
}
function getCityListMultiDis() {
  var district_id = $('.district').val();
  var base_url = $('.baseurl').data('baseurl');
  if (district_id) {
    $.ajax({
      url: base_url + '/getCity',
      dataType: "json",
      type: "GET",
      data: { _token: "{{csrf_token()}}", district_id: district_id },
      success: function (res) {
        $(".city").empty();
        $(".pincode").empty();
        if (res) {
          $(".city").append('<option value="">Select City</option>');
          $.each(res, function (key, item) {
            $('.city').append('<option value="' + item.id + '">' + item.city_name + '</option>');
          });
        }
      }
    });
  }
}
function getPincodeList() {
  var city_id = $("select[name=city_id]").val();
  var base_url = $('.baseurl').data('baseurl');
  if (city_id) {
    $.ajax({
      url: base_url + '/getPincode',
      dataType: "json",
      type: "GET",
      data: { _token: "{{csrf_token()}}", city_id: city_id },
      success: function (res) {
        $(".pincode").empty();
        if (res) {
          $(".pincode").append('<option value="">Select Pincode</option>');
          $.each(res, function (key, item) {
            $('.pincode').append('<option value="' + item.id + '">' + item.pincode + '</option>');
          });
        }
      }
    });
  }
}
function getAddressData() {
  var pincode_id = $("select[name=pincode_id]").val();
  var base_url = $('.baseurl').data('baseurl');
  var token = $("meta[name='csrf-token']").attr("content");
  if (pincode_id) {
    $.ajax({
      url: base_url + '/getAddressData',
      dataType: "json",
      type: "POST",
      data: { _token: token, pincode_id: pincode_id },
      success: function (res) {
        if (res) {
          if($(".shipping_city").val() == ""){
            $(".shipping_city").empty();
            $(".shipping_city").append('<option value="' + res.city_id + '">' + res.city_name + '</option>');            
          }
          if($(".shipping_district").val() == ""){
            $(".shipping_district").empty();
            $(".shipping_district").append('<option value="' + res.district_id + '">' + res.district_name + '</option>');
          }
          if($(".shipping_state").val() == ""){
            $(".shipping_state").empty();
            $(".shipping_state").append('<option value="' + res.state_id + '">' + res.state_name + '</option>');
          }
          if($(".shipping_country").val() == ""){
            $(".shipping_country").empty();
            $(".shipping_country").append('<option value="' + res.country_id + '">' + res.country_name + '</option>');
          }
          // $(".shipping_city").empty();
          // $(".shipping_district").empty();
          // $(".shipping_state").empty();
          // $(".shipping_country").empty();
          // $(".shipping_district").append('<option value="' + res.district_id + '">' + res.district_name + '</option>');
          // $(".shipping_state").append('<option value="' + res.state_id + '">' + res.state_name + '</option>');
          // $(".shipping_country").append('<option value="' + res.country_id + '">' + res.country_name + '</option>');
        }
        else {
          $(".city").empty();
          $(".district").empty();
          $(".state").empty();
          $(".country").empty();
        }
      }
    });
  }
}

function getUserInfo() {
  var user_id = $("select[name=user_id]").val();
  var base_url = $('.baseurl').data('baseurl');
  if (user_id) {
    $.ajax({
      url: base_url + '/getUserInfo',
      dataType: "json",
      type: "GET",
      data: { _token: "{{csrf_token()}}", user_id: user_id },
      success: function (res) {
        alert(res);
        $(".fullname").empty();
        if (res) {
          $(".fullname").val(res.name);
        }
      }
    });
  }
}

