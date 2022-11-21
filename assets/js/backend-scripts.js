/*------------------------ 
Backend related javascript 2
------------------------*/
(function( $ ) {
	"use strict";
	$(document).ready( function(  ) {
		$( '.primera-ajax-btn' ).click(function( event ){
			event.preventDefault();

			var data = {};
			data[ 'action' ] = $(this).attr( 'data-action' );
			data[ 'nonce' ] = primera.security_nonce;

			var variable_data = $( '#' + $(this).attr( 'data-action' ) + '_form' ).serializeArray();
			$.each( variable_data, function(i, field){
				data[ field.name ] = field.value;
			});

			create_primera_ajax_request( data );
		});


		function create_primera_ajax_request( data ){
			$.ajax({
				type 		: "GET",
				dataType 	: "json",
				url 		: primera.ajaxurl,
				data 		: data,
				beforeSend: function use( data ){
					// alert( data.action );
				},
				success: function( response ) {
					primera_ajax_render_status( response.data );						

					if( response.data.request_status === 'working' ){
						create_primera_ajax_request( response.data );
					}else{
						// alert( 'this request is :  ' + response.data.request_status );
					}

				}
			});
		}

		function primera_ajax_render_status( data ){
			var resp_div = data.action + '_response_div';
			var html = '';

			$.each( data.steps, function( i, step ){
				html  += primera_ajax_render_step( step , data.action );
			});

			$("#"+ resp_div ).html( html );	
		}

		function primera_ajax_render_step( step ){
			var step_status	= '';
			var step_color 	= '';

			if( step.error !== undefined ){
				step_status = step.error;
				step_color 	= 'red';
			}else{
				step.total = step.total === undefined ? 'N/A' : step.total;

				if( step.done === step.total ){
					step_status = 'completed';
					step_color 	= 'darkblue';
				}
	
				if( step.total === 'N/A' || step.total > step.done ){
					step_status = 'In Progress';
					step_color 	= 'forestgreen';
				}
	
				if( step.total > step.done || ( step.done / step.total) > 0 ){
					var step_width 	= step.done / step.total * 100 ;
				}else{
					var step_width 	= '100';
				}	
			}

			var step_style = 'style="width: ' + step_width + '%;background-color: ' + step_color + ';"' ;
			var step_title = step.title + ' : ' + step_status  + ' ( ' +  step.done + ' / ' + step.total + ' )' ;
			return '<div class="primera-ajax-resp-step"><div class="primera-ajax-resp-step-progress" ' + step_style + '><span>' + step_title + '</span></div></div>'
		}
	});
})( jQuery );