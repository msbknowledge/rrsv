<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include_once __DIR__ . '/config.php';
  ?>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>RRSV</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- base:css -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>libray/css/style.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>libray/css/custom.css">

  <!-- endinject -->
  <link rel="shortcut icon" href="<?php echo BASE_URL; ?>libray/images/logo.jpeg" />
  <style>
    .form-control {
      border-color: #ced4da;
      box-shadow: none;
    }

    .form-control:focus {
      border-color: #1a337e;
      box-shadow: 0 0 0 0.2rem rgba(26, 51, 126, 0.15);
    }
  </style>
</head>

<body>
  <div class="container-scroller d-flex">

    <!-- partial:./partials/_sidebar.html -->
    <nav class="sidebar sidebar-offcanvas" id="sidebar">
      <ul class="nav">
        <li class="nav-item sidebar-category">
          <p>Navigation</p>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>home.php">
            <i class="mdi mdi-view-quilt menu-icon"></i>
            <span class="menu-title">Dashboard</span>
            <div class="badge badge-info badge-pill"></div>
          </a>
        </li>
        <li class="nav-item sidebar-category">
          <p>Menu Bar</p>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#food-menu" aria-expanded="false"
            aria-controls="front-menu">
            <i class="mdi mdi-database menu-icon"></i>
            <span class="menu-title">Food Master</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="food-menu">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/food/grocery_items_list.php">Grocery Items</a></li>
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/food/purchase-list.php">Grocery Purchase</a></li>
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/food/stock_report.php">Stock Reports</a></li>

              <!--<li class="nav-item"> <a class="nav-link"-->
              <!--    href="<?php echo BASE_URL; ?>admin/food/daily_purchase_expense_report.php">Grocery Purchase & Expenses</a></li>-->
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/food/food_items_list.php">Menu Items</a></li>
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/food/menu-package-list.php">Menu Package</a></li>
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/food/menu_add.php">Menu Set</a></li>
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/food/student_list.php">Student Order Detail</a></li>
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/food/teacher_list.php">Teacher Order Detail</a></li>
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/food/order_meal_type_token.php">Order Daily List</a></li>
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/food/date-wise-purchase-sales-profit-loss.php">Daily Profit Summary (Sales − Purchase)</a></li>

            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#front-menu" aria-expanded="false"
            aria-controls="front-menu">
            <i class="mdi mdi-database menu-icon"></i>
            <span class="menu-title">Front Master</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="front-menu">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/admission/manage_admission.php">Admission</a></li>
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/syllabus/manage_syllabus.php">Syllabus</a></li>
              <!--<li class="nav-item"> <a class="nav-link"-->
              <!--    href="<?php echo BASE_URL; ?>admin/admission/admission.php">Admission charge</a></li>-->
              <!--<li class="nav-item"> <a class="nav-link"-->
              <!--    href="<?php echo BASE_URL; ?>admin/admission/readmission.php">Re-Admission charge</a>-->
              <!--</li>-->
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/routine/manage_routine.php">Routine</a></li>
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/vendor/manage_vendor.php">Vendor</a></li>
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/gallery/manage_gallery.php">Gallery</a></li>
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/notice/manage_notice.php">Notice</a></li>
              <li class="nav-item"> <a class="nav-link"
                  href="<?php echo BASE_URL; ?>admin/permission/permission_list.php">Permission</a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
            <i class="mdi mdi-database menu-icon"></i>
            <span class="menu-title">Master</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="ui-basic">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_class.php">Add Class</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_section.php">Add Section</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_fee.php">Add Student Fees Rate</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_book.php">Add Book Rate</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_subject.php">Add Subject</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_copy.php">Add Copy Rate</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_car.php">Add Car Rate</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_event.php">Add School Events</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_holiday.php">Add Holiday</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_time.php">Add Time</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_expencehead.php">Add Expense Head</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_incomehead.php">Add Income Head</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_stock.php">Add Stock</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_publishers.php">Add Publishers</a></li>
            </ul>
          </div>
        </li>
        <!--<li class="nav-item">-->
        <!--  <a class="nav-link" href="form.php">-->
        <!--    <i class="mdi mdi-view-headline menu-icon"></i>-->
        <!--    <span class="menu-title">Form elements</span>-->
        <!--  </a>-->
        <!--</li>-->
        <!--<li class="nav-item">-->
        <!--  <a class="nav-link" href="teachers.php">-->
        <!--    <i class="mdi mdi-security menu-icon"></i>-->
        <!--    <span class="menu-title">Teachers</span>-->
        <!--  </a>-->
        <!--</li>-->
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
            <i class="mdi mdi-account-multiple menu-icon"></i>
            <span class="menu-title">Uniform</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="auth">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>admin/uniform/student_vendor_list.php">Uniform Manage</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>admin/uniform/add_items.php">Add Uniform</a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
            <i class="mdi mdi-account-multiple menu-icon"></i>
            <span class="menu-title">Student</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="auth">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_registration.php">Add Student Registration</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_student_behaviour.php">Add Student Behaviour</a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#teacher2" aria-expanded="false" aria-controls="teacher2">
            <i class="mdi mdi-account-circle menu-icon"></i>
            <span class="menu-title">Teacher</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="teacher2">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_teacher.php">Add Teacher Registration</a></li>

            </ul>
          </div>
        </li>

        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#teacher1" aria-expanded="false" aria-controls="teacher1">

            <i class="mdi mdi-cash menu-icon"></i>
            <span class="menu-title">Payment</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="teacher1">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_inquery.php">Admission Form Sell</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_bill.php">Student Bill Payment</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>teacher_salary.php">Teachers Salary</a></li>

            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#attendence" aria-expanded="false"
            aria-controls="teacher1">

            <i class="mdi mdi-clock menu-icon"></i>
            <span class="menu-title">Attendance</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="attendence">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_student_absent_late.php">Students Attendance</a>
              </li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>teacher_attendence.php">Teachers Attendance</a></li>

            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#marksheet" aria-expanded="false"
            aria-controls="marksheet">

            <i class="mdi mdi-certificate menu-icon"></i>
            <span class="menu-title">Marksheet</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="marksheet">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_marksheet.php">Students Marksheet</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>admin/test_marksheet/manage_test_marksheet.php">Test Marksheet</a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#teacher" aria-expanded="false" aria-controls="teacher">
            <i class="mdi mdi-printer menu-icon"></i>
            <span class="menu-title">Print</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="teacher">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_icard.php">Students ID Card</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_teacher_icard.php">Teachers ID Card</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_tc.php">Transfer Certificate</a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#vendor" aria-expanded="false" aria-controls="vendor">

            <i class="mdi mdi-ticket-account menu-icon"></i>
            <span class="menu-title">Vendor Entry</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="vendor">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>admin/vendor/add_vendor_bill.php">Add Bill</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>admin/vendor/manage_vendor_bill.php">Manage Bill</a></li>

            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#expence" aria-expanded="false" aria-controls="expence">

            <i class="mdi mdi-ticket-account menu-icon"></i>
            <span class="menu-title">Income & Expence</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="expence">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_income.php">Add Income</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_expence.php">Add Expence</a></li>

            </ul>
          </div>
        </li>
        <!--<li class="nav-item">-->
        <!--         <a class="nav-link" data-bs-toggle="collapse" href="#income" aria-expanded="false" aria-controls="income">-->

        <!-- <i class="mdi mdi-account-plus menu-icon"></i>-->
        <!--           <span class="menu-title">Income</span>-->
        <!--           <i class="menu-arrow"></i>-->
        <!--         </a>-->
        <!--         <div class="collapse" id="income">-->
        <!--           <ul class="nav flex-column sub-menu">-->

        <!--           </ul>-->
        <!--         </div>-->
        <!--       </li>        -->


        <!--<li class="nav-item">-->
        <!--  <a class="nav-link" data-bs-toggle="collapse" href="#salary" aria-expanded="false" aria-controls="teacher1">-->
        <!--    <i class="mdi mdi-account-key menu-icon"></i>-->
        <!--    <span class="menu-title">Salary</span>-->
        <!--    <i class="menu-arrow"></i>-->
        <!--  </a>-->
        <!--  <div class="collapse" id="salary">-->
        <!--    <ul class="nav flex-column sub-menu">-->
        <!--      <li class="nav-item"> <a class="nav-link" href="teacher_salary.php">Teachers</a></li>-->
        <!--    </ul>-->
        <!--  </div>-->
        <!--</li>-->



        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#teacher3" aria-expanded="false" aria-controls="teacher3">
            <i class="mdi mdi-security menu-icon"></i>
            <span class="menu-title">Reports</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="teacher3">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_status.php">Status Report</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>admin/money_reports/index.php">Money Report</a></li>

              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_expenditure.php">Income & Expense Report</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>manage_report.php">Profite & Loss Report</a></li>
            </ul>
          </div>
        </li>

        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#manage" aria-expanded="false" aria-controls="manage">
            <i class="mdi mdi-security menu-icon"></i>
            <span class="menu-title">Stock</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="collapse" id="manage">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>stock.php"> Stock Manage</a></li>
              <li class="nav-item"> <a class="nav-link" href="<?php echo BASE_URL; ?>return_stock.php"> Stock Return</a></li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>admin/plan_calender/manage_event.php">
            <i class="mdi mdi-compare menu-icon"></i>
            <span class="menu-title">Calender Plan</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>logout.php">
            <i class="mdi mdi-logout menu-icon"></i>
            <span class="menu-title">Log Out</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>export.php">
            <i class="mdi mdi-logout menu-icon"></i>
            <span class="menu-title">Data Backup</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>add_chanepassword.php">
            <i class="mdi mdi-logout menu-icon"></i>
            <span class="menu-title">Change Password</span>
          </a>
        </li>
      </ul>
    </nav>
    <!-- partial -->