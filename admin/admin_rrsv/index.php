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
function formatTime($timeStr)
{
    // Convert HH:MM to 12-hour format with am/pm and dot between hours/minutes
    $timestamp = strtotime($timeStr);
    return strtolower(date('g.i a', $timestamp));
}
// Fetch Banner images
$bannerQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='Banner' ORDER BY uploaded_on DESC LIMIT 6";
$bannerRes = mysqli_query($myDB, $bannerQuery);

// Fetch Gallery images
$galleryQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='Gallery' ORDER BY uploaded_on DESC LIMIT 6";
$galleryRes = mysqli_query($myDB, $galleryQuery);

// Teacher
$teacherQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='Teacher' ORDER BY uploaded_on DESC";
$teacherRes = mysqli_query($myDB, $teacherQuery);

// Class
$classQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='Class' ORDER BY uploaded_on DESC LIMIT 6";
$classRes = mysqli_query($myDB, $classQuery);

// Contact
$contactQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='Contact' ORDER BY uploaded_on DESC LIMIT 1";
$contactRes = mysqli_query($myDB, $contactQuery);

// About
$aboutQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='About' ORDER BY uploaded_on DESC LIMIT 1";
$aboutRes = mysqli_query($myDB, $aboutQuery);

// Bank
$bankQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='Bank' ORDER BY uploaded_on DESC LIMIT 1";
$bankRes = mysqli_query($myDB, $bankQuery);
$bankRow = mysqli_fetch_assoc($bankRes);
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
    die("Query Failed1: " . mysqli_error($myDB));

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
if (!empty($_POST['unit']))
    $s_where[] = "s.unit=" . (int) $_POST['unit'];
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

// Fetch visible tables for this role
$can_display = "SELECT * FROM rrsv_permission";
$permission = mysqli_query($myDB, $can_display);

$permissions = [];
while ($row = mysqli_fetch_assoc($permission)) {
    $permissions[$row['table_name']] = [
        'view' => $row['can_view'] === 'Yes',
        'insert' => $row['can_insert'] === 'Yes',
        'update' => $row['can_update'] === 'Yes',
        'delete' => $row['can_delete'] === 'Yes',
        'landing' => $row['can_display_landing'] === 'Yes',
        'status' => $row['status']
    ];
}


$opening = "
    SELECT day_of_week, 
           MIN(start_time) AS open_time, 
           MAX(end_time) AS close_time
    FROM rrsv_routine
    WHERE session = '$currentYear'
    GROUP BY day_of_week
    ORDER BY FIELD(day_of_week, 
        'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
";

$opening_result = mysqli_query($myDB, $opening);
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
                                            <span class="text-theme-coloblue3"></span>
                                        </rs-layer>
                                        <rs-layer id="slider-2-slide-2-layer-22" data-type="text" data-rsp_ch="on"
                                            data-xy="x:l,l,c,c;xo:60px,0px,-225px,0px;yo:510px,410px,380px,360px;"
                                            data-text="w:normal;s:20,16,16,13;l:22,20,18,20;a:left,left,left,center;"
                                            data-frame_0="y:-50,-37,-28,-17;" data-frame_1="st:1700;sp:1000;sR:1700;"
                                            data-frame_999="o:0;st:w;sR:6300;" style="z-index:10;"
                                            class="font-current-theme1">
                                            <!-- <a href="#footer" class="btn btn-theme-colored4">Online Admission</a> -->
                                            <div class="text-center mb-4 btn-mobile">
                                                <button type="button" class="btn btn-theme-colored4" data-bs-toggle="modal"
                                                    data-bs-target="#checkCodeModal">
                                                    📝 Apply for Admission
                                                </button>
                                            </div>
                                            <!-- <div class="text-center mb-4 btn-mobile">
                                                <button type="button" class="btn btn-theme-colored4" data-bs-toggle="modal"
                                                    data-bs-target="#admissionModal">
                                                    📝 Apply for Admission
                                                </button>
                                            </div> -->
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
                            <script
                                src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

                            <rs-static-layers>
                                <div class="container p-0">
                                    <div class="row align-items-center justify-content-between">

                                        <!-- 🔹 LEFT SIDE: Apply Button -->
                                        <div class="col-md-5 text-center text-md-start mb-4 mb-md-0">
                                            <!-- <button type="button" class="btn btn-theme-colored4 btn-lg shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#checkCodeModal"
                                                style="cursor:pointer;">
                                                📝 Apply for Admission
                                            </button> -->
                                        </div>


                                        <!-- 🔹 RIGHT SIDE: Notices -->
                                        <div class="col-md-6 mt-5 me-5">
                                            <div class="bg-opacity-75 p-3 rounded-3 shadow-sm outline-border-5px"
                                                style="background-color: rgba(255,255,255,0.7); padding:5px; border-radius:10px;">
                                                <h4 class="text-center text-theme-colored3 mb-3">Latest Notices</h4>

                                                <div class="notice-marquee"
                                                    style="max-height: 220px; overflow-y: auto;">
                                                    <ul class="list-unstyled mb-0">
                                                        <?php if (mysqli_num_rows($notices) > 0): ?>
                                                            <?php while ($notice = mysqli_fetch_assoc($notices)): ?>
                                                                <li class="mb-3">
                                                                    <strong>📢
                                                                        <?php echo htmlspecialchars($notice['title']); ?></strong><br>
                                                                    <small><i><?php echo date("d M Y", strtotime($notice['notice_date'])); ?></i></small>
                                                                    <p><?php echo htmlspecialchars($notice['description']); ?>
                                                                    </p>
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
    <!-- <section class="divider" data-tm-bg-img="landing/images/bg/b1.png" data-tm-margin-top="-34px">
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
    </section> -->

    <!-- Section: About Us -->
    <section id="about">
        <div class="container" data-tm-padding-bottom="20px" style="padding-bottom: 20px;">
            <div class="section-content">
                <div class="row">
                    <div class="col-lg-6 col-xl-8">
                        <div class="about-box-contents">
                            <div class="destails">
                                <h3 style="color: #140bd7 !important;;margin-left: 10px;font-size: 21px;font-family: 'Bebas Neue', sans-serif;font-weight:400;"
                                    class="text-center">
                                    Welcome To RASULPUR RAMKRISHNA SARADA VIDYAPITH</h3>
                                <h4
                                    style="margin-left: 10px;font-size: 21px;font-family: 'Merriweather', sans-serif;font-weight:400;">
                                    World Best Education In Our RASULPUR RAMKRISHNA SARADA VIDYAPITH
                                </h4>
                                <p>
                                    Considering English as the most widely spoken language across the world we ventured
                                    setting up the R.R.S.V. way back in the year 2012 as the pioneer in and around our
                                    locality .Our sole purpose is to foster students to gain fluency in reading, writing
                                    and speaking English keeping pace with the global trend. This skill eventually opens
                                    doors to higher education better job opportunities and international exposure. It
                                    also bridges the communication gap between the people of different regions and
                                    countries. Thus this education plays a pivotal role in shaping students future
                                    prospect.

                                    We are privileged to have a strong and dedicated teaching faculty which ensures
                                    quality of teaching a comprehensive extracurricular activities a nurturing and
                                    convenient environment and initiatives to foster holistic development of young
                                    minds. Thus, we have been enjoying the patronage and support of the guardians
                                    throughout our journey.

                                </p>

                            </div>
                        </div>

                        <div class="row">
                            <?php
                            $features = [
                                ['icon' => 'pe-7s-scissors', 'title' => 'Active Learning', 'color' => 'theme-colored3', 'description' => ' "Build confidence through various activity". Students actively participate in classroom activities, group discussions, and creative projects that make learning fun and meaningful.'],
                                ['icon' => 'pe-7s-pen', 'title' => 'Funny and Happy', 'color' => 'theme-colored2', 'description' => 'Learning laughing and loving every moment and Let\'s make this school year unforgettable.'],
                                ['icon' => 'pe-7s-phone', 'title' => 'Fulfill With Love', 'color' => 'theme-colored4', 'description' => 'My real smile comes out when I am with school.'],
                                ['icon' => 'pe-7s-light', 'title' => 'Expert Teachers', 'color' => 'theme-colored1', 'description' => 'Good teachers are the ones who can challenge your minds without losing their own.'],
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
                                                    <p><?= $f['description'] ?></p>
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
                                <?php
                                $i = 1;
                                while ($about = mysqli_fetch_assoc($aboutRes)) { ?>
                                    <img class="img-fullwidth" src="<?= BASE_URL . $about['image_path']; ?>" alt="a1.png">
                                    <?php
                                    $i++;
                                } ?>
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
                                <p class="text-left">There are many variations of passages. But the majority have
                                    suffered alteration in
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
                                            class="fas fa-graduation-cap"></i>
                                    </a></div>
                                <div class="icon-text">
                                    <h5 class="icon-box-title text-white">Qualified Teachers</h5>
                                    <div class="content">
                                        <p class="text-white">An experience teacher is a great artist who always
                                            inspires students and teachers are eagerly learning the great knowledge.
                                        </p>
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
                                        <p class="text-white">We maintain regular classes and punctual schedules.
                                            Discipline and consistency help our students develop good habits .</p>
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
                                        <p class="text-white">The class rooms are transcendental where students and
                                            teachers are eagerly learning the great knowledge. </p>
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
                                        <p class="text-white">Creative ideas enable children to experiment with thought
                                            process and can provide good opportunities for problem solving.</p>
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
                                        <p class="text-white pb-10" style="height: 115px;">Our bus/cab service is coming
                                            soon.

                                        </p>
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
                                        <p class="text-white" style="height: 115px;">Sportsmanship grow strong and
                                            teamwork makes the dream
                                            work.</p>
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
        <div class="container-fluid pb-md-10" data-tm-padding-bottom="20px" style="padding-bottom: 10px;">
            <div class="section-title">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-xl-6">
                        <div class="tm-sc-section-title section-title text-center">
                            <div class="title-wrapper">
                                <h2 class="title">Our <span class="text-theme-colored3">Informations</span></h2>
                                <p class="text-left">At Rasulpur Ramkrishna Sarada Vidyapith, we maintain full
                                    transparency with our students and parents. All important updates such as school
                                    notices, class routines, holidays, admission details, and syllabus information are
                                    regularly updated and easily accessible.</p>
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
                                        data-bs-toggle="tab">
                                        <i class="fas fa-book"></i>
                                        <span>Syllabus</span> </a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade in active show" id="tab-notices-tabs">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-12 col-xl-12">
                                            <div class="tab-left-part mb-lg-40">
                                                <h3 class="title mb-20 text-theme-colored1 text-center">Notices</h3>
                                                <?php if (!empty($permissions['rrsv_notice']['landing'])) { ?>
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
                                                <?php } ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab-routine-tabs">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-12 col-xl-12">
                                            <div class="tab-left-part mb-lg-4">
                                                <h3 class="title mb-20 text-theme-colored3 text-center">Routine list
                                                </h3>
                                                <?php if (!empty($permissions['rrsv_routine']['landing'])) { ?>
                                                    <div class="pt-1 pb-1">
                                                        <div class="container">
                                                            <div class="row">
                                                                <div class="table-responsive shadow-lg">
                                                                    <form id="routineForm" class="row g-3">
                                                                        <!-- Session -->
                                                                        <div class="col-md-3 mt-4">
                                                                            <select name="scl_session" id="scl_session"
                                                                                class="form-control">
                                                                                <?php
                                                                                $currentYear = date('Y');
                                                                                echo "<option value='$currentYear' selected>$currentYear</option>";
                                                                                for ($y = $currentYear - 1; $y >= ($currentYear - 5); $y--) {
                                                                                    echo "<option value='$y'>$y</option>";
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>

                                                                        <!-- Class -->
                                                                        <div class="col-md-3 mt-4">
                                                                            <select name="class_id" id="class_id"
                                                                                class="form-control" required>
                                                                                <?php
                                                                                $firstClass = true;
                                                                                $class_q = mysqli_query($myDB, "SELECT id, class_name FROM rrsv_class ORDER BY class_name");
                                                                                while ($r = mysqli_fetch_assoc($class_q)) {
                                                                                    $selected = $firstClass ? 'selected' : '';
                                                                                    echo '<option value="' . $r['id'] . '" ' . $selected . '>' . htmlspecialchars($r['class_name']) . '</option>';
                                                                                    $firstClass = false;
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>

                                                                        <!-- Buttons -->
                                                                        <div class="col-md-6 text-center mt-4">
                                                                            <button type="submit"
                                                                                class="btn btn-primary px-4 me-2">
                                                                                <i class="fa fa-search"></i> View Routine
                                                                            </button>
                                                                            <button type="button" id="routinePdfBtn"
                                                                                class="btn btn-danger px-4">
                                                                                <i class="fa fa-file-pdf"></i> Download PDF
                                                                            </button>
                                                                        </div>
                                                                    </form>

                                                                    <!-- Routine Table Container -->
                                                                    <div id="routineTableContainer" class="mt-4"></div>
                                                                </div>


                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab-holiday-list-tabs">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-12 col-xl-12">
                                            <div class="tab-left-part mb-lg-40">
                                                <h4 class="title mb-50 text-theme-colored3 text-center">📅 Academic
                                                    Holiday Calendar</h4>
                                                <?php if (!empty($permissions['rrsv_holiday']['landing'])) { ?>
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
                                                                                    class="btn btn-danger">📄 Download
                                                                                    PDF</button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>

                                                                <div class="col-md-12">

                                                                    <div class="table-responsive" id="holidayTable">
                                                                        <div class="alert alert-info">Please select a
                                                                            session to load holidays.</div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                <?php } ?>
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
                                                <?php if (!empty($permissions['rrsv_admission']['landing'])) { ?>
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
                                                <?php } ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tab-syllabus-tabs">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-12 col-xl-12">
                                            <div class="tab-left-part mb-lg-40">
                                                <h3 class="title mb-20 text-theme-colored4 text-center">Syllabus</h3>
                                                <?php if (!empty($permissions['rrsv_syllabus']['landing'])) { ?>
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
                                                                        <select name="class_id" class="form-control"
                                                                            id="class_id" required>
                                                                            <?php
                                                                            $classes = mysqli_query($myDB, "SELECT id, class_name FROM rrsv_class ORDER BY id ASC");
                                                                            $selectedClass = $_POST['class_id'] ?? ''; // capture selected or empty
                                                                        
                                                                            $isFirst = true;
                                                                            while ($c = mysqli_fetch_assoc($classes)) {
                                                                                // If no POST data, select the first class by default
                                                                                $sel = ($selectedClass == $c['id'] || (empty($selectedClass) && $isFirst)) ? 'selected' : '';
                                                                                echo "<option value='{$c['id']}' $sel>{$c['class_name']}</option>";
                                                                                $isFirst = false;
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <!-- Unit -->
                                                                <div class="col-md-2">
                                                                    <div class="mt-3 mb-10">
                                                                        <select name="unit" class="form-control" id="unit">
                                                                            <?php
                                                                            $selectedUnit = $_POST['unit'] ?? 1; // default to 1st Unit
                                                                            for ($u = 1; $u <= 3; $u++) {
                                                                                $unitLabel = ($u == 1) ? '1st Unit' :
                                                                                    (($u == 2) ? '2nd Unit' :
                                                                                        (($u == 3) ? '3rd Unit' : $u . 'th Unit'));
                                                                                $sel = ($selectedUnit == $u) ? 'selected' : '';
                                                                                echo "<option value='$u' $sel>$unitLabel</option>";
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <!-- Buttons -->
                                                                <div class="col-auto mt-3 mb-10">
                                                                    <button type="submit"
                                                                        class="btn btn-primary">Search</button>
                                                                    <button type="button" id="syllabusPdfBtn"
                                                                        class="btn btn-danger">Download PDF</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="row">
                                                            <div id="syllabusTable" class="mt-3"></div>

                                                        </div>
                                                    </div>
                                                <?php } ?>
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
        <div class="container-fluid pb-10">
            <div class="section-title">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-xl-6">
                        <div class="tm-sc-section-title section-title text-center">
                            <div class="title-wrapper">
                                <h2 class="title">Our <span class="text-theme-colored3">Classes</span></h2>
                                <p class="text-left">Each class is carefully structured to build knowledge, confidence,
                                    and creativity through active learning.
                                    We focus on personalized attention so that every child develops their full
                                    potential.</p>
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
                                // Class–Age mapping array
                                $classAgeMap = [
                                    'NURSERY' => '4y - 5y',
                                    'KINDER GARDEN' => '5y - 6y',
                                    'STANDERD 1' => '6y - 7y',
                                    'STANDERD 2' => '7y - 8y',
                                    'STANDERD 3' => '8y - 9y',
                                    'STANDERD 4' => '9y - 10y'
                                ];
                                $classQuery = "SELECT * FROM rrsv_gallery WHERE status='Active' AND category='Class' ORDER BY uploaded_on DESC LIMIT 6";
                                $classRes = mysqli_query($myDB, $classQuery);
                                $sql = "SELECT id, class_name FROM rrsv_class ORDER BY id ASC";
                                $result = mysqli_query($myDB, $sql);
                                $images = [];
                                if ($classRes && mysqli_num_rows($classRes) > 0) {
                                    while ($imgRow = mysqli_fetch_assoc($classRes)) {
                                        $images[] = BASE_URL . $imgRow['image_path'];
                                    }
                                }
                                if (empty($images)) {
                                    $images = [
                                        BASE_URL . "landing/images/project/12.jpg",
                                        BASE_URL . "landing/images/project/13.jpg",
                                        BASE_URL . "landing/images/project/14.jpg",
                                        BASE_URL . "landing/images/project/15.jpg"
                                    ];
                                }

                                if ($result && mysqli_num_rows($result) > 0) {

                                    $i = 0;

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $classId = $row['id'];
                                        $className = $row['class_name'];

                                        // cycle through images
                                        $img = $images[$i % count($images)];
                                        $i++;
                                        $ageRange = $classAgeMap[$className] ?? 'N/A';
                                        $countSql = "
                                            SELECT COUNT(*) AS total_students
                                            FROM rrsv_student_registration
                                            WHERE scl_class = '$className' 
                                            AND scl_session = '$currentYear'
                                        ";
                                        $countRes = mysqli_query($myDB, $countSql);
                                        $countRow = $countRes ? mysqli_fetch_assoc($countRes) : [];
                                        $totalStudents = $countRow['total_students'] ?? 0;

                                        $sqltime = "SELECT * FROM rrsv_time WHERE id = 1";
                                        $res_time1 = mysqli_query($myDB, $sqltime);
                                        $row_time1 = mysqli_fetch_assoc($res_time1);

                                        $currentYear = date('Y');
                                        // function formatTime($timeStr1)
                                        // {
                                        //     $timestamp1 = strtotime($timeStr1);
                                        //     return strtolower(date('g.i a', $timestamp1));
                                        // }
                                        ?>

                                        <div class="tm-carousel-item">
                                            <div class="course">
                                                <div class="thumb">
                                                    <img class="img-fullwidth" src="<?php echo $img; ?>"
                                                        alt="<?php echo $className; ?>">
                                                    <div class="course-overlay"></div>
                                                </div>
                                                <div class="course-details clearfix p-20 pt-15">
                                                    <h5 class="price-tag">Class : <?php echo $className; ?></h5>
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
                                                            <li class="list-inline-item">Student
                                                                <br><?php echo $totalStudents; ?>
                                                            </li>
                                                            <li class="list-inline-item">Duration <br>
                                                                <?php
                                                                if (!empty($row_time1)) {
                                                                    echo formatTime($row_time1['in_time']) . ' - ' . formatTime($row_time1['out_time']);
                                                                } else {
                                                                    echo "Not Available";
                                                                }
                                                                ?>
                                                            </li>
                                                            <li class="list-inline-item">Age <br><?php echo $ageRange; ?></li>
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
                                <p class="text-left">From cultural programs and annual sports to classroom activities
                                    and science exhibitions, each photograph reflects the dedication of our students and
                                    teachers.</p>
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
                                <p class="text-left">Our teachers are the heart of our institution. Each educator at
                                    RRSV is well-trained, passionate, and dedicated to shaping young minds.
                                    They use interactive and child-centered methods to make every lesson meaningful and
                                    enjoyable.</p>
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
                            $image = !empty($row['image']) ? BASE_URL . "teacher_image/" . $row['image'] : BASE_URL . "landing/images/default.jpg";

                            // cycle through 4 theme colors
                            $colorClass = $colors[$i % count($colors)];
                            $i++;
                            ?>
                            <div class="col-sm-6 col-xl-3">
                                <div class="team-member">
                                    <div class="team-thumb">
                                        <img class="img-fullwidth" src="<?php echo $image; ?>" alt="<?php echo $teacherName; ?>"
                                            style="height:250px;object-fit:fill;">
                                    </div>
                                    <div class="team-details text-center <?php echo $colorClass; ?>">
                                        <div class="member-biography">
                                            <p class="mt-0 text-white mb-0"><?php echo $teacherName; ?></p>
                                            <p class="mb-0 text-white"><?php echo $designation; ?></p>
                                        </div>
                                        <!-- <div class="text-center">
                                            <ul class="styled-icons icon-dark icon-sm icon-theme-colored3 icon-circled">
                                                <li><a href="#"><i class="fab fa-facebook text-white"></i></a></li>
                                                <li><a href="#"><i class="fab fa-twitter text-white"></i></a></li>
                                                <li><a href="#"><i class="fab fa-dribbble text-white"></i></a></li>

                                            </ul>
                                        </div> -->
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
                        <?php
                        $i = 1;
                        while ($contact = mysqli_fetch_assoc($contactRes)) { ?>
                            <img class="img-fullwidth" src="<?= BASE_URL . $contact['image_path']; ?>" alt="whychose.png">
                            <?php
                            $i++;
                        } ?>
                    </div>
                </div>
                <div class="col-xl-7 pl-50">
                    <h2 class="title line-bottom mb-20 mt-0">Why <span class="text-theme-color-red">Choose
                            Us</span> ?</h2>
                    Considering English as the most widely spoken language across the world we ventured setting up the
                    R.R.S.V. way back in the year 2012 as the pioneer in and around our locality .Our sole purpose is to
                    foster students to gain fluency in reading, writing and speaking English keeping pace with the
                    global trend. This skill eventually opens doors to higher education better job opportunities and
                    international exposure. It also bridges the communication gap between the people of different
                    regions and countries. Thus this education plays a pivotal role in shaping students future prospect.
                    <br />

                    We are privileged to have a strong and dedicated teaching faculty which ensures quality of teaching
                    a comprehensive extracurricular activities a nurturing and convenient environment and initiatives to
                    foster holistic development of young minds. Thus, we have been enjoying the patronage and support of
                    the guardians throughout our journey.
                    <!-- <p class="mb-50">
                        • Recognized <strong>English-Medium Primary School</strong> (Affiliated with Govt. of West
                        Bengal)<br>
                        • <strong>Clean, Safe, and Child-Friendly</strong> learning environment<br>
                        • <strong>Personalized attention</strong> with a low student–teacher ratio<br>
                        • Balanced academics with <strong>moral and cultural education</strong><br>
                        • <strong>Affordable fees</strong> and transparent parent–school communication<br>
                        • Dedicated and <strong>experienced teachers</strong> focused on holistic development<br>
                        • Consistent record of <strong>student excellence and satisfaction</strong>
                    </p> -->
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

<!-- Admission Code Modal -->
<div class="modal fade" id="checkCodeModal" tabindex="-1" aria-labelledby="checkCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="checkCodeModalLabel">Enter Your Admission Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <input type="text" id="admission_code" class="form-control mb-3 text-center"
                    placeholder="Enter RRSV Code (e.g., RRSV_xxxx_XXX)" />
                <button type="button" id="checkCodeBtn" class="btn btn-success w-100">Verify & Continue</button>
            </div>
        </div>
    </div>
</div>

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
                        <input type="text" id="id" class="form-control" name="id" hidden />

                        <!-- Admission Info -->
                        <div class="col-sm-6 mb-3">
                            <label>Admission Date<span class="text-red m-2">*</span></label>
                            <input type="date" name="scl_date" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Admission for Class<span class="text-red m-2">*</span></label>
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
                            <label>Session<span class="text-red m-2">*</span></label>
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
                            <label>Section<span class="text-red m-2">*</span></label>
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

                        <!-- <div class="col-sm-6 mb-3">
                            <label>Roll No</label>
                            <input type="text" name="scl_roll_no" class="form-control">
                        </div> -->

                        <!-- Personal Info -->
                        <div class="col-sm-12 mt-3">
                            <h5>Personal Information</h5>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Full Name<span class="text-red m-2">*</span></label>
                            <input type="text" name="scl_name" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Student Aadhaar No<span class="text-red m-2">*</span></label>
                            <input type="text" name="scl_aadhar" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Phone No<span class="text-red m-2">*</span></label>
                            <input type="text" name="scl_phone_no" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Alternate / WhatsApp No<span class="text-red m-2">*</span></label>
                            <input type="text" name="alt_phone" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Father's Name<span class="text-red m-2">*</span></label>
                            <input type="text" name="scl_father_name" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Mother's Name<span class="text-red m-2">*</span></label>
                            <input type="text" name="scl_mother_name" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Gender<span class="text-red m-2">*</span></label>
                            <select name="scl_gender" class="form-control" required>
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>DOB<span class="text-red m-2">*</span></label>
                            <input type="date" name="scl_dob" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Caste<span class="text-red m-2">*</span></label>
                            <input type="text" name="scl_religion" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Mother Tongue<span class="text-red m-2">*</span></label>
                            <input type="text" name="scl_language" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Religion<span class="text-red m-2">*</span></label>
                            <input type="text" name="religion" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Nationality<span class="text-red m-2">*</span></label>
                            <input type="text" name="nationality" class="form-control" value="Indian" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Disability (if any)<span class="text-red m-2">*</span></label>
                            <input type="text" name="scl_disa" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Email<span class="text-red m-2">*</span></label>
                            <input type="email" name="scl_email" class="form-control" required>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label>Identification Mark<span class="text-red m-2">*</span></label>
                            <input type="text" name="scl_ide" class="form-control" required>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label>BPL Status and Number:<span class="text-red m-2">*</span></label>
                            <input type="text" name="scl_bpl" class="form-control" required>
                        </div>

                        <!-- File Uploads -->
                        <div class="col-sm-6 mb-3">
                            <label>Upload Photo <small class="form-text text-muted">JPG, PNG, GIF | Max size:
                                    2MB</small></label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label>Aadhaar / DOB Certificate<small class="form-text text-muted">JPG, PNG, GIF | Max
                                    size: 2MB</small></label>
                            <input type="file" name="aadhar_image" class="form-control" accept="image/*">
                        </div>

                        <!-- Address Info -->
                        <div class="col-sm-12 mt-3">
                            <h5>Address Information</h5>
                        </div>

                        <div class="col-sm-6 mb-3"><label>Village/City<span class="text-red m-2">*</span></label>
                            <input type="text" name="scl_address" class="form-control" required>
                        </div>
                        <div class="col-sm-6 mb-3"><label>Post Office<span class="text-red m-2">*</span></label><input
                                type="text" name="scl_pos" class="form-control" required></div>
                        <div class="col-sm-6 mb-3"><label>Police Station<span
                                    class="text-red m-2">*</span></label><input type="text" name="scl_pol"
                                class="form-control" required></div>
                        <div class="col-sm-6 mb-3"><label>Gram Panchayat / Municipality<span
                                    class="text-red m-2">*</span></label><input type="text" name="scl_mu"
                                class="form-control" required></div>
                        <div class="col-sm-6 mb-3"><label>District<span class="text-red m-2">*</span></label><input
                                type="text" name="scl_dist" class="form-control" required></div>
                        <div class="col-sm-6 mb-3"><label>Block<span class="text-red m-2">*</span></label><input
                                type="text" name="scl_block" class="form-control" required></div>
                        <div class="col-sm-6 mb-3"><label>State<span class="text-red m-2">*</span></label><input
                                type="text" name="scl_state" class="form-control" required></div>
                        <div class="col-sm-6 mb-3"><label>Pin<span class="text-red m-2">*</span></label><input
                                type="text" name="scl_pin" class="form-control" required>
                        </div>
                        <div class="col-sm-6 mb-3"><label>Locality<span class="text-red m-2">*</span></label><input
                                type="text" name="scl_location" class="form-control" required></div>

                        <!-- Bank Info -->
                        <!-- <div class="col-sm-12 mt-3">
                            <h5>Bank Information</h5>
                        </div>
                        <div class="col-sm-6 mb-3"><label>Bank Account No</label><input type="text" name="bank_ac_no"
                                class="form-control"></div>
                        <div class="col-sm-6 mb-3"><label>Bank IFSC</label><input type="text" name="bank_ifsc_code"
                                class="form-control"></div>
                        <div class="col-sm-6 mb-3"><label>Bank Branch</label><input type="text" name="branch_name"
                                class="form-control"></div> -->


                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="admission_form" id="admissionBtn" class="btn btn-theme-colored4">Submit
                    Application</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        $('#scl_session, #unit, #subject_id').on('change', function () {
            $('#syllabusForm').trigger('submit');
        });
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
        // AJAX submit
        $('#routineForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo BASE_URL; ?>admin/routine/routine_fetch.php',
                type: 'POST',
                data: $(this).serialize(),
                beforeSend: function () {
                    $('#routineTableContainer').html('<div class="text-center p-3">Loading...</div>');
                },
                success: function (data) {
                    $('#routineTableContainer').html(data);
                },
                error: function () {
                    $('#routineTableContainer').html('<div class="alert alert-danger">Error loading routine</div>');
                }
            });
        });

        // Trigger on page load
        $('#routineForm').trigger('submit');

        // PDF button
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
    // function call_ajax_submit() {
    //     $.ajax({
    //         type: "POST",
    //         url: "<?= BASE_URL ?>admission/add_admission_post.php",
    //         data: { file: 1, data: $('form').serialize() },
    //         beforeSend: function () {
    //             $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2 disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Submiting...</button>');
    //         },
    //         success: function (data) {
    //             // alert(data);
    //             if (data == 1) {
    //                 Swal.fire('sussess', 'Teacher Information  Update successfully!', 'sussess');
    //                 return;

    //                 $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2">Submit</button>');
    //             }
    //             if (data == 2) {
    //                 Swal.fire('sussess', 'Teacher Information  Insert successfully!', 'sussess');
    //                 return;
    //                 $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2">Submit</button>');
    //             }
    //             if (data == 3) {
    //                 Swal.fire('warning', 'Sory duplicate Entry!', 'warning');
    //                 return;
    //                 $('#submit_button').html('<button type="submit" id="submit_button"class="btn btn-primary me-2">Submit</button>');
    //             }
    //         }
    //     });
    // }


    $(document).ready(function () {

        $('#admissionBtn').click(function (e) {
            e.preventDefault();

            // Collect required fields
            const requiredFields = [
                'scl_date', 'scl_section', 'scl_class', 'scl_session',
                'scl_name', 'scl_aadhar', 'scl_phone_no', 'alt_phone',
                'scl_father_name', 'scl_mother_name', 'scl_gender',
                'scl_dob', 'scl_religion', 'scl_language', 'religion',
                'nationality', 'scl_disa', 'scl_email', 'scl_ide',
                'scl_bpl', 'scl_address', 'scl_pos', 'scl_pol',
                'scl_mu', 'scl_dist', 'scl_block', 'scl_state', 'scl_pin',
                'scl_location'
            ];

            let missingFields = [];

            requiredFields.forEach(field => {
                if (!$(`[name="${field}"]`).val()) {
                    missingFields.push(field.replace(/_/g, ' ').toUpperCase());
                }
            });
            if (missingFields.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Required Fields',
                    html: `<b>Please fill out the following:</b><br><small>${missingFields.join(', ')}</small>`,
                    confirmButtonColor: '#d33'
                });
                return false;
            }
            var form = $('#admission_form')[0];
            var formData = new FormData(form);

            // Disable button to prevent multiple clicks
            $('#admissionBtn').prop('disabled', true).text('Submitting...');

            $.ajax({
                url: "<?= BASE_URL ?>admin/admission/add_admission_post.php",
                type: "POST",
                data: formData,
                dataType: "json",
                processData: false, // Important for FormData
                contentType: false, // Important for FormData
                success: function (response) {
                    if (response.status === 'success') {
                        let data = response.data;
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            html: ` `,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Reset form after success
                        $('#admission_form')[0].reset();
                        $('#output').attr('src', ''); // clear image preview

                        // Close modal after 2s
                        setTimeout(function () {
                            $('#admissionModal').modal('hide');
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                            $('body').css('padding-right', '');
                            $('#admissionBtn').prop('disabled', false).text('Submit Application');
                        }, 2000);

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                        $('#admissionBtn').prop('disabled', false).text('Submit Application');
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Something went wrong: ' + error
                    });
                    $('#admissionBtn').prop('disabled', false).text('Submit Application');
                }
            });
        });
    });
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
<script>
    $(document).ready(function () {
        $('#checkCodeBtn').click(function () {
            let code = $('#admission_code').val().trim();
            if (code === '') {
                Swal.fire('Warning', 'Please enter your admission code.', 'warning');
                return;
            }

            $.ajax({
                type: 'POST',
                url: 'check_admission_code.php',
                data: { admission_code: code },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        $('#checkCodeModal').modal('hide');
                        const d = res.admission;
                        const rd = res.readmission;

                        // Admission type
                        if (d.inquery_type === 'Admission') {
                            $('input[name="scl_name"]').val(d.name || '').prop('readonly', true);
                            $('select[name="scl_class"]').val(d.class_name || '').prop('disabled', true);
                            $('input[name="scl_phone_no"]').val(d.phone || '').prop('readonly', true);
                            $('select[name="scl_session"]').val(d.scl_session || '').prop('disabled', true);
                            $('input[name="scl_date"]').val(d.d_o_i || '');
                        } else {
                            $('input[name="id"]').val(rd.id) ?? '';
                            $('input[name="scl_name"]').val(d.name || '').prop('readonly', true);
                            $('select[name="scl_class"]').val(d.class_name || '').prop('disabled', true);
                            $('input[name="scl_phone_no"]').val(d.phone || '').prop('readonly', true);
                            $('select[name="scl_session"]').val(d.scl_session || '').prop('disabled', true);
                            $('select[name="scl_section"]').val(rd.scl_section || '');
                            $('input[name="scl_date"]').val(new Date().toISOString().split('T')[0]);

                            $('input[name="scl_father_name"]').val(rd.scl_father_name || '');
                            $('input[name="scl_mother_name"]').val(rd.scl_mother_name || '');
                            $('input[name="scl_dob"]').val(rd.scl_dob || '');
                            $('select[name="scl_gender"]').val(
                                (rd.scl_gender || '').charAt(0).toUpperCase() + (rd.scl_gender || '').slice(1).toLowerCase()
                            );

                            $('input[name="scl_aadhar"]').val(rd.scl_aadhar || '');
                            $('input[name="scl_address"]').val(rd.scl_address || '');
                            $('input[name="scl_dist"]').val(rd.scl_dist || '');
                            $('input[name="scl_state"]').val(rd.scl_state || '');
                            $('input[name="scl_pin"]').val(rd.scl_pin || '');
                            $('input[name="alt_phone"]').val(rd.alt_phone || '');
                            $('input[name="scl_email"]').val((rd.scl_email || '').toLowerCase());
                            $('input[name="scl_disa"]').val((rd.scl_disa || '').toUpperCase());
                            $('input[name="scl_ide"]').val((rd.scl_ide || '').toUpperCase());
                            $('input[name="scl_bpl"]').val((rd.scl_bpl || '').toUpperCase());
                            $('input[name="scl_religion"]').val(rd.scl_religion || '');
                            $('input[name="religion"]').val(rd.religion || '');
                            $('input[name="scl_language"]').val(rd.scl_language || '');
                            $('input[name="scl_block"]').val(rd.scl_block || '');
                            $('input[name="scl_pol"]').val(rd.scl_pol || '');
                            $('input[name="scl_mu"]').val(rd.scl_mu || '');
                            $('input[name="scl_pos"]').val(rd.scl_pos || '');
                            $('input[name="scl_location"]').val(rd.scl_location || '');
                        }

                        $('#admissionModal').modal('show');
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }
            });
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('#top-primary-nav a[href="#information"]').forEach(link => {
            link.addEventListener("click", function (e) {
                const tabName = this.textContent.trim().toLowerCase();
                e.preventDefault();

                // Scroll to Information section
                document.querySelector("#information")?.scrollIntoView({ behavior: "smooth" });

                // Map text → tab IDs
                const tabMap = {
                    "notice": "#tab-notices-tabs",
                    "routine": "#tab-routine-tabs",
                    "holiday list": "#tab-holiday-list-tabs",
                    "admission chargers": "#tab-admission-charges-tabs",
                    "syllabus": "#tab-syllabus-tabs"
                };

                const targetTab = tabMap[tabName];
                if (targetTab) {
                    setTimeout(() => {
                        // Remove all active classes
                        document.querySelectorAll('.tab-pane, .nav-tabs li, .nav-tabs a').forEach(el => {
                            el.classList.remove('active', 'show');
                        });

                        // Activate target tab
                        const tabPane = document.querySelector(targetTab);
                        const tabLink = document.querySelector(`a[href="${targetTab}"]`);
                        if (tabPane && tabLink) {
                            tabPane.classList.add('active', 'show');
                            tabLink.classList.add('active');
                            tabLink.parentElement.classList.add('active', 'show');
                        }
                    }, 400);
                }
            });
        });
    });

</script>
<script>
    document.getElementById('applyBox').addEventListener('click', function () {
        const modal = new bootstrap.Modal(document.getElementById('checkCodeModal'));
        modal.show();
    });
</script>