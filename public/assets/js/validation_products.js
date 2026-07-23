$(document).ready(function () {
  var base_url = $('.baseurl').data('baseurl');
  var token = $("meta[name='csrf-token']").attr("content");
  /*=============== Category Validation =====================*/
  $('#createCategoryForm').validate({
    rules: {
      category_name:
      {
        required: true,
        minlength: 3,
        maxlength: 250,
        remote: {
          url: base_url + "/uniqueValidation",
          type: "post",
          data: {
            "_token": token,
            value: function () { return $("#category_name").val(); },
            table: 'categories',
            column: 'category_name',
            id: function () { return $("#category_id").val(); },
          }
        }
      },
    },
    highlight: function (element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function (element) {
      $(element).closest('.error').css("display", "block");
    },
    messages: {
      category_name: {
        remote: "This Category Name already exits.",
        required: "Please enter Category Name",
      },
    }
  });

    /*=============== Gift Category Validation =====================*/
    $('#giftCreateCategoryForm').validate({
      rules: {
        category_name:
        {
          required: true,
          minlength: 3,
          maxlength: 250,
          remote: {
            url: base_url + "/uniqueValidation",
            type: "post",
            data: {
              "_token": token,
              value: function () { return $("#category_name").val(); },
              table: 'gift_categories',
              column: 'category_name',
              id: function () { return $("#category_id").val(); },
            }
          }
        },
      },
      highlight: function (element) {
        $(element).closest('.error').css("display", "none");
      },
      unhighlight: function (element) {
        $(element).closest('.error').css("display", "block");
      },
      messages: {
        category_name: {
          remote: "This Category Name already exits.",
          required: "Please enter Category Name",
        },
      }
    });

  /*=============== SubCategory Validation =====================*/
  $('#createsubcategoryForm').validate({
    rules: {
      subcategory_name:
      {
        required: true,
        minlength: 3,
        maxlength: 250,
      category_id:
      {
        required: true,
        number: true,
      },
    },
    highlight: function (element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function (element) {
      $(element).closest('.error').css("display", "block");
    },
    messages: {
      subcategory_name: {
        required: "Please enter SubCategory Name",
      },
      category_id: {
        required: "Please Select Category",
      },
    }
  });

   /*=============== SubCategory Validation =====================*/
   $('#createModelForm').validate({
    rules: {
      model_name:
      {
        required: true,
        minlength: 3,
        maxlength: 250,
        remote: {
          url: base_url + "/uniqueValidation",
          type: "post",
          data: {
            "_token": token,
            value: function () { return $("#model_name").val(); },
            table: 'gift_models',
            column: 'model_name',
            id: function () { return $("#sub_category_id").val(); },
          }
        }
      },
      sub_category_id:
      {
        required: true,
        number: true,
      },
    },
    highlight: function (element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function (element) {
      $(element).closest('.error').css("display", "block");
    },
    messages: {
      subcategory_name: {
        remote: "This Model Name already exits.",
        required: "Please enter Model Name",
      },
      category_id: {
        required: "Please Select Sub Category",
      },
    }
  });

   /*=============== Gift SubCategory Validation =====================*/
   $('#createsgiftsubcategoryForm').validate({
    rules: {
      subcategory_name:
      {
        required: true,
        minlength: 3,
        maxlength: 250,
      },
      category_id:
      {
        required: true,
        number: true,
      },
    },
    highlight: function (element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function (element) {
      $(element).closest('.error').css("display", "block");
    },
    messages: {
      subcategory_name: {
        remote: "This SubCategory Name already exits.",
        required: "Please enter SubCategory Name",
      },
      category_id: {
        required: "Please Select Category",
      },
    }
  });

  /*=============== Brand Validation =====================*/
  $('#createBrandForm').validate({
    rules: {
      brand_name:
      {
        required: true,
        minlength: 3,
        maxlength: 250,
        remote: {
          url: base_url + "/uniqueValidation",
          type: "post",
          data: {
            "_token": token,
            value: function () { return $("#brand_name").val(); },
            table: 'brands',
            column: 'brand_name',
            id: function () { return $("#brand_id").val(); },
          }
        }
      },
    },
    highlight: function (element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function (element) {
      $(element).closest('.error').css("display", "block");
    },
    messages: {
      brand_name: {
        remote: "This Brand Name already exits.",
        required: "Please enter Brand Name",
      },
    }
  });
  /*=============== Leave Validation =====================*/
  $('#createProductForm').validate({
    rules: {
      product_name:
      {
        required: true,
        minlength: 3,
        maxlength: 250,
      },
      display_name:
      {
        required: true,
        minlength: 3,
        maxlength: 250,
      },
      description:
      {
        required: true,
        minlength: 3,
        maxlength: 250,
      },
      subcategory_id:
      {
        required: true,
        number: true,
      },
      category_id:
      {
        required: true,
        number: true,
      },
      brand_id:
      {
        required: true,
        number: true,
      },
      unit_id:
      {
        required: true,
        number: true,
      },
      mrp:
      {
        required: true,
        number: true,
      },
      price:
      {
        required: true,
        number: true,
      },
      selling_price:
      {
        required: true,
        number: true,
      },
      gst:
      {
        required: true,
        number: true,
        max: 50,
      },
      discount:
      {
        number: true,
        max: 90,
      },
      max_discount:
      {
        number: true,
        min: "#discount",
      },
    },
    highlight: function (element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function (element) {
      $(element).closest('.error').css("display", "block");
    },
    messages: {
      product_name: {
        required: "Please enter Product Name",
      },
      display_name: {
        required: "Please enter Product Display Name",
      },
      description: {
        required: "Please enter Product Description",
      },
      subcategory_id: {
        required: "Please Select SubCategory",
      },
      category_id: {
        required: "Please Select Category",
      },
      brand_id: {
        required: "Please Select Brand",
      },
      unit_id: {
        required: "Please Select Unit",
      },
    }
  });

  /*=============== Unit Validation =====================*/
  $('#createunitForm').validate({
    rules: {
      unit_name:
      {
        required: true,
        minlength: 3,
        maxlength: 250,
        remote: {
          url: base_url + "/uniqueValidation",
          type: "post",
          data: {
            "_token": token,
            value: function () { return $("#unit_name").val(); },
            table: 'unit_measures',
            column: 'unit_name',
            id: function () { return $("#unit_id").val(); },
          }
        }
      },
      unit_code:
      {
        required: true,
        minlength: 2,
        maxlength: 250,
      },
    },
    highlight: function (element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function (element) {
      $(element).closest('.error').css("display", "block");
    },
    messages: {
      unit_name: {
        remote: "This Unit Name already exits.",
        required: "Please enter Unit Name",
      },
      unit_code: {
        required: "Please enter Unit Code",
      },
    }
  });

  /*=============== Gift Validation =====================*/
  $('#createGiftForm').validate({
    rules: {
      product_name:
      {
        required: true,
        minlength: 3,
        maxlength: 250,
      },
      display_name:
      {
        required: true,
        minlength: 3,
        maxlength: 250,
      },
      description:
      {
        required: true,
        minlength: 3,
        maxlength: 250,
      },
      subcategory_id:
      {
        required: true,
        number: true,
      },
      category_id:
      {
        required: true,
        number: true,
      },
      brand_id:
      {
        required: true,
        number: true,
      },
      unit_id:
      {
        required: true,
        number: true,
      },
      mrp:
      {
        required: true,
        number: true,
      },
      price:
      {
        required: true,
        number: true,
      },
      points:
      {
        required: true,
        number: true,
      },
    },
    highlight: function (element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function (element) {
      $(element).closest('.error').css("display", "block");
    },
    messages: {
      product_name: {
        required: "Please enter Product Name",
      },
      display_name: {
        required: "Please enter Product Display Name",
      },
      description: {
        required: "Please enter Product Description",
      },
      subcategory_id: {
        required: "Please Select SubCategory",
      },
      category_id: {
        required: "Please Select Category",
      },
      brand_id: {
        required: "Please Select Brand",
      },
      unit_id: {
        required: "Please Select Unit",
      },
    }
  });
});

