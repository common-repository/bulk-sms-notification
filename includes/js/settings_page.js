function mobsms_nav_popup() {
  document.getElementById("notice_div").style.width = "40%";
  setTimeout(function(){ jQuery('#notice_div').fadeOut('slow'); }, 3000);
}

function mobsms_error_msg(error) {
jQuery('#mobsms_message').empty();
var msg = "<div id='notice_div' class='overlay_error'><div class='popup_text'>&nbsp; &nbsp; "+error+"</div></div>";
jQuery('#mobsms_message').append(msg);
window.onload = mobsms_nav_popup();
}

function mobsms_success_msg(success) {
jQuery('#mobsms_message').empty();
var msg = "<div id='notice_div' class='overlay_success'><div class='popup_text'>&nbsp; &nbsp; "+success+"</div></div>";
jQuery('#mobsms_message').append(msg);
window.onload = mobsms_nav_popup();
}
