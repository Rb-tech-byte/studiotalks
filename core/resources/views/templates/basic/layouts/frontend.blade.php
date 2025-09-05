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

  @include($activeTemplate. 'partials.header')

  <div class="main-wrapper">

    <!-- board section start -->
    <section class="pt-50 pb-50 px-xxl-5">
        <div class="container-fluid">
          <div class="row">

          @include($activeTemplate. 'partials.left_bar')

          <main class="xxxl-8 col-lg-6 px-lg-4">
              @yield('content')
          </main>

          @include($activeTemplate. 'partials.right_bar')

          </div>
        </div>
    </section>
    <!-- board section end -->

  </div><!-- main-wrapper end -->

  @include($activeTemplate. 'partials.footer')

    <!-- jQuery library -->
  <script src="{{ asset($activeTemplateTrue. 'js/lib/jquery-3.6.0.min.js') }}"></script>
  <!-- bootstrap js -->
  <script src="{{ asset($activeTemplateTrue. 'js/lib/bootstrap.bundle.min.js') }}"></script>
  <!-- main js -->
  <script src="{{ asset($activeTemplateTrue. 'js/app.js') }}"></script>

  @stack('script-lib')

  @stack('script')

  @include('partials.plugins')

  @include('partials.notify')

  <script>

        (function ($) {
            "use strict";

            $(".langSel").on("change", function() {
                window.location.href = "{{route('home')}}/change/"+$(this).val() ;
            });

            $('.ajaxCount').on('click',function(){
              var id = $(this).data('id');
              $.get("{{ route('ad.count.ajax') }}",{id:id},function(response){
                console.log(response);
              });

            });

        })(jQuery);

    </script>

  </body>
</html>
