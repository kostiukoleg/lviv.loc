<?php $showSkills = freddo_options('_onepage_section_skills', ''); ?>
<?php if ($showSkills == 1) : ?>
	<?php
		$skillsSectionID = freddo_options('_onepage_id_skills', 'skills');
		$skillsTitle = freddo_options('_onepage_title_skills', __('Our Skills', 'freddo'));
		$skillsSubTitle = freddo_options('_onepage_subtitle_skills', __('What We Do', 'freddo'));
		$skillsPageBox = 119;
		$skillName = array();
		$skillValue = array();
		for( $number = 1; $number < FREDDO_VALUE_FOR_SKILLS; $number++ ){
			$skillName["$number"] = freddo_options('_onepage_skillname_'.$number.'_skills', '');
			$skillValue["$number"] = freddo_options('_onepage_skillvalue_'.$number.'_skills', '');
		}
	?>
<section class="freddo_onepage_section freddo_skills" id="<?php echo esc_attr($skillsSectionID); ?>">
	<div class="freddo_skills_color"></div>
	<div class="freddo_action_skills">
	<?php if($skillsTitle || is_customize_preview()): ?>
		<h2 class="freddo_main_text"><?php echo esc_html($skillsTitle); ?></h2>
	<?php endif; ?>
	<?php if($skillsSubTitle || is_customize_preview()): ?>
		<p class="freddo_subtitle"><?php echo esc_html($skillsSubTitle); ?></p>
	<?php endif; ?>
		<div class="skills_columns">
			<div class="one skills_columns_three">
				<div class="skillsInner">
					<?php if($skillsPageBox) : ?>
					<h3><?php echo get_the_title(intval($skillsPageBox)); ?></h3>
					<?php 
						$post_content = get_post(intval($skillsPageBox));
						$content = $post_content->post_content;
						$content = apply_filters( 'the_content', $content );
						$content = str_replace( ']]>', ']]&gt;', $content );
						echo $content;
					?>
					<?php endif; ?>
					<?php if($aboutusButtonText || is_customize_preview()): ?>
						<div class="freddoButton skills"><a href="<?php echo esc_url($skillsButtonLink); ?>"><?php echo esc_html($featuresButtonText); ?></a></div>
					<?php endif; ?>
				</div>
			</div>
			<?php if ('' != get_the_post_thumbnail($skillsPageBox)) : ?>
				<div class="two skills_columns_three">
					<div class="skillsInnerImage">
						<?php echo get_the_post_thumbnail(intval($skillsPageBox), 'large'); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>