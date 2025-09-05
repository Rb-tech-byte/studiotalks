@php
  $user = Auth::user();
@endphp


<div class="col-xl-3 col-lg-6 col-md-8 pe-xl-4">
  <div class="user-sidebar">
    <div class="user-widget">
      <div class="thumb">
        <img src="{{ getImage(imagePath()['profile']['user']['path'].'/'. $user->image,imagePath()['profile']['user']['size']) }}" alt="image" class="w-100">
      </div>
      <h5 class="name text-white mt-4">{{ __($user->fullname) }}</h5>
      <ul class="user-info-list text-white mt-3">
        <li>
          <i class="las la-map-marked-alt"></i>
          <p>
            {{ __(@$user->address->country) }}
          </p>
        </li>
        <li>
          <i class="las la-envelope"></i>
          <p>{{ $user->email }}</p>
        </li>
        <li>
          <i class="las la-phone"></i>
          <p>{{ $user->mobile }}</p>
        </li>
      </ul>
    </div><!-- user-widget end -->
    <div class="user-menu-widget" id="navLink">
      <ul class="user-menu">
        <li>
          <a href="{{ route('user.home') }}"><i class="las la-layer-group"></i> <span>@lang('Dashboard')</span></a>
        </li>
        <li>
            <a href="{{ route('user.post.form') }}"><i class="las la-plus"></i> <span>@lang('Create Topic')</span></a>
          </li>
        <li>
          <a href="{{ route('user.profile.setting') }}"><i class="las la-user"></i> <span>@lang('Profile')</span></a>
        </li>
        <li>
          <a href="{{ route('user.twofactor') }}"><i class="las la-cog"></i> <span>@lang('2FA Security')</span></a>
        </li>
        <li>
          <a href="{{ route('user.change.password') }}"><i class="las la-key"></i> <span>@lang('Change Password')</span></a>
        </li>
        <li>
          <a href="{{ route('user.logout') }}"><i class="las la-sign-out-alt"></i> <span>@lang('Logout')</span></a>
        </li>
      </ul>
    </div>
  </div><!-- user-sidebar end -->
</div>
