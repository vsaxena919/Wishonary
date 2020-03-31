( function( $ ) {

	'use strict';

	$( document ).ready( function() {

		// Get Page Number
		function yz_media_find_page_number( el ) {
			el.find( '.yz-page-symbole' ).remove();
			return parseInt( el.text() );
		}

		function yz_init_media_resolutions() {

			var media_img = $( '.yz-media-small-box .yz-media-group-photos .yz-media-item-img,.yz-media-small-box video, .yz-media-small-box .yz-media-group-videos .yz-media-item-img' );
			var media_img2 = $( '.yz-media-small-box .yz-media-group-photos .yz-media-item-img,.yz-media-small-box video, .yz-media-small-box .yz-media-group-videos .yz-media-item-img' );
	
			media_img.height( media_img.width() );
			media_img2.height( media_img2.width() );

		}

		yz_init_media_resolutions();

		var mediaresizeTimer;

		$( window ).on( 'resize', function ( e ) {
		    clearTimeout( mediaresizeTimer );
		    mediaresizeTimer = setTimeout( function () {
		        // Init Media Itels After Resize.
				yz_init_media_resolutions();
		    }, 250 );
		});

		// Media Widget Filter.
		$( document ).on( 'click', '.yz-media-filter .yz-filter-content', function( e ) {
			
			var button = $( this );

			if ( $( '.yz-media-filter .yz-filter-content.loading')[0] || button.hasClass( 'yz-current-filter' ) ) {
				return;
			}


			// Get Data.
			var parent = button.closest( '.yz-media' );
			var type = button.data( 'type' );

			if ( parent.find( '.yz-media-group-' + type )[0] ) {
				// Set New Current Tab & Remove Loading Icon.
				parent.find( '.yz-media-filter .yz-filter-content' ).removeClass( 'yz-current-filter' );
				button.removeClass( 'loading' ).addClass( 'yz-current-filter' );
				parent.find( 'div[data-active="true"]' ).fadeOut( 100, function() {
					$( this ).attr( 'data-active', false );
					parent.find( '.yz-media-group-' + type ).attr( 'data-active', true ).fadeIn();
				} );
				return;
			}

			var main_content = parent.find( '.yz-media-widget' );


			$.ajax( {
				url: Youzer.ajax_url,
				type: 'post',
				data: {
					action: 'yz_media_pagination',
					data : button.data(),
				},
				beforeSend: function() {
					button.addClass( 'loading' );
				},
				success: function( html ) {

				var	$c = $( '<div class="yz-media-group-' + type + '" data-active="true"></div>' ).append( '<div class="yz-media-widget-content">' + html + '</div>' );
				var view_all  = $c.find( '.yz-media-view-all' ).clone();
					$c.find( '.yz-media-view-all' ).remove();
					// Set New Current Tab & Remove Loading Icon.
					parent.find( '.yz-media-filter .yz-filter-content' ).removeClass( 'yz-current-filter' );
					button.removeClass( 'loading' ).addClass( 'yz-current-filter' );
						$c.append( view_all );
						parent.find( 'div[data-active="true"]' ).fadeOut( 100, function(){
						$( this ).attr( 'data-active', false );
						main_content.append( $c );
						yz_init_media_resolutions();
					});

				}
			});

		});

		// Get Comments Page
		$( document ).on( 'click', '.yz-media .yz-pagination a', function( e ) {
			
			var button = $( this );

			e.preventDefault();
			var pagination = button.closest( '.yz-pagination' );
			var main_content = button.closest( '.yz-media' ).find( '.yz-media-items' );

			$.ajax( {
				url: Youzer.ajax_url,
				type: 'post',
				data: {
					action: 'yz_media_pagination',
					// base:  pagination.attr( 'data-base' ),
					// user_id: pagination.attr( 'data-user-id' ),
					data : $( this ).closest( '.yz-pagination' ).data(),
					page: yz_media_find_page_number( button.clone() ),
				},
				beforeSend: function() {
					var button_clone = button.clone().html( '<i class="fas fa-spinner fa-spin"></i>' );
					button.hide( 0, function(){
						button_clone.insertAfter( $( this ) );
					});
				},
				success: function( html ) {

		            $( 'html, body' ).animate( {
		                scrollTop: main_content.offset().top - 150
		            }, 1000 );
		            
					main_content.html( html ).fadeIn();
				}
			});

		});

	});

})( jQuery );