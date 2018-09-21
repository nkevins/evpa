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

    <!-- Vendor css -->
    <link href="{{ public_asset('/assets/slim/lib/font-awesome5/css/all.min.css') }}" rel="stylesheet"/>
    <link href="{{ public_asset('/assets/slim/lib/Ionicons/css/ionicons.css') }}" rel="stylesheet">
    <link href="{{ public_asset('/assets/slim/lib/select2/css/select2.min.css') }}" rel="stylesheet">
    
    <link href="{{ public_asset('/assets/global/css/vendor.css') }}" rel="stylesheet"/>
    @yield('css')
    @yield('scripts_head')

    <!-- Slim CSS -->
    <link rel="stylesheet" href="{{ public_asset('/assets/slim/css/slim.css') }}">

    <style type="text/css">
      .signin-right {
        background-color: #eee;
      }
      .signin-box {
        background-color: #eee;
      }
      .signin-left {
        background-color: #d8131b;
        color: #fff;
      }
      .slim-logo a {
        color: #fff;
      }
      .btn-primary {
        background-color: #d8131b;
        border-color: #d8131b;
        background-image: none;
      }
      .btn-primary:hover {
        background-color: #d8131b;
        border-color: #d8131b;
      }
      .btn-primary:not(:disabled):not(.disabled):active {
        background-color: #d8131b;
        border-color: #d8131b;
      }
    </style>
  </head>
  <body>

    <div class="d-md-flex flex-row-reverse">
      <div class="signin-right">
        @yield('content')
      </div><!-- signin-right -->
      <div class="signin-left">
        <div class="signin-box">
          <h2 class="slim-logo"><a href="index.html">Emirates Virtual Pilot Association</a></h2>

          <p>EVPA is a worldwide virtual airline community comprised of real world pilots and aviation enthusiasts who want more realism added to their flights. Using flight simulation software, EVPA simulate its daily operations of Dubai airline operator based on real world schedules, equipment and routes.</p>
          
          <p>Fly virtually one of the world’s most modern fleet and live in one of the world’s most dynamic cities. Enhance your simulation realism.</p>

          <p class="tx-12">&copy; Copyright {{ now()->year }}. All Rights Reserved Emirates Virtual Pilot Association.</p>
        </div>
      </div><!-- signin-left -->
    </div><!-- d-flex -->

    <script src="{{ public_asset('/assets/slim/lib/jquery/js/jquery.js') }}"></script>
    <script src="{{ public_asset('/assets/slim/lib/popper.js/js/popper.js') }}"></script>
    <script src="{{ public_asset('/assets/slim/lib/bootstrap/js/bootstrap.js') }}"></script>
    <script src="{{ public_asset('/assets/slim/lib/select2/js/select2.min.js') }}"></script>
    
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