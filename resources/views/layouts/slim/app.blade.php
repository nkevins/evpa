<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Meta -->
    <meta name="description" content="Emirates Virtual Pilot Association">
    <meta name="author" content="Jetline System">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-key" content="{{ Auth::check() ? Auth::user()->api_key: '' }}">

    <title>@yield('title') - {{ config('app.name') }}</title>

    <!-- vendor css -->
    <link href="{{ public_asset('/assets/slim/lib/font-awesome5/css/all.min.css') }}" rel="stylesheet"/>
    <link href="{{ public_asset('/assets/slim/lib//Ionicons/css/ionicons.css') }}" rel="stylesheet">
    <link href="{{ public_asset('/assets/slim/lib/rickshaw/css/rickshaw.min.css') }}" rel="stylesheet">
    
    <link href="{{ public_asset('/assets/global/css/vendor.css') }}" rel="stylesheet"/>
    @yield('css')
    @yield('scripts_head')

    <!-- Slim CSS -->
    <link rel="stylesheet" href="{{ public_asset('/assets/slim/css/slim.css') }}">
    <link rel="stylesheet" href="{{ public_asset('/assets/slim/css/slim.header-epva.css') }}">
  </head>
  <body>
    <div class="slim-header">
      <div class="container">
        <div class="slim-header-left">
          <h2 class="slim-logo"><a href="{{ url('/') }}"><img src="{{ public_asset('/assets/slim/img/Logo.png') }}" style="height:70px;" /></a></h2>
        </div><!-- slim-header-left -->
        <div class="slim-header-right">
          @if (Auth::check())
          <div class="dropdown dropdown-c">
            <a href="#" class="logged-user" data-toggle="dropdown">
              <span>{{ Auth::user()->name }}</span>
              <i class="fa fa-angle-down"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <nav class="nav">
                <a href="{{ url('/profile') }}" class="nav-link"><i class="icon ion-person"></i> My Profile</a>
                @role('admin')
                  <a href="{{ url('/admin') }}" class="nav-link"><i class="icon ion-ios-gear"></i> Admin Panel</a>
                @endrole
                <a href="{{ url('/logout') }}" class="nav-link"><i class="icon ion-forward"></i> Sign Out</a>
              </nav>
            </div><!-- dropdown-menu -->
          </div><!-- dropdown -->
          @endif
        </div><!-- header-right -->
      </div><!-- container -->
    </div><!-- slim-header -->

    <div class="slim-navbar">
      <div class="container">
        @include('nav')
      </div><!-- container -->
    </div><!-- slim-navbar -->

    <div class="slim-mainpanel">
      <div class="container">
        @include('flash.message')
        @yield('content')
      </div><!-- container -->
    </div><!-- slim-mainpanel -->

    <div class="slim-footer">
      <div class="container">
        <p>Copyright {{ now()->year }} &copy; All Rights Reserved. Emirates Virtual Pilot Association</p>
      </div><!-- container -->
    </div><!-- slim-footer -->

    <script src="{{ public_asset('/assets/slim/lib/jquery/js/jquery.js') }}"></script>
    <script src="{{ public_asset('/assets/slim/lib/popper.js/js/popper.js') }}"></script>
    <script src="{{ public_asset('/assets/slim/lib/bootstrap/js/bootstrap.js') }}"></script>
    <script src="{{ public_asset('/assets/slim/lib/jquery.cookie/js/jquery.cookie.js') }}"></script>
    <script src="{{ public_asset('/assets/slim/lib/d3/js/d3.js') }}"></script>
    <script src="{{ public_asset('/assets/slim/lib/rickshaw/js/rickshaw.min.js') }}"></script>
    <script src="{{ public_asset('/assets/slim/lib/Flot/js/jquery.flot.js') }}"></script>
    <script src="{{ public_asset('/assets/slim/lib/Flot/js/jquery.flot.resize.js') }}"></script>
    <script src="{{ public_asset('/assets/slim/lib/peity/js/jquery.peity.js') }}"></script>

    <script src="{{ public_asset('/assets/slim/js/ResizeSensor.js') }}"></script>
    
    <script src="{{ public_asset('/assets/global/js/vendor.js') }}"></script>
    <script src="{{ public_asset('/assets/frontend/js/app.js') }}"></script>
    @yield('scripts')
    
    {{--
    It's probably safe to keep this to ensure you're in compliance
    with the EU Cookie Law https://privacypolicies.com/blog/eu-cookie-law
    --}}
    <script>
      window.addEventListener("load", function () {
        window.cookieconsent.initialise({
          palette: {
            popup: {
              background: "#edeff5",
              text: "#838391"
            },
            button: {
              "background": "#067ec1"
            }
          },
          position: "top",
        })
      });
    </script>
    {{-- End the required tags block --}}
    
    <script>
      $(document).ready(function () {
          $(".select2").select2({width: 'resolve'});
      });
    </script>
  </body>
</html>