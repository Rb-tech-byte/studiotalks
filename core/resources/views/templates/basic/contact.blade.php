@extends($activeTemplate.'layouts.basic')

@section('content')

@php
    $contact = getContent('contact_us.content', true);
    $contacts = getContent('contact_us.element');
@endphp

    <!-- contact section start -->
    <section class="conatact-section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-xl-10">
            <div class="contact-wrapper">
              <div class="text-center">
                <h2 class="">{{ __(@$contact->data_values->heading) }}</h2>
                <p>{{ __(@$contact->data_values->sub_heading) }}</p>
              </div>
              <div class="row mt-5 gy-4">

              @foreach($contacts as $singleContact)  
                <div class="col-md-4">
                  <div class="contact-item">
                    @php echo $singleContact->data_values->icon; @endphp
                    <p>{{ __($singleContact->data_values->contact) }}</p>
                  </div>
                </div>
              @endforeach

              </div><!-- row end -->
              <form class="mt-5" method="post" action="">
                @csrf
                <div class="row">
                  <div class="form-group col-lg-6">
                    <label for="name">@lang('Name')</label>
                    <input type="text" name="name" id="name" class="form--control" value="@if(auth()->user()) {{ auth()->user()->fullname }} @else {{ old('name') }} @endif" @if(auth()->user()) readonly @endif required>
                  </div>
                  <div class="form-group col-lg-6">
                    <label for="email">@lang('Email')</label>
                    <input name="email" type="text" id="email" class="form--control" value="@if(auth()->user()) {{ auth()->user()->email }} @else {{old('email')}} @endif" @if(auth()->user()) readonly @endif required>
                  </div>                 
                  <div class="form-group col-lg-12">
                    <label for="subject">@lang('Subject')</label>
                    <input name="subject" id="subject" type="text" class="form--control" value="{{old('subject')}}" required>
                  </div>
                  <div class="form-group col-lg-12">
                    <label for="message">@lang('Message')</label>
                    <textarea name="message" id="message" wrap="off" class="form--control">{{old('message')}}</textarea>
                  </div>
                  <div class="col-lg-12 text-end">
                    <button type="submit" class="btn btn--gradient">@lang('Submit Now')</button>
                  </div>
                </div>
              </form>
            </div><!-- contact-wrapper end -->
          </div>
        </div>
      </div>
    </section>
    <!-- contact section end -->

@endsection
