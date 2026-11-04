<!-- Footer -->
<footer id="footer" class="footer divider layer-overlay overlay-dark-8"
    data-tm-bg-img="<?php echo BASE_URL; ?>landing/images/footer_bg.jpg">
    <div class="footer-widget-area">
        <div class="container pt-90 pb-40">
            <div class="row">
                <div class="col-md-6 col-lg-6 col-xl-4">
                    <div class="widget tm-widget-contact-info contact-info-style1 contact-icon-theme-colored4">
                        <div class="col-sm-auto align-self-center">
                            <div class="thumb">
                                <img class="rounded-circle" height="60" width="60" alt="Logo"
                                    src="<?php echo BASE_URL; ?>libray/images/logo.jpeg">
                            </div>
                        </div>
                        <div class="description">RASULPUR RAMKRISHNA SARADA VIDYAPITH</div>
                        <ul class="mb-30">
                            <li class="contact-phone">
                                <div class="icon"><i class="flaticon-contact-042-phone-1"></i></div>
                                <div class="text"><a href="tel:+917431838365">+917431838365</a></div>
                            </li>
                            <li class="contact-email">
                                <div class="icon"><i class="flaticon-contact-043-email-1"></i></div>
                                <div class="text"><a href="mailto:rrsvidyapith@gmail.com"><span class="__cf_email__"
                                            data-cfemail="187b77766c797b6c5861776d6a7c7775797176367b7775">rrsvidyapith@gmail.com</span></a>
                                </div>
                            </li>
                            <li class="contact-website">
                                <div class="icon"><i class="flaticon-contact-035-website"></i></div>
                                <div class="text"><a target="_blank" href="http://rrsv.in/">rrsv.in</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="widget">
                        <h4 class="widget-title">Payment Details</h4>
                        <?php if (!empty($bankRes)): ?>
                            <div class="bank-info">
                                <?php if (!empty($bankRow['image_path'])):
                                    $imageURL = BASE_URL . $bankRow['image_path'];
                                    ?>
                                    <a href="<?php echo $imageURL; ?>" download>
                                        <img src="<?php echo $imageURL; ?>" alt="Bank Image" class="img-fluid"
                                            style="max-width:150px; margin-bottom:10px; cursor:pointer;"
                                            title="Click to download">
                                    </a>
                                <?php endif; ?>


                                <br />
                                <span>
                                    Account Name: RASULPUR RAMKRISHNA SARADA VIDYAPITH .<br />
                                    Account Number: 50151961492 .<br />
                                    IFSC Code: IDIB000R611 .<br />
                                    Branch: RASULPUR .<br />

                                </span>
                            </div><?php else: ?>
                            <p>No active bank details found.</p>
                        <?php endif; ?>

                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-2">
                    <div class="widget widget_nav_menu">
                        <h4 class="widget-title">Services</h4>
                        <ul>


                            <li><a href="#information">Routine Table</a></li>
                            <li><a href="#information">Holiday List</a></li>
                            <li><a href="#information">Admission Chargers</a></li>
                            <li><a href="#information">Syllabus</a></li>
                            <li><a href="#courses">Classes</a></li>
                            <li><a href="#gallery">Gallery</a></li>
                            <li><a href="#about">About Us</a></li>
                            <li><a href="#footer">Contact Us</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="widget">
                        <h4 class="widget-title">Opening Hours <?php echo $currentYear; ?></h4>

                        <div class="opening-hours border-dark">
                            <ul>
                                <?php

                                $sql = "SELECT * FROM rrsv_time WHERE id = 1";
                                $res_time = mysqli_query($myDB, $sql);
                                $row_time = mysqli_fetch_assoc($res_time);

                                $currentYear = date("Y");
                                function formatTime1($timeStr)
                                {
                                    // Convert HH:MM to 12-hour format with am/pm and dot between hours/minutes
                                    $timestamp = strtotime($timeStr);
                                    return strtolower(date('g.i a', $timestamp));
                                }


                                ?>

                                <li class='clearfix'>
                                    <span>Monday -Saturday :</span>
                                    <div class='value'><?php
                                    if (!empty($row_time)) {
                                        echo formatTime1($row_time['in_time']) . ' - ' . formatTime1($row_time['out_time']);
                                    } else {
                                        echo "Not Available";
                                    }
                                    ?></div>
                                </li>
                                <li class='clearfix'>
                                    <span>Sunday :</span>
                                    <div class='value'>Closed</div>
                                </li>
                               
                            </ul>
                        </div>

                        <!-- <div class="opening-hours border-dark">
                            <ul>
                                <li class="clearfix"> <span> Mon - Tues : </span>
                                    <div class="value"> 6.00 am - 10.00 pm </div>
                                </li>
                                <li class="clearfix"> <span> Wednes - Thurs :</span>
                                    <div class="value"> 8.00 am - 6.00 pm </div>
                                </li>
                                <li class="clearfix"> <span> Fri :</span>
                                    <div class="value"> 3.00 pm - 8.00 pm </div>
                                </li>
                                <li class="clearfix"> <span> Sun : </span>
                                    <div class="value"> Closed </div>
                                </li>
                            </ul>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-md-3 col-lg-3">
                    <div class="widget dark">
                        <h5 class="widget-title mb-10">Call Us Now</h5>
                        <div class="text-gray">
                            <a href="tel:+917431838365">+917431838365</a><br>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="widget dark">
                        <h5 class="widget-title mb-10">Connect With Us</h5>
                        <ul class="styled-icons icon-dark icon-theme-colored4 icon-rounded clearfix">
                            <li><a class="social-link" href="https://www.facebook.com/rrsvidyapith/" target="blank"><i
                                        class="fab fa-facebook"></i></a></li>
                            <!-- <li><a class="social-link" href="#"><i class="fab fa-twitter"></i></a></li>
                            <li><a class="social-link" href="#"><i class="fab fa-youtube"></i></a></li>
                            <li><a class="social-link" href="#"><i class="fab fa-instagram"></i></a></li> -->
                        </ul>
                    </div>
                </div>
                <div class="col-md-5 col-lg-5">
                    <!-- Mailchimp Subscription Form-->
                    <!-- <form id="mailchimp-subscription-form1" class="newsletter-form">
                        <label for="mce-EMAIL"></label>
                        <div class="input-group">
                            <input type="email" id="mce-EMAIL" data-tm-height="45px" class="form-control"
                                placeholder="Your Email" name="EMAIL" value="">
                            <div class="input-group-append tm-sc-button">
                                <button type="submit" class="btn btn-theme-colored4 btn-sm"
                                    data-tm-height="45px">Subscribe</button>
                            </div>
                        </div>
                    </form> -->
                    <!-- Mailchimp Subscription Form Validation-->

                </div>
            </div>
        </div>
        <div class="footer-bottom" data-tm-bg-color="#2A2A2A">
            <div class="container">
                <div class="row pt-20 pb-20">
                    <a href="https://www.facebook.com/share/178nUdiheL/?mibextid=wwXIfr" target="blank">
                        <div class="col-sm-12">
                            <div class="footer-paragraph text-center">
                                <div class="d-flex align-items-center justify-content-center" style="height: 75px;">
                        <p class="mb-0">
                            
                        <a style="display: inline-block; padding: 5px;" target="_blank" href="" title="Connect with us on Facebook">
                        <!--<img src="img/logo.png" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">-->
                        </a>
                        <a class="text-white border-bottom" href="" title="Connect with us on Facebook">RRSV</a> © All Rights Reserved ★ Designed by - 
                        <a class="text-white border-bottom" target="_blank" href="https://www.facebook.com/profile.php?id=61569526103558" title="Connect with us on Facebook">iTech Solutions &amp; Services</a>
                        <a style="display: inline-block; padding: 5px;" target="_blank" href="https://www.facebook.com/profile.php?id=61569526103558" title="Connect with us on Facebook">
                        <img src="<?php echo BASE_URL; ?>landing/images/itech.jpg" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        </a>
                        @2021-<?=date('Y')?>  .
                        </p>
                    </div>
                            </div>
                        </div>
                    </a>
                    <!-- <div class="col-sm-6">
                        <div class="footer-paragraph text-right">

                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</footer>
<a class="scrollToTop" href="#"><i class="fa fa-angle-up"></i></a>
</div>
<!-- end wrapper -->

<!-- Footer Scripts -->
<!-- JS | Custom script for all pages -->
<script src="<?php echo BASE_URL; ?>landing/js/custom.js"></script>

</body>

</html>