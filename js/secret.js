/**
 * 
 */
jQuery( document ).ready(function( $ ) {
	var search = "";

	$( ".site-header" ).after( "<div id='search-results'></div>" );
	$( "#search-results" )
		.html( "<div class='gcse-searchresults-only'></div>" )
		.css( "display", "none" );

	$( ".search-submit" ).click(function( event ) {
		var q = $( ".search-field" ).val();
		var element = google.search.cse.element.getElement("searchresults-only0");
		if ( q !== "" && q !== search ) {
			element.execute( q );
			$( "#search-results" ).removeAttr( "style" ).css({
				"border-bottom": "1px solid #ededed",
				"margin-bottom": "24px",
				"margin-bottom": "1.714285714rem",
				"padding-bottom": "24px",
				"padding-bottom": "1.714285714rem",
				"position": "relative"
			});
		} else {
			element.clearAllResults();
			$( "#search-results" ).css( "display", "none" );
		}
		event.preventDefault();
	});
});

(function( $ ) {}( jQuery ));
