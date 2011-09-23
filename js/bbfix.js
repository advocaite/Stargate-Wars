//-------------------------------------------------
//-------------------------------------------------

var bb_count = 0;
var bb_curr_idx = "";
var bb_cache = new Array;
var bb_debug = false;
var bb_iframe_script = "count.php";
var bb_iframe_loaded = false;
var bb_target_div = "";

// If debug is enabled via bb_init(), then
// we append some data to the divTrail 
// element.

function bb_debug_update(str) {
    if (bb_debug) {
        var divBBDebug = document.getElementById("divBBDebug");
	divBBDebug.innerHTML = divBBDebug.innerHTML + "<br>" + str;
    }
	//alert("debug update");
}

// Run from the interval timer (every 1.5 seconds), 
// this function reads a cache index value that 
// is stored in the DIV element of the child IFRAME.
// 
// If this extracted cache index differs from the
// current cache index, then the back button was
// pressed.  In this case, we pull the corresponding
// data from the cache and update the page.

function bb_check_state() {
	//alert("check");
    if (bb_iframe_loaded == false) {
	return;
    }

    var doc =  window.frames['bbFrame1'].document;
    var new_idx = doc.getElementById('divFrameCount').innerHTML;

    if (new_idx != bb_curr_idx) {

	var debug_msg = "IFRAME changed. Was " 
	    + bb_curr_idx 
            + ", now " 
            + new_idx;

	// Pull a previous state from the cache (if it exists).

	if (bb_cache[new_idx]) {
	    var divBody = document.getElementById("divBody");
	    divBody.innerHTML = bb_cache[new_idx];

	    debug_msg += " [pulled " 
	        + new_idx 
		+ " from cache]";
        }
        bb_curr_idx = new_idx;

	bb_debug_update(debug_msg);
    }
}

// Called by child IFRAME

function bb_done_loading() {
		//alert("done");
    bb_iframe_loaded = true;
}

// Update the hidden IFRAME.

function bb_loadframe() {
		//alert("loadframe");
    var bbFrame1 = document.getElementById("bbFrame1");
    bb_iframe_loaded = false;
    bbFrame1.src = bb_iframe_script + "?count=" + bb_count;
}

// When requested, save the current state
// in a cache.

function bb_save_state() {
		//alert("save state");
    // Store the new contents in the cache.
    var div_to_cache = document.getElementById(bb_target_div);
    bb_count++;
    bb_cache[bb_count] = div_to_cache.innerHTML;

    bb_debug_update("Added " + bb_count + " to cache");

    // Load new page into iframe.
    bb_loadframe();

    bb_curr_idx = bb_count;
}

// Load the hidden IFRAME and start an interval timer.

function bb_init(div_name, debug_val) {
	//alert("inited");
    bb_target_div = div_name;
    bb_debug = debug_val;
    bb_loadframe();
    window.setInterval('bb_check_state()', 1000);
    bb_save_state();
}