<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
      <!-- CSS Files -->
      <link href="{{ url('/').'/'.asset('assets/css/material-dashboard.css') }}" rel="stylesheet" />
      <link href="{{ url('/').'/'.asset('assets/css/custom1.css') }}" rel="stylesheet" />
        <!-- CSS Just for demo purpose, don't include it in your project -->
      <link href="{{ url('/').'/'.asset('assets/demo/demo.css') }}" rel="stylesheet" />
    </head>
    <body style="background-color: #f2fbff;">
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>
    </body>
</html>
