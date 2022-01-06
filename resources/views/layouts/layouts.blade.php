<!DOCTYPE html>
<html>
  <head>
    <!-- basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    
    <!--title-->
    <title>@yield('title') | Atmanirbhar-MeitY</title>
    
    <!--author-->
    <meta name="author" content="" />
    
    <!--description-->
    <meta name="description" content="Atmanirbhar Project KISAN Cloud, ESNA Device, Livelihood Entrepreneurship for self-Employment, MeitY GoI Project, IGNTU"/>
    
    <!--keyword-->
    <meta name=keywords content="Atmanirbhar, Atmanirbhar Bharat, KISAN Cloud, ESNA Device, Livelihood, Entrepreneurship, self Employment, MeitY, GoI Project, IGNTU">
    
    <!--favicon-->
    <link rel="icon" href="{{ asset('assets/front/img/action_img.png') }}">
    
    <!--open graph-->
    <meta property="og:image" content="http://atmanirbharproject.com/assets/front/img/action_img.png"/>  
    <meta property="og:title" content="Atmanirbhar-MeitY, GoI Sponsored Project, IGNTU."/>  
    <meta property="og:description" content="KISAN Cloud, ESNA Device, Livelihood Entrepreneurship for self Employment"/>  
    
    
    <!--twitter-->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@atmanirbharproject">
    <meta name="twitter:creator" content="@atmanirbharproject">
    <meta name="twitter:title" content="">
    <meta name="twitter:description" content="">
    <meta name="twitter:image" content="">

  
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/front/vendor/bootstrap-4.3.1-dist/css/bootstrap.min.css') }}">
  
  </head>
  <body>
    <div class="body_wrapper">
      <!-- header page -->
      @include('include.header')
      <!-- Main content -->
      @yield('content')
      <!-- footer page -->
      @include('include.footer')
    </div>
    
    <!--JQuery-->
    <script src="{{ asset('assets/front/vendor/jQuery/jquery-3.5.1.min.js') }}"></script>

    <!--Bootstrap Js-->
    <script src="{{ asset('assets/front/vendor/bootstrap-4.3.1-dist/js/bootstrap.min.js') }}"></script>

  </body>
</html>