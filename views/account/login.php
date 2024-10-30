<?php

echo'	<form name="f" method="post" action="">
			<input type="hidden" name="option" value="mobsms_verify_customer" />
			<input type="hidden" name="nonce" value='. wp_create_nonce("mobsms-account-nonce").' >
			<div class="mo_wpns_divided_layout">
				<div class="mo_wpns_setting_layout">
					
					<h3>Login with miniOrange</h3>
					<p><b>It seems you already have an account with miniOrange. Please enter your miniOrange email and password.<a target="_blank" href="https://login.xecurify.com/moas/idp/resetpassword"> Click here if you forgot your password?</a></b></p>
					<table class="mo_wpns_settings_table">
						<tr>
							<td><b><font color="#FF0000">*</font>Email:</b></td>
							<td><input class="mo_wpns_table_textbox" type="email" name="email"
								required placeholder="person@example.com"
								value="'.esc_html($admin_email).'" /></td>
						</tr>
						<tr>
							<td><b><font color="#FF0000">*</font>Password:</b></td>
							<td><input class="mo_wpns_table_textbox" required type="password"
								name="password" placeholder="Enter your miniOrange password" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="submit" value="Sign In" class="mo_wpns_button mo_wpns_button1" />
								<a href="#cancel_link" class="mo_wpns_button mo_wpns_button1">New User? Register</a>
						</tr>
					</table>
				</div>
			</div>
		</form>
		<form id="cancel_form" method="post" action="">
			<input type="hidden" name="option" value="mobsms_cancel" />
			<input type="hidden" name="nonce" value='. wp_create_nonce("mobsms-account-nonce").' >
		</form>
		<script>
			jQuery(document).ready(function(){
				jQuery("a[href=\'#cancel_link\']").click(function(){
					jQuery("#cancel_form").submit();
				});		
			});
		</script>';
