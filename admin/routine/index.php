<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>


<style>
    .notice-marquee {
        height: 200px;
        /* visible area */
        overflow: hidden;
        position: relative;
        background-color: rgba(255, 255, 255, 0.7);
        border-radius: 10px;
        padding: 5px;
    }

    .notice-marquee ul {
        position: absolute;
        width: 100%;
        animation: scrollUp 12s linear infinite;
        padding: 0;
        margin: 0;
    }

    .notice-marquee li {
        margin: 0 0 15px 0;
        padding: 0;
    }

    /* Keyframes for scrolling up */
    @keyframes scrollUp {
        0% {
            top: 100%;
        }

        100% {
            top: -100%;
        }
    }
</style>

<?php
include 'include/config.php';
include 'front_end/header.php';
include('include/dbcon.php');
$id = 0;
$status = "";
$rows = 0;
$res = 0;
// Current year
$currentYear = date("Y");
// Fetch Banner images
$bannerQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='Banner' ORDER BY uploaded_on DESC";
$bannerRes = mysqli_query($myDB, $bannerQuery);

// Fetch Gallery images
$galleryQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='Gallery' ORDER BY uploaded_on DESC";
$galleryRes = mysqli_query($myDB, $galleryQuery);

// Teacher
$teacherQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='Teacher' ORDER BY uploaded_on DESC";
$teacherRes = mysqli_query($myDB, $teacherQuery);

// Course
$courseQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='Course' ORDER BY uploaded_on DESC";
$courseRes = mysqli_query($myDB, $courseQuery);

// Contact
$contactQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='Contact' ORDER BY uploaded_on DESC";
$contactRes = mysqli_query($myDB, $contactQuery);

// About
$aboutQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='About' ORDER BY uploaded_on DESC";
$aboutRes = mysqli_query($myDB, $aboutQuery);

// Blog
$blogQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='Blog' ORDER BY uploaded_on DESC";
$blogRes = mysqli_query($myDB, $blogQuery);

// Notices
$noticeQuery = "SELECT * FROM rrsv_notice WHERE status='Active' ORDER BY notice_date DESC LIMIT 5";
$notices = mysqli_query($myDB, $noticeQuery);


/// ------------------- ROUTINE FILTERS ------------------- ///
$r_where = [];
if (!empty($_POST['class_id']))
    $r_where[] = "r.class_id=" . (int) $_POST['class_id'];
if (!empty($_POST['section_id']))
    $r_where[] = "r.section_id=" . (int) $_POST['section_id'];
if (!empty($_POST['subject_id']))
    $r_where[] = "r.subject_id=" . (int) $_POST['subject_id'];
if (!empty($_POST['scl_session']))
    $r_where[] = "r.session='" . mysqli_real_escape_string($myDB, $_POST['scl_session']) . "'";

$r_whereSQL = $r_where ? "WHERE " . implode(" AND ", $r_where) : "";

$sql = "
    SELECT r.*, 
           c.class_name, 
           s.sub_name, 
           sec.section_name
    FROM rrsv_routine r
    JOIN rrsv_class c ON r.class_id = c.id
    JOIN rrsv_subject s ON r.subject_id = s.id
    JOIN rrsv_section sec ON r.section_id = sec.id
    $r_whereSQL
    ORDER BY FIELD(r.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday'), r.start_time
";

$res = mysqli_query($myDB, $sql);
if (!$res)
    die("Query Failed: " . mysqli_error($myDB));

$timetable = [];
$timeSlots = [];
$days = [];

while ($row = mysqli_fetch_assoc($res)) {
    $day = $row['day_of_week'];
    $slot = $row['start_time'] . " - " . $row['end_time'];

    $days[$day] = $day;
    $timeSlots[$slot] = $slot;

    $timetable[$day][$slot] = $row['sub_name'] . "<br><small>(" . $row['teacher_name'] . ")</small>";
}

$daysOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$days = array_intersect($daysOrder, $days);
sort($timeSlots);


/// ------------------- HOLIDAYS ------------------- ///
$holidays = mysqli_query($myDB, "SELECT * FROM rrsv_holidays ORDER BY holiday_date ASC");


/// ------------------- SYLLABUS FILTERS ------------------- ///
$s_where = [];
if (!empty($_POST['class_id']))
    $s_where[] = "s.class_id=" . (int) $_POST['class_id'];
if (!empty($_POST['subject_id']))
    $s_where[] = "s.subject_id=" . (int) $_POST['subject_id'];
if (!empty($_POST['scl_session']))
    $s_where[] = "s.session='" . mysqli_real_escape_string($myDB, $_POST['scl_session']) . "'";

$s_whereSQL = $s_where ? "WHERE " . implode(" AND ", $s_where) : "";

$syll = "
    SELECT s.*, c.class_name, sub.sub_name
    FROM rrsv_syllabus s
    JOIN rrsv_class c ON s.class_id=c.id
    JOIN rrsv_subject sub ON s.subject_id=sub.id
    $s_whereSQL
    ORDER BY s.class_id, s.subject_id
";

$syllabus = mysqli_query($myDB, $syll);
if (!$syllabus) {
    die("SQL Error: " . mysqli_error($myDB) . "<br>Query: " . $syll);
}
?>

<style>
    select.form-control {
        height: auto;
    }

    .btn {
        padding: 0.5rem 1.5rem;
    }
</style>

<!-- Start main-content -->
<div class="main-content-area">

    <!-- Section: home Start -->
    <section id="home">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col">
                    <!-- START Industrivo Rev Slider 2 REVOLUTION SLIDER 6.1.7 -->
                    <p class="rs-p-wp-fix"></p>
                    <rs-module-wrap id="rev_slider_1_1_wrapper" data-alias="firoox-revolution-slider"
                        data-source="gallery"
                        style="background:transparent;padding:0;margin:0px auto;margin-top:0;margin-bottom:0;">
                        <rs-module id="rev_slider_1_1" style="" data-version="6.3.3">
                            <rs-slides>
                                <?php
                                $i = 1;
                                while ($banner = mysqli_fetch_assoc($bannerRes)) { ?>
                                    <rs-slide data-key="rs-<?= $i; ?>"
                                        data-title="<?= htmlspecialchars($banner['title']); ?>"
                                        data-thumb="<?= BASE_URL . $banner['image_path']; ?>"
                                        data-anim="ei:d;eo:d;s:d;r:default;t:slotslide-horizontal;sl:d;">
                                        <img src="<?= BASE_URL . $banner['image_path']; ?>"
                                            title="<?= htmlspecialchars($banner['title']); ?>" width="1920" height="1280"
                                            data-bg="p:center top;" data-parallax="off" class="rev-slidebg" data-no-retina>

                                        <rs-layer id="slider-2-slide-2-layer-10" data-type="text" data-rsp_ch="on"
                                            data-xy="x:l,l,c,c;xo:50px,0,-155px,0;yo:330px,243px,235px,229px;"
                                            data-text="w:normal;s:28,22,18,18;l:36,36,36,36;ls:1px,1px,0px,1px;fw:500;a:center,center,center,center;"
                                            data-dim="w:345px,400px,330px,356px;h:auto,auto,auto,auto;"
                                            data-frame_0="y:-50,-37,-28,-17;" data-frame_1="st:500;sp:1000;sR:500;"
                                            data-frame_999="o:0;st:w;sR:7500;" style="z-index:12;"
                                            class="font-current-theme2 bg-white text-theme-colored3 border-radius-5"><?= htmlspecialchars($banner['title']); ?>
                                        </rs-layer>
                                        <rs-layer id="slider-2-slide-2-layer-18" data-type="text" data-rsp_ch="on"
                                            data-xy="x:l,l,c,c;xo:55px,0px,8px,0;yo:388px,300px,280px,285px;"
                                            data-text="w:normal;s:18,12,8,8;fw:900;a:left,left,left,center;"
                                            data-dim="w:1000px,743px,659px,455px;" data-frame_0="y:-50,-37,-28,-17;"
                                            data-frame_1="st:1100;sp:1000;sR:1100;" data-frame_999="o:0;st:w;sR:6900;"
                                            style="z-index:11;text-transform:capitalize;" class="">
                                            <span
                                                class="text-theme-coloblue3"><?= htmlspecialchars($banner['description']); ?></span>
                                        </rs-layer>
                                        <rs-layer id="slider-2-slide-2-layer-22" data-type="text" data-rsp_ch="on"
                                            data-xy="x:l,l,c,c;xo:60px,0px,-225px,0px;yo:510px,410px,380px,360px;"
                                            data-text="w:normal;s:20,16,16,13;l:22,20,18,20;a:left,left,left,center;"
                                            data-frame_0="y:-50,-37,-28,-17;" data-frame_1="st:1700;sp:1000;sR:1700;"
                                            data-frame_999="o:0;st:w;sR:6300;" style="z-index:10;"
                                            class="font-current-theme1">
                                            <!-- <a href="#footer" class="btn btn-theme-colored4">Online Admission</a> -->
                                            <div class="text-center mb-4">
                                                <button type="button" class="btn btn-theme-colored4" data-bs-toggle="modal"
                                                    data-bs-target="#admissionModal">
                                                    📝 Apply for Admission
                                                </button>
                                            </div>
                                        </rs-layer>
                                        <rs-layer id="slider-2-slide-2-layer-0" data-type="shape" data-rsp_ch="on"
                                            data-text="w:normal;s:20,14,10,6;l:0,18,13,8;" data-dim="w:100%;h:100%;"
                                            data-basealign="slide" data-frame_999="o:0;st:w;sR:8700;"
                                            style="z-index:8;background-color:rgba(0,10,22,0.17);">
                                        </rs-layer>
                                    </rs-slide>
                                    <?php
                                    $i++;
                                } ?>

                            </rs-slides>
                            <rs-static-layers>

                                <div class="row justify-content-md-center" style="margin-top:55px;margin-left:595px">
                                    <div class="col-md-6">
                                        <div style="background-color: rgba(255,255,255,0.7); padding:5px; border-radius:10px;"
                                            class="text-left outline-border-5px">

                                            <!-- Notice marquee up -->
                                            <h4 class="text-center text-theme-colored3">Latest Notices</h4>
                                            <div class="notice-marquee">
                                                <ul class="list-unstyled">
                                                    <?php if (mysqli_num_rows($notices) > 0): ?>
                                                        <?php while ($notice = mysqli_fetch_assoc($notices)): ?>
                                                            <li class="mb-3">
                                                                <strong>📢
                                                                    <?php echo htmlspecialchars($notice['title']); ?></strong><br>
                                                                <small><i><?php echo date("d M Y", strtotime($notice['notice_date'])); ?></i></small>
                                                                <p><?php echo htmlspecialchars($notice['description']); ?></p>
                                                            </li>
                                                        <?php endwhile; ?>
                                                    <?php else: ?>
                                                        <li>No notices found.</li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </rs-static-layers>
                            <rs-progress class="rs-bottom"
                                style="height: 5px; background: rgba(199,199,199,0.5);"></rs-progress>
                        </rs-module>
                        <script data-cfasync="false"
                            src="../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
                        <script>
                            if (typeof revslider_showDoubleJqueryError === "undefined") {
                                function revslider_showDoubleJqueryError(sliderID) {
                                    var err = "<div class='rs_error_message_box'>";
                                    err += "<div class='rs_error_message_oops'>Oops...</div>";
                                    err += "<div class='rs_error_message_content'>";
                                    err += "You have some jquery.js library include that comes after the Slider Revolution files js inclusion.<br>";
                                    err += "To fix this, you can:<br>&nbsp;&nbsp;&nbsp; 1. Set 'Module General Options' -> 'Advanced' -> 'jQuery & OutPut Filters' -> 'Put JS to Body' to on";
                                    err += "<br>&nbsp;&nbsp;&nbsp; 2. Find the double jQuery.js inclusion and remove it";
                                    err += "</div>";
                                    err += "</div>";
                                    jQuery(sliderID).show().html(err);
                                }
                            }
                        </script>
                    </rs-module-wrap>
                    <!-- END REVOLUTION SLIDER -->
                </div>
            </div>
        </div>
    </section>
    <!-- Section: home End -->

    <!-- Section: Feature Divider -->
    <section class="divider" data-tm-bg-img="landing/images/bg/b1.png" data-tm-margin-top="-34px">
        <div class="container">
            <div class="section-content">
                <div class="row">
                    <div class="col-sm-6 col-md-3 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s">
                        <div
                            class="tm-sc-icon-box icon-box text-center iconbox-centered-in-responsive iconbox-theme-colored1 animate-icon-on-hover animate-icon-rotate-y mb-sm-30">
                            <div class="icon-box-wrapper">
                                <div class="icon-wrapper mb-20">
                                    <a class="icon icon-xl icon-dark icon-circled bg-theme-colored1">
                                        <i class="fas fa-user text-white"></i>
                                    </a>
                                </div>
                                <div class="icon-text">
                                    <h4 class="icon-box-title">Expert Teachers</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">
                        <div
                            class="tm-sc-icon-box icon-box text-center iconbox-centered-in-responsive iconbox-theme-colored1 animate-icon-on-hover animate-icon-rotate-y mb-sm-30">
                            <div class="icon-box-wrapper">
                                <div class="icon-wrapper mb-20">
                                    <a class="icon icon-xl icon-dark icon-circled bg-theme-colored2">
                                        <i class="fas fa-graduation-cap text-white"></i>
                                    </a>
                                </div>
                                <div class="icon-text">
                                    <h4 class="icon-box-title">Fully Equiped</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s">
                        <div
                            class="tm-sc-icon-box icon-box text-center iconbox-centered-in-responsive iconbox-theme-colored1 animate-icon-on-hover animate-icon-rotate-y mb-sm-30">
                            <div class="icon-box-wrapper">
                                <div class="icon-wrapper mb-20">
                                    <a class="icon icon-xl icon-dark icon-circled bg-theme-colored3">
                                        <i class="far fa-smile text-white"></i>
                                    </a>
                                </div>
                                <div class="icon-text">
                                    <h4 class="icon-box-title">Funny and Happy</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.7s">
                        <div
                            class="tm-sc-icon-box icon-box text-center iconbox-centered-in-responsive iconbox-theme-colored1 animate-icon-on-hover animate-icon-rotate-y mb-sm-30">
                            <div class="icon-box-wrapper">
                                <div class="icon-wrapper mb-20">
                                    <a class="icon icon-xl icon-dark icon-circled bg-theme-colored4">
                                        <i class="fas fa-heart text-white"></i>
                                    </a>
                                </div>
                                <div class="icon-text">
                                    <h4 class="icon-box-title">Fulfill With Love</h4>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: About Us -->
    <section id="about">
        <div class="container" data-tm-padding-bottom="220px" style="padding-bottom: 220px;">
            <div class="section-content">
                <div class="row">
                    <div class="col-lg-6 col-xl-8">
                        <div class="about-box-contents">
                            <div class="destails">
                                <h3 class="text-theme-colored2">Welcome To Kindergarten</h3>
                                <h4 class="text-theme-colored3 line-bottom">
                                    World Best Education In Our Kindergarten
                                </h4>
                                <p>
                                    Cum sociis natoque penatibus et ultrices volutpat. Nullam wisi ultricies a,
                                    gravida vitae, dapibus risus ante sodales lectus.
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <?php
                            $features = [
                                ['icon' => 'pe-7s-scissors', 'title' => 'Active Learning', 'color' => 'theme-colored3'],
                                ['icon' => 'pe-7s-pen', 'title' => 'Funny and Happy', 'color' => 'theme-colored2'],
                                ['icon' => 'pe-7s-phone', 'title' => 'Fulfill With Love', 'color' => 'theme-colored4'],
                                ['icon' => 'pe-7s-light', 'title' => 'Expert Teachers', 'color' => 'theme-colored1'],
                            ];

                            foreach ($features as $f): ?>
                                <div class="col-sm-6">
                                    <div class="tm-sc-icon-box icon-box icon-left text-center text-lg-start 
                                                iconbox-centered-in-responsive iconbox-<?= $f['color'] ?> 
                                                animate-icon-on-hover animate-icon-rotate-y mb-30">
                                        <div class="icon-box-wrapper">
                                            <div class="icon-wrapper">
                                                <a class="icon icon-sm icon-dark icon-circled"><i
                                                        class="<?= $f['icon'] ?>"></i></a>
                                            </div>
                                            <div class="icon-text">
                                                <h5 class="icon-box-title mb-0"><?= $f['title'] ?></h5>
                                                <div class="content">
                                                    <p>Lorem ipsum dolor sit amet, consectetur ipsum dolor sit amet.</p>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-lg-6 col-xl-4">
                        <div class="text-center">
                            <div class="thumb">
                                <img class="img-fullwidth" src="landing/images/about/a1.png" alt="a1.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tm-floating-objects">
            <span class="z-index-1 bg-img-cover" data-tm-bg-img="landing/images/bg/f2.png" data-tm-width="100%"
                data-tm-height="143" data-tm-top="auto" data-tm-bottom="0" data-tm-left="0" data-tm-right="0"
                data-tm-opacity="-100px"
                style="background-image: url('landing/images/bg/f2.png'); height: 143px; width: 100%; inset: auto 0px 0px;">
            </span>
        </div>
    </section>

    <!-- Section: Features -->
    <section class="">
        <div class="container pb-90">
            <div class="section-title">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-xl-6">
                        <div class="tm-sc-section-title section-title text-center">
                            <div class="title-wrapper">
                                <h2 class="title">Our <span class="text-theme-colored3">Features</span></h2>
                                <p>There are many variations of passages. But the majority have suffered alteration in
                                    some form, by injected humour, or randomised words.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-content">
                <div class="row">
                    <div class="col-sm-6 col-xl-4">
                        <div class="tm-sc-icon-box icon-box icon-left text-left iconbox-centered-in-responsive bg-theme-colored3 animate-icon-on-hover animate-icon-rotate-y p-30 mb-30"
                            data-tm-border-radius="15px" style="border-radius: 15px;">
                            <div class="icon-box-wrapper">
                                <div class="icon-wrapper"> <a class="icon text-white"> <i
                                            class="fas fa-graduation-cap"></i> </a></div>
                                <div class="icon-text">
                                    <h5 class="icon-box-title text-white">Qualified Teachers</h5>
                                    <div class="content">
                                        <p class="text-white">Lorem ipsum dolor sit aetcons ect adipsicing elium
                                            consecte sit ullam perspiciatis.</p>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="tm-sc-icon-box icon-box icon-left text-left iconbox-centered-in-responsive bg-theme-colored2 animate-icon-on-hover animate-icon-rotate-y p-30 mb-30"
                            data-tm-border-radius="15px" style="border-radius: 15px;">
                            <div class="icon-box-wrapper">
                                <div class="icon-wrapper"> <a class="icon text-white"> <i class="fas fa-book"></i> </a>
                                </div>
                                <div class="icon-text">
                                    <h5 class="icon-box-title text-white">Regular Classes</h5>
                                    <div class="content">
                                        <p class="text-white">Lorem ipsum dolor sit aetcons ect adipsicing elium
                                            consecte sit ullam perspiciatis.</p>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="tm-sc-icon-box icon-box icon-left text-left iconbox-centered-in-responsive bg-theme-colored1 animate-icon-on-hover animate-icon-rotate-y p-30 mb-30"
                            data-tm-border-radius="15px" style="border-radius: 15px;">
                            <div class="icon-box-wrapper">
                                <div class="icon-wrapper"> <a class="icon text-white"> <i class="fas fa-home"></i> </a>
                                </div>
                                <div class="icon-text">
                                    <h5 class="icon-box-title text-white">Sufficient Classrooms</h5>
                                    <div class="content">
                                        <p class="text-white">Lorem ipsum dolor sit aetcons ect adipsicing elium
                                            consecte sit ullam perspiciatis.</p>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="tm-sc-icon-box icon-box icon-left text-left iconbox-centered-in-responsive bg-theme-colored4 animate-icon-on-hover animate-icon-rotate-y p-30 mb-30"
                            data-tm-border-radius="15px" style="border-radius: 15px;">
                            <div class="icon-box-wrapper">
                                <div class="icon-wrapper"> <a class="icon text-white"> <i class="fas fa-comments"></i>
                                    </a></div>
                                <div class="icon-text">
                                    <h5 class="icon-box-title text-white">Creative Ideas</h5>
                                    <div class="content">
                                        <p class="text-white">Lorem ipsum dolor sit aetcons ect adipsicing elium
                                            consecte sit ullam perspiciatis.</p>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="tm-sc-icon-box icon-box icon-left text-left iconbox-centered-in-responsive bg-theme-colored3 animate-icon-on-hover animate-icon-rotate-y p-30 mb-30"
                            data-tm-border-radius="15px" style="border-radius: 15px;">
                            <div class="icon-box-wrapper">
                                <div class="icon-wrapper"> <a class="icon text-white"> <i class="fas fa-bus-alt"></i>
                                    </a></div>
                                <div class="icon-text">
                                    <h5 class="icon-box-title text-white">Bus Services</h5>
                                    <div class="content">
                                        <p class="text-white">Lorem ipsum dolor sit aetcons ect adipsicing elium
                                            consecte sit ullam perspiciatis.</p>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="tm-sc-icon-box icon-box icon-left text-left iconbox-centered-in-responsive bg-theme-colored2 animate-icon-on-hover animate-icon-rotate-y p-30 mb-30"
                            data-tm-border-radius="15px" style="border-radius: 15px;">
                            <div class="icon-box-wrapper">
                                <div class="icon-wrapper"> <a class="icon text-white"> <i class="fas fa-trophy"></i>
                                    </a></div>
                                <div class="icon-text">
                                    <h5 class="icon-box-title text-white">Sports Facilities</h5>
                                    <div class="content">
                                        <p class="text-white">Lorem ipsum dolor sit aetcons ect adipsicing elium
                                            consecte sit ullam perspiciatis.</p>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--  Informations-->
    <section id="information" class="bg-img-cover bg-img-center" data-tm-bg-img="images/bg/p2.jpg"
        style="background-image: url(&quot;images/bg/p2.jpg&quot;);">
        <div class="container-fluid pb-md-120" data-tm-padding-bottom="205px" style="padding-bottom: 205px;">
            <div class="section-title">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-xl-6">
                        <div class="tm-sc-section-title section-title text-center">
                            <div class="title-wrapper">
                                <h2 class="title">Our <span class="text-theme-colored3">Informations</span></h2>
                                <p>There are many variations of passages. But the majority have suffered
                                    alteration in some form, by
                                    injected humour, or randomised words.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-content">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="tm-sc-departments tm-sc-departments-tab">
                            <ul class="nav nav-tabs">
                                <li class="bg-theme-colored1 active"> <a href="#tab-notices-tabs" class="show active"
                                        data-bs-toggle="tab"> <i class="fas fa-bell"></i>
                                        <span>Notice</span> </a></li>
                                <li class="bg-theme-colored2"> <a href="#tab-routine-tabs" class=""
                                        data-bs-toggle="tab">
                                        <i class="fas fa-graduation-cap"></i>
                                        <span>Routine</span> </a></li>
                                <li class="bg-theme-colored3"> <a href="#tab-holiday-list-tabs" class=""
                                        data-bs-toggle="tab"> <i class="fas fa-paint-brush"></i>
                                        <span>Holiday list</span> </a></li>
                                <li class="bg-theme-colored1"> <a href="#tab-admission-charges-tabs" class=""
                                        data-bs-toggle="tab">
                                        <i class="far fa-flag"></i>
                                        <span>Admission Charges</span> </a></li>
                                <li class="bg-theme-colored4"> <a href="#tab-syllabus-tabs" class=""
                                        data-bs-toggle="tab"> <i class="fas fa-book"></i>
                                        <span>Syllabus</span> </a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade in active show" id="tab-notices-tabs">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-12 col-xl-12">
                                            <div class="tab-left-part mb-lg-40">
                                                <h3 class="title mb-20 text-theme-colored1 text-center">Notices</h3>
                                                <div>
                                                    <div class="container pt-5 pb-5">
                                                        <div class="row">
                                                            <div class="col-md-12">

                                                                <h4>Latest Notices</h4>
                                                                <ul class="list-unstyled">
                                                                    <?php
                                                                    // Notices
                                                                    $noticeQuery = "SELECT * FROM rrsv_notice WHERE status='Active' ORDER BY notice_date DESC LIMIT 5";
                                                                    $notices = mysqli_query($myDB, $noticeQuery);
                                                                    if (mysqli_num_rows($notices) > 0) {
                                                                        while ($notice = mysqli_fetch_assoc($notices)) { ?>
                                                                            <li class="mb-3">
                                                                                <strong>📢
                                                                                    <?php echo $notice['title']; ?></strong>
                                                                                <br>
                                                                                <small><i><?php echo date("d M Y", strtotime($notice['notice_date'])); ?></i></small>
                                                                                <p><?php echo $notice['description']; ?></p>


                                                                            </li>
                                                                        <?php }
                                                                    } else { ?>
                                                                        <li>No notices found.</li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab-routine-tabs">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-12 col-xl-12">
                                            <div class="tab-left-part mb-lg-4">

                                                <div class="pt-1 pb-1">
                                                    <div class="container">
                                                        <div class="row">
                                                            <div class="table-responsive shadow-lg">
                                                                <form method="post" class="mb-3">
                                                                    <div class="row">
                                                                        <!-- Session -->
                                                                        <div class="col-md-2">
                                                                            <div class="mt-3 mb-10">
                                                                                <select name="scl_session"
                                                                                    class="form-control"
                                                                                    id="scl_session">
                                                                                    <option value="">-Select a Session-
                                                                                    </option>
                                                                                    <?php
                                                                                    for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) {
                                                                                        $selected = (isset($_POST['scl_session']) && $_POST['scl_session'] == $i) ? 'selected' : '';
                                                                                        echo "<option value='$i' $selected>$i</option>";
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Class -->
                                                                        <div class="col-md-2">
                                                                            <div class="mt-3 mb-10">
                                                                                <select name="class_id"
                                                                                    class="form-control">
                                                                                    <option value="">Select Class
                                                                                    </option>
                                                                                    <?php
                                                                                    $classes = mysqli_query($myDB, "SELECT id, class_name FROM rrsv_class");
                                                                                    while ($c = mysqli_fetch_assoc($classes)) {
                                                                                        $selected = (isset($_POST['class_id']) && $_POST['class_id'] == $c['id']) ? 'selected' : '';
                                                                                        echo "<option value='{$c['id']}' $selected>{$c['class_name']}</option>";
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <!-- <div class="col-md-2">
                                                                            <div class="mt-3 mb-10">
                                                                                <select name="section_id"
                                                                                    class="form-control">
                                                                                    <option value="">Select Section
                                                                                    </option>
                                                                                    <?php
                                                                                    $sections = mysqli_query($myDB, "SELECT id, section_name FROM rrsv_section");
                                                                                    while ($s = mysqli_fetch_assoc($sections)) {
                                                                                        $selected = (isset($_POST['section_id']) && $_POST['section_id'] == $s['id']) ? 'selected' : '';
                                                                                        echo "<option value='{$s['id']}' $selected>{$s['section_name']}</option>";
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <div class="mt-3 mb-10">
                                                                                <select name="subject_id"
                                                                                    class="form-control">
                                                                                    <option value="">Select Subject
                                                                                    </option>
                                                                                    <?php
                                                                                    $subjects = mysqli_query($myDB, "SELECT id, sub_name FROM rrsv_subject");
                                                                                    while ($sub = mysqli_fetch_assoc($subjects)) {
                                                                                        $selected = (isset($_POST['subject_id']) && $_POST['subject_id'] == $sub['id']) ? 'selected' : '';
                                                                                        echo "<option value='{$sub['id']}' $selected>{$sub['sub_name']}</option>";
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div> -->

                                                                        <!-- Submit -->
                                                                        <div class="col-md-2 mt-4">
                                                                            <button type="submit"
                                                                                class="btn btn-primary">Search</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                <form method="post"
                                                                    action="<?php echo BASE_URL; ?>admin/routine/download_routine.php">
                                                                    <input type="hidden" name="class_id"
                                                                        value="<?php echo $_POST['class_id'] ?? ''; ?>">
                                                                    <input type="hidden" name="section_id"
                                                                        value="<?php echo $_POST['section_id'] ?? ''; ?>">
                                                                    <input type="hidden" name="subject_id"
                                                                        value="<?php echo $_POST['subject_id'] ?? ''; ?>">
                                                                    <div class="col-md-2 mt-2"> <button type="submit"
                                                                            class="btn btn-primary">Download
                                                                            PDF</button> </div>
                                                                </form>

                                                                <!-- <div class="col-md-1 mt-1">
                                                                    <button onclick="downloadRoutinePDF()"
                                                                        class="btn btn-primary">📄 Download</button>

                                                                </div> -->
                                                                <div id="routineContainer">
                                                                    <h3
                                                                        class="title mb-2 text-theme-colored2 text-center">
                                                                        Routine</h3>
                                                                    <div
                                                                        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">

                                                                        <!-- Left Side (School Info) -->
                                                                        <div style="flex: 1; text-align: left;">
                                                                            <b>RASULPUR RAMKRISHNA SARADA
                                                                                VIDYAPITH</b><br>
                                                                            Reg. No.: SO196094, U-DISE Code:
                                                                            19251610302, ESTD: 2012<br>
                                                                            Baidyadanga, Rasulpur, Memari,
                                                                            Bardhaman,<br>
                                                                            Pin - 713151
                                                                        </div>

                                                                        <!-- Right Side (Logo) -->
                                                                        <div>
                                                                            <img src="<?php echo BASE_URL; ?>libray/images/logo.jpeg"
                                                                                alt="School Logo"
                                                                                style="width: 85px; height: 78px; object-fit: contain;">
                                                                        </div>
                                                                    </div>


                                                                    <table
                                                                        class="table table-bordered table-striped text-center"
                                                                        id="routineTable">
                                                                        <thead class="bg-theme-colored2 text-white">

                                                                            <tr>
                                                                                <th>Day</th>
                                                                                <?php foreach ($timeSlots as $slot) { ?>
                                                                                    <th><?= $slot; ?></th>
                                                                                <?php } ?>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php foreach ($days as $day) { ?>
                                                                                <tr>
                                                                                    <td><strong><?= $day; ?></strong></td>
                                                                                    <?php foreach ($timeSlots as $slot) { ?>
                                                                                        <td>
                                                                                            <?= isset($timetable[$day][$slot]) ? $timetable[$day][$slot] : "—"; ?>
                                                                                        </td>
                                                                                    <?php } ?>
                                                                                </tr>
                                                                            <?php } ?>
                                                                        </tbody>
                                                                    </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab-holiday-list-tabs">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-12 col-xl-12">
                                            <div class="tab-left-part mb-lg-40">
                                                <h3 class="title mb-20 text-theme-colored3 text-center">Holiday list
                                                </h3>

                                                <div class="row mb-5">
                                                    <div class="container">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <form id="holidayForm" method="post">
                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <div class="mb-10">
                                                                                <select name="scl_session"
                                                                                    id="scl_session"
                                                                                    class="form-control">
                                                                                    <?php
                                                                                    for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) {
                                                                                        $selected = ($i == $currentYear) ? "selected" : "";
                                                                                        echo "<option value='$i' $selected>$i</option>";
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-auto">
                                                                            <button type="submit"
                                                                                class="btn btn-success me-2">🔍
                                                                                Search</button>
                                                                            <button type="button" id="holidayPdfBtn"
                                                                                class="btn btn-primary">📄 Download
                                                                                PDF</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>

                                                            <div class="col-md-12 mt-4">
                                                                <h4 class="mb-20">📅 Academic Holiday Calendar</h4>
                                                                <div class="table-responsive" id="holidayTable">
                                                                    <div class="alert alert-info">Please select a
                                                                        session to load holidays.</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab-admission-charges-tabs">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-12 col-xl-12">
                                            <div class="tab-left-part mb-lg-40">
                                                <h3 class="title mb-20 text-theme-colored1 text-center">Admission
                                                    Charges</h3>

                                                <div class="row mb-20">
                                                    <div>
                                                        <div>
                                                            <?php
                                                            include('include/dbcon.php');



                                                            // Get first class dynamically
                                                            $defaultClassId = 0;
                                                            $sql = "SELECT * FROM rrsv_class ORDER BY id LIMIT 1";
                                                            $res = mysqli_query($myDB, $sql);
                                                            if ($res && mysqli_num_rows($res) > 0) {
                                                                $firstClass = mysqli_fetch_assoc($res);
                                                                $defaultClassId = $firstClass['id'];
                                                            }
                                                            ?>

                                                            <div class="container pt-50 pb-50">
                                                                <form id="filterForm">
                                                                    <div class="row align-items-end g-3 mb-4">

                                                                        <!-- Session Dropdown -->
                                                                        <div class="col-md-3">
                                                                            <label for="scl_session"
                                                                                class="form-label">Session</label>
                                                                            <select name="scl_session" id="scl_session"
                                                                                class="form-control">
                                                                                <?php
                                                                                for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) {
                                                                                    $selected = ($i == $currentYear) ? "selected" : "";
                                                                                    echo "<option value='$i' $selected>$i</option>";
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>

                                                                        <!-- Class Dropdown -->
                                                                        <div class="col-md-3">
                                                                            <label for="scl_class"
                                                                                class="form-label">Class</label>
                                                                            <select name="scl_class"
                                                                                class="form-control" id="scl_class"
                                                                                required>
                                                                                <option value="">-Select a Class-
                                                                                </option>
                                                                                <?php
                                                                                $sql = "SELECT * FROM rrsv_class ORDER BY id";
                                                                                $res = mysqli_query($myDB, $sql);
                                                                                $firstClass = true; // Flag to select the first option by default
                                                                                while ($obj = mysqli_fetch_assoc($res)) {
                                                                                    $selected = $firstClass ? "selected" : "";
                                                                                    echo '<option value="' . htmlspecialchars($obj['class_name']) . '" ' . $selected . '>'
                                                                                        . htmlspecialchars($obj['class_name']) . '</option>';
                                                                                    $firstClass = false; // Only the first option gets selected
                                                                                }
                                                                                ?>
                                                                            </select>

                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label class="form-label">Fee
                                                                                Type:</label><br>
                                                                            <input type="radio" name="fee_type"
                                                                                value="admission" id="fee_admission"
                                                                                checked>
                                                                            <label for="fee_admission">Admission</label>

                                                                            <input type="radio" name="fee_type"
                                                                                value="readmission"
                                                                                id="fee_readmission">
                                                                            <label
                                                                                for="fee_readmission">Readmission</label>
                                                                        </div>


                                                                        <!-- Search Button -->
                                                                        <div class="col-md-2">
                                                                            <button type="submit"
                                                                                class="btn btn-primary w-100">Search</button>
                                                                        </div>
                                                                    </div>
                                                                </form>

                                                                <!-- Results will be loaded here -->
                                                                <div id="resultTable" class="mt-4"></div>
                                                            </div>




                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab-syllabus-tabs">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-12 col-xl-12">
                                            <div class="tab-left-part mb-lg-40">
                                                <h3 class="title mb-20 text-theme-colored4 text-center">Syllabus</h3>
                                                <div class="container">
                                                    <form id="syllabusForm">
                                                        <div class="row">
                                                            <!-- Session -->
                                                            <div class="col-md-2">
                                                                <div class="mt-3 mb-10">
                                                                    <select name="scl_session" class="form-control"
                                                                        id="scl_session">
                                                                        <option value="">-Select a Session-</option>
                                                                        <?php
                                                                        $selectedSession = $_POST['scl_session'] ?? date("Y"); // default to current year
                                                                        for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) {
                                                                            $sel = ($i == $selectedSession) ? 'selected' : '';
                                                                            echo "<option value='$i' $sel>$i</option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <!-- Class -->
                                                            <div class="col-md-2">
                                                                <div class="mt-3 mb-10">
                                                                    <select name="class_id" class="form-control">
                                                                        <option value="">Select Class</option>
                                                                        <?php
                                                                        $selectedClass = $_POST['class_id'] ?? 0;
                                                                        $classes = mysqli_query($myDB, "SELECT id, class_name FROM rrsv_class");
                                                                        while ($c = mysqli_fetch_assoc($classes)) {
                                                                            $sel = ($c['id'] == $selectedClass) ? 'selected' : '';
                                                                            echo "<option value='{$c['id']}' $sel>{$c['class_name']}</option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <!-- Buttons -->
                                                            <div class="col-md-1 mt-4">
                                                                <button type="submit"
                                                                    class="btn btn-primary">Search</button>
                                                            </div>

                                                            <div class="col-md-2 mt-4">
                                                                <button type="button" id="syllabusPdfBtn"
                                                                    class="btn btn-success">Download PDF</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <div class="row">
                                                        <div id="syllabusTable" class="mt-3"></div>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tm-floating-objects">
            <span class="z-index-1 bg-img-cover" data-tm-bg-img="images/bg/f2.png" data-tm-width="100%"
                data-tm-height="143" data-tm-top="auto" data-tm-bottom="0" data-tm-left="0" data-tm-right="0"
                data-tm-opacity="-100px"
                style="background-image: url(&quot;images/bg/f2.png&quot;); height: 143px; width: 100%; inset: auto 0px 0px;"></span>
        </div>
    </section>
    <!-- Section: Course -->
    <section id="courses" class="bg-img-cover bg-img-center" data-tm-bg-img="landing/images/bg/p2.jpg">
        <div class="container-fluid pb-90">
            <div class="section-title">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-xl-6">
                        <div class="tm-sc-section-title section-title text-center">
                            <div class="title-wrapper">
                                <h2 class="title">Our <span class="text-theme-colored3">Courses</span></h2>
                                <p>There are many variations of passages. But the majority have suffered
                                    alteration in some form, by
                                    injected humour, or randomised words.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="course-carousel owl-nav-outer">
                            <div class="owl-carousel owl-theme tm-owl-carousel-3col" data-nav="true"
                                data-autoplay="true" data-loop="true" data-duration="6000" data-smartspeed="300"
                                data-margin="10" data-stagepadding="0">

                                <?php
                                // Fetch classes
                                $sql = "SELECT id, class_name FROM rrsv_class ORDER BY id ASC";
                                $result = mysqli_query($myDB, $sql);

                                if ($result && mysqli_num_rows($result) > 0) {
                                    $images = [
                                        "landing/images/project/12.jpg",
                                        "landing/images/project/13.jpg",
                                        "landing/images/project/14.jpg",
                                        "landing/images/project/15.jpg"
                                    ];
                                    $i = 0;

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $classId = $row['id'];
                                        $className = $row['class_name'];

                                        // cycle through images
                                        $img = $images[$i % count($images)];
                                        $i++;
                                        ?>
                                        <div class="tm-carousel-item">
                                            <div class="course">
                                                <div class="thumb">
                                                    <img class="img-fullwidth" src="<?php echo $img; ?>"
                                                        alt="<?php echo $className; ?>">
                                                    <div class="course-overlay"></div>
                                                </div>
                                                <div class="course-details clearfix p-20 pt-15">
                                                    <h5 class="price-tag">Class ID: <?php echo $classId; ?></h5>
                                                    <h4 class="mt-0 mb-0">
                                                        <a class="text-theme-colored1" href="#">
                                                            <?php echo $className; ?>
                                                        </a>
                                                    </h4>
                                                    <ul class="review_text">
                                                        <li class="list-inline-item">
                                                            <div class="star-rating" title="Rated 5.00 out of 5">
                                                                <span data-width="100%">5.00</span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                    <p class="mb-10">
                                                        This is the <?php echo $className; ?> section of our school.
                                                    </p>
                                                    <div class="course-details-bottom mt-15">
                                                        <ul>
                                                            <li class="list-inline-item">Capacity <br>20 kids</li>
                                                            <li class="list-inline-item">Duration <br>45 min</li>
                                                            <li class="list-inline-item">Age <br>5y - 6y</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo "<p class='text-center'>No classes found.</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- Section: Gallery -->
    <section id="gallery">
        <div class="container pb-90">
            <div class="section-title">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-xl-6">
                        <div class="tm-sc-section-title section-title text-center">
                            <div class="title-wrapper">
                                <h2 class="title">Our <span class="text-theme-colored3">Gallery</span></h2>
                                <p>There are many variations of passages. But the majority have suffered alteration in
                                    some form, by injected humour, or randomised words.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-content">
                <div class="row">
                    <?php
                    $sql = "SELECT id, title, image_path 
                    FROM rrsv_gallery 
                    WHERE status='Active' AND category='Gallery' 
                    ORDER BY uploaded_on DESC";
                    $res = mysqli_query($myDB, $sql);

                    if ($res && mysqli_num_rows($res) > 0) {
                        $delay = 0.1;
                        $colors = ["", "red", "yellow", "green", "blue", "sky"]; // overlay colors
                        $i = 0;

                        while ($row = mysqli_fetch_assoc($res)) {
                            $title = !empty($row['title']) ? $row['title'] : "Gallery Image";
                            $img = BASE_URL . $row['image_path'];

                            $colorClass = $colors[$i % count($colors)];
                            $i++;
                            ?>
                            <div class="col-sm-6 col-lg-4 " data-wow-duration="1s" data-wow-delay="<?php echo $delay; ?>s">
                                <div class="gallery-block">
                                    <div class="gallery-thumb">
                                        <img alt="<?php echo htmlspecialchars($title); ?>" src="<?php echo $img; ?>"
                                            class="img-fullwidth" style="height:250px;object-fit:cover;">
                                    </div>
                                    <div class="overlay-shade <?php echo $colorClass; ?>"></div>
                                    <div class="icons-holder">
                                        <div class="icons-holder-inner">
                                            <div class="gallery-icon">
                                                <a href="<?php echo $img; ?>" data-lightbox-gallery="gallery">
                                                    <i class="pe-7s-science"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $delay += 0.2;
                        }
                    } else {
                        echo "<p class='text-center'>No gallery images found.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>


    <!-- Section: Team -->
    <section id="teacher" data-tm-bg-color="#f9f9f9" style="background-color: rgb(249, 249, 249) !important;">
        <div class="container pb-90">
            <div class="section-title">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-xl-6">
                        <div class="tm-sc-section-title section-title text-center">
                            <div class="title-wrapper">
                                <h2 class="title">Our <span class="text-theme-colored3">Teachers</span></h2>
                                <p>There are many variations of passages. But the majority have suffered alteration in
                                    some form, by injected humour, or randomised words.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-content">
                <div class="row">
                    <?php
                    $sql = "SELECT id, full_name, designation, image, status 
                        FROM rrsv_teacher 
                        WHERE status='active' 
                        ORDER BY id ASC";
                    $result = mysqli_query($myDB, $sql);

                    if ($result && mysqli_num_rows($result) > 0) {
                        $delay = 0.1;
                        $colors = ["bg-theme-colored1", "bg-theme-colored2", "bg-theme-colored3", "bg-theme-colored4"];
                        $i = 0;

                        while ($row = mysqli_fetch_assoc($result)) {
                            $teacherName = $row['full_name'];
                            $designation = !empty($row['designation']) ? $row['designation'] : "Teacher";
                            $image = !empty($row['image']) ? BASE_URL . "teacher_image/" . $row['image'] : "<?php echo BASE_URL?>landing/images/team/default.jpg";

                            // cycle through 4 theme colors
                            $colorClass = $colors[$i % count($colors)];
                            $i++;
                            ?>
                            <div class="col-sm-6 col-xl-3">
                                <div class="team-member">
                                    <div class="team-thumb">
                                        <img class="img-fullwidth" src="<?php echo $image; ?>" alt="<?php echo $teacherName; ?>"
                                            style="height: 180px;">
                                    </div>
                                    <div class="team-details text-center <?php echo $colorClass; ?>">
                                        <div class="member-biography mb-15">
                                            <p class="mt-0 text-white mb-0"><?php echo $teacherName; ?></p>
                                            <p class="mb-0 text-white"><?php echo $designation; ?></p>
                                        </div>
                                        <div class="text-center">
                                            <ul class="styled-icons icon-dark icon-sm icon-theme-colored3 icon-circled">
                                                <li><a href="#"><i class="fab fa-facebook text-white"></i></a></li>
                                                <li><a href="#"><i class="fab fa-twitter text-white"></i></a></li>
                                                <li><a href="#"><i class="fab fa-dribbble text-white"></i></a></li>

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $delay += 0.2; // increment animation delay
                        }
                    } else {
                        echo "<p class='text-center'>No teachers found.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Why Chose Us -->
    <section id="why">
        <div class="container" data-tm-padding-bottom="50px">
            <div class="row">
                <div class="col-lg-6 col-xl-5 m-lg-auto">
                    <div class="whychose-thumb">
                        <img class="img-fullwidth" src="landing/images/photos/3.png" alt="WhyChoseImage">
                    </div>
                </div>
                <div class="col-xl-7 pl-50">
                    <h2 class="title line-bottom mb-20 mt-0">Why <span class="text-theme-color-red">Choose
                            Us</span> ?</h2>
                    <p class="mb-50">The Cweren Law Firm is a recognized leader in landlord tenant
                        representation throughout
                        Texas.The largests professional property management companies the region.The largest
                        professional
                        property management companies is a recognized leader in landlord tenant representation
                        throughout Texas
                    </p>
                    <div class="row">
                        <div class="col-sm-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s">
                            <div class="icon-box text-center">
                                <a href="#"
                                    class="icon bg-theme-colored3 icon-circled icon-border-effect effect-circle icon-md">
                                    <i class="fas fa-bell text-white"></i>
                                </a>
                                <h5 class="icon-box-title mt-15 mb-0 letter-space-1 text-uppercase">Responsive
                                </h5>
                            </div>
                        </div>
                        <div class="col-sm-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s">
                            <div class="icon-box text-center">
                                <a href="#"
                                    class="icon bg-theme-colored2 icon-circled icon-border-effect effect-circle icon-md">
                                    <i class="fas fa-pencil-alt text-white"></i>
                                </a>
                                <h5 class="icon-box-title mt-15 mb-0 letter-space-1 text-uppercase">Validation
                                </h5>
                            </div>
                        </div>
                        <div class="col-sm-4 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.5s">
                            <div class="icon-box text-center">
                                <a href="#"
                                    class="icon bg-theme-colored4 icon-circled icon-border-effect effect-circle icon-md">
                                    <i class="fas fa-certificate text-white"></i>
                                </a>
                                <h5 class="icon-box-title mt-15 mb-0 letter-space-1 text-uppercase">
                                    Certification</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tm-floating-objects">
            <span class="z-index-1 bg-img-cover" data-tm-bg-img="landing/images/bg/f2.png" data-tm-width="100%"
                data-tm-height="143" data-tm-top="auto" data-tm-bottom="0" data-tm-left="0" data-tm-right="0"
                data-tm-opacity="-100px"></span>
        </div>
    </section>



</div>
<!-- Button to Open Admission Form Modal -->


<!-- Admission Form Modal -->
<div class="modal fade" id="admissionModal" tabindex="-1" aria-labelledby="admissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content bg-theme-colored2 text-white">
            <div class="modal-header">
                <h5 class="modal-title" id="admissionModalLabel">Admission Form</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="admission_form" name="admission_form"
                    action="<?= BASE_URL ?>admin/admission/add_admission_post.php" method="post"
                    enctype="multipart/form-data">
                    <div class="row">

                        <!-- Admission Info -->
                        <div class="col-sm-6 mb-3">
                            <label>Admission Date</label>
                            <input type="date" name="scl_date" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Admission for Class</label>
                            <select name="scl_class" class="form-control" id="scl_class" required>
                                <option value="">-Select a Class-</option>
                                <?php
                                $rows = 0;
                                $id = 0;
                                $sql = "select * from rrsv_class order by id";
                                $res = mysqli_query($myDB, $sql);
                                while ($obj = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                                    ?>
                                    <option value="<?php echo $obj['class_name']; ?>">
                                        <?php echo $obj['class_name']; ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Session</label>
                            <select class="form-control" name="scl_session" id="scl_session" required>
                                <option value="">-Select a Session -</option>
                                <?php
                                for ($i = date("Y") - 3; $i <= date("Y") + 10; $i++) {
                                    ?>
                                    <option value="<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </option>
                                    <?php
                                }

                                ?>

                            </select>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Section</label>
                            <select class="form-control" name="scl_section" id="scl_section" required>

                                <option value="">-Select a Section-</option>
                                <?php
                                $id = 0;
                                $sql = "select * from  rrsv_section group by section_name  order by id ";
                                $res = mysqli_query($myDB, $sql);
                                while ($obj = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                                    ?>
                                    <option value="<?php echo $obj['section_name']; ?>">
                                        <?php echo $obj['section_name']; ?>
                                    </option>
                                    <?php
                                }
                                ?>

                            </select>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Roll No</label>
                            <input type="text" name="scl_roll_no" class="form-control">
                        </div>

                        <!-- Personal Info -->
                        <div class="col-sm-12 mt-3">
                            <h5>Personal Information</h5>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Full Name</label>
                            <input type="text" name="scl_name" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Student Aadhaar No</label>
                            <input type="text" name="scl_aadhar" class="form-control">
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Phone No</label>
                            <input type="text" name="scl_phone_no" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Alternate / WhatsApp No</label>
                            <input type="text" name="alt_phone" class="form-control">
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Father's Name</label>
                            <input type="text" name="scl_father_name" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Mother's Name</label>
                            <input type="text" name="scl_mother_name" class="form-control">
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Gender</label>
                            <select name="scl_gender" class="form-control" required>
                                <option value="">Select</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>DOB</label>
                            <input type="date" name="scl_dob" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Caste</label>
                            <input type="text" name="scl_religion" class="form-control">
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Mother Tongue</label>
                            <input type="text" name="scl_language" class="form-control">
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Religion</label>
                            <input type="text" name="religion" class="form-control">
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Nationality</label>
                            <input type="text" name="nationality" class="form-control" value="Indian">
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Disability (if any)</label>
                            <input type="text" name="scl_disa" class="form-control">
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="scl_email" class="form-control">
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Identification Mark</label>
                            <input type="text" name="scl_ide" class="form-control">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label>BPL Status and Number:</label>
                            <input type="text" name="scl_bpl" class="form-control">
                        </div>

                        <!-- Address Info -->
                        <div class="col-sm-12 mt-3">
                            <h5>Address Information</h5>
                        </div>

                        <div class="col-sm-6 mb-3"><label>Village</label>
                            <input type="text" name="scl_address" class="form-control">
                        </div>
                        <div class="col-sm-6 mb-3"><label>Post Office</label><input type="text" name="scl_pos"
                                class="form-control"></div>
                        <div class="col-sm-6 mb-3"><label>Police Station</label><input type="text" name="scl_pol"
                                class="form-control"></div>
                        <div class="col-sm-6 mb-3"><label>Gram Panchayat / Municipality</label><input type="text"
                                name="scl_mu" class="form-control"></div>
                        <div class="col-sm-6 mb-3"><label>District</label><input type="text" name="scl_dist"
                                class="form-control"></div>
                        <div class="col-sm-6 mb-3"><label>Block</label><input type="text" name="scl_block"
                                class="form-control"></div>
                        <div class="col-sm-6 mb-3"><label>State</label><input type="text" name="scl_state"
                                class="form-control"></div>
                        <div class="col-sm-6 mb-3"><label>Pin</label><input type="text" name="scl_pin"
                                class="form-control">
                        </div>
                        <div class="col-sm-6 mb-3"><label>Locality</label><input type="text" name="scl_location"
                                class="form-control"></div>

                        <!-- Bank Info -->
                        <div class="col-sm-12 mt-3">
                            <h5>Bank Information</h5>
                        </div>
                        <div class="col-sm-6 mb-3"><label>Bank Account No</label><input type="text" name="bank_ac_no"
                                class="form-control"></div>
                        <div class="col-sm-6 mb-3"><label>Bank IFSC</label><input type="text" name="bank_ifsc_code"
                                class="form-control"></div>
                        <div class="col-sm-6 mb-3"><label>Bank Branch</label><input type="text" name="branch_name"
                                class="form-control"></div>

                        <!-- File Uploads -->
                        <div class="col-sm-6 mb-3"><label>Upload Photo</label><input type="file" name="image"
                                class="form-control"></div>
                        <div class="col-sm-6 mb-3"><label>Aadhaar / DOB Certificate</label><input type="file"
                                name="aadhar_image" class="form-control"></div>

                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="admission_form" class="btn btn-theme-colored4">Submit Application</button>
            </div>
        </div>
    </div>
</div>


<script>
    function downloadRoutinePDF() {
        let element = document.getElementById("routineContainer");

        // check if element exists
        if (!element) {
            alert("Routine container not found!");
            return;
        }

        let opt = {
            margin: 0.5,
            filename: 'Routine.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(element).save();
    }
</script>
<script>
    $(document).ready(function () {
        // Initialize Menuzord safely
        if ($.fn.menuzord) {
            $("#menuzord").menuzord();
        }

        // AJAX form submission
        $('#filterForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: '<?php echo BASE_URL; ?>admin/admission/admission_fees.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function (data) {
                    // Inject HTML response into the table container
                    $('#resultTable').html(data);
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    $('#resultTable').html('<div class="alert alert-danger">Error loading data</div>');
                }
            });
        });

        // Trigger default load on page ready
        $('#filterForm').trigger('submit');

        // PDF download using event delegation
        $(document).on('click', '#downloadPdfBtn', function () {
            const params = $('#filterForm').serialize();
            window.open('<?php echo BASE_URL; ?>admin/admission/admission_fee_pdf.php?' + params, '_blank');
        });
    });
</script>
<script>
    $(document).ready(function () {
        // AJAX search
        $('#syllabusForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo BASE_URL; ?>admin/syllabus/syllabus_ajax.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function (data) {
                    $('#syllabusTable').html(data);
                },
                error: function (xhr, status, error) {
                    $('#syllabusTable').html('<div class="alert alert-danger">Error loading data</div>');
                    console.error("AJAX Error:", status, error);
                }
            });
        });

        // Trigger initial load
        $('#syllabusForm').trigger('submit');

        // PDF download
        $('#syllabusPdfBtn').on('click', function () {
            const params = $('#syllabusForm').serialize();
            window.open('<?php echo BASE_URL; ?>admin/syllabus/download_syllabus.php?' + params, '_blank');
        });
    });
</script>
<script>
    $(document).ready(function () {
        // AJAX form submit for holiday list
        $('#holidayForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: '<?php echo BASE_URL; ?>admin/holiday/holiday_fetch.php',
                type: 'POST',
                data: $(this).serialize(),
                beforeSend: function () {
                    $('#holidayTable').html('<div class="text-center p-3">Loading...</div>');
                },
                success: function (data) {
                    $('#holidayTable').html(data);
                },
                error: function (xhr, status, error) {
                    $('#holidayTable').html('<div class="alert alert-danger">Error loading data</div>');
                    console.error("AJAX Error:", status, error);
                }
            });
        });
         $('#holidayForm').trigger('submit');

        // PDF download button
        $('#holidayPdfBtn').on('click', function () {
            const params = $('#holidayForm').serialize();
            window.open('<?php echo BASE_URL; ?>admin/holiday/holiday_pdf.php?' + params, '_blank');
        });
    });
</script>
<script>
    $(document).ready(function () {
        // AJAX form submit for routine list
        $('#routineForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: '<?php echo BASE_URL; ?>admin/routine/routine_fetch.php',
                type: 'POST',
                data: $(this).serialize(),
                beforeSend: function () {
                    $('#routineTable').html('<div class="text-center p-3">Loading...</div>');
                },
                success: function (data) {
                    $('#routineTable').html(data);
                },
                error: function (xhr, status, error) {
                    $('#routineTable').html('<div class="alert alert-danger">Error loading routine</div>');
                    console.error("AJAX Error:", status, error);
                }
            });
        });

        // Auto-trigger load on page ready
        $('#routineForm').trigger('submit');

        // PDF download button
        $('#routinePdfBtn').on('click', function () {
            const params = $('#routineForm').serialize();
            window.open('<?php echo BASE_URL; ?>admin/routine/routine_pdf.php?' + params, '_blank');
        });
    });
</script>

<?php include 'front_end/footer.php'; ?>

<script>

    var loadFile = function (event) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function () {
            URL.revokeObjectURL(output.src) // free memory
        }
    };

    if ('<?php echo $image; ?>' != "") {
        var output = document.getElementById('output');
        output.src = 'http://rasulpuranathsamitykgschool.com/template/teacher_image/' + '<?php echo $image; ?>';
    }
    $(document).ready(function () {
        $('select[name="gender"]').val('<?php echo $gender; ?>');
        $('select[name="designation"]').val('<?php echo $designation; ?>');

        $('#form_reset').click(function () {
            $('#form_submit')[0].reset();
        });

    });

    (function ($) {
        'use strict';
        $(function () {
            $('.file-upload-browse').on('click', function () {
                var file = $(this).parent().parent().parent().find('.file-upload-default');
                file.trigger('click');
            });
            $('.file-upload-default').on('change', function () {
                $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
            });
        });
    })(jQuery);

    // $('form').submit(function(e){
    //     e.preventDefault();
    //     //alert(JSON.stringify($('form').serialize()));
    //     //return false;
    //     var full_name = $('#full_name').val();
    //     var fathers_name = $('#fathers_name').val();
    //     var gender_name = $('#gender_name').val();
    //     var d_o_b = $('#d_o_b').val();
    //     var address = $('textarea#address').val();

    //     if(full_name == ''){
    //         $('#full_name_error').html('Please Enter Full Name.');
    //     }
    //     if(fathers_name == ''){
    //         $('#fathers_name_error').html('Please Enter Fathers Name.');
    //     }
    //     if(gender_name == ''){
    //         $('#gender_name_error').html('Please Enter Gender Name.');
    //     }
    //     if(d_o_b == ''){
    //         $('#d_o_b_error').html('Please Enter Date Of Birth Name.');
    //     }
    //     if(address == ''){
    //         $('#address_error').html('Please Enter Address.');
    //     }
    //     // if(fathers_name == ''){
    //     //   $('#fathers_name_error').html('Please Enter Fathers Name.');
    //     // }
    //     // if(full_name == ''){
    //     //   $('#full_name_error').html('Please Enter Full Name.');
    //     // }
    //     // if(fathers_name == ''){
    //     //   $('#fathers_name_error').html('Please Enter Fathers Name.');
    //     // }
    //     // if(full_name == ''){
    //     //   $('#full_name_error').html('Please Enter Full Name.');
    //     // }
    //     // if(fathers_name == ''){
    //     //   $('#fathers_name_error').html('Please Enter Fathers Name.');
    //     // }

    //     if(full_name !="" && fathers_name !="" && gender_name !="" && d_o_b !="" && address !="") {
    //         //call_ajax_submit();
    //         $('form').submit();
    //     }
    //     //return false;


    // });
    // 
    function call_ajax_submit() {
        //var property = document.getElementById('photo').files[0];

        $.ajax({
            type: "POST",
            url: "<?= BASE_URL ?>admission/add_admission_post.php",
            data: { file: 1, data: $('form').serialize() },
            beforeSend: function () {
                $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2 disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Submiting...</button>');
            },
            success: function (data) {
                // alert(data);
                if (data == 1) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Teacher Information  Update successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2">Submit</button>');
                }
                if (data == 2) {
                    $('#alert').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Teacher Information  Insert successfully!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2">Submit</button>');
                }
                if (data == 3) {
                    $('#alert').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Sory duplicate Entry!</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2">Submit</button>');
                }
            }
        });
    }


</script>
<script type="text/javascript">
    $(document).ready(function () {
        // Trigger on change of session or class dropdown
        $('#scl_session, #scl_class').change(function () {
            //alert(1);
            const session = $('#scl_session').val();
            const className = $('#scl_class').val();

            if (session && className) {
                // Make an AJAX call
                $.ajax({
                    url: 'fetch_names.php',
                    type: 'POST',
                    data: {
                        session: session,
                        class: className
                    },
                    dataType: 'json',
                    success: function (response) {
                        // Populate the scl_name dropdown
                        let nameDropdown = $('#email');
                        nameDropdown.empty(); // Clear existing options
                        nameDropdown.append('<option value="">-Select a Name-</option>');

                        if (response.length > 0) {
                            response.forEach(function (item) {
                                nameDropdown.append(`<option value="${item.name}">${item.name}</option>`);
                            });
                        } else {
                            nameDropdown.append('<option value="">No names available</option>');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ', error);
                    }
                });
            }
        });
    });

    $(document).ready(function () {

        $('#scl_class').change(function () {

            var scl_class = $('#scl_class').val();

            $.ajax({

                url: 'section.php',

                type: 'POST',

                data: 'scl_class=' + scl_class,

                success: function (result) {

                    $('#scl_section').html(result);

                    // $('#scl_section').selectpicker('refresh');

                }

            });

        });

        $('#scl_section').change(function () {
            var scl_class = $('#scl_class').val();
            var scl_section = $('#scl_section').val();
            var scl_session = $('#scl_session').val();
            $.ajax({
                url: 'roll-ajax.php',
                type: 'POST',
                data: { scl_class: scl_class, scl_section: scl_section, scl_session: scl_session },
                success: function (result) {
                    $('#scl_roll_no').val(result);
                }

            });

        });


    });

    function dateDiff(startingDate, endingDate) {
        var startDate = new Date(new Date(startingDate).toISOString().substr(0, 10));
        if (!endingDate) {
            endingDate = new Date().toISOString().substr(0, 10);    // need date in YYYY-MM-DD format
        }
        var endDate = new Date(endingDate);
        if (startDate > endDate) {
            var swap = startDate;
            startDate = endDate;
            endDate = swap;
        }
        var startYear = startDate.getFullYear();
        var february = (startYear % 4 === 0 && startYear % 100 !== 0) || startYear % 400 === 0 ? 29 : 28;
        var daysInMonth = [31, february, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        var yearDiff = endDate.getFullYear() - startYear;
        var monthDiff = endDate.getMonth() - startDate.getMonth();
        if (monthDiff < 0) {
            yearDiff--;
            monthDiff += 12;
        }
        var dayDiff = endDate.getDate() - startDate.getDate();
        if (dayDiff < 0) {
            if (monthDiff > 0) {
                monthDiff--;
            } else {
                yearDiff--;
                monthDiff = 11;
            }
            dayDiff += daysInMonth[startDate.getMonth()];
        }

        return yearDiff + 'Year ' + monthDiff + 'Month ' + dayDiff + 'Day';
    }


    var end = new Date().toISOString().slice(0, 10);

    $('#date').change(function () {
        var start = $('#date').val();
        document.getElementById('agecal').innerText = dateDiff(start, end);

    });
</script>
<script>

    var loadFile = function (event) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function () {
            URL.revokeObjectURL(output.src) // free memory
        }
        if ('<?php echo $aadhar_image; ?>' != "") {
            //alert('<?php echo $aadhar_image; ?>');
            var output = document.getElementById('output');
            output.src = 'http://' + '<?php echo $host_name; ?>' + '/student_aadhar/' + '<?php echo $aadhar_image; ?>';
        }
    }
</script>
<script>

    //  var img_upload = function(event) {
    // var output = document.getElementById('img_upld');
    // output.src = URL.createObjectURL(event.target.files[0]);
    // output.onload = function() {
    //   URL.revokeObjectURL(output.src) // free memory
    // }
    function img_upload(event) {
        var output = document.getElementById('img_upld');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function () {
            URL.revokeObjectURL(output.src) // free memory
        }
    }

    if ('<?php echo $image; ?>' != "") {
        var output = document.getElementById('img_upld');
        output.src = 'http://' + '<?php echo $host_name; ?>' + '/student_reg_image/' + '<?php echo $image; ?>';
    }
</script>