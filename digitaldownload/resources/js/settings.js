// Automatically update path as it is changed
function updateDemoPath() {
	// Get current value
	var demoPath = $('#settings-shortPath').val();
	// Safety net
	if (undefined === demoPath) {
		demoPath = '';
	}
	// Clean up
	demoPath = demoPath.replace(/^[ /]*/, '');
	demoPath = demoPath.replace(/[ /]*$/, '');
	// If path specified
	if (demoPath) {
		// Append trailing slash
		demoPath = '<strong>' + demoPath + '</strong>/';
	} else {
		// Default to long path
		demoPath = longPath;
	}
	// Display path
	$('#settings-demo-path').html(demoPath);
}

// =================================================================== //

// Update path while typing
$('#settings-shortPath').on('keyup', function() {
	updateDemoPath();
}).focus();

// Update path on page load
$(function () {
	updateDemoPath();
});