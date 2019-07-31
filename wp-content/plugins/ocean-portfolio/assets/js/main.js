var $j = jQuery.noConflict();

$j( document ).on( 'ready', function() {
	"use strict";
	// Masonry
	op_portfolioMasonry();
    // Isotope
    op_portfolioIsotope();
    // Lightbox
    op_portfolioLightbox();
} );

// Run on orientation change
$j( window ).on( 'orientationchange', function() {
	"use strict";
	// Masonry
	op_portfolioMasonry();
	// Isotope
	op_portfolioIsotope();
} );

/* ==============================================
MASONRY
============================================== */
function op_portfolioMasonry() {
	"use strict"

	// Make sure scripts are loaded
    if ( undefined == $j.fn.imagesLoaded || undefined == $j.fn.isotope ) {
        return;
    }

    // Loop through items
    $j( '.portfolio-entries.masonry-grid .portfolio-wrap' ).each( function() {

        // Var
        var $wrap = $j( this );

        // Run only once images have been loaded
        $wrap.imagesLoaded( function() {

            // Create the isotope layout
            var $grid = $wrap.isotope( {
                itemSelector       : '.portfolio-entry',
                transformsEnabled  : true,
                isOriginLeft       : oceanwpLocalize.isRTL ? false : true,
                transitionDuration : '0.4s',
                layoutMode         : 'masonry'
            } );

        } );

    } );

}

/* ==============================================
ISOTOPE
============================================== */
function op_portfolioIsotope() {
	"use strict"

	// Make sure scripts are loaded
    if ( undefined == $j.fn.imagesLoaded || undefined == $j.fn.isotope ) {
        return;
    }

    // Loop through items
    $j( '.portfolio-entries.isotope-grid .portfolio-wrap' ).each( function() {

        // Var
        var $wrap = $j( this );

        // Run only once images have been loaded
        $wrap.imagesLoaded( function() {

            // Create the isotope layout
            var $grid = $wrap.isotope( {
                itemSelector       : '.portfolio-entry',
                transformsEnabled  : true,
                isOriginLeft       : oceanwpLocalize.isRTL ? false : true,
                transitionDuration : '0.4s',
                layoutMode         : $wrap.data( 'layout' ) ? $wrap.data( 'layout' ) : 'masonry'
            } );

            // Filter links
            var $filter = $wrap.prev( 'ul.portfolio-filters' );
            if ( $filter.length ) {

                var $filterLinks = $filter.find( 'a' );

                $filterLinks.click( function() {

                    $grid.isotope( {
                        filter : $j( this ).attr( 'data-filter' )
                    } );

                    $j( this ).parents( 'ul' ).find( 'li' ).removeClass( 'active' );
                    $j( this ).parent( 'li' ).addClass( 'active' );

                    return false;

                } );

            }

        } );

    } );

}

/* ==============================================
LIGHTBOX
============================================== */
function op_portfolioLightbox() {
    "use strict"

    // Make sure lightbox script is enabled
    if ( $j( 'body' ).hasClass( 'no-lightbox' )
        || $j( '.portfolio-entries' ).hasClass( 'no-lightbox' ) ) {
        return;
    }

    var $pswp = $j( '.pswp' )[0],
        image = [];

    $j( '.portfolio-wrap' ).each( function() {
        var $pic     = $j( this ),
            getItems = function() {
                var items = [];
                $pic.find( '.portfolio-lightbox' ).each( function() {
                    var $href   = $j( this ).attr( 'href' ),
                        $size   = $j( this ).data( 'size' ).split( 'x' ),
                        $width  = $size[0],
                        $height = $size[1];

                    var item = {
                        src : $href,
                        w   : $width,
                        h   : $height
                    }

                    items.push(item);
                });
                return items;
            }

        var items = getItems();

        $j.each( items, function( index, value ) {
            image[index]     = new Image();
            image[index].src = value['src'];
        } );

        $pic.find( '.portfolio-lightbox' ).closest( 'figure' ).on( 'click', function( e ) {
            e.preventDefault();
            
            var $index = $pic.find( '.portfolio-lightbox' ).closest( 'figure' ).index( $j( this ) );
            var options = {
                index: $index,
                bgOpacity: 0.7,
                showHideOpacity: true,

                shareButtons: [
                    {id:'facebook',     label: oceanwpLocalize.shareFacebook,   url:'https://www.facebook.com/sharer/sharer.php?u={{url}}'},
                    {id:'twitter',      label: oceanwpLocalize.shareTwitter,    url:'https://twitter.com/intent/tweet?text={{text}}&url={{url}}'},
                    {id:'pinterest',    label: oceanwpLocalize.sharePinterest,  url:'http://www.pinterest.com/pin/create/button/'+'?url={{url}}&media={{image_url}}&description={{text}}'},
                    {id:'download',     label: oceanwpLocalize.pswpDownload,    url:'{{raw_image_url}}', download: true}
                ]
            }

            var lightBox = new PhotoSwipe( $pswp, PhotoSwipeUI_Default, items, options );
            lightBox.init();
        } );

        // Stop propagation for the links
        $pic.find( '.op-link' ).on( 'click', function( e ) {
            e.stopPropagation();
        } );

    } );

}