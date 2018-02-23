<?php $showFeatures = freddo_options('_onepage_section_features', ''); ?>
<?php if ($showFeatures == 1) : ?>
	<?php
		$featuresSectionID = freddo_options('_onepage_id_features', 'features');
		$featuresTitle = freddo_options('_onepage_title_features', __('Elements', 'freddo'));
		$featuresSubTitle = freddo_options('_onepage_subtitle_features', __('Amazing Features', 'freddo'));
		$featuresPageBox = 102;
		$howManyBoxes = freddo_options('_onepage_manybox_features', '3');
		$textLenght = freddo_options('_onepage_lenght_features', '20');
		$customMore = freddo_options('_excerpt_more', '&hellip;');
	?>
<section class="freddo_onepage_section freddo_features" id="<?php echo esc_attr($featuresSectionID); ?>">
	<div class="freddo_features_color"></div>
	<div class="freddo_action_features">
		<?php if($featuresTitle || is_customize_preview()): ?>
			<h2 class="freddo_main_text"><?php echo esc_html($featuresTitle); ?></h2>
		<?php endif; ?>
		<?php if($featuresSubTitle || is_customize_preview()): ?>
			<p class="freddo_subtitle"><?php echo esc_html($featuresSubTitle); ?></p>
		<?php endif; ?>
		<div class="features_columns">

			<div class="one features_columns_three">
				<div class="featuresInner">
					<?php if($featuresPageBox) : ?>
					<h3><?php echo get_the_title(intval($featuresPageBox)); ?></h3>
					<?php 
						$post_content = get_post(intval($featuresPageBox));
						$content = $post_content->post_content;
						$content = apply_filters( 'the_content', $content );
						$content = str_replace( ']]>', ']]&gt;', $content );
						echo $content;
					?>
					<?php endif; ?>
					<?php if($aboutusButtonText || is_customize_preview()): ?>
						<div class="freddoButton features"><a href="<?php echo esc_url($featuresButtonLink); ?>"><?php echo esc_html($featuresButtonText); ?></a></div>
					<?php endif; ?>
				</div>
			</div>
			<?php if ('' != get_the_post_thumbnail($featuresPageBox)) : ?>
				<div class="two features_columns_three">
					<div class="featuresInnerImage">
						<?php echo get_the_post_thumbnail(intval($featuresPageBox), 'large'); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>