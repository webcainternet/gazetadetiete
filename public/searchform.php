<?php
/**
 * Template for search form.
 * @package themify
 * @since 1.0.0
 */
?>
<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
	<i class="fa fa-search"></i>
	<input type="text" name="s" id="s" placeholder="<?php _e('Search', 'themify'); ?>" />

</form>