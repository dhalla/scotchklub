


jQuery(function($){
	
	$('document').ready(function() {
		sk.init();
	});
	
	// Vegas Background-Image: http://vegas.jaysalvat.com
	$.vegas({
		src: '/wp-content/themes/scotchklub-child/img/bg/scotland.jpg'
		//src: '/wp-content/themes/scotchklub-child/img/bg/berg.jpeg'
	}) 
	('overlay', {
		//src: '/wp-content/themes/scotchklub-child/img/overlays/13.png'
	}) ;
	/*('slideshow', {
		delay: 20000,
		backgrounds:[
	    { src:'/wp-content/themes/scotchklub-child/img/bg/bar.jpg', fade:1000 },
	    { src:'/wp-content/themes/scotchklub-child/img/bg/scotland.jpg', fade:1000 },
	]
	}) */

	
});

var sk = {
		
	init: function() {
	}
		
		
}


