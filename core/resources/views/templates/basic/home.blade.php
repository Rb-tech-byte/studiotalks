@extends($activeTemplate.'layouts.frontend')
@section('content')


@foreach($forums as $singleForum)

  <div class="forum-block">
    <div class="forum-block__header">
      <h4 class="forum-block__title">{{ __($singleForum->name) }}</h4>
    </div>
    <div class="forum-block__body">

      @forelse($singleForum->category->take(5) as $cat)
        <div class="single-thread">
          <div class="single-thread__left"> 
            <h5 class="single-thread__title">
                <a href="{{ route('category.post', ['slug'=>slug($cat->name), 'id'=>$cat->id]) }}">
                    {{ __($cat->name) }}
                </a>
            </h5>
            <p class="mt-2">
              {{ __($cat->description) }}
            </p>
            @if($cat->subCategory()->where('status',1)->count() > 0)
            <div class="d-flex flex-wrap fs--12px mt-2">
              <strong>@lang('Sub Forum'): </strong> &nbsp;
              <ul class="sub-forum-list d-flex flex-wrap align-items-center">
                  <li>
                    @php
                        $subCatId = [];
                    @endphp

                    @foreach($cat->subCategory()->where('status',1)->get() as $subCat)
                        @php
                            $subCatId[] = $subCat->id;
                        @endphp
                        <a href="{{ route('sub.category.post', ['slug'=>slug($subCat->name), 'id'=>$subCat->id]) }}">
                            {{ $subCat->name }}
                        </a>
                        {{ !$loop->last?',':'' }}
                    @endforeach
                  </li>
              </ul>
            </div>
            @endif
          </div>
          <div class="single-thread__right">
            <div class="top">
              <ul class="top__list">
                <li>
                  <span class="fs--14px">@lang('Topics')</span>
                  <h3>{{ $cat->topics()->count() }}</h3>
                </li>
                <li class="d-flex flex-wrap align-items-center">
                  <span class="w-100 fs--14px">@lang('Users')</span>
                  <ul class="top__list-user me-2">

                    @php
                        $usersId = array_unique($cat->topics()->pluck('user_id')->toArray());
                        $users = App\Models\User::whereIn('id',$usersId)->get(['image', 'id', 'username']);
                    @endphp

                    @foreach($users->take(5) as $user)
                    <li>
                        <a href="{{ route('user', ['username'=>$user->username, 'id'=>$user->id]) }}">
                            <img src="{{ getImage(imagePath()['profile']['user']['path'].'/'. @$user->image,imagePath()['profile']['user']['size']) }}" alt="@lang('image')">
                        </a>
                    </li>
                    @endforeach

                  </ul>
                <strong class="fs--14px">
                    @if($users->count() > 5)
                        5+
                    @else
                        {{ $users->count() }}
                    @endif
                </strong>
                </li>
                <li>
                  <span class="fs--14px">@lang('Activities')</span>
                  <h6 class="fs--14px">{{ lastActivities($cat->topics()->cursor()) }}</h6>
                </li>
              </ul>
            </div>

            @php
            $latestTitle = null;
            $latestId = null;
            $latestUser = null;

            $latestTopic = $cat->topics()->whereHas('subCategory', function($subCat){
            $subCat->where('status', 1)->whereHas('category', function($cat){
                    $cat->where('status', 1);
                });
            })->where('posts.status',1)->latest()->first();

            if($latestTopic){
              $latestTitle = $latestTopic->post_title;
              $latestId = $latestTopic->id;
              $latestUser = $latestTopic->user;
            }
            @endphp
            <div class="bottom">
              <span class="fs--14px mb-2">@lang('Latest Topic')</span>
              <div class="latest-topic">
                <div class="latest-topic__thumb">
                @if($latestUser)
                    <a href="{{ route('user', ['username'=>$latestUser->username, 'id'=>$latestUser->id]) }}">
                        <img src="{{ $latestUser->photo }}" alt="@lang('image')">
                    </a>
                @endif
                </div>
                <div class="latest-topic__content">
                  <h6 class="latest-topic__title">
                    <a href="{{ $latestId ? route('post.details', ['slug'=>slug($latestTitle), 'id'=>$latestId]) : '#0' }}">
                        {{ __($latestTitle) }}
                    </a>
                  </h6>
                </div>
              </div>
            </div>
          </div>
        </div>

      @empty
        <h6 class="text-center single-">@lang('Data Not Found')!</h6>
      @endforelse

    </div>
  </div>

@endforeach

<div class="mt-5">
  <ul class="pagination pagination-md justify-content-end">
    {{ $forums->links() }}
  </ul>
</div>

@endsection
