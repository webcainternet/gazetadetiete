<?php
/**
 * Template for site footer
 * @package themify
 * @since 1.0.0
 */
?>
<?php
/** Themify Default Variables
 @var object */
	global $themify; ?>

	<?php themify_layout_after(); //hook ?>
    </div>
	<!-- /body -->
		
	<div id="footerwrap">
		<div id="footerwrap-inner">
    
			<?php themify_footer_before(); // hook ?>
			<footer id="footer" class="pagewidth clearfix">
				<?php themify_footer_start(); // hook ?>	
	
				<?php get_template_part( 'includes/footer-widgets'); ?>
		
				<div class="footer-text clearfix">
					<?php themify_the_footer_text(); ?>
					<?php themify_the_footer_text('right'); ?>
				</div>
				<!-- /footer-text --> 
				<?php themify_footer_end(); // hook ?>
			</footer>
			<!-- /#footer --> 
			<?php themify_footer_after(); // hook ?>

		</div>
		<!-- /footerwrap-inner -->

		<div id="footer-tab">
			<a href="#"></a>
		</div>
		<!-- /footer-tab -->

	</div>
	<!-- /#footerwrap -->
	
</div>
<!-- /#pagewrap -->

<?php
/**
 *  Stylesheets and Javascript files are enqueued in theme-functions.php
 */
?>

<?php themify_body_end(); // hook ?>
<!-- wp_footer -->
<?php wp_footer(); ?>

</body>
</html>