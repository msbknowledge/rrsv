<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Mailbox — Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 3 -->
  <link rel="stylesheet" href="/../admin_panel/assets/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/../admin_panel/assets/css/font-awesome.min.css">
  <!-- AdminLTE -->
  <link rel="stylesheet" href="/../admin_panel/assets/css/AdminLTE.min.css">
  <link rel="stylesheet" href="/../admin_panel/assets/css/_all-skins.min.css">

  <!-- SweetAlert2 (optional) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">

  <style>
    /* ---------- Outlook-style mailbox custom CSS ---------- */
    .mailbox-outlook { background: #f7f7f7; padding: 12px 15px; }
    .mailbox-left { background: #fff; border-right:1px solid #e5e5e5; padding: 15px; height: calc(100vh - 120px); overflow:auto; }
    .mailbox-middle { background: #fff; border-right:1px solid #e5e5e5; padding:0; height: calc(100vh - 120px); overflow:auto; }
    .mailbox-right { background: #fff; padding:0; height: calc(100vh - 120px); overflow:auto; }
    .mailbox-nav .nav > li > a { padding: 10px 12px; font-size: 14px; }
    .mailbox-header { padding: 10px 12px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center; background:#fafafa; }
    .mailbox-title { margin:0; font-size:16px; }
    .mailbox-items { list-style:none; margin:0; padding:0; }
    .mailbox-item { padding:12px; border-bottom:1px solid #f1f1f1; cursor:pointer; display:flex; align-items:center; }
    .mailbox-item .meta { margin-left:auto; color:#999; font-size:12px; }
    .mailbox-item:hover { background:#fbfbfb; }
    .mailbox-item.active { background:#e9f5ff; border-left:4px solid #3c8dbc; }
    .mailbox-item .from { font-weight:600; font-size:13px; }
    .mailbox-item .subject { color:#666; font-size:13px; margin-top:3px; }
    .mailbox-read-header { padding:15px; border-bottom:1px solid #eee; }
    .mailbox-read-body { padding:18px; font-size:14px; min-height:220px; color:#333; }
    .mailbox-read-actions { padding:12px 15px; border-top:1px solid #eee; background:#fafafa; }
    .mailbox-left .folder-count { float:right; background:#3c8dbc; color:#fff; padding:2px 6px; border-radius:12px; font-size:12px; }
    /* Compose modal adjustments */
    .modal .form-control { box-shadow:none; }
    /* Responsive tweaks */
    @media (max-width: 991px) {
      .mailbox-left { height: auto; }
      .mailbox-middle { height: auto; margin-top:12px; }
      .mailbox-right { height: auto; margin-top:12px; }
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">

  <div class="wrapper">
    <!-- Use your existing header/sidebar from AdminLTE if you want -->
    <!-- Content Wrapper -->
    <div class="content-wrapper" style="min-height: 600px;">
      <section class="content-header">
        <h1>Mailbox <small>Outlook-style</small></h1>
      </section>

      <section class="content">
        <div class="mailbox-outlook">
          <div class="row no-gutters">
            <!-- LEFT NAV -->
            <div class="col-sm-2 mailbox-left">
              <button class="btn btn-primary btn-block margin-bottom" id="composeBtn">
                <i class="fa fa-pencil"></i> Compose
              </button>

              <ul class="nav nav-pills nav-stacked mailbox-nav">
                <li class="active"><a href="#" data-folder="inbox"><i class="fa fa-inbox"></i> Inbox <span class="folder-count pull-right">3</span></a></li>
                <li><a href="#" data-folder="sent"><i class="fa fa-envelope-o"></i> Sent</a></li>
                <li><a href="#" data-folder="drafts"><i class="fa fa-file-text-o"></i> Drafts</a></li>
                <li><a href="#" data-folder="trash"><i class="fa fa-trash"></i> Trash</a></li>
              </ul>

              <hr>

              <h5 class="text-muted">Folders</h5>
              <ul class="nav nav-pills nav-stacked mailbox-nav">
                <li><a href="#"><i class="fa fa-calendar"></i> Assignments</a></li>
                <li><a href="#"><i class="fa fa-users"></i> Class Notices</a></li>
                <li><a href="#"><i class="fa fa-bullhorn"></i> Announcements</a></li>
              </ul>
            </div>

            <!-- MIDDLE LIST -->
            <div class="col-sm-4 mailbox-middle">
              <div class="mailbox-header">
                <h3 class="mailbox-title">Inbox</h3>
                <div class="btn-group">
                  <button class="btn btn-default btn-sm" id="refreshBtn"><i class="fa fa-refresh"></i></button>
                  <button class="btn btn-default btn-sm" id="markReadBtn"><i class="fa fa-check"></i></button>
                </div>
              </div>

              <div class="mailbox-list">
                <ul class="mailbox-items" id="mailList">
                  <!-- JS will fill items -->
                </ul>
              </div>
            </div>

            <!-- RIGHT PREVIEW -->
            <div class="col-sm-6 mailbox-right">
              <div class="box box-solid">
                <div class="mailbox-read-header">
                  <h3 id="read-title">Select a message</h3>
                  <small id="read-meta" class="text-muted">No message selected</small>
                </div>

                <div class="mailbox-read-body" id="read-body">
                  <p class="text-muted">Click any email from the middle panel to preview it here. Use the Compose button to create a new message.</p>
                </div>

                <div class="mailbox-read-actions">
                  <div class="btn-group">
                    <button class="btn btn-default" id="replyBtn"><i class="fa fa-reply"></i> Reply</button>
                    <button class="btn btn-default" id="printBtn"><i class="fa fa-print"></i> Print</button>
                  </div>
                  <div class="pull-right">
                    <button class="btn btn-danger" id="deleteBtn"><i class="fa fa-trash"></i> Delete</button>
                  </div>
                  <div style="clear:both"></div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer text-center" style="padding:10px;">
      <strong>Copyright &copy; 2025</strong>
    </footer>
  </div>

  <!-- Compose Modal -->
  <div id="composeModal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="composeForm">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Compose New Message</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>To</label>
              <input type="text" name="to" class="form-control" placeholder="student@example.com or class name">
            </div>
            <div class="form-group">
              <label>Subject</label>
              <input type="text" name="subject" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Message</label>
              <textarea name="body" class="form-control" rows="8" required></textarea>
            </div>
            <div class="form-group">
              <label>Attachment</label>
              <input type="file" name="attachment" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" type="submit"><i class="fa fa-send"></i> Send</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- jQuery -->
  <script src="/../admin_panel/assets/js/jquery-2.2.3.min.js"></script>
  <!-- Bootstrap -->
  <script src="/../admin_panel/assets/js/bootstrap.min.js"></script>
  <!-- AdminLTE app (optional) -->
  <script src="/../admin_panel/assets/js/app.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>

  <script>
    // Sample mailbox data (replace with server data)
    const mailbox = [
      {
        id: 1,
        from: "Office Admin",
        subject: "Tomorrow meeting at 1:00 PM",
        time: "Today 10:30 AM",
        body: "A meeting is scheduled tomorrow at 1:00 PM regarding campus maintenance planning.\n\nRegards,\nOffice Admin",
        read: false
      },
      {
        id: 2,
        from: "Principal",
        subject: "New academic session notice",
        time: "Yesterday 05:45 PM",
        body: "The new academic session will start on Monday. All teachers must update their timetables.",
        read: false
      },
      {
        id: 3,
        from: "Teacher Panel",
        subject: "Updated exam schedule",
        time: "2 days ago",
        body: "Please note that the exam schedule has been updated. Please check the portal.",
        read: true
      }
    ];

    // Render list
    function renderMailList() {
      const $list = $("#mailList");
      $list.empty();
      mailbox.forEach(m => {
        const activeCls = m.read ? "" : "active"; // highlight unread as active-like
        const li = $(`
          <li class="mailbox-item ${activeCls}" data-id="${m.id}">
            <div>
              <div class="from">${m.from}</div>
              <div class="subject">${m.subject}</div>
            </div>
            <div class="meta">${m.time}</div>
          </li>
        `);
        $list.append(li);
      });
    }

    // Open mail by id
    function openMailById(id) {
      const mail = mailbox.find(m => m.id === id);
      if (!mail) return;
      $("#read-title").text(mail.subject);
      $("#read-meta").text(mail.from + " — " + mail.time);
      // preserve line breaks
      $("#read-body").html(mail.body.replace(/\n/g, "<br>"));
      // mark read
      mail.read = true;
      renderMailList();
      // highlight current item
      $(".mailbox-item").removeClass("active");
      $(`.mailbox-item[data-id="${id}"]`).addClass("active");
      // store selected id
      $("#deleteBtn").data("id", id);
      $("#replyBtn").data("id", id);
    }

    $(function() {
      renderMailList();

      // When clicking an item
      $(document).on("click", ".mailbox-item", function(e) {
        const id = parseInt($(this).data("id"));
        openMailById(id);
      });

      // Compose
      $("#composeBtn").on("click", function() {
        $("#composeModal").modal("show");
      });

      // Send (compose) — this is placeholder: do AJAX post to server in production
      $("#composeForm").on("submit", function(e) {
        e.preventDefault();
        const form = new FormData(this);
        // TODO: AJAX submit to server
        $("#composeModal").modal("hide");
        Swal.fire({
          icon: "success",
          title: "Message sent",
          toast: true,
          position: "top-end",
          timer: 1500,
          showConfirmButton: false
        });
      });

      // Refresh
      $("#refreshBtn").on("click", function() {
        // TODO: reload from server
        Swal.fire({
          toast: true,
          position: "top-end",
          icon: "success",
          title: "Mailbox refreshed",
          showConfirmButton: false,
          timer: 1000
        });
      });

      // Reply: opens compose modal and prefill
      $("#replyBtn").on("click", function() {
        const id = $(this).data("id");
        const mail = mailbox.find(m => m.id === id);
        if (!mail) return Swal.fire("No message selected");
        $("#composeModal").modal("show");
        $("#composeForm [name=to]").val(mail.from);
        $("#composeForm [name=subject]").val("Re: " + mail.subject);
        $("#composeForm [name=body]").val("\n\n---\nOn " + mail.time + ", " + mail.from + " wrote:\n" + mail.body);
      });

      // Print
      $("#printBtn").on("click", function() {
        var content = $("#read-title").text() + "\n\n" + $("#read-body").text();
        var newWin = window.open('', '_blank');
        newWin.document.write("<pre>" + content + "</pre>");
        newWin.print();
        newWin.close();
      });

      // Delete with confirmation
      $("#deleteBtn").on("click", function() {
        const id = $(this).data("id");
        if (!id) return Swal.fire("No message selected");
        Swal.fire({
          title: 'Delete message?',
          text: "This will move the message to Trash.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it'
        }).then(result => {
          if (result.isConfirmed) {
            // In production: AJAX call to server to delete
            const idx = mailbox.findIndex(m => m.id === id);
            if (idx !== -1) mailbox.splice(idx, 1);
            renderMailList();
            $("#read-title").text("Select a message");
            $("#read-body").html('<p class="text-muted">Message deleted.</p>');
            Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Deleted', showConfirmButton:false, timer:1200 });
          }
        });
      });

      // Clicking folder left nav (basic client routing)
      $(".mailbox-nav a").on("click", function(e) {
        e.preventDefault();
        $(".mailbox-nav li").removeClass("active");
        $(this).parent().addClass("active");
        const folder = $(this).data("folder") || "inbox";
        $(".mailbox-title").text(folder.charAt(0).toUpperCase() + folder.slice(1));
        // TODO: load folder from server
        renderMailList();
      });

      // Pre-open first unread
      const firstUnread = mailbox.find(m => !m.read);
      if (firstUnread) openMailById(firstUnread.id);
    });
  </script>
</body>
</html>
