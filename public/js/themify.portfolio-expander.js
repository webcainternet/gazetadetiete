/*
 *  Themify Portfolio Expander
 *  Expand Fullscreen Portfolio Content
 *  http://themify.me/themes/fullpane
 */
;(function ( $, window, document, undefined ) {

	var pluginName = "themifyPortfolioExpander",
		defaults = {
			itemContainer: '.shortcode.portfolio',
			animspeed: 700,
			animeasing: 'jswing',
			anchorLink: '.portfolio-post .post-content',
			itemSelector: '.portfolio-post',
			navigationLink: '.portfolio-expanded .post-nav .prev a, .portfolio-expanded .post-nav .next a',
			loader: '<div class="themify-loader">' +
						'<div class="themify-loader_1 themify-loader_blockG"></div>' +
						'<div class="themify-loader_2 themify-loader_blockG"></div>' +
						'<div class="themify-loader_3 themify-loader_blockG"></div>' +
					'</div>',
			template : '<div id="portfolio-full" class="portfolio-full"></div>'
		};

	function Plugin ( element, options ) {
		this.element = element;
		this.settings = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this._name = pluginName;
		this.init();
	}

	Plugin.prototype = {
		init: function () {
			var self = this,
				$body = $('body'),
				$fullscreen = $(self.settings.template).prependTo( $body );
			
			// When portfolio clicked
			$(self.settings.anchorLink).on('click', function(e){
				e.preventDefault();
				var $this = $(this).find('.porto-expand-js'),
					$portfolio = $this.closest(self.settings.itemSelector),
					url = self.UpdateQueryString( 'porto_expand', 1, $this.attr('href') );
				
				$.ajax({
					type: "POST",
					url: url,
					dataType: 'html',
					beforeSend: function(xhr){
						// Hide scrollbar
						$('html,body').css( 'overflow', 'hidden' );
						self.animateExpand( $portfolio, 'expand', true );
					},
					success: function( data ){
						var $newElems = $(data);
						$portfolio.addClass('current-expanded');
						$fullscreen.hide().html( $newElems ).fadeIn(800).promise().done(function(){
							$('#portfolio-clone').remove();
							$body.trigger('portfolioExpanded', $newElems);
						});
					}
				});
			});

			// When button close clicked on fullscreen
			$body.on('click', '.post-nav .close-portfolio', function(e){
				e.preventDefault();
				var $portfolio = $(self.settings.itemSelector + '.current-expanded').removeClass('current-expanded');
				self.hidePortfolio( $portfolio );
			});

			// Navigation Click
			$body.on('click', self.settings.navigationLink, function(e){
				e.preventDefault();
				var url = self.UpdateQueryString( 'porto_expand', 1, $(this).attr('href') ),
					$portfolio = $(self.settings.itemSelector + '.current-expanded');

				$.ajax({
					type: "POST",
					url: url,
					dataType: 'html',
					beforeSend: function(xhr){
						$('html,body').css( 'overflow', 'hidden' );
						$fullscreen.empty();
						self.animateExpand( $portfolio, 'expand', false );
						$('#portfolio-clone').addClass('done');
					},
					success: function( data ){
						var $newElems = $(data);
						$fullscreen.hide().html($newElems).fadeIn(800).promise().done(function(){
							$('#portfolio-clone').remove();
							$body.trigger('portfolioExpanded', $newElems);
						});
					}
				});
			});
		},

		hidePortfolio: function( $portfolio ) {
			var self = this;
			self.animateExpand( $portfolio, 'expand', false, 'no' );

			var $clone			= $('#portfolio-clone'),
				$fullscreen = $('#portfolio-full');

			// fade in the clone
			$clone.hide().fadeIn(200)
			.promise().done(function(){
				$fullscreen.empty();
				
				$(this).animate({
					left	: $portfolio.offset().left + 'px',
					top		: $portfolio.offset().top + 'px',
					width	: $portfolio.width() + 'px',
					height	: $portfolio.height() + 'px'
				}, self.settings.animspeed, self.settings.animeasing, function() {
					$(this).remove();
					$('body').trigger('portfolioClosed');
				});
			});
		},

		animateExpand: function( $portfolio, effect, anim, showLoader ){
			effect = effect || 'expand';
			showLoader = showLoader || 'yes';

			var	self = this,
				$clone	= $portfolio.clone()
					.removeClass('current-expanded').addClass('expanding')
					.css({
						left	: $portfolio.offset().left + 'px',
						top		: $portfolio.offset().top + 'px',
						zIndex	: 1001,
						margin	: '0px',
						height	: $portfolio.height() + 'px',
						opacity : 1
					}).attr( 'id', 'portfolio-clone' );
			
			// remove unnecessary elements from the clone
			$clone.children().remove().end();

			if (showLoader == 'yes') { 
				$clone.html(self.settings.loader);
			}
			
			// animate?
			$.fn.applyStyle = ( anim ) ? $.fn.animate : $.fn.css;
			
			var clonestyle 	= {
				width	: $(window).width() + 'px',
				height	: $(window).height() + 'px',
				left	: '0px',
				top		: $(window).scrollTop() + 'px'
			};

			var animateAction = {
				duration : self.settings.animspeed,
				easing : self.settings.animeasing,
				complete : function() {
					$(this).addClass('done');
				}
			};

			$clone.appendTo( $('body') )
			.stop().applyStyle( clonestyle, $.extend( true, [], animateAction) );
		},

		UpdateQueryString: function(a,b,c){
			c||(c=window.location.href);var d=RegExp("([?|&])"+a+"=.*?(&|#|$)(.*)","gi");if(d.test(c))return b!==void 0&&null!==b?c.replace(d,"$1"+a+"="+b+"$2$3"):c.replace(d,"$1$3").replace(/(&|\?)$/,"");if(b!==void 0&&null!==b){var e=-1!==c.indexOf("?")?"&":"?",f=c.split("#");return c=f[0]+e+a+"="+b,f[1]&&(c+="#"+f[1]),c}return c;
		}

	};

	$.fn[ pluginName ] = function ( options ) {
		return this.each(function() {
			if ( !$.data( this, "plugin_" + pluginName ) ) {
				$.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
			}
		});
	};

})( jQuery, window, document );