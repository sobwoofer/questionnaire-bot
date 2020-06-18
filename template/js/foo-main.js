/* =======================================================
Table of Content
---------------------
1. ISOTOPE FILTERING INIT
	1.1 - ISOTOPE FILTERING FOR minimalio-services-menus ITEMS
2.  APPEAR JS FOR SKILL DATA ANIMATION INIT
3. GOOGLE MAP JS FOR minimalio-map-section
4. Owl Carousal Js for Hero Section
5. Toggle Menu Js for Header Menu
6. LavaLamp JS for minimalio-services-menus in Home & Portfolio Page
7. LavaLamp JS for Catagories Items in Blog and Single-blog Pages
	7.1 - Custom Js for Prevent Default Behavior on Click
8. Magnific JS for Lightbox PopUp in minimalio-grid
9. Synced OwlCarosel JS for minimalio-ceo-description
	9.1 - Custom Navigation Events with Owl Carousal
10. Classie JS for Minimal Form
=========================================================== */

(function($){
	jQuery.fn.exists = function(){return this.length>0;}

	/* =======================================================
	### ISOTOPE FILTERING INIT
	========================================================== */
	jQuery(window).on('load', function(){		
	    var $container = $('.minimalio-grid'),
	      colWidth = function () {
	        var w = $container.width(), 
	          columnNum = 1,
	          columnWidth = 0;
	        if (w > 1200) {
	          columnNum  = 3;
	        } else if (w > 900) {
	          columnNum  = 3;
	        } else if (w > 600) {
	          columnNum  = 2;
	        } else if (w > 450) {
	          columnNum  = 2;
	        } else if (w > 385) {
	          columnNum  = 1;
	        }
	        columnWidth = Math.floor(w/columnNum);
	        $container.find('.minimalio-grid-item').each(function() {
	          var $item = $(this),
	            multiplier_w = $item.attr('class').match(/minimalio-grid-item-w(\d)/),
	            multiplier_h = $item.attr('class').match(/minimalio-grid-item-h(\d)/),
	            width = multiplier_w ? columnWidth*multiplier_w[1] : columnWidth,
	            height = multiplier_h ? columnWidth*multiplier_h[1] : columnWidth;
	          $item.css({
	            width: width
	            // height: height
	          });
	        });
	        return columnWidth;
	      },
	      isotope = function () {
	        $container.isotope({
	          itemSelector: '.minimalio-grid-item',
	          masonry: {
	            columnWidth: colWidth(),
	            gutter: 1
	          }
	        });
	      };
	    isotope();
	    $(window).resize(isotope);

	/*==============================================================
    10.  ISOTOPE FILTERING FOR minimalio-services-menus ITEMS
  	================================================================*/
		if($('.minimalio-services-menus ul li a').exists()){
		    $(document).on('click', '.minimalio-services-menus ul li a', function(e){
		    	e.preventDefault();

		    	var filterData = $(this).attr('data-filter');

		    	$container.isotope({
		          filter: filterData
		        });
		    });
		}
	});


  /*==============================================================
    10.  APPEAR JS FOR SKILL DATA ANIMATION INIT 
  ================================================================*/
	if($('.minimalio-number-percentage').exists()){
		(function(){
			var number_percentage = $(".minimalio-number-percentage");

		    number_percentage.appear();
		    $(document.body).on('appear', '.minimalio-number-percentage-count', function () {
		      number_percentage.each(function () {
		        $(this).animateNumbers($(this).attr("data-value"), true, parseInt($(this).attr("data-animation-duration"), 10));
		        var value = $(this).attr("data-value");
		        var duration = $(this).attr("data-animation-duration");
		        $(this).closest('.minimalio-single-team-skill').find('.minimalio-team-skill .minimalio-skill-right').animate({width : value+'%'}, 4500);
		      });
	    });

		var appear = $('.appear');
		appear.appear();
		$.fn.animateNumbers = function (stop, commas, duration, ease) {
			return this.each(function () {
			  var $this = $(this);
			  var start = parseInt($this.text().replace(/,/g, ""), 10);
			  commas = (commas === undefined) ? true : commas;
			  $({
			    value: start
			  }).animate({
			      value: stop
			    }, {
			      duration: duration == undefined ? 500 : duration,
			      easing: ease == undefined ? "swing" : ease,
			      step: function () {
			        $this.text(Math.floor(this.value));
			        if (commas) {
			          $this.text($this.text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
			        }
			      },
			      complete: function () {
			        if (parseInt($this.text(), 10) !== stop) {
			          $this.text(stop);
			          if (commas) {
			            $this.text($this.text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
			          }
			        }
			      }
			    });
			});
		}
		})();
	}

	/* ========================================================= */
	// GOOGLE MAP JS FOR minimalio-map-section
	/* ========================================================= */
	if($('#minimalio-map-section').exists()){
    	(function(){
    	// When the window has finished loading create our google map below
	    google.maps.event.addDomListener(window, 'load', init);

	    function init() {
	        // Basic options for a simple Google Map
	        // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
	        var mapOptions = {
	            // How zoomed in you want the map to start at (always required)
	            zoom: 11,

	            // The latitude and longitude to center the map (always required)
	            center: new google.maps.LatLng(40.6700, -73.9400), // New York

	            // How you would like to style the map. 
	            // This is where you would paste any style found on Snazzy Maps.
	            styles: [{"featureType":"administrative","elementType":"all","stylers":[{"saturation":"-100"}]},{"featureType":"administrative.province","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","elementType":"all","stylers":[{"saturation":-100},{"lightness":"50"},{"visibility":"simplified"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":"-100"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"lightness":"30"}]},{"featureType":"road.local","elementType":"all","stylers":[{"lightness":"40"}]},{"featureType":"transit","elementType":"all","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ededed"},{"lightness":-25},{"saturation":-97}]},{"featureType":"water","elementType":"labels","stylers":[{"lightness":-25},{"saturation":-100}]}]
	        };

	        // Get the HTML DOM element that will contain your map 
	        // We are using a div with id="map" seen below in the <body>
	        var mapElement = document.getElementById('minimalio-map-section');

	        // Create the Google Map using our element and options defined above
	        var map = new google.maps.Map(mapElement, mapOptions);

	        // Let's also add a marker while we're at it
	        var marker = new google.maps.Marker({
	            position: new google.maps.LatLng(40.6700, -73.9400),
	            map: map,
	            title: 'Snazzy!'
	        });
	    }
    	})();
	}
    
	/*--------------------------------------------------------------
	  Minimalio - Appear Animate INIT
	--------------------------------------------------------------*/
	 $('.number-animate').appear();
		$(document.body).on('appear', '.numeric-count', function () {
			$('.number-animate').each(function () {
				$(this).animateNumbers($(this).attr("data-value"), true, parseInt($(this).attr("data-animation-duration")));
			});
		});

		$('.appear').appear();
		$.fn.animateNumbers = function (stop, commas, duration, ease) {
			return this.each(function () {
				var $this = $(this);
				var start = parseInt($this.text().replace(/,/g, ""));
				commas = (commas === undefined) ? true : commas;
				$({
					value: start
				}).animate({
						value: stop
					}, {
						duration: duration == undefined ? 500 : duration,
						easing: ease == undefined ? "swing" : ease,
						step: function () {
							$this.text(Math.floor(this.value));
							if (commas) {
								$this.text($this.text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
							}
						},
						complete: function () {
							if (parseInt($this.text()) !== stop) {
								$this.text(stop);
								if (commas) {
									$this.text($this.text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
								}
							}
						}
					});
			});
		};

	/* ========================================================= */
	// Owl Carousal Js for Hero Section
	/* ========================================================= */
	if($('.minimalio-welcome-section').exists()){
		$(".minimalio-welcome-section").owlCarousel({
			autoPlay: 3000, //Set AutoPlay to 3 seconds
			singleItem: true,			
			navigation: false,
			transitionStyle: "fade"		
		});
	}

	/* ========================================================= */
	// Toggle Menu Js for Header Menu
	/* ========================================================= */
	if($('#minimalio-overlay-id').exists()){
		$('#toggle').on('click', function () {
		    $(this).toggleClass('active');
		    $('#minimalio-overlay-id').toggleClass('open');
		});

		 $('.minimalio-overlay-menu a').on('click', function () {
		      
	      $('html, body').animate({
	         
	          scrollTop: $('#' + $(this).data('value')).offset().top
	         
	      }, 1000);
	      
	      $('#toggle').removeClass('active');
	      
	      $('#minimalio-overlay-id').removeClass('open');
	      
  		});
	}

	/* ===================================================================
    ### LavaLamp JS for minimalio-services-menus in Home & Portfolio Page
    ====================================================================== */	  
	if($('ul.minimalio-lavalamp-darrow').exists()){
	  $(function() {
        $('ul.minimalio-lavalamp-darrow').lavaLamp({
        	'target': 'li',
        	'container': 'li',
        	'fx': 'swing',
        	'autoReturn': true,
			'speed': 200,
			'returnDelay': 0,
			'setOnClick': true,
        });
      });
    }
	  
	/* ===============================================================
    ### LavaLamp JS for Catagories Items in Blog and Single-blog Pages
    ================================================================== */
	if($('ul.minimalio-lavalamp-rarrow').exists()){
	  $(function() {
        $('ul.minimalio-lavalamp-rarrow').lavaLamp({
        	'target': 'li',
        	'container': 'li',
        	'fx': 'swing',
        	'autoReturn': true,
			'speed': .1,
			'returnDelay': .1,
			'setOnClick': true,
        });
      });
	}

	/* ====================================================
    ### Custom Js for Prevent Default Behavior on Click
    ======================================================= */
	$(".minimalio-lavalamp-rarrow li").on('click',function(e){
		e.preventDefault();
	});


    /* ====================================================
    ###  Magnific JS for Lightbox PopUp in minimalio-grid 
    ======================================================= */
    if($('.minimalio-img-container').exists()){
	  $('.minimalio-img-container').magnificPopup({
	  	  delegate: 'a',
		  type: 'image',
		  gallery: {
		    enabled: true
		  },
          mainClass: 'mfp-with-zoom mfp-fade', // class to remove default margin from left and right side
		});
	}
	//======== End Magnific JS for Lightbox PopUp =========//


    /* ====================================================
    ###  Magnific JS Activation for Lightbox PopUp in About Page
    ======================================================= */

    $('.minimalio-about-bottom-image a.video-popup').magnificPopup({
      disableOn: 700,
      type: 'iframe',
      mainClass: 'mfp-fade',
      removalDelay: 160,
      preloader: false,

      fixedContentPos: false
    });


	/* ====================================================
    ###  Synced OwlCarosel JS for minimalio-ceo-description
    ======================================================= */
	  
	  var sync1 = $("#sync1");
	  var sync2 = $("#sync2");
	 	if(sync1.length > 0 && sync2.length > 0){
		  sync1.owlCarousel({
		    singleItem : true,
		    slideSpeed : 1000,
		    afterAction : syncPosition
		  });
		 
		  sync2.owlCarousel({
		    items : 3,
		    itemsDesktop      : [1199,3],
		    itemsTablet       : [768,3],
		    afterInit : function(el){
		      el.find(".owl-item").eq(0).addClass("synced");
		    }
		  });
	  	}
  	/* ============================================
    ###  Custom Navigation Events with Owl Carousal
    =============================================== */
	   
	 if($('.minimalio-img-container a').exists()){
	   $(".minimali-next-nav").on('click',function(e){
	  	 e.preventDefault();
	     sync1.trigger('owl.next');
	   })
	 }

	 if($('.minimalio-img-container a').exists()){
	   $(".minimali-prev-nav").on('click',function(e){
	   	 e.preventDefault();
	     sync1.trigger('owl.prev');
	   })
	 }
	 
	 //========= End Custom Navigation Events with Owl Carousal ==========//
	  function syncPosition(el){
	    var current = this.currentItem;
	    $("#sync2")
	      .find(".owl-item")
	      .removeClass("synced")
	      .eq(current)
	      .addClass("synced")
	    if($("#sync2").data("owlCarousel") !== undefined){
	      center(current)
	    }
	  }
	 
	  $("#sync2").on("click", ".owl-item", function(e){
	    e.preventDefault();
	    var number = $(this).data("owlItem");
	    sync1.trigger("owl.goTo",number);
	  });
	 
	  function center(number){
	    var sync2visible = sync2.data("owlCarousel").owl.visibleItems;
	    var num = number;
	    var found = false;
	    for(var i in sync2visible){
	      if(num === sync2visible[i]){
	        var found = true;
	      }
	    }
	 
	    if(found===false){
	      if(num>sync2visible[sync2visible.length-1]){
	        sync2.trigger("owl.goTo", num - sync2visible.length+2)
	      }else{
	        if(num - 1 === -1){
	          num = 0;
	        }
	        sync2.trigger("owl.goTo", num);
	      }
	    } else if(num === sync2visible[sync2visible.length-1]){
	      sync2.trigger("owl.goTo", sync2visible[1])
	    } else if(num === sync2visible[0]){
	      sync2.trigger("owl.goTo", num-1)
	    }
	  }
	//========== End Synced OwlCarosel JS =============//

	/* ============================================
    ###  Classie JS for Minimal Form 
    =============================================== */	  
	  if($('#minimalio-contact-form').exists()){
	  var minimalio_contact_form = document.getElementById( 'minimalio-contact-form' );

	  	jQuery('.minimalio-form-next-button').addClass('show');

		new stepsForm( minimalio_contact_form, {
			onSubmit : function( form ) {
				// hide form
				classie.addClass( minimalio_contact_form.querySelector( '.minimalio-simform-inner' ), 'hide');

				// let's just simulate something...
				var messageEl = minimalio_contact_form.querySelector( '.minimalio-final-message' );
				messageEl.innerHTML = 'Thank you! We\'ll be in touch.';
				classie.addClass( messageEl, 'show' );
			}
		} );
	}
	//================ End Classie JS ===========//
}(jQuery));