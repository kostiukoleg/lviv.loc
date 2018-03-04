<?php 
$showBlog = freddo_options('_onepage_section_blog', '');
?>
<?php if ($showBlog == 1) : ?>
	<?php
		$blogSectionID = freddo_options('_onepage_id_blog','blog');
		$blogTitle = freddo_options('_onepage_title_blog',__('News', 'freddo'));
		$blogSubTitle = freddo_options('_onepage_subtitle_blog', __('Latest Posts', 'freddo'));
		$blogPageBox = freddo_options('_onepage_choosepage_blog');
		$blogtoShow = freddo_options('_onepage_noposts_blog','3');
		$blogTextButton = freddo_options('_onepage_textbutton_blog',__('Go to the blog!', 'freddo'));
		$blogLinkButton = freddo_options('_onepage_linkbutton_blog', '#');
	?>
<section class="freddo_onepage_section freddo_blog" id="<?php echo esc_attr($blogSectionID); ?>">
	<div class="freddo_blog_color"></div>
	<div class="freddo_action_blog">
	<?php if($blogTitle || is_customize_preview()): ?>
		<h2 class="freddo_main_text"><?php echo esc_html($blogTitle); ?></h2>
	<?php endif; ?>
	<?php if($blogSubTitle || is_customize_preview()): ?>
		<p class="freddo_subtitle"><?php echo esc_html($blogSubTitle); ?></p>
	<?php endif; ?>
		<div class="blog_columns">
			<div class="one blog_columns_four">
				<div class="blogInner">
					<?php if($blogPageBox) : ?>
					<h3><?php echo get_the_title(intval($blogPageBox)); ?></h3>
					<?php 
						$post_content = get_post(intval($blogPageBox));
						$content = $post_content->post_content;
						$content = apply_filters( 'the_content', $content );
						$content = str_replace( ']]>', ']]&gt;', $content );
						echo $content;
					?>
					<?php endif; ?>
					<?php if($blogTextButton || is_customize_preview()): ?>
						<div class="freddoButton aboutus"><a href="<?php echo esc_url($blogLinkButton); ?>"><?php echo esc_html($blogTextButton); ?></a></div>
					<?php endif; ?>
				</div>
			</div>
			<?php if ('' != get_the_post_thumbnail($blogPageBox)) : ?>
				<div class="two features_columns_three">
					<div class="featuresInnerImage">
						<?php echo get_the_post_thumbnail(intval($blogPageBox), 'large'); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>