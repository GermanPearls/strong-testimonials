<div class="subsubsection">
	<table class="first">
		<thead>
			<tr>
				<th colspan="2"><?php _e( "From:", 'strong-testimonials' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<label for="wpmtst-options-sender-name">
						<span><?php _e( "Name", 'strong-testimonials' ); ?></span>
					</label>
				</td>
				<td><?php _e( "Email", 'strong-testimonials' ); ?></td>
			</tr>
			<tr>
				<td>
					<span class="controls"></span>
					<input id="wpmtst-options-sender-name" type="text" class="name-email" size="30" placeholder="(optional)"
					       name="wpmtst_form_options[sender_name]"
					       value="<?php echo esc_attr( $form_options['sender_name'] ); ?>"/>
				</td>
				<td>
					<label class="block">
						<input id="wpmtst-options-sender-site-email-1" type="radio"
						       name="wpmtst_form_options[sender_site_email]" <?php checked( $form_options['sender_site_email'], 1 ); ?>
						       value="1"/> site admin email: <?php echo get_bloginfo( 'admin_email' ); ?>
					</label>
					<label class="block">
						<input id="wpmtst-options-sender-site-email-0" class="focus-next-field" type="radio"
						       name="wpmtst_form_options[sender_site_email]" <?php checked( $form_options['sender_site_email'], 0 ); ?>
						       value="0"/>
						<input id="wpmtst-options-sender-email" type="email" class="name-email" size="30" placeholder="sender's email"
						       name="wpmtst_form_options[sender_email]"
						       value="<?php echo esc_attr( $form_options['sender_email'] ); ?>"/>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
</div>
