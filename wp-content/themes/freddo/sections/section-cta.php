<?php 
$showCta = freddo_options('_onepage_section_cta', '');
?>
<?php if ($showCta == 1) : ?>
	<?php
		$ctaSectionID = freddo_options('_onepage_id_cta','cta');
		$ctaTitle = freddo_options('_onepage_title_cta', __('Elements', 'freddo'));
		$ctaSubTitle = freddo_options('_onepage_subtitle_cta', __('Amazing Call To Action', 'freddo'));
		$ctaPageBox = freddo_options('_onepage_choosepage_cta');
		$howManyBoxes = freddo_options('_onepage_manybox_cta', '3');
		$textLenght = freddo_options('_onepage_lenght_cta', '20');
		$customMore = freddo_options('_excerpt_more', '&hellip;');
		$ctaIcon = freddo_options('_onepage_fontawesome_cta','fa fa-flash');
		$ctaPhrase = freddo_options('_onepage_phrase_cta','');
		$ctaDesc = freddo_options('_onepage_desc_cta','');
		$ctaTextButton = freddo_options('_onepage_textbutton_cta',__('More Information', 'freddo'));
		$ctaLinkButton = freddo_options('_onepage_urlbutton_cta','#');
		$ctaOpenLink = freddo_options('_onepage_openurl_cta','_blank');
	?>
<section class="freddo_onepage_section freddo_cta <?php echo $ctaDesc ? 'withDesc' : 'noDesc' ?>" id="<?php echo esc_attr($ctaSectionID); ?>">
	<div class="freddo_cta_color"></div>
	<div class="freddo_action_cta">
		<?php if($ctaTitle || is_customize_preview()): ?>
			<h2 class="freddo_main_text"><?php echo esc_html($ctaTitle); ?></h2>
		<?php endif; ?>
		<?php if($ctaSubTitle || is_customize_preview()): ?>
			<p class="freddo_subtitle"><?php echo esc_html($ctaSubTitle); ?></p>
		<?php endif; ?>
		<div class="cta_columns">
			<?php if ($howManyBoxes == 1): ?>
			<?php
				$fontAwesomeIcon1 = freddo_options('_onepage_fontawesome_1_cta', 'fa fa-bell');
				$choosePageBox1 = freddo_options('_onepage_choosepage_1_cta');
				$textButton1 = freddo_options('_onepage_boxtextbutton_1_cta', __('More Information', 'freddo'));
				$linkButton1 = freddo_options('_onepage_boxlinkbutton_1_cta', '#');
			?>
			<div class="one cta_columns_single">
				<?php if($fontAwesomeIcon1): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon1); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox1): ?>
					<h3><?php echo get_the_title(intval($choosePageBox1)); ?></h3>
					<?php
					$post_content1 = get_post(intval($choosePageBox1));
					$content1 = $post_content1->post_content;
					?>
					<p><?php echo wp_trim_words($content1 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton1 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton1); ?>"><?php echo esc_html($textButton1); ?></a></div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if ($howManyBoxes == 2): ?>
			<?php
				$fontAwesomeIcon1 = freddo_options('_onepage_fontawesome_1_cta', 'fa fa-bell');
				$choosePageBox1 = freddo_options('_onepage_choosepage_1_cta');
				$textButton1 = freddo_options('_onepage_boxtextbutton_1_cta', __('More Information', 'freddo'));
				$linkButton1 = freddo_options('_onepage_boxlinkbutton_1_cta', '#');
				$fontAwesomeIcon2 = freddo_options('_onepage_fontawesome_2_cta', 'fa fa-bell');
				$choosePageBox2 = freddo_options('_onepage_choosepage_2_cta');
				$textButton2 = freddo_options('_onepage_boxtextbutton_2_cta', __('More Information', 'freddo'));
				$linkButton2 = freddo_options('_onepage_boxlinkbutton_2_cta', '#');
			?>
			<div class="two cta_columns_single">
				<?php if($fontAwesomeIcon1): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon1); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox1): ?>
					<h3><?php echo get_the_title(intval($choosePageBox1)); ?></h3>
					<?php
					$post_content1 = get_post(intval($choosePageBox1));
					$content1 = $post_content1->post_content;
					?>
					<p><?php echo wp_trim_words($content1 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton1 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton1); ?>"><?php echo esc_html($textButton1); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="two cta_columns_single">
				<?php if($fontAwesomeIcon2): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon2); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox2): ?>
					<h3><?php echo get_the_title(intval($choosePageBox2)); ?></h3>
					<?php
					$post_content2 = get_post(intval($choosePageBox2));
					$content2 = $post_content2->post_content;
					?>
					<p><?php echo wp_trim_words($content2 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton2 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton2); ?>"><?php echo esc_html($textButton2); ?></a></div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if ($howManyBoxes == 3): ?>
			<?php
				$fontAwesomeIcon1 = freddo_options('_onepage_fontawesome_1_cta', 'fa fa-bell');
				$choosePageBox1 = freddo_options('_onepage_choosepage_1_cta');
				$textButton1 = freddo_options('_onepage_boxtextbutton_1_cta', __('More Information', 'freddo'));
				$linkButton1 = freddo_options('_onepage_boxlinkbutton_1_cta', '#');
				$fontAwesomeIcon2 = freddo_options('_onepage_fontawesome_2_cta', 'fa fa-bell');
				$choosePageBox2 = freddo_options('_onepage_choosepage_2_cta');
				$textButton2 = freddo_options('_onepage_boxtextbutton_2_cta', __('More Information', 'freddo'));
				$linkButton2 = freddo_options('_onepage_boxlinkbutton_2_cta', '#');
				$fontAwesomeIcon3 = freddo_options('_onepage_fontawesome_3_cta', 'fa fa-bell');
				$choosePageBox3 = freddo_options('_onepage_choosepage_3_cta');
				$textButton3 = freddo_options('_onepage_boxtextbutton_3_cta', __('More Information', 'freddo'));
				$linkButton3 = freddo_options('_onepage_boxlinkbutton_3_cta', '#');
			?>
			<div class="three cta_columns_single">
				<?php if($fontAwesomeIcon1): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon1); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox1): ?>
					<h3><?php echo get_the_title(intval($choosePageBox1)); ?></h3>
					<?php
					$post_content1 = get_post(intval($choosePageBox1));
					$content1 = $post_content1->post_content;
					?>
					<p><?php echo wp_trim_words($content1 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton1 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton1); ?>"><?php echo esc_html($textButton1); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="three cta_columns_single">
				<?php if($fontAwesomeIcon2): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon2); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox2): ?>
					<h3><?php echo get_the_title(intval($choosePageBox2)); ?></h3>
					<?php
					$post_content2 = get_post(intval($choosePageBox2));
					$content2 = $post_content2->post_content;
					?>
					<p><?php echo wp_trim_words($content2 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton2 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton2); ?>"><?php echo esc_html($textButton2); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="three cta_columns_single">
				<?php if($fontAwesomeIcon3): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon3); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox3): ?>
					<h3><?php echo get_the_title(intval($choosePageBox3)); ?></h3>
					<?php
					$post_content3 = get_post(intval($choosePageBox3));
					$content3 = $post_content3->post_content;
					?>
					<p><?php echo wp_trim_words($content3 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton3 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton3); ?>"><?php echo esc_html($textButton3); ?></a></div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if ($howManyBoxes == 4): ?>
			<?php
				$fontAwesomeIcon1 = freddo_options('_onepage_fontawesome_1_cta', 'fa fa-bell');
				$choosePageBox1 = freddo_options('_onepage_choosepage_1_cta');
				$textButton1 = freddo_options('_onepage_boxtextbutton_1_cta', __('More Information', 'freddo'));
				$linkButton1 = freddo_options('_onepage_boxlinkbutton_1_cta', '#');
				$fontAwesomeIcon2 = freddo_options('_onepage_fontawesome_2_cta', 'fa fa-bell');
				$choosePageBox2 = freddo_options('_onepage_choosepage_2_cta');
				$textButton2 = freddo_options('_onepage_boxtextbutton_2_cta', __('More Information', 'freddo'));
				$linkButton2 = freddo_options('_onepage_boxlinkbutton_2_cta', '#');
				$fontAwesomeIcon3 = freddo_options('_onepage_fontawesome_3_cta', 'fa fa-bell');
				$choosePageBox3 = freddo_options('_onepage_choosepage_3_cta');
				$textButton3 = freddo_options('_onepage_boxtextbutton_3_cta', __('More Information', 'freddo'));
				$linkButton3 = freddo_options('_onepage_boxlinkbutton_3_cta', '#');
				$fontAwesomeIcon4 = freddo_options('_onepage_fontawesome_4_cta', 'fa fa-bell');
				$choosePageBox4 = freddo_options('_onepage_choosepage_4_cta');
				$textButton4 = freddo_options('_onepage_boxtextbutton_4_cta', __('More Information', 'freddo'));
				$linkButton4 = freddo_options('_onepage_boxlinkbutton_4_cta', '#');
			?>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon1): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon1); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox1): ?>
					<h3><?php echo get_the_title(intval($choosePageBox1)); ?></h3>
					<?php
					$post_content1 = get_post(intval($choosePageBox1));
					$content1 = $post_content1->post_content;
					?>
					<p><?php echo wp_trim_words($content1 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton1 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton1); ?>"><?php echo esc_html($textButton1); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon2): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon2); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox2): ?>
					<h3><?php echo get_the_title(intval($choosePageBox2)); ?></h3>
					<?php
					$post_content2 = get_post(intval($choosePageBox2));
					$content2 = $post_content2->post_content;
					?>
					<p><?php echo wp_trim_words($content2 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton2 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton2); ?>"><?php echo esc_html($textButton2); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon3): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon3); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox3): ?>
					<h3><?php echo get_the_title(intval($choosePageBox3)); ?></h3>
					<?php
					$post_content3 = get_post(intval($choosePageBox3));
					$content3 = $post_content3->post_content;
					?>
					<p><?php echo wp_trim_words($content3 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton3 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton3); ?>"><?php echo esc_html($textButton3); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon4): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon4); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox4): ?>
					<h3><?php echo get_the_title(intval($choosePageBox4)); ?></h3>
					<?php
					$post_content4 = get_post(intval($choosePageBox4));
					$content4 = $post_content4->post_content;
					?>
					<p><?php echo wp_trim_words($content4 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton4 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton4); ?>"><?php echo esc_html($textButton4); ?></a></div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if ($howManyBoxes == 5): ?>
			<?php
				$fontAwesomeIcon1 = freddo_options('_onepage_fontawesome_1_cta', 'fa fa-bell');
				$choosePageBox1 = freddo_options('_onepage_choosepage_1_cta');
				$textButton1 = freddo_options('_onepage_boxtextbutton_1_cta', __('More Information', 'freddo'));
				$linkButton1 = freddo_options('_onepage_boxlinkbutton_1_cta', '#');
				$fontAwesomeIcon2 = freddo_options('_onepage_fontawesome_2_cta', 'fa fa-bell');
				$choosePageBox2 = freddo_options('_onepage_choosepage_2_cta');
				$textButton2 = freddo_options('_onepage_boxtextbutton_2_cta', __('More Information', 'freddo'));
				$linkButton2 = freddo_options('_onepage_boxlinkbutton_2_cta', '#');
				$fontAwesomeIcon3 = freddo_options('_onepage_fontawesome_3_cta', 'fa fa-bell');
				$choosePageBox3 = freddo_options('_onepage_choosepage_3_cta');
				$textButton3 = freddo_options('_onepage_boxtextbutton_3_cta', __('More Information', 'freddo'));
				$linkButton3 = freddo_options('_onepage_boxlinkbutton_3_cta', '#');
				$fontAwesomeIcon4 = freddo_options('_onepage_fontawesome_4_cta', 'fa fa-bell');
				$choosePageBox4 = freddo_options('_onepage_choosepage_4_cta');
				$textButton4 = freddo_options('_onepage_boxtextbutton_4_cta', __('More Information', 'freddo'));
				$linkButton4 = freddo_options('_onepage_boxlinkbutton_4_cta', '#');				
				$fontAwesomeIcon5 = freddo_options('_onepage_fontawesome_5_cta', 'fa fa-bell');
				$choosePageBox5 = freddo_options('_onepage_choosepage_5_cta');
				$textButton5 = freddo_options('_onepage_boxtextbutton_5_cta', __('More Information', 'freddo'));
				$linkButton5 = freddo_options('_onepage_boxlinkbutton_5_cta', '#');				
			?>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon1): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon1); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox1): ?>
					<h3><?php echo get_the_title(intval($choosePageBox1)); ?></h3>
					<?php
					$post_content1 = get_post(intval($choosePageBox1));
					$content1 = $post_content1->post_content;
					?>
					<p><?php echo wp_trim_words($content1 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton1 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton1); ?>"><?php echo esc_html($textButton1); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon2): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon2); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox2): ?>
					<h3><?php echo get_the_title(intval($choosePageBox2)); ?></h3>
					<?php
					$post_content2 = get_post(intval($choosePageBox2));
					$content2 = $post_content2->post_content;
					?>
					<p><?php echo wp_trim_words($content2 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton2 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton2); ?>"><?php echo esc_html($textButton2); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon3): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon3); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox3): ?>
					<h3><?php echo get_the_title(intval($choosePageBox3)); ?></h3>
					<?php
					$post_content3 = get_post(intval($choosePageBox3));
					$content3 = $post_content3->post_content;
					?>
					<p><?php echo wp_trim_words($content3 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton3 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton3); ?>"><?php echo esc_html($textButton3); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon4): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon4); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox4): ?>
					<h3><?php echo get_the_title(intval($choosePageBox4)); ?></h3>
					<?php
					$post_content4 = get_post(intval($choosePageBox4));
					$content4 = $post_content4->post_content;
					?>
					<p><?php echo wp_trim_words($content4 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton4 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton4); ?>"><?php echo esc_html($textButton4); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon5): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon5); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox5): ?>
					<h3><?php echo get_the_title(intval($choosePageBox5)); ?></h3>
					<?php
					$post_content5 = get_post(intval($choosePageBox5));
					$content5 = $post_content5->post_content;
					?>
					<p><?php echo wp_trim_words($content5 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton5 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton5); ?>"><?php echo esc_html($textButton5); ?></a></div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if ($howManyBoxes == 6): ?>
			<?php
				$fontAwesomeIcon1 = freddo_options('_onepage_fontawesome_1_cta', 'fa fa-bell');
				$choosePageBox1 = freddo_options('_onepage_choosepage_1_cta');
				$textButton1 = freddo_options('_onepage_boxtextbutton_1_cta', __('More Information', 'freddo'));
				$linkButton1 = freddo_options('_onepage_boxlinkbutton_1_cta', '#');
				$fontAwesomeIcon2 = freddo_options('_onepage_fontawesome_2_cta', 'fa fa-bell');
				$choosePageBox2 = freddo_options('_onepage_choosepage_2_cta');
				$textButton2 = freddo_options('_onepage_boxtextbutton_2_cta', __('More Information', 'freddo'));
				$linkButton2 = freddo_options('_onepage_boxlinkbutton_2_cta', '#');
				$fontAwesomeIcon3 = freddo_options('_onepage_fontawesome_3_cta', 'fa fa-bell');
				$choosePageBox3 = freddo_options('_onepage_choosepage_3_cta');
				$textButton3 = freddo_options('_onepage_boxtextbutton_3_cta', __('More Information', 'freddo'));
				$linkButton3 = freddo_options('_onepage_boxlinkbutton_3_cta', '#');
				$fontAwesomeIcon4 = freddo_options('_onepage_fontawesome_4_cta', 'fa fa-bell');
				$choosePageBox4 = freddo_options('_onepage_choosepage_4_cta');
				$textButton4 = freddo_options('_onepage_boxtextbutton_4_cta', __('More Information', 'freddo'));
				$linkButton4 = freddo_options('_onepage_boxlinkbutton_4_cta', '#');				
				$fontAwesomeIcon5 = freddo_options('_onepage_fontawesome_5_cta', 'fa fa-bell');
				$choosePageBox5 = freddo_options('_onepage_choosepage_5_cta');
				$textButton5 = freddo_options('_onepage_boxtextbutton_5_cta', __('More Information', 'freddo'));
				$linkButton5 = freddo_options('_onepage_boxlinkbutton_5_cta', '#');					
				$fontAwesomeIcon6 = freddo_options('_onepage_fontawesome_6_cta', 'fa fa-bell');
				$choosePageBox6 = freddo_options('_onepage_choosepage_6_cta');
				$textButton6 = freddo_options('_onepage_boxtextbutton_6_cta', __('More Information', 'freddo'));
				$linkButton6 = freddo_options('_onepage_boxlinkbutton_6_cta', '#');				
			?>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon1): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon1); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox1): ?>
					<h3><?php echo get_the_title(intval($choosePageBox1)); ?></h3>
					<?php
					$post_content1 = get_post(intval($choosePageBox1));
					$content1 = $post_content1->post_content;
					?>
					<p><?php echo wp_trim_words($content1 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton1 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton1); ?>"><?php echo esc_html($textButton1); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon2): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon2); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox2): ?>
					<h3><?php echo get_the_title(intval($choosePageBox2)); ?></h3>
					<?php
					$post_content2 = get_post(intval($choosePageBox2));
					$content2 = $post_content2->post_content;
					?>
					<p><?php echo wp_trim_words($content2 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton2 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton2); ?>"><?php echo esc_html($textButton2); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon3): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon3); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox3): ?>
					<h3><?php echo get_the_title(intval($choosePageBox3)); ?></h3>
					<?php
					$post_content3 = get_post(intval($choosePageBox3));
					$content3 = $post_content3->post_content;
					?>
					<p><?php echo wp_trim_words($content3 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton3 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton3); ?>"><?php echo esc_html($textButton3); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon4): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon4); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox4): ?>
					<h3><?php echo get_the_title(intval($choosePageBox4)); ?></h3>
					<?php
					$post_content4 = get_post(intval($choosePageBox4));
					$content4 = $post_content4->post_content;
					?>
					<p><?php echo wp_trim_words($content4 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton4 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton4); ?>"><?php echo esc_html($textButton4); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon5): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon5); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox5): ?>
					<h3><?php echo get_the_title(intval($choosePageBox5)); ?></h3>
					<?php
					$post_content5 = get_post(intval($choosePageBox5));
					$content5 = $post_content5->post_content;
					?>
					<p><?php echo wp_trim_words($content5 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton5 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton5); ?>"><?php echo esc_html($textButton5); ?></a></div>
				<?php endif; ?>
			</div>
			<div class="four cta_columns_single">
				<?php if($fontAwesomeIcon6): ?>
					<div class="ctaIcon"><i class="<?php echo esc_attr($fontAwesomeIcon6); ?>" aria-hidden="true"></i></div>
				<?php endif; ?>
				<?php if($choosePageBox6): ?>
					<h3><?php echo get_the_title(intval($choosePageBox6)); ?></h3>
					<?php
					$post_content6 = get_post(intval($choosePageBox6));
					$content6 = $post_content6->post_content;
					?>
					<p><?php echo wp_trim_words($content6 , intval($textLenght), esc_html($customMore) ); ?></p>
				<?php endif; ?>
				<?php if($textButton6 || is_customize_preview()): ?>
					<div class="freddoButton cta"><a href="<?php echo esc_url($linkButton6); ?>"><?php echo esc_html($textButton6); ?></a></div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>