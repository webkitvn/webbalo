(function($){

"use strict";

	var ivpa_strings = {};

	ivpa_strings.variable = typeof ivpa !== 'undefined' ? ivpa.localization.variable : '';
	ivpa_strings.simple = typeof ivpa !== 'undefined' ? ivpa.localization.simple : '';;

	if (!Object.keys) {
		Object.keys = function (obj) {
			var keys = [],
				k;
			for (k in obj) {
				if (Object.prototype.hasOwnProperty.call(obj, k)) {
					keys.push(k);
				}
			}
			return keys;
		};
	}

	function getObjects(obj, key, val) {
		var objects = [];
		for (var i in obj) {
			if (!obj.hasOwnProperty(i)) continue;
			if (typeof obj[i] == 'object') {
				objects = objects.concat(getObjects(obj[i], key, val));
			} else if (i == key && obj[key] == val || obj[key] == '' ) {
				objects.push(obj);
			}
		}
		return objects;
	}

	function baseName( str ) {
		var base = new String(str);
		if(base.lastIndexOf('.') != -1) {
			base = base.substring(0, base.lastIndexOf('.'));
		}
		return base;
	}

	$('.ivpa-stepped .ivpa_attribute:first').show();

	function ivpa_register() {

		if ( $(document).find('.ivpa-register:not(.ivpa_registered):not(.ivpa_showonly)').length > 0 ) {

			var $dropdowns = $('#ivpa-content .ivpa_term');

			$dropdowns
			.on('mouseover', function()
			{
				var $this = $(this);

				if ( ivpa.outofstock !== 'clickable' ) {
					if ( $this.hasClass('ivpa_outofstock') ) {
						return false;
					}
				}

				if ($this.prop('hoverTimeout'))
				{
					$this.prop('hoverTimeout', clearTimeout($this.prop('hoverTimeout')));
				}

				$this.prop('hoverIntent', setTimeout(function()
				{
					$this.addClass('ivpa_hover');
				}, 250));
				})
			.on('mouseleave', function()
				{
				var $this = $(this);

				if ( ivpa.outofstock !== 'clickable' ) {
					if ( $this.hasClass('ivpa_outofstock') ) {
						return false;
					}
				}

				if ($this.prop('hoverIntent'))
				{
					$this.prop('hoverIntent', clearTimeout($this.prop('hoverIntent')));
				}

				$this.prop('hoverTimeout', setTimeout(function()
				{
					$this.removeClass('ivpa_hover');
				}, 250));
			});

			$('.ivpa-register').each( function() {

				var curr_element = $(this);
				var curr_variations;
				curr_variations = $.parseJSON( curr_element.attr('data-variations') );

				curr_element.addClass('ivpa_registered');

				curr_element.find('.ivpa_attribute').each( function() {
					var curr_el = $(this);
					var curr_el_term = curr_el.attr('data-term');

					var curr = curr_el.parent();
					var curr_attr = curr.attr('data-attribute');

					var main = curr.parent();

					var curr_selectbox = $(document.getElementById(curr_attr));
					if ( curr_element.attr('id') == 'ivpa-content' ) {
						curr_selectbox.find('option:selected').removeAttr('selected').trigger('change');
						curr_selectbox.find('option[value="'+curr_el_term+'"]').attr('selected', 'selected').trigger('change');
					}

					curr.find('.ivpa_term.ivpa_clicked').each( function() {
						$(this).parent().addClass('ivpa_activated', 'ivpa_clicked');
					});

					$.each( main.find('.ivpa_attribute'), function() {

						var curr_keys = [];
						var curr_vals = [];
						var curr_objects = {};

						var ins_curr = $(this);
						var ins_curr_attr = ins_curr.attr('data-attribute');

						var ins_curr_par = ins_curr.parent();

						var m=0;

						$.each( ins_curr_par.find('.ivpa_attribute:not([data-attribute="'+ins_curr_attr+'"]) .ivpa_term.ivpa_clicked'), function() {

							var sep_curr = $(this);
							var sep_curr_par = sep_curr.parent();

							var a = $(this).parent().attr('data-attribute');
							var t = sep_curr.attr('data-term');

							curr_keys.push( a );
							curr_vals.push( t );

							m++;

						} );

						$.each(curr_variations, function(vrl_curr_index, vrl_curr) {

							var found = false;

							var p=0;

							$.each(curr_keys, function(l,b) {
								var curr_set = getObjects(vrl_curr['attributes'], 'attribute_'+b, curr_vals[l]);
								if ( $.isEmptyObject(curr_set) === false ) {
									p++;
								}
							})

							if ( p === m ) {
								found = true;
							}

							if ( found === true && vrl_curr['is_in_stock'] === true ) {
								$.each(vrl_curr['attributes'] , function(hlp_curr_index, hlp_curr_item) {

									var hlp_curr_attr = hlp_curr_index.replace('attribute_', '');

									if ( ins_curr_attr == hlp_curr_attr ) {

										if ( curr_objects[hlp_curr_attr] == undefined ) {
											curr_objects[hlp_curr_attr] = [];
										}

										if ( $.inArray(hlp_curr_item, curr_objects[hlp_curr_attr]) == -1 ) {
											curr_objects[hlp_curr_attr].push(hlp_curr_item);
										}

									}

								} );
							}

						} );
						if ( $.isEmptyObject(curr_objects) === false ) {
							$.each(curr_objects , function(curr_stock_attr, curr_stock_item) {
								curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term').removeClass('ivpa_instock').removeClass('ivpa_outofstock');
								if ( curr_stock_item.length == 1 && curr_stock_item[0] == '' ) {
									curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term').addClass('ivpa_instock');
								}
								else {
									$.each( curr_stock_item, function(curr_stock_id, curr_stock_term) {
										if ( curr_stock_term !== '' ) {
											curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term[data-term="'+curr_stock_term+'"]').addClass('ivpa_instock');
										}
										else {
											curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term:not(.ivpa_instock)').addClass('ivpa_instock');
										}
									});
									curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term:not(.ivpa_instock)').addClass('ivpa_outofstock');
								}
							});
						}
						else if ( $('.ivpa_attribute:not(.ivpa_activated)').length > 0 ) {
							curr_element.find('.ivpa_attribute:not(.ivpa_activated)').each( function() {
								$(this).find('.ivpa_term:not(.ivpa_outofstock)').addClass('ivpa_outofstock');
							});
						}

					} );

					if ( curr_element.attr('id') !== 'ivpa-content' ) {

						if ( curr_element.find('.ivpa_attribute').length > 0 && curr_element.find('.ivpa_attribute:not(.ivpa_activated)').length == 0 ) {

							var curr_elements = curr_element.find('.ivpa_attribute.ivpa_activated');
							var curr_var = {};

							curr_elements.each( function() {
								curr_var['attribute_'+$(this).attr('data-attribute')] = $(this).find('span.ivpa_clicked').attr('data-term');
							});

							var i = curr_element.find('.ivpa_attribute').length;

							$.each( curr_variations, function(t,f) {

								var o = 0;
								var found = false;

								$.each( curr_var, function(w,c) {
									var curr_set = getObjects(f['attributes'], w, c);
									if ( $.isEmptyObject(curr_set) === false ) {
										o++;
									}
								});

								if ( o === i ) {
									found = true;
								}

								if ( found === true && f['is_in_stock'] === true ) {

									curr_element.attr('data-selected', f['variation_id']);

									var container = curr_element.closest(ivpa.settings.archive_selector);
									var curr_button = container.find('[data-product_id="'+curr_element.attr('data-id')+'"]');

									var image = f['ivpa_image'];

									if ( ivpa.imageattributes.length == 0 || $.inArray(curr_attr,ivpa.imageattributes) > -1 ) {

										if ( image != '' ) {

											if ( container.find('img[data-default-image]').attr('srcset-ivpa') ) {
												container.find('img[data-default-image]').attr('srcset', container.find('img[data-default-image]').attr('srcset-ivpa')).removeAttr('srcset-ivpa');
											}

											container.find('img[data-default-image]').attr('src', curr_element.attr('data-image')).removeAttr('data-default-image');

											var archive_image = container.find('img[src^="'+baseName(curr_element.attr('data-image'))+'"]:first');

											archive_image.attr('data-default-image',archive_image.attr('src')).attr('src',image);

											if ( archive_image.attr('srcset') ) {
												archive_image.attr('srcset-ivpa',archive_image.attr('srcset')).removeAttr('srcset');
											}

										}

									}

									if ( !$.isEmptyObject(ivpa_strings) && curr_button.text().indexOf( ivpa_strings.variable ) > -1 ) {
										curr_button.html( curr_button.html().replace(ivpa_strings.variable, ivpa_strings.simple) );
									}

									var quantity = container.find('.ivpa_quantity');
									if ( quantity.length > 0 ) {
										quantity.slideDown();
									}

									var price = f['price_html'];

									if ( price != '' ) {
										container.find(ivpa.settings.price_selector).each( function() {
											if ( $(this).parents('.ivpa-content').length > 0 ) {
												return true;
											}
											$(this).replaceWith(price);
										});
									}

								}
							});

						}
						else {

							var container = curr_element.closest(ivpa.settings.archive_selector);
							var curr_button = container.find('[data-product_id="'+curr_element.attr('data-id')+'"]');

							if ( !$.isEmptyObject(ivpa_strings) && curr_button.text().indexOf( ivpa_strings.simple ) > -1 ) {
								curr_button.html( curr_button.html().replace(ivpa_strings.simple, ivpa_strings.variable) );
							}

							var quantity = container.find('.ivpa_quantity');
							if ( quantity.length > 0 ) {
								quantity.slideUp();
							}

							if ( ivpa.imageattributes.length == 0 || $.inArray(curr_attr,ivpa.imageattributes) > -1 ) {

								container.find('img[data-default-image]').each( function() {
									$(this).attr('src', $(this).attr('data-default-image')).removeAttr('data-default-image');
								});

							}

						}


					}


					return false;

				});

			});

		}
	}
	ivpa_register();

	if ( ivpa.outofstock == 'clickable' ) {
		var ivpaElements = '.ivpa_attribute:not(.ivpa_showonly) .ivpa_term';
	}
	else {
		var ivpaElements = '.ivpa_attribute:not(.ivpa_showonly) .ivpa_term:not(.ivpa_outofstock)';
	}
	if ( ivpa.disableunclick == 'yes' ) {
		ivpaElements += ':not(.ivpa_clicked)';
	}

	var ivpaProcessing = false;
	$(document).on( 'click', ivpaElements, function() {

		if ( ivpaProcessing === true ) {
			return false;
		}

		ivpaProcessing = true;

		var curr_element = $(this).closest('.ivpa-register');
		var curr_variations;
		curr_variations = $.parseJSON( curr_element.attr('data-variations') );

		var curr_el = $(this);
		var curr_el_term = curr_el.attr('data-term');

		var curr = curr_el.parent();
		var curr_attr = curr.attr('data-attribute');

		var main = curr.parent();

		curr_element.attr('data-selected', '');

		var curr_selectbox = $(document.getElementById(curr_attr));
		if ( !curr_el.hasClass('ivpa_clicked') ) {
			curr.find('.ivpa_term').removeClass('ivpa_clicked');
			curr_el.addClass('ivpa_clicked');
			curr.addClass('ivpa_clicked');
			if ( curr_element.attr('id') == 'ivpa-content' ) {
				curr_selectbox.trigger('focusin');
				if ( curr_selectbox.find('option[value="'+curr_el_term+'"]').length > 0 ) {
					curr_selectbox.val(curr_el_term).trigger('change');
				}
				else {
					curr_selectbox.append('<option value="'+curr_el_term+'" class="attached enabled">'+curr_el_term+'</option>');
					curr_selectbox.val(curr_el_term).trigger('focusin').trigger('change');
				}
			}
			if ( curr.hasClass('ivpa_selectbox') ) {
				curr.removeAttr('style').scrollTop(0).removeClass('ivpa_selectbox_opened');
				var sel = curr.find('span[data-term="'+curr_el_term+'"]').text();
				curr.find('.ivpa_title').text(sel);
			}
		}
		else {
			curr_el.removeClass('ivpa_clicked');
			curr.removeClass('ivpa_clicked');
			if ( curr_element.attr('id') == 'ivpa-content' ) {
				curr_selectbox.find('option:selected').removeAttr('selected').trigger('change');
			}
			if ( curr.hasClass('ivpa_selectbox') ) {
				curr.removeAttr('style').scrollTop(0).removeClass('ivpa_selectbox_opened');
				curr.find('.ivpa_title').text(ivpa.localization.select);
			}
		}

		if ( !curr.hasClass('ivpa_activated') ) {
			curr.addClass('ivpa_activated');
		}
		else if ( curr.find('.ivpa_term.ivpa_clicked').length == 0 ) {
			curr.removeClass('ivpa_activated');
		}

		$.each( main.find('.ivpa_attribute'), function() {

			var curr_keys = [];
			var curr_vals = [];
			var curr_objects = {};

			var ins_curr = $(this);
			var ins_curr_attr = ins_curr.attr('data-attribute');

			var ins_curr_par = ins_curr.parent();

			var m=0;

			$.each( ins_curr_par.find('.ivpa_attribute:not([data-attribute="'+ins_curr_attr+'"]) .ivpa_term.ivpa_clicked'), function() {

				var sep_curr = $(this);
				var sep_curr_par = sep_curr.parent();

				var a = $(this).parent().attr('data-attribute');
				var t = sep_curr.attr('data-term');

				curr_keys.push( a );
				curr_vals.push( t );

				m++;

			} );

			$.each(curr_variations, function(vrl_curr_index, vrl_curr) {

				var found = false;

				var p=0;

				$.each(curr_keys, function(l,b) {

					var curr_set = getObjects(vrl_curr['attributes'], 'attribute_'+b, curr_vals[l]);
					if ( $.isEmptyObject(curr_set) === false ) {
						p++;
					}
				})

				if ( p === m ) {
					found = true;
				}

				if ( found === true && vrl_curr['is_in_stock'] === true ) {
					$.each(vrl_curr['attributes'] , function(hlp_curr_index, hlp_curr_item) {

						var hlp_curr_attr = hlp_curr_index.replace('attribute_', '');

						if ( ins_curr_attr == hlp_curr_attr ) {

							if ( curr_objects[hlp_curr_attr] == undefined ) {
								curr_objects[hlp_curr_attr] = [];
							}

							if ( $.inArray(hlp_curr_item, curr_objects[hlp_curr_attr]) == -1 ) {
								curr_objects[hlp_curr_attr].push(hlp_curr_item);
							}

						}

					} );
				}

			} );

			if ( $.isEmptyObject(curr_objects) === false ) {
				$.each(curr_objects , function(curr_stock_attr, curr_stock_item) {
					curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term').removeClass('ivpa_instock').removeClass('ivpa_outofstock');
					if ( curr_stock_item.length == 1 && curr_stock_item[0] == '' ) {
						curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term').addClass('ivpa_instock');
					}
					else {
						$.each( curr_stock_item, function(curr_stock_id, curr_stock_term) {
							if ( curr_stock_term !== '' ) {
								curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term[data-term="'+curr_stock_term+'"]').addClass('ivpa_instock');
							}
							else {
								curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term:not(.ivpa_instock)').addClass('ivpa_instock');
							}
						});
						curr_element.find('.ivpa_attribute[data-attribute="'+curr_stock_attr+'"] .ivpa_term:not(.ivpa_instock)').addClass('ivpa_outofstock');
					}
				});
			}
			else if ( $('.ivpa_attribute:not(.ivpa_activated)').length > 0 ) {
				curr_element.find('.ivpa_attribute:not(.ivpa_activated)').each( function() {
					$(this).find('.ivpa_term:not(.ivpa_outofstock)').addClass('ivpa_outofstock');
				});
			}

		} );

		if ( curr_element.attr('id') !== 'ivpa-content' ) {

			if ( curr_element.find('.ivpa_attribute').length > 0 && curr_element.find('.ivpa_attribute:not(.ivpa_activated)').length == 0 ) {

				if ( curr_element.hasClass('ivpa-stepped') ) {
					$('.ivpa-stepped .ivpa_attribute:hidden:first').slideDown();
				}

				var curr_elements = curr_element.find('.ivpa_attribute.ivpa_activated');
				var curr_var = {};

				curr_elements.each( function() {
					curr_var['attribute_'+$(this).attr('data-attribute')] = $(this).find('span.ivpa_clicked').attr('data-term');
				});

				var i = curr_element.find('.ivpa_attribute').length;

				$.each( curr_variations, function(t,f) {

					var o = 0;
					var found = false;

					$.each( curr_var, function(w,c) {
						var curr_set = getObjects(f['attributes'], w, c);
						if ( $.isEmptyObject(curr_set) === false ) {
							o++;
						}
					});

					if ( o === i ) {
						found = true;
					}

					if ( found === true && f['is_in_stock'] === true ) {

						curr_element.attr('data-selected', f['variation_id']);

						var container = curr_element.closest(ivpa.settings.archive_selector);
						var curr_button = container.find('[data-product_id="'+curr_element.attr('data-id')+'"]');

						var image = f['ivpa_image'];

						if ( ivpa.imageattributes.length == 0 || $.inArray(curr_attr,ivpa.imageattributes) > -1 ) {

							if ( image != '' ) {

								if ( container.find('img[data-default-image]').attr('srcset-ivpa') ) {
									container.find('img[data-default-image]').attr('srcset', container.find('img[data-default-image]').attr('srcset-ivpa')).removeAttr('srcset-ivpa');
								}

								var imgPreload = new Image();
								$(imgPreload).attr({
									src: image
								});

								if (imgPreload.complete || imgPreload.readyState === 4) {

								}
								else {

									container.addClass('ivpa-image-loading');
									container.fadeTo( 100, .7 );

									$(imgPreload).load(function (response, status, xhr) {
										if (status == 'error') {
											console.log('101 Error!');
										}
										else {
											container.removeClass('ivpa-image-loading');
											container.fadeTo( 100, 1 );
										}
									});
								}

								if ( container.find('img[data-default-image]').length > 0 ) {
									var archive_image = container.find('img[data-default-image]');
									archive_image.attr('src',image);
								}
								else {
									var archive_image = container.find('img[src^="'+baseName(curr_element.attr('data-image'))+'"]:first');
									archive_image.attr('data-default-image',archive_image.attr('src')).attr('src',image);
								}

								if ( archive_image.attr('srcset') ) {
									archive_image.attr('srcset-ivpa',archive_image.attr('srcset')).removeAttr('srcset');
								}

							}
							else {
								if ( container.find('img[data-default-image]').length > 0 ) {
									var archive_image = container.find('img[data-default-image]');
									container.find('img[data-default-image]').attr('src', curr_element.attr('data-image')).removeAttr('data-default-image');
								}
							}

						}

						if ( !$.isEmptyObject(ivpa_strings) && curr_button.text().indexOf( ivpa_strings.variable ) > -1 ) {
							curr_button.html( curr_button.html().replace(ivpa_strings.variable, ivpa_strings.simple) );
						}

						var quantity = container.find('.ivpa_quantity');
						if ( quantity.length > 0 ) {
							quantity.slideDown();
						}

						var price = f['price_html'];

						if ( price != '' ) {
							container.find(ivpa.settings.price_selector).each( function() {
								if ( $(this).parents('.ivpa-content').length > 0 ) {
									return true;
								}
								$(this).replaceWith(price);
							});
						}

						ivpaProcessing= false;

					}
				});

			}
			else {

				if ( curr_element.find('.ivpa_attribute.ivpa_activated').length > 0 ) {

					if ( curr_element.hasClass('ivpa-stepped') ) {
						$('.ivpa-stepped .ivpa_attribute:hidden:first').slideDown();
					}

					var curr_elements = curr_element.find('.ivpa_attribute.ivpa_activated');
					var curr_var = {};

					var vL = 0;
					curr_elements.each( function() {
						curr_var['attribute_'+$(this).attr('data-attribute')] = $(this).find('span.ivpa_clicked').attr('data-term');
						vL++;
					});

					var i = curr_element.find('.ivpa_attribute').length;
					var defaultImg = curr_element.attr('data-image');
					var curr_variations_length = curr_variations.length;
					var found = [];
					var o = 0;
					var iL = 0;

					var hasCount = 0;
					curr_element.find('.ivpa_attribute:not(.ivpa_activated)').each( function() {
						hasCount = $(this).find('.ivpa_term').length*(hasCount==0?1:hasCount);
					});

					$.each( curr_variations, function(t,f) {

						var o = 0;
						$.each( curr_var, function(w,c) {
							var curr_set = getObjects(f['attributes'], w, c);
							if ( $.isEmptyObject(curr_set) === false ) {
								o++;
							}
						});

						if ( vL == o ) {
							if ( $.inArray( f['ivpa_image'], found ) < 0 ) {
								found.push(f['ivpa_image']);
								iL++;
							}
						}

						if ( !--curr_variations_length ) {

							var container = curr_element.closest(ivpa.settings.archive_selector);

							if ( typeof found[0] !== "undefined" &&  ( hasCount !== iL || curr_element.find('.ivpa_attribute:not(.ivpa_activated)').length == 1 ) !== false ) {

								var image = found[0];

								if ( ivpa.imageattributes.length == 0 || $.inArray(curr_attr,ivpa.imageattributes) > -1 ) {

									if ( image != '' ) {

										var imgPreload = new Image();
										$(imgPreload).attr({
											src: image
										});

										if (imgPreload.complete || imgPreload.readyState === 4) {

										}
										else {

											container.addClass('ivpa-image-loading');
											container.fadeTo( 100, .7 );

											$(imgPreload).load(function (response, status, xhr) {
												if (status == 'error') {
													console.log('101 Error!');
												}
												else {
													container.removeClass('ivpa-image-loading');
													container.fadeTo( 100, 1 );
												}
											});
										}

										if ( container.find('img[data-default-image]').length > 0 ) {
											var archive_image = container.find('img[data-default-image]');
											archive_image.attr('src',image);
										}
										else {
											var archive_image = container.find('img[src^="'+baseName(curr_element.attr('data-image'))+'"]:first');
											archive_image.attr('data-default-image',archive_image.attr('src')).attr('src',image);
										}

										if ( archive_image.attr('srcset') ) {
											archive_image.attr('srcset-ivpa',archive_image.attr('srcset')).removeAttr('srcset');
										}

									}
									else {
										if ( container.find('img[data-default-image]').length > 0 ) {
											var archive_image = container.find('img[data-default-image]');
											container.find('img[data-default-image]').attr('src', curr_element.attr('data-image')).removeAttr('data-default-image');
										}
									}

								}

							}

							var quantity = container.find('.ivpa_quantity:visible');
							if ( quantity.length > 0 ) {
								quantity.slideUp();
							}
							var curr_button = container.find('[data-product_id="'+curr_element.attr('data-id')+'"]');

							if ( !$.isEmptyObject(ivpa_strings) && curr_button.text().indexOf( ivpa_strings.simple ) > -1 ) {
								curr_button.html( curr_button.html().replace(ivpa_strings.simple, ivpa_strings.variable) );
							}

							var curr_price = container.find('.ivpa-hidden-price').html();
							container.find(ivpa.settings.price_selector).replaceWith(curr_price);

							ivpaProcessing= false;

						}

					});

				}
				else {

					if ( curr_element.hasClass('ivpa-stepped') ) {
						$('.ivpa-stepped .ivpa_attribute:visible:last').slideUp();
					}

					var container = curr_element.closest(ivpa.settings.archive_selector);
					var curr_button = container.find('[data-product_id="'+curr_element.attr('data-id')+'"]');

					if ( !$.isEmptyObject(ivpa_strings) && curr_button.text().indexOf( ivpa_strings.simple ) > -1 ) {
						curr_button.html( curr_button.html().replace(ivpa_strings.simple, ivpa_strings.variable) );
					}

					var quantity = container.find('.ivpa_quantity:visible');
					if ( quantity.length > 0 ) {
						quantity.slideUp();
					}

					if ( ivpa.imageattributes.length == 0 || $.inArray(curr_attr,ivpa.imageattributes) > -1 ) {

						container.find('img[data-default-image]').each( function() {
							$(this).attr('src', $(this).attr('data-default-image')).removeAttr('data-default-image');
						});

					}

					var curr_price = container.find('.ivpa-hidden-price').html();
					container.find(ivpa.settings.price_selector).replaceWith(curr_price);

					ivpaProcessing= false;

				}

			}


		}
		else {

			if ( ivpa.imageswitch == 'no' ) {
				ivpaProcessing= false;
				return false;
			}

			if ( curr_element.find('.ivpa_attribute.ivpa_activated').length > 0 ) {

				if ( curr_element.hasClass('ivpa-stepped') ) {
					$('.ivpa-stepped .ivpa_attribute:hidden:first').slideDown();
				}

				var curr_elements = curr_element.find('.ivpa_attribute.ivpa_activated');
				var curr_var = {};

				var vL = 0;
				curr_elements.each( function() {
					curr_var['attribute_'+$(this).attr('data-attribute')] = $(this).find('span.ivpa_clicked').attr('data-term');
					vL++;
				});

				var i = curr_element.find('.ivpa_attribute').length;
				var defaultImg = curr_element.attr('data-image');
				var curr_variations_length = curr_variations.length;
				var found = [];
				var o = 0;
				var iL = 0;

				var hasCount = 0;
				curr_element.find('.ivpa_attribute:not(.ivpa_activated)').each( function() {
					hasCount = $(this).find('.ivpa_term').length*(hasCount==0?1:hasCount);
				});

				$.each( curr_variations, function(t,f) {

					var o = 0;
					$.each( curr_var, function(w,c) {
						var curr_set = getObjects(f['attributes'], w, c);
						if ( $.isEmptyObject(curr_set) === false ) {
							o++;
						}
					});

					if ( vL == o ) {
						if ( $.inArray( f['ivpa_image'], found ) < 0 ) {
							found.push(f['ivpa_image']);
							iL++;
						}
					}

					if ( !--curr_variations_length ) { 

						if ( ivpa.settings.single_selector == '' ) {
							var container = curr_element.closest(ivpa.settings.archive_selector).find('.images');
						}
						else {
							var container = $(ivpa.settings.single_selector);
						}

						if ( typeof found[0] !== "undefined" &&  ( hasCount !== iL || curr_element.find('.ivpa_attribute:not(.ivpa_activated)').length == 1 ) !== false ) {

							var image = found[0];

							if ( ivpa.imageattributes.length == 0 || $.inArray(curr_attr,ivpa.imageattributes) > -1 ) {

								if ( image != '' ) {

									var imgPreload = new Image();
									$(imgPreload).attr({
										src: image
									});

									if (imgPreload.complete || imgPreload.readyState === 4) {

									}
									else {

										container.addClass('ivpa-image-loading');
										container.fadeTo( 100, .7 );

										$(imgPreload).load(function (response, status, xhr) {
											if (status == 'error') {
												console.log('101 Error!');
											}
											else {
												container.removeClass('ivpa-image-loading');
												container.fadeTo( 100, 1 );
											}
										});
									}

									if ( container.find('img[data-default-image]').length > 0 ) {
										var archive_image = container.find('img[data-default-image]');
										archive_image.attr('src',image);
									}
									else {
										var archive_image = container.find('img[src^="'+baseName(curr_element.attr('data-image'))+'"]:first');
										archive_image.attr('data-default-image',archive_image.attr('src')).attr('src',image);
									}

									if ( archive_image.attr('srcset') ) {
										archive_image.attr('srcset-ivpa',archive_image.attr('srcset')).removeAttr('srcset');
									}

								}
								else {
									if ( container.find('img[data-default-image]').length > 0 ) {
										var archive_image = container.find('img[data-default-image]');
										container.find('img[data-default-image]').attr('src', curr_element.attr('data-image')).removeAttr('data-default-image');
									}
								}

							}

						}

						ivpaProcessing= false;

					}

				});

			}
			else {

				if ( curr_element.hasClass('ivpa-stepped') ) {
					$('.ivpa-stepped .ivpa_attribute:visible:last').slideUp();
				}

				if ( ivpa.settings.single_selector == '' ) {
					var container = curr_element.closest(ivpa.settings.archive_selector).find('.images');
				}
				else {
					var container = $(ivpa.settings.single_selector);
				}

				if ( ivpa.imageattributes.length == 0 || $.inArray(curr_attr,ivpa.imageattributes) > -1 ) {

					container.find('img[data-default-image]').each( function() {
						$(this).attr('src', $(this).attr('data-default-image')).removeAttr('data-default-image');
					});

				}

				ivpaProcessing= false;

			}

		}

		return false;

	});

	$(document).on( 'click', ivpa.settings.addcart_selector, function() {

		var container = $(this).closest(ivpa.settings.archive_selector);
		var var_id = container.find('.ivpa-content').attr('data-selected');

		if ( var_id !== undefined && var_id !== '' ) {

			var product_id = $(this).attr('data-product_id');

			var quantity = container.find('input.ivpa_qty');
			if ( quantity.length > 0 ) {
				var qty = quantity.val();
			}
			var quantity = ( typeof qty !== "undefined" ? qty : $(this).attr('data-quantity') );

			var item = {};

			container.find('.ivpa-content .ivpa_attribute').each( function() {

				var attribute = $(this).attr('data-attribute');
				var attribute_value = $(this).find('.ivpa_term.ivpa_clicked').attr('data-term');
				
				item[attribute] = attribute_value;
			});

			var $thisbutton = $( this );

			if ( $thisbutton.is( ivpa.settings.addcart_selector ) ) {

				$thisbutton.removeClass( 'added' );
				$thisbutton.addClass( 'loading' );

				var data = {
					action: 'ivpa_add_to_cart_callback',
					product_id: product_id,
					quantity: quantity,
					variation_id: var_id,
					variation: item
				};

				$( 'body' ).trigger( 'adding_to_cart', [ $thisbutton, data ] );

				$.post( ivpa.ajax, data, function( response ) {

					if ( ! response )
						return;

					var this_page = window.location.toString();

					this_page = this_page.replace( 'add-to-cart', 'added-to-cart' );

					$thisbutton.removeClass('loading');

					if ( response.error && response.product_url ) {
						window.location = response.product_url;
						return;
					}

					var fragments = response.fragments;
					var cart_hash = response.cart_hash;

					if ( fragments ) {
						$.each(fragments, function(key, value) {
							$(key).addClass('updating');
						});
					}

					$('.shop_table.cart, .updating, .cart_totals,.widget_shopping_cart_top').fadeTo('400', '0.6').block({message: null, overlayCSS: {background: 'transparent url(' + woocommerce_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } } );

					$thisbutton.addClass( 'added' );

					if ( ! wc_add_to_cart_params.is_cart && $thisbutton.parent().find( '.added_to_cart' ).size() === 0 ) {
						$thisbutton.after( ' <a href="' + wc_add_to_cart_params.cart_url + '" class="added_to_cart wc-forward" title="' + 
						wc_add_to_cart_params.i18n_view_cart + '">' + wc_add_to_cart_params.i18n_view_cart + '</a>' );
					}

					if ( fragments ) {
						$.each(fragments, function(key, value) {
							$(key).replaceWith(value);
						});
					}

					$('.widget_shopping_cart, .updating, .widget_shopping_cart_top').stop(true).css('opacity', '1').unblock();

					$('.widget_shopping_cart_top').load( this_page + ' .widget_shopping_cart_top:eq(0) > *', function() {

						$("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").addClass('buttons_added').append('<input type="button" value="+" id="add1" class="plus" />').prepend('<input type="button" value="-" id="minus1" class="minus" />');

						$('.widget_shopping_cart_top').stop(true).css('opacity', '1').unblock();

						$('body').trigger('cart_page_refreshed');
					});

					$('.shop_table.cart').load( this_page + ' .shop_table.cart:eq(0) > *', function() {

						$("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").addClass('buttons_added').append('<input type="button" value="+" id="add1" class="plus" />').prepend('<input type="button" value="-" id="minus1" class="minus" />');

						$('.shop_table.cart').stop(true).css('opacity', '1').unblock();

						$('body').trigger('cart_page_refreshed');
					});

					$('.cart_totals').load( this_page + ' .cart_totals:eq(0) > *', function() {
						$('.cart_totals').stop(true).css('opacity', '1').unblock();
					});

					$('body').trigger( 'added_to_cart', [ fragments, cart_hash ] );
				});

				return false;

			} else {
				return true;
			}

		}

	});

	$(document).ajaxComplete( function() {
		ivpa_register();
	})

	$(document).on('click', '.ivpa_selectbox .ivpa_title', function() {
		var el = $(this).parent();

		if ( el.hasClass('ivpa_selectbox_opened') ) {
			el.removeClass('ivpa_selectbox_opened').css({'overflow-y':'hidden'});
		}
		else {
			el.addClass('ivpa_selectbox_opened').delay(200).queue(function(next){
				$(this).css({'overflow-y':'auto'});
			});
		}

	});

	$('#ivpa-content .ivpa_selectbox, .ivpa-content .ivpa_selectbox').each(function(i,c){
		$(c).css('z-index',99-i);
	});

})(jQuery);