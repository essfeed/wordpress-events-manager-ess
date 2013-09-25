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
		$('#btAddESS').click(function()
		{
			$('.em-warning').hide();
			$('#add_feed_form').slideToggle();
		});
		
		$('#em-menu-import').click(function()
		{
			$('#btAddESS').show();
			ess_admin.hide_warning();
		});
		
		$('#em-menu-export').click(function()
		{
			$('#btAddESS').hide();
			ess_admin.hide_warning();
			
			if ( $('#add_feed_form').css('display') != 'none' )
				$('#add_feed_form').slideToggle();
		});
		
		$('#btViewErrors').click(function()
		{
			$('.em-warning').slideToggle();
		});
	},
	
	blink_bt_error : function()
	{
		$('#btViewErrors').show();
		
		for( var i = 0 ; i < 4 ; i++ ) 
		{
			$('#btViewErrors').fadeTo( 'slow', 0 ).fadeTo( 'slow', 1.0 );
	  	}
	},
	
	hide_warning : function()
	{
		var w = $('.em-warning');
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
		$('#ess_mode').toggles({
	    	clickable	: true,
	        dragable	: true,
	        click		: undefined,
	        on			: !$('#ess_mode').hasClass('off'),
	        checkbox	: ($('#ess_mode').data('checkbox')) ? $('.'+$('#ess_mode').data('checkbox')) : undefined,
	        ontext		: $('#ess_mode').data('ontext'),
	        offtext		: $('#ess_mode').data('offtext')
	    });
	    $('.'+$('#ess_mode').data('checkbox')).hide();
	    
		$('.toggle').each(function() 
		{
			var el =  $(this);
		    el.toggles({
		    	clickable	: true,
		        dragable	: true,
		        click		: undefined,
		        on			: !el.hasClass('off'),
		        checkbox	: (el.data('checkbox')) ? $('.'+el.data('checkbox')) : undefined,
		        ontext		: el.data('ontext') || 'ON',
		        offtext		: el.data('offtext') || 'OFF'
		    });
		    $('.'+el.data('checkbox')).hide();
		});
	},
	
	handle_owner_block : function()
	{
		$('#ess_owner_activate-div').click(function(){
	    	$('#block_owner').css({'opacity':($('.ess_owner_activate-checkbox').attr('checked')!=undefined)?1:0.3});
	    });
	},
	
	handle_tab_selected : function()
	{
		var navUrl = document.location.toString();
		
		if (navUrl.match('#')) 
		{
			var nav_tab = navUrl.split('#')[1].split(':');
				
			$('a#em-menu-' + ((nav_tab=='import')?'import':'export') ).trigger('click');
			
		}
	},
	
	handle_timezone : function() 
	{
		$('#timezone-image').timezonePicker({
			target		: '#edit-date-default-timezone',
			timezone	: $('#edit-date-default-timezone').val(),
			pin			: '.timezone-pin'
		});
		
		$('#edit-date-default-timezone').css({opacity:0});
		
		$('#timezone-detect').click(function() {
			$('#timezone-image').timezonePicker('detectLocation');
			
		});
   },
	
	control_ess_import_field : function(el)
	{
		if ( el.val() != 'http://' )
			el.select();
	}
	
};

$(document).ready(function() { ess_admin.init();});