 <script src="<?php echo BASE_URL; ?>libray/ckeditor/ckeditor.js"></script>
<script>
   

  var baseurl = window.location.origin + '/';
  var activeEditor;

  $(function() {
    // Initialize all editors
    $('.ckeditor-field').each(function () {
      var editor = CKEDITOR.replace(this.id, { height: 300 });
      editor.on('focus', function() { activeEditor = this; });
    });

    // Special editor
    var mainEditor = CKEDITOR.replace('editor1', {
      customConfig: baseurl + 'ckeditor_config.js'
    });
    mainEditor.on('focus', function() { activeEditor = this; });
  });

  function InsertHTML(content_html) {
    if (activeEditor && activeEditor.mode === 'wysiwyg') {
      activeEditor.insertHtml(content_html);
    } else {
      alert('Please focus an editor first.');
    }
  }
</script>
<script>
function refreshNotifications() {
  $.get('<?BASE_URL?>admin_panel/notification_count.php', function(data){
    let count = parseInt(data);
    if (count > 0) {
      $('#notifCount').text(count).show();
    } else {
      $('#notifCount').hide();
    }
  });
}
//setInterval(refreshNotifications, 10000); // refresh every 10 sec
</script>

  <!-- Footer Start -->
  <footer class="main-footer" style="text-align:center;">

    
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

  </footer>  <!-- Footer End -->
 