(function($){
"use strict";

	function getIndex(itm, list) {
		var i;
		for (i = 0; i < list.length; i++) {
			if (itm[0] === list[i]) break;
		}
		return i >= list.length ? -1 : i;
	}

	$('.ivpa_customizations').sortable({
		cursor:'move',
		stop: function(event, ui) {

			var count = $('.ivpa_customizations > .ivpa_element').length;

			if ( count > 0 ) {
				var i = 0;
				$('.ivpa_customizations > .ivpa_element').each( function() {
					var curr_el = $(this);
					var curr = curr_el.attr('data-id');

					curr_el.find('[name]').each( function() {
						var attr = $(this).attr('name');
						$(this).attr('name', attr.replace('['+curr+']', '['+i+']'))
					});

					curr_el.attr('data-id', i);

					i++;
				});

			}

		}
	});


	var ajax_loading = false;
	$(document).on('click', '.ivpa_add_customization', function() {
		if ( ajax_loading === true ) {
			return false;
		}

		ajax_loading = true;

		var curr_el = $(this).parent().next();
		var curr = curr_el.find('.ivpa_element').length;


		var curr_data = {
			action: 'ivpa_get_fields'
		};

		$.post(ivpa.ajax, curr_data, function(response) {
			if (response) {

				response = response.replace(/\%%/g, curr);

				var adv_ui = '<div class="ivpa_element" data-id="'+curr+'"><div class="ivpa_manipulate"><a href="#" class="ivpa_attribute_title">Add New Attribute</a><a href="#" class="ivpa_remove"><i class="ivpa-remove"></i></a><a href="#" class="ivpa_reorder"><i class="ivpa-reorder"></i></a><a href="#" class="ivpa_slidedown"><i class="ivpa-slidedown"></i></a><div class="ivpa_clear"></div></div><div class="ivpa_holder">'+response+'</div></div>';

				var curr_append = curr_el.append(adv_ui);

				curr_append.find('.ivpa_element[data-id="'+curr+'"] .ivpa_slidedown').trigger('click');

				ajax_loading = false;

			} else { 

				alert('Error!');
				ajax_loading = false;

			}
		});

		return false;

	});

	$(document).on('click', '.ivpa_slidedown', function() {
		var curr_holder = $(this).closest('.ivpa_element').find('.ivpa_holder');
		var curr_icon = $(this).find('i');

		if ( curr_icon.hasClass('ivpa-slidedown') ) {
			curr_icon.removeClass('ivpa-slidedown').addClass('ivpa-slideup');
			curr_holder.css({'max-height':'100000px','opacity':'1'});
		}
		else {
			curr_icon.removeClass('ivpa-slideup').addClass('ivpa-slidedown');
			curr_holder.css({'max-height':'0','opacity':'0'});
		}

		return false;
	});

	$(document).on('click', '.ivpa_attribute_title', function() {
		$(this).parent().find('.ivpa_slidedown').trigger('click');

		return false;
	});

	$(document).on('click', '.ivpa_reorder', function() {
		return false;
	});

	$(document).on('click', '.ivpa_remove', function() {
		$(this).closest('.ivpa_element ').remove();
		var count = $('.ivpa_customizations > .ivpa_element').length;

		if ( count > 0 ) {
			var i = 0;
			$('.ivpa_customizations > .ivpa_element').each( function() {
				var curr_el = $(this);
				var curr = curr_el.attr('data-id');

				curr_el.find('[name]').each( function() {
					var attr = $(this).attr('name');
					$(this).attr('name', attr.replace('['+curr+']', '['+i+']'))
				});

				curr_el.attr('data-id', i);

				i++;
			});

		}
		return false;
	});


	$(document).on('change', '.ivpa_attr_select', function() {

		var curr_el = $(this).closest('.ivpa_element');
		var curr = curr_el.attr('data-id');

		var curr_tax = curr_el.find('select[name^="ivpa_attr"] option:selected').attr('value');
		var curr_style = curr_el.find('select[name^="ivpa_style"] option:selected').attr('value');

		if ( curr_tax == '' ) {
			alert('Please select attribute.');
			curr_el.find('.ivpa_terms').html('');
			return false;
		}

		if ( curr_el.parent().find('select[name^="ivpa_attr"] option[value="'+curr_tax+'"]:selected').length > 1 ) {
			alert('You have already set this attribute style.');
			curr_el.find('.ivpa_terms').html('');
			curr_el.find('select[name^="ivpa_attr"] option:first').prop('selected', true);
			return false;
		}

		var curr_data = {
			action: 'ivpa_get_terms',
			taxonomy: curr_tax,
			style: curr_style
		};

		$.post(ivpa.ajax, curr_data, function(response) {
			if (response) {
				response = response.replace(/\%%/g, curr);
				curr_el.find('.ivpa_terms').html(response);

				curr_el.find('.ivpa_color').each(function(){
					$(this).wpColorPicker({
						defaultColor: true,
						hide: true
					});
				});

			} else { 
				alert('Error!');
			}
		});

	});



	$(document).on( 'click', '.ivpa_upload_media', function () {

		var frame;
		var el = $(this);
		var curr = el.parent().prev();

		if ( frame ) {
			frame.open();
			return;
		}

		frame = wp.media({
			title: el.data('choose'),
			button: {
				text: el.data('update'),
				close: false
			}
		});

		frame.on( 'select', function() {

			var attachment = frame.state().get('selection').first();
			frame.close();
			curr.find('input').val( attachment.attributes.url );

		});

		frame.open();

		return false;
	});

	$('#ivpa_manager .ivpa_color').each(function(){
		$(this).wpColorPicker({
			defaultColor: true,
			hide: true
		});
	});

})(jQuery);