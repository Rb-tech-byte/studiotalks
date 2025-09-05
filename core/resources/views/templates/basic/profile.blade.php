@extends($activeTemplate.'layouts.frontend')

@section('content')

<div class="profile-section">
    <div class="profile-header bg_img" style="background-image: url('{{ asset($activeTemplateTrue."images/bg/bg1.jpg") }}');">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 text-center">
            <div class="profile-thumb">
              <img src="{{ $user->photo }}" alt="image">
            </div>
            <h3 class="profile-name text-white mt-3">{{ __($user->fullname) }}</h3>
            <ul class="profile-info-list d-flex flex-wrap align-items-center text-white justify-content-center mt-1">
              <li><i class="las la-flag"></i> {{ __($user->address->country) }}</li>
              <li><i class="las la-user-clock"></i> @lang('Since') {{ showDateTime($user->created_at, 'Y') }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    @include($activeTemplate. 'partials.profileNav')

    <div class="profile-details-wrapper">
        <div class="container">
            <div class="row justify-content-center">
            <div class="col-lg-12">
                <h3 class="mb-3">@lang('About')</h3>
                <p>{{ __($user->about) }}</p>
                @if(!$user->about)
                    <div class="no-data-wrapper">
                    <img src="{{ asset($activeTemplateTrue."images/no-data.png") }}" alt="image">
                    <h4 class="mt-3">@lang('No Data Found')</h4>
                    </div>
                @endif
            </div>
            </div>
        </div>
    </div>

</div>
  <!-- profile section end -->

@endsection

@push('script')
  <script>

  </script>
@endpush
