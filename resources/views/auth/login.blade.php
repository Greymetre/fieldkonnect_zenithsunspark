<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FieldKonnect Login</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body,
    html {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f4f4;
    }

    .container-fluid {
      height: 100vh;
    }

    .left-side {
      background: url("{{ asset('assets/img/login.png') }}") no-repeat center center;
      background-size: cover;
      height: 100%;
    }

    .right-side {
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
    }

    .login-box {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      min-width: 400px;
      max-width: 450px;
    }

    .logo {
      font-size: 26px;
      font-weight: bold;
      color: #007bff;
    }

    .company-logos {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 20px;
    }

    .company-logos img {
      max-height: 40px;
      margin: 0 10px;
    }

    .form-control {
      border-radius: 5px;
    }

    .btn-primary {
      background-color: #007bff;
      border-color: #007bff;
      border-radius: 5px;
    }

    .footer {
      font-size: 12px;
      margin-top: 20px;
      color: #888;
    }
    .btn-lg{
      border-radius: 30px;
    }
  </style>
</head>

<body>

  <div class="container-fluid p-0">
    <div class="row h-100 no-gutters">
      <div class="col-md-6 left-side d-none d-md-block">
        <!-- <img src="{{ url('/').'/'.asset('assets/img/login_side.png') }}" alt="" class="side-img" width="100%"> -->
      </div>
      <div class="col-md-6 right-side text-center">
        @if(session()->has('error'))
        <div class="alert alert-danger">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span>
            {{ session()->get('error') }}
          </span>
        </div>
        @endif
        @if (session('status'))
        <div class="alert alert-success">
          {{ session('status') }}
        </div>
        @endif
        @if($errors->any())
        <div>
          <ul class="alert alert-danger">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        <div class="logo mb-3"><img src="{{asset('assets/img/brand_logo.png') }}" alt="" width="100%"></div>
        <div class="login-box">
          <div class="german-logo-cont">
            <img src="{{ url('/').'/'.asset('assets/img/zenithsunspark.png') }}?" alt="Zenith Sun Spark" width="140" class="blueneba-logo">
            <!--<img src="{{ url('/').'/'.asset('assets/img/silver.png') }}" alt="" class="german-logo">-->
          </div>
          <p class="mb-3 font-weight-bold text-primary">Please log in to your account</p>
          <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group text-left">
              <label for="email">Email address</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
            </div>
            <div class="form-group text-left">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Login</button>
          </form>
        </div>
        <div class="footer text-primary">&copy; 2025 Field Konnect. All rights reserved</div>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script>
    $("#pass-seen").on('click', function() {
      let currentText = $(this).text();
      let passwordField = $("#password");

      if (currentText === 'visibility') {
        $(this).text('visibility_off');
        $(this).attr('title', 'Show');
        passwordField.attr("type", "password");
      } else {
        $(this).text('visibility');
        $(this).attr('title', 'Hide');
        passwordField.attr("type", "text");
      }
    });
  </script>

</body>

</html>