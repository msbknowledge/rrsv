<!-- content-wrapper ends -->
        <!-- partial:./partials/_footer.html -->
        <footer class="footer">
          <div class="card">
            <div class="card-body">
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
                        @2021-<?=date('Y')?> .
                        </p>
                    </div>
            </div>
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- base:js -->
  <script src="<?php echo BASE_URL; ?>libray/js/jquery-3.3.1.min.js"></script>
  <script src="<?php echo BASE_URL; ?>vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page-->
  <script src="<?php echo BASE_URL; ?>vendors/chart.js/Chart.min.js"></script>
  <script src="<?php echo BASE_URL; ?>libray/js/jquery.cookie.js" type="text/javascript"></script>
  <!-- End plugin js for this page-->
  <!-- DATATABLES (YOU WERE MISSING THIS) -->
<!--<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">-->
<!--<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>-->
  <!-- inject:js -->
  <script src="<?php echo BASE_URL; ?>libray/js/off-canvas.js"></script>
  <script src="<?php echo BASE_URL; ?>libray/js/hoverable-collapse.js"></script>
  <script src="<?php echo BASE_URL; ?>libray/js/template.js"></script>
  <!-- endinject -->
  <!-- plugin js for this page -->
    <script src="<?php echo BASE_URL; ?>libray/js/jquery.cookie.js" type="text/javascript"></script>
  <!-- End plugin js for this page -->
  <!-- Custom js for this page-->
  <!--<script src="libray/js/dashboard.js"></script>-->
   <script src="<?php echo BASE_URL; ?>js/jquery.js"></script>
  <script src="<?php echo BASE_URL; ?>js/bootstrap.min.js"></script>
  <!-- nice scroll -->
  <script src="<?php echo BASE_URL; ?>js/jquery.scrollTo.min.js"></script>
  <script src="<?php echo BASE_URL; ?>libray/js/jquery.nicescroll.js" type="text/javascript"></script>
  <!-- jquery validate js -->
  <script type="text/javascript" src="<?php echo BASE_URL; ?>js/jquery.validate.min.js"></script>

  <!-- custom form validation script for this page-->
  <script src="<?php echo BASE_URL; ?>js/form-validation-script.js"></script>
  <!--custome script for all page-->
  <script src="<?php echo BASE_URL; ?>js/scripts.js"></script>
  <!-- End custom js for this page-->
  <script>
      $(function() {

    if ($("#audience-chart").length) {
      var AudienceChartCanvas = $("#audience-chart").get(0).getContext("2d");
      var AudienceChart = new Chart(AudienceChartCanvas, {
        type: 'bar',
        data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
          datasets: [
            {
              type: 'line',
              fill: false,
              data: [10, 23, 13, 14, 27, 14],
              borderColor: '#ff4c5b'
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          layout: {
            padding: {
              left: 0,
              right: 0,
              top: 10,
              bottom: 0
            }
          },
          scales: {
            yAxes: [{
              display: true,
              gridLines: {
                display: true,
                drawBorder: false,
                color: "#f8f8f8",
                zeroLineColor: "#f8f8f8"
              },
              ticks: {
                display: true,
                min: 0,
                max: 50,
                stepSize: 10,
                fontColor: "#b1b0b0",
                fontSize: 10,
                padding: 10
              }
            }],
            xAxes: [{
              stacked: false,
              ticks: {
                beginAtZero: true,
                fontColor: "#b1b0b0",
                fontSize: 10
              },
              gridLines: {
                color: "rgba(0, 0, 0, 0)",
                display: false
              },
              barPercentage: .9,
              categoryPercentage: .7,
            }]
          },
          legend: {
            display: false
          },
          elements: {
            point: {
              radius: 3,
              backgroundColor: '#ff4c5b'
            }
          }
        },
      });
    }
    
    
    if ($("#balance-chart").length) {
      var areaData = {
        labels: ["Mon","Tue","Wed","Thu","Fri","Sat"],
        datasets: [
          {
            data: [2600, 1400, 2200, 1200, 2300, 2400],
            borderColor: [
              '#1faf47'
            ],
            borderWidth: 3,
            fill: false,
            label: "services"
          },
        ]
      };
      var areaOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          filler: {
            propagate: false
          }
        },
        scales: {
          xAxes: [{
            display: true,
            ticks: {
              display: false,
            },
            gridLines: {
              display: false,
              drawBorder: false,
              color: 'transparent',
              zeroLineColor: '#eeeeee'
            }
          }],
          yAxes: [{
            display: true,
            ticks: {
              display: true,
              autoSkip: false,
              maxRotation: 0,
              stepSize: 100,
              fontColor: "#000",
              fontSize: 14,
              padding: 18,
              stepSize: 1000,
              max: 3000,
              fontSize: 10,
              fontColor: "#b1b0b0",
              callback: function(value) {
                var ranges = [
                    { divider: 1e6, suffix: 'M' },
                    { divider: 1e3, suffix: 'k' }
                ];
                function formatNumber(n) {
                    for (var i = 0; i < ranges.length; i++) {
                      if (n >= ranges[i].divider) {
                          return (n / ranges[i].divider).toString() + ranges[i].suffix;
                      }
                    }
                    return n;
                }
                return formatNumber(value);
              }
            },
            gridLines: {
              drawBorder: false,
              color: "#f8f8f8",
              zeroLineColor: "#f8f8f8"
            }
          }]
        },
        legend: {
          display: false
        },
        tooltips: {
          enabled: true
        },
        elements: {
          line: {
            tension: 0
          },
          point: {
            radius: 0
          }
        }
      }
      var balanceChartCanvas = $("#balance-chart").get(0).getContext("2d");
      var balanceChart = new Chart(balanceChartCanvas, {
        type: 'line',
        data: areaData,
        options: areaOptions
      });
    }
    
      });
  </script>
</body>

</html>