 <!-- header-section start  -->
  <header class="header">
    <div class="header__bottom px-xl-5">
      <div class="container-fluid">
        <nav class="navbar navbar-expand-xl p-0 align-items-center">
          <a class="site-logo site-title" href="{{ route('home') }}">
            <img src="{{getImage(imagePath()['logoIcon']['path'] .'/logo.png')}}" alt="logo">
          </a>
          <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="menu-toggle"></span>
          </button>
          <button class="header-search-open-btn">
            <i class="las la-search"></i>
          </button>
          <form class="header-search-form header-search-form-mobile" action="{{ route('search') }}">
            <input type="text" name="title" placeholder="@lang('Search Post Title')..." class="header-search-form__input text-white">
            <button
            type="submit" class="header-search-form__btn"><i class="las la-search"></i></button>
          </form>
          <div class="collapse navbar-collapse mt-lg-0 mt-3" id="navbarSupportedContent">
            <div class="header-search-area ms-auto">
              <form class="header-search-form" action="{{ route('search') }}">
                <input type="text" name="title" placeholder="@lang('Search Post Title')..." class="header-search-form__input text-white">
                <button type="submit" class="header-search-form__btn"><i class="las la-search"></i></button>
              </form>
            </div>

            <ul class="navbar-nav main-menu ms-auto">
              <li><a href="{{ route('home') }}">@lang('Home')</a></li>
              <li><a href="{{ route('user.home') }}">@lang('Dashboard')</a></li>
              <li><a href="{{ route('user.post.form') }}">@lang('Topics')</a></li>
              <li><a href="{{ route('ticket.open') }}">@lang('Support')</a></li>
            </ul>
            <div class="nav-right">
              <a href="{{ route('user.logout') }}" class="btn btn-md btn--gradient d-flex align-items-center"><i class="las la-user fs--18px me-2"></i>@lang('Logout')</a>
            </div>
          </div>
        </nav>
      </div>
    </div><!-- header__bottom end -->
  </header>
  <!-- header-section end  -->
