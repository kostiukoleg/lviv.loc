<?php $showServices = freddo_options('_onepage_section_services', ''); ?>
<?php if ($showServices == 1) : ?>
	<?php
		$servicesSectionID = freddo_options('_onepage_id_services', 'services');
		$servicesTitle = freddo_options('_onepage_title_services', __('Services', 'freddo'));
		$servicesSubTitle = freddo_options('_onepage_subtitle_services', __('What We Offer', 'freddo'));
		$servicesPhrase = freddo_options('_onepage_phrase_services', '');
		$servicesTextarea = freddo_options('_onepage_textarea_services', '');
		$servicesImage = freddo_options('_onepage_servimage_services');
		$textLenght = freddo_options('_onepage_lenght_services', '30');
		$customMore = freddo_options('_excerpt_more', '&hellip;');
		$servicesPageBox = freddo_options('_onepage_choosepage_services');
		$servicesButtonText = freddo_options('_onepage_textbutton_services', __('More Information', 'freddo'));
		$servicesButtonLink = freddo_options('_onepage_linkbutton_services', '#');
		$singleServiceBox = array();
		$singleServiceFont = array();
		for( $number = 1; $number < FREDDO_VALUE_FOR_SERVICES; $number++ ){
			$singleServiceBox["$number"] = freddo_options('_onepage_choosepage_'.$number.'_services', '');
			$singleServiceFont["$number"] = freddo_options('_onepage_fontawesome_'.$number.'_services', '');
		}
	?>
<section class="freddo_onepage_section freddo_services" id="<?php echo esc_attr($servicesSectionID); ?>">
	<div class="freddo_services_color"></div>
	<div class="freddo_action_services">
		<?php if($servicesTitle || is_customize_preview()): ?>
			<h2 class="freddo_main_text"><?php echo esc_html($servicesTitle); ?></h2>
		<?php endif; ?>
		<?php if($servicesSubTitle || is_customize_preview()): ?>
			<p class="freddo_subtitle"><?php echo esc_html($servicesSubTitle); ?></p>
		<?php endif; ?>
		<div class="services_columns">
			<div class="one services_columns_three">
				<div class="servicesInner">
					<?php if($servicesPageBox) : ?>
					<h3><?php echo get_the_title(intval($servicesPageBox)); ?></h3>
					<?php 
						$post_content = get_post(intval($servicesPageBox));
						$content = $post_content->post_content;
						$content = apply_filters( 'the_content', $content );
						$content = str_replace( ']]>', ']]&gt;', $content );
						echo $content;
					?>
					<?php endif; ?>
					<!--<?php if($servicesButtonText || is_customize_preview()): ?>
						<div class="freddoButton services"><a href="<?php echo esc_url($servicesButtonLink); ?>"><?php echo esc_html($servicesButtonText); ?></a></div>
					<?php endif; ?>-->
				</div>
			</div>
			<?php if ('' != get_the_post_thumbnail($servicesPageBox)) : ?>
				<div class="two services_columns_three">
					<div class="servicesInnerImage">
						<?php echo get_the_post_thumbnail(intval($servicesPageBox), 'large'); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>