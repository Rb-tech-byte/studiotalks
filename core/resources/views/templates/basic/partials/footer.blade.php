@php
  $footer = getContent('footer.content', true);
  $icons = getContent('social_icon.element');
  $policy_pages = getContent('policy_pages.element');
@endphp

<!-- footer section start -->
<footer class="footer-section">
  <div class="container-fluid px-xxl-5">
    <div class="row gy-3 align-items-center">
      <div class="col-lg-5 col-md-6 col-sm-12 order-lg-1 order-2 text-md-start text-center">
        <p>{{ __(@$footer->data_values->text) }}</p>
      </div>
      <div class="col-lg-2 order-lg-2 order-1 text-center">
        <a href="{{ route('home') }}" class="footer-logo"><img src="{{getImage(imagePath()['logoIcon']['path'] .'/logo.png')}}" alt="logo image"></a>
      </div>

      <div class="col-lg-5 col-md-6 col-sm-12 order-lg-3 order-3">
        
        <ul class="footer-inline-menu d-flex flex-wrap align-items-center justify-content-md-end justify-content-center">
          @foreach($policy_pages as $singlePolicy)
            <li>
              <a href="{{ route('policy.page', ['page'=>slug($singlePolicy->data_values->title), 'id'=>$singlePolicy->id]) }}" target="_blank">
                  {{ __($singlePolicy->data_values->title) }}
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</footer>
<!-- footer section end -->
