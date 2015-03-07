/* <![CDATA[ */
$(document).ready(function(){
	/* IE8 expose doesn't work, TODO: fix in the next version
	$(function() {

		// expose the form when it's clicked or cursor is focused
		var form = $(".yform").bind("click keydown", function() {

			$(this).expose({
				color: '#8F8F8F',
				// when exposing is done, change form's background color
				onLoad: function() {
					form.css({backgroundColor: '#fff'});
					form.css({borderColor:'#000000'});
				},

				// when "unexposed", return to original background color
				onClose: function() {
					form.css({backgroundColor: '#F4F4F4'});
					form.css({border:'1px solid #ddd'});
				}

			});
		});
	});
	*/
	$(function() {
/*
		$(".cartShipLink").overlay({
			mask: '#8F8F8F',
			target: '#overlay_frame',
			onBeforeLoad: function() {
				var wrap = this.getOverlay().find(".contentWrap");
				wrap.load(this.getTrigger().attr("href"));
			}
		});
*/
/*
		$(".thickbox").overlay({
			mask: '#8F8F8F',
			target: '#overlay_frame',
			onBeforeLoad: function() {
				var wrap = this.getOverlay().find(".contentWrap");
				wrap.load(this.getTrigger().attr("href"));
			}
		});
*/
	});
	$("ul.tabbedHeading").tabs("div.tabbedBody > div");
	$("#addCartJquery").click(function(){
		$(".productImage .imgLeft").clone().appendTo(".copyProductImage");
		$(".copyProductImage img").animate({
			top: -500,
			left: 1300,
			width: 70,
			opacity: 0
		}, 1000, function() {
			$(".addCartHidden input").trigger('click');
		});
	});
});
/* ]]> */