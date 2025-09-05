<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  @include('partials.seo')

  <title>{{ $general->sitename(__($pageTitle)) }}</title>

  <!-- bootstrap 5  -->
  <link rel="stylesheet" href="{{ asset($activeTemplateTrue. 'css/lib/bootstrap.min.css') }}">
  <!-- fontawesome 5  -->
  <link rel="stylesheet" href="{{ asset($activeTemplateTrue. 'css/all.min.css') }}">
  <!-- lineawesome font -->
  <link rel="stylesheet" href="{{ asset($activeTemplateTrue. 'css/line-awesome.min.css') }}">
  <!-- main css -->
  <link rel="stylesheet" href="{{ asset($activeTemplateTrue. 'css/main.css') }}">

  <link rel="stylesheet" href="{{ asset($activeTemplateTrue. 'css/color.php?color='.$general->base_color.'&secondColor='.$general->secondary_color) }}">

  @stack('style-lib')

  @stack('style')

  </head>

  <body>

    @stack('fbComment')

    <!-- scroll-to-top start -->
    <div class="scroll-to-top">
      <span class="scroll-icon">
        <i class="las la-arrow-up"></i>
      </span>
    </div>
    <!-- scroll-to-top end -->

    <div class="preloader-holder">
      <div class="preloader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
    </div>

  @include($activeTemplate. 'partials.auth_header')

  <div class="main-wrapper">

    <div class="pt-100 pb-100 section--bg">
      <div class="container">
        <div class="row justify-content-center">

            @include($activeTemplate. 'partials.auth_left_bar')

            @yield('content')

        </div>
      </div>
    </div>

  </div><!-- main-wrapper end -->

  @include($activeTemplate. 'partials.footer')

    <!-- jQuery library -->
  <script src="{{ asset($activeTemplateTrue. 'js/lib/jquery-3.6.0.min.js') }}"></script>
  <!-- bootstrap js -->
  <script src="{{ asset($activeTemplateTrue. 'js/lib/bootstrap.bundle.min.js') }}"></script>

  @stack('script-lib')

  <!-- main js -->
  <script src="{{ asset($activeTemplateTrue. 'js/app.js') }}"></script>

  @stack('script')

  @include('partials.plugins')

  @include('partials.notify')

  <script>

        (function ($) {
            "use strict";

            $(".langSel").on("change", function() {
                window.location.href = "{{route('home')}}/change/"+$(this).val() ;
            });

            let navLink = $('#navLink ul a');
            let currentRoute = '{{ url()->current() }}'

            $.each(navLink, function(index, value) {
                if(value.href == currentRoute){
                  let li = value.closest('li');
                  $(li).addClass('active');
                }
            });


        })(jQuery);

    </script>

  </body>
</html>
