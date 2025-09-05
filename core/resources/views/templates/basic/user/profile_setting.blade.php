@extends($activeTemplate.'layouts.master')
@section('content')

    <div class="col-xl-9 mt-xl-0 mt-5">
        <div class="card">

            <div class="forum-block__header">
                <div class="forum-block__title">
                    <h5 class="text-white">@lang('Profile')</h5>
                </div>
            </div>

            <div class="card-body">
                <form class="register prevent-double-click" action="" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group col-sm-12">
                        <label for="des">@lang('About')</label>
                        <textarea name="about" id="about" class="form--control" required="" oninput="carRemaining('aboutSpan', this.value, 60000)">{{ $user->about }}</textarea>
                        <span id="aboutSpan" class="remaining">
                            <span class="charDes">
                                <span class="aboutLength">
                                    60000
                                </span>
                            </span>
                            @lang('characters remaining')
                        </span>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="InputFirstname" class="col-form-label">@lang('First Name'):</label>
                            <input type="text" class="form--control" id="InputFirstname" name="firstname" placeholder="@lang('First Name')" value="{{$user->firstname}}" minlength="3">
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="lastname" class="col-form-label">@lang('Last Name'):</label>
                            <input type="text" class="form--control" id="lastname" name="lastname" placeholder="@lang('Last Name')" value="{{$user->lastname}}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="email" class="col-form-label">@lang('E-mail Address'):</label>
                            <input class="form--control" id="email" placeholder="@lang('E-mail Address')" value="{{$user->email}}" readonly>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="phone" class="col-form-label">@lang('Mobile Number')</label>
                            <input class="form--control" id="phone" value="{{$user->mobile}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="address" class="col-form-label">@lang('Address'):</label>
                            <input type="text" class="form--control" id="address" name="address" placeholder="@lang('Address')" value="{{@$user->address->address}}" required="">
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="state" class="col-form-label">@lang('State'):</label>
                            <input type="text" class="form--control" id="state" name="state" placeholder="@lang('state')" value="{{@$user->address->state}}" required="">
                        </div>
                    </div>


                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="zip" class="col-form-label">@lang('Zip Code'):</label>
                            <input type="text" class="form--control" id="zip" name="zip" placeholder="@lang('Zip Code')" value="{{@$user->address->zip}}" required="">
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="city" class="col-form-label">@lang('City'):</label>
                            <input type="text" class="form--control" id="city" name="city" placeholder="@lang('City')" value="{{@$user->address->city}}" required="">
                        </div>

                </div>


                    <div class="row">
                         <div class="form-group col-sm-6">
                            <label class="col-form-label">@lang('Country'):</label>
                            <input class="form--control" value="{{@$user->address->country}}" disabled>
                        </div>
                        <div class="form-group col-sm-6">
                            <label class="col-form-label d-block">@lang('Image'):</label>
                            <input type="file" name="image" accept="image/*" class="form--control">
                        </div>
                    </div>

                    <div class="form-group row pt-5">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-block bg-primary text-white w-100">@lang('Update Profile')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('style-lib')
    <link href="{{ asset($activeTemplateTrue.'css/bootstrap-fileinput.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/common/custom.js') }}"></script>
@endpush

@push('style')
    <link rel="stylesheet" href="{{asset('assets/admin/build/css/intlTelInput.css')}}">
    <style>
        .intl-tel-input {
            position: relative;
            display: inline-block;
            width: 100%;!important;
        }
    </style>
@endpush

@push('script')
<script>
  (function ($) {
    "use strict";

    let aboutLength = parseInt({{ strlen($user->about) }});
    $('.aboutLength').text(60000-aboutLength);

  })(jQuery);
</script>
@endpush
