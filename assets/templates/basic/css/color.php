<?php
header("Content-Type:text/css");
$color = "#f0f"; // Change your Color Here
$secondColor = "#ff8"; // Change your Color Here

function checkhexcolor($color){
    return preg_match('/^#[a-f0-9]{6}$/i', $color);
}

if (isset($_GET['color']) AND $_GET['color'] != '') {
    $color = "#" . $_GET['color'];
}

if (!$color OR !checkhexcolor($color)) {
    $color = "#336699";
}


function checkhexcolor2($secondColor){
    return preg_match('/^#[a-f0-9]{6}$/i', $secondColor);
}

if (isset($_GET['secondColor']) AND $_GET['secondColor'] != '') {
    $secondColor = "#" . $_GET['secondColor'];
}

if (!$secondColor OR !checkhexcolor2($secondColor)) {
    $secondColor = "#336699";
}
?>

.bg-primary{
	color: white;
	background-color: <?php echo $color; ?> !important;
}

.btn-check:focus+.btn, .btn:focus{
	box-shadow: 0 0 0 0.25rem <?php echo $color; ?>40 !important;
}

.btn:hover{
	color: white;
}


.scroll-to-top, .preloader-holder, .custom--table thead, .header__bottom, .header-search-form__input, .header-search-form.header-search-form-mobile, .forum-block__header, .user-widget, .sidebar-widget__header, .footer-section, .conatact-section::after{
	background-color: <?php echo $secondColor; ?>;
}

.custom--accordion-two .accordion-button:not(.collapsed), .header .site-logo.site-title, .sub-forum-list li a, .d-widget__icon, .user-info-list li i, .user-menu li:hover a, .category-list li:hover a, .category-list li.active a, .social-link li a:hover i, .contact-item i, .contact-item a:hover, a:hover, .profile-info-list li i, .profile-menu li.active a{
	color: <?php echo $color; ?>;
}

body::-webkit-scrollbar-thumb, .post-filter-list li a::after, .single-post .forum-badge, .post-details__badge, .d-widget:hover .d-widget__icon, .d-widget__btn, .user-menu li.active a, .user-menu li.active:hover a{
	background-color: <?php echo $color; ?>;
}
.header .main-menu li a:hover, .header .main-menu li a:focus{
	color: white;
}

.btn--base{
	background-color: <?php echo $color; ?>;
}

.btn--base:hover{
	background-color: <?php echo $color; ?>;
}

.account-section {
    background-color: <?php echo $secondColor; ?>fa;
}


.btn--gradient, .bg--gradient {
	background-color: <?php echo $color; ?>;
	background-image: linear-gradient(134deg, <?php echo $color; ?>, #f37609, #fba10b, #ffc90f);
}
.text--base {
	color:  <?php echo $color; ?> !important;
}