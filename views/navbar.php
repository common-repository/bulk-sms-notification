<?php
    echo '<div>
				<div id ="mobsms_message"></div>
				<div>
				<img  style="float:left;" src="'.esc_url($logo_url).'">					
				</div>
				<div>
				<h1 style="font-weight: 400;">miniOrange Bulk SMS &nbsp;
				<a class="add-new-h2 mobsms_nav_menu" style="font-size:17px;border-radius:4px;" href="'.esc_url($profile_url).'" >Account</a>
				<a class="add-new-h2 mobsms_nav_menu" style="font-size:17px;border-radius:4px;" href="'.esc_url($otp_recharge_url).'" target="_blank">Recharge OTP</a>
				<a class="add-new-h2 mobsms_nav_menu" style="font-size:17px;border-radius:4px;" href="'.esc_url($view_transaction_url).'" target="_blank">View Remaining SMS</a>
				</h1>
				</div>
				<br>
			</div>';
    echo '<a id="mobsms_menu" class="nav-tab '.esc_html(($active_tab == 'mobsms_menu'
                        ? 'nav-tab-active' : '')).'" href="'.esc_url($configuration_url).'">Configuration</a>';
    echo '<a id="mobsms_send_bulk_sms" class="nav-tab '.esc_html(($active_tab == 'mobsms_send_bulk_sms'
                        ? 'nav-tab-active' : '')).'" href="'.esc_url($send_bulk_sms_url).'">Send Bulk SMS</a>';
