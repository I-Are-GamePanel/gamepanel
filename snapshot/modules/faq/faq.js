window.location.hash = ''
$(function() {
  $("#search").jcOnPageFilter({
    animateHideNShow: false,
    focusOnLoad: true,
    highlightColor: "yellow",
    textColorForHighlights: "#000000",
    caseSensitive: false,
    hideNegatives: true,
    parentLookupClass: "accordion-content",
    childBlockClass: "accordion-toggle, .faqanswer"
  });
});

/* To make one of the topics the default use "accordion-content default" */
$(document).ready(function($) {	
	$('#accordion').find('.accordion-toggle').click(function(){

		//Expand or collapse this panel
		$(this).next().slideToggle('fast');

		//Hide the other panels
		$(".accordion-content").not($(this).next()).slideUp('fast');

	});
	
	$('.faqanswer').each(function(){
		$(this).html($(this).html().replace(/\n/g, '<br>'));
		$(this).find('pre').each(function(){
			$(this).html($(this).html().replace(/<br>/g, '\n'));
		});
	});
});

SyntaxHighlighter.all();