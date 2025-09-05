@php
  $register = getContent('register.content', true);
@endphp

<aside class="xxxl-2 col-lg-3 d-lg-block d-none">

@guest
    <div class="rounded-3 bg--gradient p-4 text-center mb-4">
        <h3 class="fw-normal text-white">@lang('JOIN OUR COMMUNITY')</h3>
        <p class="text-white fs--14px mt-3">{{ __(@$register->data_values->text) }}</p>
        <a href="{{ route('user.register') }}" class="btn btn--base mt-4">@lang('Registration Now')</a>
    </div>
@endguest

  <div class="sidebar-widget">
    <div class="sidebar-widget__header">
      <h5 class="sidebar-widget__title">@lang('Statistics')</h5>
    </div>
    <div class="sidebar-widget__body">
      <ul class="statistics-list">
        <li class="single-stat">
            <h3 class="single-stat__number">{{ $forum }}</h3>
            <span class="single-stat__caption fs--14px">@lang('Forum')</span>
        </li>
        <li class="single-stat">
          <h3 class="single-stat__number">{{ $category }}</h3>
          <span class="single-stat__caption fs--14px">@lang('Category')</span>
        </li>
        <li class="single-stat">
          <h3 class="single-stat__number">{{ $subCategory }}</h3>
          <span class="single-stat__caption fs--14px">@lang('Sub Category')</span>
        </li>
        <li class="single-stat">
            <h3 class="single-stat__number">{{ $post }}</h3>
            <span class="single-stat__caption fs--14px">@lang('Topic')</span>
        </li>
      </ul>
    </div>
  </div><!-- sidebar-widget end -->

  <div class="sidebar-widget mt-4">
    <div class="sidebar-widget__header">
      <h5 class="sidebar-widget__title">@lang('Top Contributors')</h5>
    </div>
    <div class="sidebar-widget__body">
      <ul class="contributor-list">

        @foreach($topContributors as $top)
          <li class="single-contributor">
            <div class="single-contributor__thumb">
            <a href="{{ route('user', ['username'=>$top->user->username, 'id'=>$top->user_id]) }}">
              <img src="{{ $top->user->photo }}" alt="@lang('image')">
            </a>
            </div>
            <h6 class="single-contributor__name">
                <a href="{{ route('user', ['username'=>$top->user->username, 'id'=>$top->user_id]) }}">
                    {{ __($top->user->fullname) }}
                </a>
            </h6>
            <span class="single-contributor__comment fs--14px"><i class="las la-comments"></i> {{ $top->total }}</span>
          </li>
        @endforeach

      </ul>
    </div>
  </div><!-- sidebar-widget end -->

  <div class="sidebar-widget mt-4">
    <div class="sidebar-widget__header">
      <h5 class="sidebar-widget__title">@lang('Unanswered Talks')</h5>
    </div>
    <div class="sidebar-widget__body">
      <ul class="unanswered-list">

        @foreach($unTalks as $unTalk)
          <li class="single-unanswered">
            <div class="single-unanswered__top">
              <div class="single-unanswered__thumb">
                <a href="{{ route('user', ['username'=>$unTalk->user->username, 'id'=>$unTalk->user_id]) }}">
                  <img src="{{ getImage(imagePath()['profile']['user']['path'].'/'. $unTalk->user->image,imagePath()['profile']['user']['size']) }}" alt="@lang('image')">
                </a>
              </div>
              <div class="single-unanswered__content d-flex align-items-center justify-content-between">
                <h6 class="single-unanswered__name"><a href="{{ route('user', ['username'=>$unTalk->user->username, 'id'=>$unTalk->user_id]) }}">{{ __($unTalk->user->fullname) }}</a></h6>
                <span class="fs--12px">{{ showDateTime($unTalk->created_at, 'd/m/Y') }}</span>
              </div>
            </div>
            <h6 class="single-unanswered__title">
              <a href="{{ route('post.details', ['slug'=>slug($unTalk->post_title), 'id'=>$unTalk->id]) }}">
                {{ __($unTalk->post_title) }}
              </a>
            </h6>
            <span class="fs--12px">
              <i class="las la-comments fs--14px"></i>
              {{ $unTalk->comment }} @lang('comment')
            </span>
          </li>
        @endforeach

      </ul>
    </div>
  </div><!-- sidebar-widget end -->

  <div class="sidebar-widget mt-4">
    <div class="sidebar-widget__header">
      <h5 class="sidebar-widget__title">@lang('Hot Topics')</h5>
    </div>
    <div class="sidebar-widget__body">
      <ul class="topic-list">
      @foreach($hots as $hot)
        <li class="single-topic">
          <div class="single-topic__thumb">
            <a href="{{ route('user', ['username'=>$top->user->username, 'id'=>$top->user_id]) }}">
                <img src="{{ $hot->post->user->photo }}" alt="@lang('image')">
            </a>
          </div>
          <div class="single-topic__content">
            <h6 class="single-topic__title">
                <a href="{{ route('post.details', ['slug'=>slug($hot->post->post_title), 'id'=>$hot->post_id]) }}">
                    {{ __($hot->post->post_title) }}
                </a>
            </h6>
            <span class="fs--12px"><i class="las la-calendar fs--14px"></i> {{ showDateTime($hot->post->cretaed_at, 'd/m/Y') }}</span>
          </div>
        </li>
      @endforeach

      </ul>
    </div>
  </div><!-- sidebar-widget end -->
</aside>
