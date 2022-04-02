// Path elements
const $shortPath = $('#settings-shortPath');
const $demoPath  = $('#settings-demoPath');

// Automatically update path as it is changed
function updateDemoPath() {
    // Get current value
    var demoPath = $shortPath.val();
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
    $demoPath.html(demoPath);
}

// =========================================================================

// Update path while typing
$shortPath.on('keyup', function() {
    updateDemoPath();
}).focus();

// Update path on page load
$(function () {
    updateDemoPath();
});
