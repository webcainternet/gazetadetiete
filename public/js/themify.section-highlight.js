/**
 * Themify Section Highlight
 * Copyright (c) Themify
 */
;
(function ( $, window, document, undefined ) {

	var pluginName = "themifySectionHighlight",
		defaults = {
			speed: 1500
		};

	function Plugin( element, options ) {
		this.element = element;
		this.options = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this.onClicking = false;
		this.init();
	}

	Plugin.prototype = {
		init: function () {
			var self = this,
				sections = [],
				$mainNavLink = $( 'a', $( '#main-nav' ) );

			// collects position scrollto
			$mainNavLink.each( function () {
				var url = $( this ).attr( 'href' );
				if ( 'undefined' != typeof(url) && url.indexOf( '#' ) != - 1 && url.length > 1 ) {
					sections.push( $( this ).prop( 'hash' ) );
				}
			});
			sections.push( '#header' );

			// set caching position top
			this.updateSecPosition( sections );
			$( window ).resize( function () {
				self.updateSecPosition( sections );
			});

			// change hash url
			// collects scrollto hash
			$mainNavLink.each( function () {
				var url = $( this ).prop( 'hash' );
				if ( 'undefined' != typeof(url) && url.indexOf( '#' ) != - 1 && url.length > 1 ) {
					sections.push( url );
				}
			});

			if ( sections.length > 0 ) {
				$.each( sections, function ( index, value ) {
					var section = value, obj = $( value );
					if ( obj.length > 0 ) {
						var offsetY = obj.data( 'offsetY' ),
							elemHeight = obj.height(),
							didScroll = false;

						$(window).scroll(function() {
							didScroll = true;
						});

						setInterval(function() {
							if ( didScroll ) {
								didScroll = false;

								var headerWrapHeight = $( '#headerwrap' ).outerHeight();
								var scrollAmount = $( window ).scrollTop() + headerWrapHeight;
								// If hit top of element
								if ( scrollAmount > offsetY && ( offsetY + elemHeight ) > scrollAmount ) {
									if ( self.onClicking ) {
										return;
									}
									if ( section.replace( '#', '' ) !== 'header' ) {
										location.hash = '#!/' + section.replace( '#', '' );
									}

									$( 'a[href*=' + section + ']' ).parent( 'li' ).addClass( 'current_page_item' ).siblings().removeClass( 'current_page_item' );

									// remove hash if header
									if ( section.replace( '#', '' ) == 'header' ) {
										self.clearHash();
										$mainNavLink.parent( 'li' ).siblings().removeClass( 'current_page_item' );
									}
								}
							}
						}, 500);
					}
				});
			}
		},

		isTouchDevice: function () {
			try {
				document.createEvent( 'TouchEvent' );
				return true;
			} catch ( e ) {
				return false;
			}
		},

		clearHash: function () {
			// remove hash
			if ( window.history && window.history.replaceState ) {
				window.history.replaceState( '', '', window.location.pathname );
			} else {
				window.location.href = window.location.href.replace( /#.*$/, '#' );
			}
		},

		updateSecPosition: function ( sections ) {
			if ( sections.length > 0 ) {
				$.each( sections, function ( index, value ) {
					// cache the position
					$( value ).each( function () {
						$( this ).data( 'offsetY', parseInt( $( this ).offset().top ) );
					});
				});
			}
		}
	};

	$.fn[pluginName] = function ( options ) {
		return this.each( function () {
			if ( ! $.data( this, "plugin_" + pluginName ) ) {
				$.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
			}
		});
	};

})( jQuery, window, document );