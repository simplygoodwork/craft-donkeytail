$(function () {

	// Draggable dot
	$(".donkeytail .donkeytail__dot").draggable({
		containment: 'parent',
		stop: function(event, ui){
			percentages = updateFields($(this), ui.position);
			$(this).css("left", percentages['left'] + '%')
						 .css("top", percentages['top'] + '%');
		},
	});

	// // Clickable canvas
	$("#fields").on("click", ".donkeytail .donkeytail__canvas, .donkeytail .donkeytail__sibling", function(e){
		$canvas = $(this).parent().find(".donkeytail__canvas");
		$dot = $(this).parent().find(".donkeytail__dot");

		if (e.shiftKey && (typeof $(this).attr("data-siblingtitle") !== "undefined")) {
			var jumpToEntry = confirm("Jump to editing entry '" + $(this).attr("data-siblingtitle") + "'?");
			if (jumpToEntry) {
				window.location = $(this).attr('data-siblingediturl')
			}
		} else {
			var offset = {};
			offset.left = e.pageX - $canvas.offset().left;
			offset.top = e.pageY - $canvas.offset().top;

			$dot.css("left", offset.left).css("top", offset.top);
			var percentageOffsets = updateFields($dot, offset);
			$dot.css("left", percentageOffsets.left + '%').css("top", percentageOffsets.top + '%');
		}

	});

});

// Update hidden fields
function updateFields($dot, offset) {	
	var $parent = $dot.parent().find(".donkeytail__canvas");
	var percentages = new Array();

	percentages['left'] = parseFloat((offset.left / $parent.width()) * 100).toFixed(4);
	percentages['top'] = parseFloat((offset.top / $parent.height()) * 100).toFixed(4);

	$fields = $dot.closest(".field");
	$fields.find(".leftPercentage").val(percentages['left']);
	$fields.find(".topPercentage").val(percentages['top']);
	$parent.parent().addClass("active");

	return percentages;
}
