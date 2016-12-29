(function($){
"use strict";

	var ajax_active = false;

	function get_shares() {

		var fb = $('.wcspp-facebook:not(.wcspp-activated)');
		if ( fb.length > 0 ) {
			$.getJSON( 'http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' + wcspp.product_url, function( fbdata ) {
				fb.find('span').html(fbdata[0].total_count);
				fb.addClass('wcspp-activated');
			});
		}

/*
		var tw = $('.wcspp-twitter:not(.wcspp-activated)');
		if ( tw.length > 0 ) {
			$.getJSON( 'http://cdn.api.twitter.com/1/urls/count.json?url=' + wcspp.product_url + '&callback=?', function( twitdata ) {
				tw.find('span').html(twitdata.count);
				tw.addClass('wcspp-activated');
			});
		}
*/

		var lin = $('.wcspp-linked:not(.wcspp-activated)');
		if ( lin.length > 0 ) {
			$.getJSON( 'http://www.linkedin.com/countserv/count/share?url=' + wcspp.product_url + '&callback=?', function( linkdindata ) {
				lin.find('span').html(linkdindata.count);
				lin.addClass('wcspp-activated');
			});
		}

	}
	if ( wcspp.showcounts == 'yes' ) {
		get_shares();
	}


	$.fn.print = function() {
		if (this.size() > 1){
			this.eq( 0 ).print();
			return;
		} else if (!this.size()){
			return;
		}

		var strFrameName = ("wpspp-printer-" + (new Date()).getTime());

		var jFrame = $( "<iframe name='" + strFrameName + "'>" );

		jFrame
			.css( "width", "1px" )
			.css( "height", "1px" )
			.css( "position", "absolute" )
			.css( "left", "-999px" )
			.appendTo( $( "body:first" ) )
		;

		var objFrame = window.frames[ strFrameName ];

		var objDoc = objFrame.document;

		objDoc.open();
		objDoc.write( "<!DOCTYPE html>" );
		objDoc.write( "<html>" );
		objDoc.write( "<head>" );
		objDoc.write( "<title>" );
		objDoc.write( document.title );
		objDoc.write( "</title>" );
		objDoc.write( "<style>" + wcspp.style + "</style>" );
		objDoc.write( "</head>" );
		objDoc.write( "<body>" );
		objDoc.write( this.html() );
		objDoc.write( "</body>" );
		objDoc.write( "</html>" );
		objDoc.close();

		objFrame.focus();
		objFrame.print();

		setTimeout(
			function(){
			jFrame.remove();
		},
		(60 * 1000)
		);
	}

	$.fn.printPdf = function(vars) {

		var strFrameName = ("wpspp-pdf-" + (new Date()).getTime());

		var jFrame = $( "<iframe name='" + strFrameName + "'>" );

		jFrame
			.css( "width", "1px" )
			.css( "height", "1px" )
			.css( "position", "absolute" )
			.css( "left", "-999px" )
			.appendTo( $( "body:first" ) )
		;

		var objFrame = window.frames[ strFrameName ];

		var objDoc = objFrame.document;

		var site_logo = {
			width:45,
			image:'data:image/x-icon;base64,'+vars.site_logo,
			fit: [37, 37]
		};

		if ( vars.site_logo == '' ) {
			site_logo = {
				width:0,
				image:'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVQIW2NkAAIAAAoAAggA9GkAAAAASUVORK5CYII=',
				fit: [0, 0]
			};
		}

		var header_after = [
			'\n',
			vars.header_after
		];

		if ( vars.header_after == '' ) {
			header_after = [];
		}

		var product_before = [
			'\n',
			vars.product_before
		];

		if ( vars.product_before == '' ) {
			product_before = [];
		}

		var product_after = [
			'\n',
			vars.product_after
		];

		if ( vars.product_after == '' ) {
			product_after = [];
		}

		var product_img0 = {
			width:125,
			image:'data:image/x-icon;base64,'+vars.product_img0,
			fit: [125, 9999]
		};

		if ( vars.product_img0 == '' ) {
			product_img0 = {
				width:0,
				image:'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVQIW2NkAAIAAAoAAggA9GkAAAAASUVORK5CYII=',
				fit: [0, 0]
			};
		}

		var product_img1 = {
			width:125,
			image:'data:image/x-icon;base64,'+vars.product_img1,
			fit: [125, 9999]
		};

		if ( vars.product_img1 == '' ) {
			product_img1 = {
				width:0,
				image:'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVQIW2NkAAIAAAoAAggA9GkAAAAASUVORK5CYII=',
				fit: [0, 0]
			};
		}

		var product_img2 = {
			width:125,
			image:'data:image/x-icon;base64,'+vars.product_img2,
			fit: [125, 9999]
		};

		if ( vars.product_img2 == '' ) {
			product_img2 = {
				width:0,
				image:'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVQIW2NkAAIAAAoAAggA9GkAAAAASUVORK5CYII=',
				fit: [0, 0]
			};
		}

		var product_img3 = {
			width:125,
			image:'data:image/x-icon;base64,'+vars.product_img3,
			fit: [125, 9999]
		};

		if ( vars.product_img3 == '' ) {
			product_img3 = {
				width:0,
				image:'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVQIW2NkAAIAAAoAAggA9GkAAAAASUVORK5CYII=',
				fit: [0, 0]
			};
		}

		var pdfcontent = {
			content: [
				{
					alignment: 'justify',
					columns: [
						site_logo,
						[
							{
								text: vars.site_title,
								style: 'header'
							},
							{
								text: vars.site_description
							}
						]
					]
				},
				header_after,
				'\n',
				{
					image: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAYAAABytg0kAAAAEklEQVQIW2NkYGD4D8QMjDAGABMaAgFVG7naAAAAAElFTkSuQmCC',
					width:510,
					height:0.5,
					alignment: 'center'
				},
				product_before,
				'\n',
				{
					alignment: 'justify',
					columns: [
						{
							text: vars.product_title,
							style: 'title',
							alignment: 'left'
						},
						{
							text: vars.product_price,
							style: 'title',
							alignment: 'right'
						}
					]
				},
				'\n',
				vars.product_meta,
				vars.product_link,
				vars.product_categories,
				vars.product_tags,
				'\n\n',
				{
					alignment: 'justify',
					columns: [
						{
							width:270,
							image: vars.product_image,
							fit: [250,9999]
						},
						[
							{
								text: vars.product_description
							},
							'\n',
							{
								text: vars.product_attributes,
								style: 'meta'
							},
							'\n',
							{
								text: vars.product_dimensions,
								style: 'meta'
							},
							'\n',
							{
								text: vars.product_weight,
								style: 'meta'
							}
						]
					]
				},
				'\n',
				{
					alignment: 'justify',
					columns: [
						product_img0,
						product_img1,
						product_img2,
						product_img3
					]
				},
				'\n\n',
				vars.product_content,
				product_after
				
			],
			styles: {
				header: {
					fontSize: 20
				},
				title: {
					fontSize: 24
				},
				meta: {
					fontSize: 12
				}
			},
			defaultStyle: {
				fontSize: 11
			}
		}

		objDoc.open();
		objDoc.write( "<!DOCTYPE html>" );
		objDoc.write( "<html>" );
		objDoc.write( "<head>" );
		objDoc.write( "<title>" );
		objDoc.write( document.title );
		objDoc.write( "</title>" );
		objDoc.write( "<script type='text/javascript' src='" + wcspp.pdfmake + "'></script>" );
		objDoc.write( "<script type='text/javascript' src='" + wcspp.pdffont + "'></script>" );
		objDoc.write( "</head>" );
		objDoc.write( "<body>" );
		objDoc.write( "<script>pdfMake.createPdf("+JSON.stringify(pdfcontent, null, 4)+").download('"+vars.site_title+' - '+vars.product_title+".pdf');</script>" );
		objDoc.write( "</body>" );
		objDoc.write( "</html>" );
		objDoc.close();

		objFrame.focus();

		setTimeout(
			function(){
			jFrame.remove();
		},
		(60 * 1000)
		);
	}



	var ajax = 'notactive';

	function wcspp_ajax( action, product_id, type ) {

		var data = {
			action: action,
			type: type,
			product_id: product_id
		}

		return $.post(wcspp.ajax, data, function(response) {
			if (response) {
				ajax = 'notactive';
			}
			else {
				alert('Error!');
				ajax = 'notactive';
			}

		});

	}

	$(document).on('click', '.wcspp-navigation .wcspp-print a', function() {

		if ( ajax == 'active' ) {
			return false;
		}

		var curr = $(this);
		var product_id = curr.closest('.wcspp-navigation').data('wcspp-id');

		ajax = 'active';


		$.when( wcspp_ajax( 'wcspp_quickview', product_id, 'print' ) ).done( function(response) {

			response = $(response);

			response.find('img[srcset]').removeAttr('srcset');

			$('body').append(response);

		});

		return false;
	});

	$(document).on('click', '.wcspp-navigation .wcspp-pdf a', function() {

		if ( ajax == 'active' ) {
			return false;
		}

		var curr = $(this);
		var product_id = curr.closest('.wcspp-navigation').data('wcspp-id');

		ajax = 'active';


		$.when( wcspp_ajax( 'wcspp_quickview', product_id, 'pdf' ) ).done( function(response) {

			response = $(response);

			response.find('img[srcset]').removeAttr('srcset');

			$('body').append(response);

		});

		return false;
	});

	$(document).on( 'click', '.wcspp-quickview .wcspp-quickview-close', function() {

		$(this).parent().remove();

		return false;

	});

	$(document).on( 'click', '.wcspp-quickview .wcspp-page-wrap .wcspp-go-print', function() {

		$('.wcspp-page-wrap').print();

		return false;

	});

	$(document).on( 'click', '.wcspp-quickview .wcspp-page-wrap .wcspp-go-pdf', function() {

		var vars = $(this).parent().data('wcspp-pdf');

		$(this).parent().printPdf(vars);

		return false;

	});

})(jQuery);