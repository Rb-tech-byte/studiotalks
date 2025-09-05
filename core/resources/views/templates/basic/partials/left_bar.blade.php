<aside class="xxxl-2 col-lg-3 d-lg-block d-none">

  <div class="sidebar-widget">
    <div class="sidebar-widget__header">
      <h5 class="sidebar-widget__title">@lang('Forum')</h5>
    </div>
    <div class="sidebar-widget__body">
      <ul class="category-list">
        @foreach($forums as $forum)
          <li>
            <a href="{{ route('forum', ['slug'=>slug($forum->name), 'id'=>$forum->id]) }}">
              @php echo $forum->icon; @endphp
              {{ __($forum->name) }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>
  </div><!-- sidebar-widget end -->

  <div class="sidebar-widget mt-4">
    <div class="sidebar-widget__header">
      <h5 class="sidebar-widget__title">@lang('Categories')</h5>
    </div>
    <div class="sidebar-widget__body">
      <ul class="category-list">
        @foreach($categories as $singleCat)
          <li>
            <a href="{{ route('category.post', ['slug'=>slug($singleCat->name), 'id'=>$singleCat->id]) }}">
              @php echo $singleCat->icon; @endphp
              {{ __($singleCat->name) }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>
  </div><!-- sidebar-widget end -->

    {{ showAdvertisement() }}

  <div class="sidebar-widget mt-4">
    <div class="sidebar-widget__header">
      <h5 class="sidebar-widget__title">@lang('Disscussion Now')</h5>
    </div>
    <div class="sidebar-widget__body">
      <ul class="discussion-list">
        @foreach($disscussions as $disscussion)
        <li class="single-discussion">
          <h6 class="single-discussion__title">
            <a href="{{ route('post.details', ['slug'=>slug('sadfsadf'), 'id'=>$disscussion->id]) }}">
                {{ __($disscussion->post_title) }}
            </a>
          </h6>
          <span class="fs--12px">
            <i class="las la-comments fs--14px"></i>
            {{ $disscussion->comment }} @lang('answered')
        </span>
        </li>
        @endforeach
      </ul>
    </div>
  </div><!-- sidebar-widget end -->

    {{ showAdvertisement() }}

</aside>
