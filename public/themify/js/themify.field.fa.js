jQuery(function($){

	var $target;

	function getDocHeight() {
		var D = document;
		return Math.max(
			Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
			Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
			Math.max(D.body.clientHeight, D.documentElement.clientHeight)
		);
	}

	function show_lightbox( selected ) {
		var top = $(document).scrollTop() + 80,
			$lightbox = $("#themify_lightbox_fa");

		$('#themify_lightbox_overlay').show();
		$lightbox
		.show()
		.css('top', getDocHeight())
		.animate({
			top: top
		}, 800 );
		if( selected ) {
			$('a', $lightbox)
			.removeClass('selected')
			.find('.' + selected)
			.closest('a')
				.addClass('selected');
		}
	}

	function close_lightbox() {
		$('#themify_lightbox_fa').animate({
			top: getDocHeight()
		}, 800, function() {
			$('#themify_lightbox_overlay').hide();
			$('#themify_lightbox_fa').hide();
		});
	}

	$('.themify_fa_toggle').live('click', function(){
		$target = $($(this).attr('data-target'));
		if( ! $target.length > 0 )
			$target = $(this).prev();
		show_lightbox( $target.val() );
		return false;
	});
	$('.lightbox_container a', '#themify_lightbox_fa').live('click', function(){
		$target.val( $(this).attr('data-name') );
		close_lightbox();
		return false;
	});

	$('#themify_lightbox_overlay, #themify_lightbox_fa .close_lightbox').live('click', function(){
		close_lightbox();
	});
});