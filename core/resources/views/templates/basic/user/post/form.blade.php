@extends($activeTemplate.'layouts.master')


@section('content')

  <div class="col-xl-9 mt-xl-0 mt-5">
    <div class="custom--card">
      <div class="forum-block__header justify-content-between d-flex align-items-center">
        <h6 class="forum-block__title">{{ __($pageTitle) }}</h6>
        <a href="{{ route('user.post.all') }}" class="btn btn-sm bg-primary">
          @lang('My Topics')
        </a>
      </div>
      <div class="card-body">
          <form method="post" action="{{ route('user.post.create') }}" enctype="multipart/form-data">
            <div class="row">
              @csrf

              <div class="form-group col-lg-12">
                <label for="sub_category">@lang('Sub Forum') <span class="text--danger">*</span></label>
                <select id="sub_category" name="sub_category" class="form--control select2-basic" required="">
                  <option value="">---@lang('Select Sub Forum')---</option>
                  @foreach($subCategories as $subCat)
                      <option value="{{ $subCat->id }}">{{ __($subCat->name) }}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group col-lg-12">
                <label for="title">@lang('Topic Title') <span class="text--danger">*</span></label>
                <input type="text" name="title" id="title" class="form--control" required=""
                oninput="carRemaining('titleSpan', this.value, 191)" value="{{ old('title') }}">
                  <span id="titleSpan" class="remaining">
                      191 @lang('characters remaining')
                  </span>
              </div>

                <label for="tags">@lang('Tags') <span class="text--danger">*</span></label>
                <select class="js-example-tokenizer form--control select2-basic" name="tags[]" multiple="multiple"></select>

                <div class="form-group col-lg-12">
                  <label>@lang('Image')</label>
                  <input type="file" name="image" class="form-control">
                </div>

                <div class="form-group col-lg-12">
                  <label>@lang('Youtube Embeded Video URL')</label>
                  <input type="text" name="video" class="form--control" value="{{ old('video') }}">
                </div>

               <div class="form-group col-lg-12">
                <label for="des">@lang('Description') <span class="text--danger">*</span></label>
                <textarea name="des" id="des" class="form--control" required="" oninput="carRemaining('desSpan', this.value, 64000)">{{ old('des') }}</textarea>
                  <span id="desSpan" class="remaining">
                      64000 @lang('characters remaining')
                  </span>
              </div>

               <div class="form-group col-lg-12 text-center">
                  <button type="submit" class="btn bg-primary text-white w-100">@lang('Create Topic')</button>
              </div>

            </div>
          </form>
      </div>
    </div>
  </div>

@endsection

@push('script-lib')
    <script src="{{asset('assets/admin/js/vendor/select2.min.js')}}"></script>
    <script src="{{ asset('assets/common/custom.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor/select2.min.css')}}">
@endpush

@push('script')

<script>

  (function ($) {
    "use strict";

    $('.select2-basic').select2();

    $(".js-example-tokenizer").select2({
        tags: true,
        tokenSeparators: [',', ' ']
    })

    $('select[name=sub_category]').val('{{ old('sub_category') }}');

  })(jQuery);

</script>

@endpush

@push('style')
<style>
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 50px;
}
.select2-container--default .select2-selection--single {
    height: 50px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    top: 14px;
}

</style>
@endpush


