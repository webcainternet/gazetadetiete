<!-- post-nav -->
<div class="post-nav clearfix"> 
	<?php previous_post_link('<span class="prev">%link</span>', '<i class="icon-arrow-prev"></i>') ?>
	<?php next_post_link('<span class="next">%link</span>', '<i class="icon-arrow-next"></i>') ?>
	<?php if( isset($_GET['porto_expand']) && $_GET['porto_expand'] == 1 ): ?>
		<span class="right">
			<a href="#" class="close-portfolio"><i class="icon-close"></i></a>
		</span>
	<?php endif; ?>
</div>
<!-- /post-nav -->