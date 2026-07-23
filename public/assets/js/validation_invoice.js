$(document).ready(function () {
  var base_url =$('.baseurl').data('baseurl');
  var token = $("meta[name='csrf-token']").attr("content");

  $('#createInvoiceForm').validate({
    ignore: [], // Needed for select2
    rules: {
      customer_id: {
        required: true
      },
      'product_id[]': {
        required: true
      },
      invoice_date: {
        required: true
      },
      invoice_no: {
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#invoice_no").val();},
            table :'invoices',
            column : 'invoice_no',
            id: function() {return $("#invoice_no").val();},
          }
        },
      }
    },
    messages: {
      customer_id: {
        required: "Please select Customer",
      },
      'product_id[]': {
        required: "Please select at least one Product",
      },
      invoice_date: {
        required: "Please select Invoice Date",
      },
      invoice_no: {
        remote: "This Invoice No already exits.",
      }

    },
    errorPlacement: function (error, element) {
      if (element.hasClass("select2-hidden-accessible")) {
        error.insertAfter(element.next('span.select2'));
      } else {
        error.insertAfter(element);
      }
    },
    highlight: function (element) {
      if ($(element).hasClass("select2-hidden-accessible")) {
        $(element).next(".select2").find(".select2-selection").addClass("is-invalid");
      } else {
        $(element).addClass("is-invalid");
      }
    },
    unhighlight: function (element) {
      if ($(element).hasClass("select2-hidden-accessible")) {
        $(element).next(".select2").find(".select2-selection").removeClass("is-invalid");
      } else {
        $(element).removeClass("is-invalid");
      }
    }
  });

  $('#createEstimateForm').validate({
    ignore: [], // Needed for select2
    rules: {
      customer_id: {
        required: true
      },
      'product_id[]': {
        required: true
      },
      estimate_date: {
        required: true
      },
      estimate_no: {
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#estimate_no").val();},
            table :'estimates',
            column : 'estimate_no',
            id: function() {return $("#estimate_no").val();},
          }
        },
      }
    },
    messages: {
      customer_id: {
        required: "Please select Customer",
      },
      'product_id[]': {
        required: "Please select at least one Product",
      },
      estimate_date: {
        required: "Please select Invoice Date",
      },
      estimate_no: {
        remote: "This Estimate No already exits.",
      }

    },
    errorPlacement: function (error, element) {
      if (element.hasClass("select2-hidden-accessible")) {
        error.insertAfter(element.next('span.select2'));
      } else {
        error.insertAfter(element);
      }
    },
    highlight: function (element) {
      if ($(element).hasClass("select2-hidden-accessible")) {
        $(element).next(".select2").find(".select2-selection").addClass("is-invalid");
      } else {
        $(element).addClass("is-invalid");
      }
    },
    unhighlight: function (element) {
      if ($(element).hasClass("select2-hidden-accessible")) {
        $(element).next(".select2").find(".select2-selection").removeClass("is-invalid");
      } else {
        $(element).removeClass("is-invalid");
      }
    }
  });

  // For select2 real-time remove error on change
  $(".select2").on("change", function () {
    $(this).valid();
  });
});
