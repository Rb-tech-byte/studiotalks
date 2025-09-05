@extends($activeTemplate.'layouts.basic')
@section('content')

@php
  $register = getContent('register.content', true);
  $bg = getContent('auth.content', true);
  $policy_pages = getContent('policy_pages.element');
@endphp

    <!-- account section start -->
    <section class="pt-100 pb-100">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="account-wrapper">
              <div class="row">
                <div class="col-lg-12">
                  <div class="account-thumb bg_img h-100 d-flex flex-wrap align-items-center justify-content-center rounded-3" style="background-image: url('{{ getImage('assets/images/frontend/auth/' .@$bg->data_values->image, '1920x1280') }}');">
                    <div class="text-center">
                      <h2 class="text-white">@lang('Welcome to') {{ __(@$general->sitename) }}</h2>
                      <p class="text-white mt-2">{{ __(@$register->data_values->text) }}</p>
                      <p class="text-white mt-2 fs--14px">@lang('Already have an account')? <a href="{{ route('user.login') }}" class="text--base">@lang('Login')</a></p>
                    </div>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="account-content">
                    <form action="{{ route('user.register') }}" method="POST" onsubmit="return submitUserForm();">

                        @csrf

                        <div class="row gy-4">
                          <div class="col-md-6">
                            <h3 class="mb-3">@lang('Perosnal Information')</h3>
                              <div class="form-group">
                                <label  for="firstname">@lang('First Name')</label>
                                <div class="custom-icon-field">
                                  <i class="las la-user-edit"></i>
                                  <input id="firstname" type="text" class="form--control" name="firstname" value="{{ old('firstname') }}" required>
                                </div>
                              </div>
                              <div class="form-group">
                                <label  for="lastname">@lang('Last Name')</label>
                                <div class="custom-icon-field">
                                  <i class="las la-user-edit"></i>
                                  <input id="lastname" type="text" class="form--control" name="lastname" value="{{ old('lastname') }}" required>
                                </div>
                              </div>
                              <div class="form-group">
                                <label  for="country">@lang('Country')</label>
                                <select name="country" id="country" class="form--control select">
                                    @foreach($countries as $key => $country)
                                        <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}">{{ __($country->country) }}</option>
                                    @endforeach
                                </select>
                              </div>
                            <div class="form-group">
                                <label  for="mobile">@lang('Mobile')</label>
                                    <div class="input-group ">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text mobile-code">
                                            </span>
                                            <input type="hidden" name="mobile_code">
                                            <input type="hidden" name="country_code">
                                        </div>
                                        <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}" class="form--control checkUser">
                                    </div>
                                    <small class="text-danger mobileExist"></small>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <h3 class="mb-3">@lang('Account Information')</h3>
                            <div class="form-group">
                                <label  for="username">@lang('Username')</label>
                                <div class="custom-icon-field">
                                  <i class="las la-user"></i>
                                  <input id="username" type="text" class="form--control checkUser" name="username" value="{{ old('username') }}" required>
                                </div>
                                <small class="text-danger usernameExist"></small>
                            </div>
                            <div class="form-group">
                                <label  for="email">@lang('Email')</label>
                                <div class="custom-icon-field">
                                  <i class="las la-user"></i>
                                  <input id="email" type="email" class="form--control checkUser" name="email" value="{{ old('email') }}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label  for="password">@lang('Password')</label>
                                <div class="custom-icon-field hover-input-popup">
                                  <i class="las la-key"></i>
                                  <input id="password" type="password" class="form--control" name="password" required>
                                  @if($general->secure_password)
                                        <div class="input-popup">
                                          <p class="error lower">@lang('1 small letter minimum')</p>
                                          <p class="error capital">@lang('1 capital letter minimum')</p>
                                          <p class="error number">@lang('1 number minimum')</p>
                                          <p class="error special">@lang('1 special character minimum')</p>
                                          <p class="error minimum">@lang('6 character password')</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label  for="password-confirm">@lang('Confirm Password')</label>
                                <div class="custom-icon-field">
                                  <i class="las la-key"></i>
                                  <input id="password-confirm" type="password" class="form--control" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>
                          </div>
                        </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            @php echo loadReCaptcha() @endphp
                        </div>
                    </div>

                    @include($activeTemplate.'partials.custom_captcha')

                    @if($general->agree)
                        <div class="form-group row">
                            <div class="col-md-12">
                                <input type="checkbox" id="agree" name="agree">
                                <label for="agree">
                                    @lang('I agree with ')
                                    @foreach($policy_pages as $singlePolicy)
                                        <a href="{{ route('policy.page', ['page'=>slug($singlePolicy->data_values->title), 'id'=>$singlePolicy->id]) }}" target="_blank">
                                            {{ __($singlePolicy->data_values->title) }}
                                            {{ $loop->last ? '.' : ', ' }}
                                        </a>
                                    @endforeach
                                </label>
                            </div>
                        </div>
                    @endif

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn--gradient w-100">@lang('Create Now')</button>
                    </div>

                    </form>
                  </div>
                </div>
              </div>
            </div><!-- account-wrapper end -->
          </div>
        </div>
      </div>
    </section>
    <!-- account section end -->


<div class="modal fade" id="existModalCenter" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="existModalLongTitle">@lang('You are with us')</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6 class="text-center">@lang('You already have an account please Sign in ')</h6>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-md btn-danger" data-bs-dismiss="modal">@lang('Close')</button>
        <a href="{{ route('user.login') }}" class="btn btn-md bg-primary">@lang('Login')</a>
      </div>
    </div>
  </div>
</div>

@endsection
@push('style')
<style>
    .country-code .input-group-prepend .input-group-text{
        background: #fff !important;
    }
    .country-code select{
        border: none;
    }
    .country-code select:focus{
        border: none;
        outline: none;
    }
    .hover-input-popup {
        position: relative;
    }
    .hover-input-popup:hover .input-popup {
        opacity: 1;
        visibility: visible;
    }
    .input-popup {
        position: absolute;
        bottom: 130%;
        left: 50%;
        width: 280px;
        background-color: #1a1a1a;
        color: #fff;
        padding: 20px;
        border-radius: 5px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        -ms-border-radius: 5px;
        -o-border-radius: 5px;
        -webkit-transform: translateX(-50%);
        -ms-transform: translateX(-50%);
        transform: translateX(-50%);
        opacity: 0;
        visibility: hidden;
        -webkit-transition: all 0.3s;
        -o-transition: all 0.3s;
        transition: all 0.3s;
    }
    .input-popup::after {
        position: absolute;
        content: '';
        bottom: -19px;
        left: 50%;
        margin-left: -5px;
        border-width: 10px 10px 10px 10px;
        border-style: solid;
        border-color: transparent transparent #1a1a1a transparent;
        -webkit-transform: rotate(180deg);
        -ms-transform: rotate(180deg);
        transform: rotate(180deg);
    }
    .input-popup p {
        padding-left: 20px;
        position: relative;
    }
    .input-popup p::before {
        position: absolute;
        content: '';
        font-family: 'Line Awesome Free';
        font-weight: 900;
        left: 0;
        top: 4px;
        line-height: 1;
        font-size: 18px;
    }
    .input-popup p.error {
        text-decoration: line-through;
    }
    .input-popup p.error::before {
        content: "\f057";
        color: #ea5455;
    }
    .input-popup p.success::before {
        content: "\f058";
        color: #28c76f;
    }
    .mobile-code{
        height: 100% !important;
    }
</style>
@endpush
@push('script-lib')
<script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
<script src="{{ asset('assets/common/custom.js') }}"></script>
@endpush
@push('script')
    <script>
      "use strict";
        function submitUserForm() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML = '<span class="text-danger">@lang("Captcha field is required.")</span>';
                return false;
            }
            return true;
        }
        (function ($) {
            @if($mobile_code)
            $(`option[data-code={{ $mobile_code }}]`).attr('selected','');
            @endif

            $('select[name=country]').change(function(){
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+'+$('select[name=country] :selected').data('mobile_code'));
            });
            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+'+$('select[name=country] :selected').data('mobile_code'));

            @if($general->secure_password)
                $('input[name=password]').on('input',function(){
                    secure_password($(this));
                });
            @endif

            $('.checkUser').on('focusout',function(e){
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    var data = {mobile:mobile,_token:token}
                }
                if ($(this).attr('name') == 'email') {
                    var data = {email:value,_token:token}
                }
                if ($(this).attr('name') == 'username') {
                    var data = {username:value,_token:token}
                }
                $.post(url,data,function(response) {
                  if (response['data'] && response['type'] == 'email') {
                    $('#existModalCenter').modal('show');
                  }else if(response['data'] != null){
                    $(`.${response['type']}Exist`).text(`${response['type']} already exist`);
                  }else{
                    $(`.${response['type']}Exist`).text('');
                  }
                });
            });

            $('.main-wrapper').addClass('d-flex flex-wrap justify-content-center align-items-center h-100 account-section');

            $(".main-wrapper").css("background-image", "url('{{ getImage('assets/images/frontend/auth/' .@$bg->data_values->image, '1920x1280') }}')");

        })(jQuery);

    </script>
@endpush
