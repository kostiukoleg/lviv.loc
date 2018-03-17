<?php
$showSlider = freddo_options('_onepage_section_slider', '');
?>
<?php if ($showSlider == 1) : ?>
<?php
	$showScrollDown = freddo_options('_onepage_scrolldown_slider', '1');
	$sliderEffectScroll = freddo_options('_onepage_effect_slider', 'withZoom');
	$sliderSectionID = freddo_options('_onepage_id_slider', 'slider');
	$slideImage = array();
	$slideText = array();
	$slideSubText = array();
	for( $number = 1; $number < FREDDO_VALUE_FOR_SLIDER; $number++ ){
		$slideImage["$number"] = freddo_options('_onepage_image_'.$number.'_slider', '');
		$slideText["$number"] = freddo_options('_onepage_text_'.$number.'_slider', '');
		$slideSubText["$number"] = freddo_options('_onepage_subtext_'.$number.'_slider', '');
	}
?>
<section class="freddo_onepage_section freddo_slider <?php echo esc_attr($sliderEffectScroll); ?>" id="<?php echo esc_attr($sliderSectionID); ?>">
	<div class="flexslider">
	  <ul class="slides">
		<?php for( $number = 1; $number < FREDDO_VALUE_FOR_SLIDER; $number++ ) : ?>
			<?php if ($slideImage["$number"]) : ?>
				<li>
					<div class="flexImage" style="background-image: url(<?php echo esc_url($slideImage["$number"]); ?>), url(<?php echo get_template_directory_uri().'/images/slider-bg.png' ?>);background-blend-mode: screen;">
					</div>
					<div class="flexText">
						<div class="inside">
							<?php if ($slideText["$number"] || is_customize_preview()) : ?>
							<h2><?php echo esc_html($slideText["$number"]); ?></h2>
							<?php endif; ?>
							<?php if ($slideSubText["$number"] || is_customize_preview()) : ?>
							<span><?php echo esc_html($slideSubText["$number"]); ?></span>
							<?php endif; ?>
						</div>
					</div>
				</li>
			<?php endif; ?>
		<?php endfor; ?>
	  </ul>
	  <?php if ($showScrollDown) : ?>
		<?php $scrollText = freddo_options('_onepage_scrolldown_text', __('Scroll Down', 'freddo')); ?>
		<div class="scrollDown"><span><?php echo esc_html($scrollText); ?></span></div>
	<?php endif; ?>
	</div>
</section>
<?php endif; ?>