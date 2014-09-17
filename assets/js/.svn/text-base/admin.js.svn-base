var ess_admin = 
{
	init : function()
	{
		ess_admin.set_button_actions();
		ess_admin.set_radio_buttons();
		ess_admin.handle_owner_block();
		ess_admin.handle_tab_selected();
		ess_admin.handle_timezone();
	},
	
	set_button_actions : function()
	{
		jQuery('#btAddESS').click(function()
		{
			jQuery('.em-warning').hide();
			jQuery('#add_feed_form').slideToggle();
		});
		
		jQuery('#em-menu-import').click(function()
		{
			jQuery('#btAddESS').show();
			ess_admin.hide_warning();
		});
		
		jQuery('#em-menu-export').click(function()
		{
			jQuery('#btAddESS').hide();
			ess_admin.hide_warning();
			
			if ( jQuery('#add_feed_form').css('display') != 'none' )
				jQuery('#add_feed_form').slideToggle();
		});
		
		jQuery('#btViewErrors').click(function()
		{
			jQuery('.em-warning').slideToggle();
		});
	},
	
	blink_bt_error : function()
	{
		jQuery('#btViewErrors').show();
		
		for( var i = 0 ; i < 4 ; i++ ) 
		{
			jQuery('#btViewErrors').fadeTo( 'slow', 0 ).fadeTo( 'slow', 1.0 );
	  	}
	},
	
	hide_warning : function()
	{
		var w = jQuery('.em-warning');
		var cll =  w.attr('class');
		
		if (cll==undefined) return;
		
		//alert( cll.match(/error/gi).length );
		
		if ( w.css('display') != 'none' && cll.match(/error/gi).length > 0 )
		{
			setTimeout( function() {
				w.slideToggle();
				ess_admin.blink_bt_error();
			}, 10000 );
		}
	},
	
	set_radio_buttons : function()
	{
		jQuery('#ess_mode').toggles({
	    	clickable	: true,
	        dragable	: true,
	        click		: undefined,
	        on			: !jQuery('#ess_mode').hasClass('off'),
	        checkbox	: (jQuery('#ess_mode').data('checkbox')) ? jQuery('.'+jQuery('#ess_mode').data('checkbox')) : undefined,
	        ontext		: jQuery('#ess_mode').data('ontext'),
	        offtext		: jQuery('#ess_mode').data('offtext')
	    });
	    jQuery('.'+jQuery('#ess_mode').data('checkbox')).hide();
	    
		jQuery('.toggle').each(function() 
		{
			var el =  jQuery(this);
		    el.toggles({
		    	clickable	: true,
		        dragable	: true,
		        click		: undefined,
		        on			: !el.hasClass('off'),
		        checkbox	: (el.data('checkbox')) ? jQuery('.'+el.data('checkbox')) : undefined,
		        ontext		: el.data('ontext') || 'ON',
		        offtext		: el.data('offtext') || 'OFF'
		    });
		    jQuery('.'+el.data('checkbox')).hide();
		});
	},
	
	handle_owner_block : function()
	{
		jQuery('#ess_owner_activate-div').click(function(){
	    	jQuery('#block_owner').css({'opacity':(jQuery('.ess_owner_activate-checkbox').attr('checked')!=undefined)?1:0.3});
	    });
	},
	
	handle_tab_selected : function()
	{
		var navUrl = document.location.toString();
		
		if (navUrl.match('#')) 
		{
			var nav_tab = navUrl.split('#')[1].split(':');
				
			jQuery('a#em-menu-' + ((nav_tab=='import')?'import':'export') ).trigger('click');
			
		}
	},
	
	handle_timezone : function() 
	{
		jQuery('#timezone-image').timezonePicker({
			target		: '#edit-date-default-timezone',
			timezone	: jQuery('#edit-date-default-timezone').val(),
			pin			: '.timezone-pin'
		});
		
		jQuery('#edit-date-default-timezone').css({opacity:0});
		
		jQuery('#timezone-detect').click(function() {
			jQuery('#timezone-image').timezonePicker('detectLocation');
			
		});
   },
	
	control_ess_import_field : function(el)
	{
		if ( el.val() != 'http://' )
			el.select();
	},
	
	loader : function()
	{
		jQuery('#ess_loader').show();
	},
	
	set_event_id : function( event_id )
	{
		jQuery('#selected_event_id').val( event_id );
	}
	
};

jQuery(document).ready(function() { ess_admin.init();});