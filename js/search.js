var mySuggest1;

function suggestTHIS()
{
	mySuggest1 = new Suggest("mySuggest1", document.getElementById("keyword"), "autocomplete", "userlist.php?val=");
}
function Suggest(inObjectName, inQueryField, inAutoCompleteDivName, inQueryUrl) {
    
    var objectName = inObjectName;
    var queryField = inQueryField;
    var autocompleteDivName = inAutoCompleteDivName;
    var queryUrl = inQueryUrl;
    
    var req;
    var suggestionValues=[];
    var resultsObj;
    var selected = -1;
    var latestServerQuery = "";
    var THROTTLE_PERIOD = 500;
    
    this.requestLoop = requestLoop;
    this.highlight = highlight;
    this.setValues = setValues;
    this.hideAutocompleteDiv = hideAutocompleteDiv;
    
    startup();
    
   /**
    *
    * It all starts here!
    *
    */
    
    function startup() {
        hideAutocompleteDiv();
        queryField.autocomplete = "off";
        queryField.onkeydown = keypressHandler;
        queryField.onkeyup = keyupHandler;
        queryField.focus();
        requestLoop();
    }
    
    function requestLoop() {
        var keyword = query().toLowerCase();
        if ((keyword!=latestServerQuery) && (keyword != '')) {
            sendQuery(keyword.toLowerCase());
            latestServerQuery = query().toLowerCase();
        }
        if (keyword == '') {
            hideAutocompleteDiv();
            suggestionValues = [];
            latestServerQuery = null;
            selected=0;
        }
        setTimeout(objectName + '.requestLoop();', THROTTLE_PERIOD);
    }
    
    function query() {
        var textbox = queryField;
        if(textbox.createTextRange){
            var fa=document.selection.createRange().duplicate();
            N=fa.text.length;
            }else if(textbox.setSelectionRange){
            N=textbox.selectionEnd-textbox.selectionStart;
        }
        return textbox.value.substring(0, textbox.value.length-N);
    }
    
    
    function sendQuery(key)
    {
        initialize();
        var url = queryUrl + key;
        
        if(req!=null) {
            req.onreadystatechange = process;
            req.open("GET", url, true);
            req.send(null);
        }
        
    }
    
    function initialize()
    {
        try {
            req=new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch(e) {
            try {
                req=new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch(oc) {
                req=null;
            }
        }
        
        if(!req&&typeof XMLHttpRequest!="undefined") {
            req= new XMLHttpRequest();
        }
    }
    
    
    function process()
    {
        if (req.readyState == 4) {
            // only if "OK"
            if (req.status == 200) {
                if(req.responseText=="")
                hideAutocompleteDiv();
                else {
                    showAutocompleteDiv();
                    try {
                        resultsObj = eval('(' + req.responseText + ')');
                        htmlFormat();
                    }
                    catch(e) {
                        var msg = (typeof e == "string") ? e : ((e.message) ? e.message : "Unknown Error");
                        alert(msg);
                    }
                }
            }
            else {
                document.getElementById(autocompleteDivName).innerHTML=
                "There was a problem retrieving data:<br>"+req.statusText;
            }
        }
    }
    
    
    function htmlFormat() {
        
        var output = document.getElementById(autocompleteDivName)
        while(output.childNodes.length>0) {
            output.removeChild(output.childNodes[0]);
        }
        
        suggestionValues = [];
        for (var i=0;i<resultsObj.result.length;i++) {
            if (resultsObj.result[i][0]) {
                suggestionValues.push(resultsObj.result[i][0]);
                
                var x1 = document.createElement("div");
                var x1 = document.createElement("div");
                if (i==selected) {
                    x1.className = "srs";
                    } else {
                    x1.className = "sr";
                }
                
                var onMouseFn = objectName + ".highlight(this, " + i + ");return false;";
                x1.onmousemove = new Function(onMouseFn);
                var onClickFn = objectName + ".setValues();" + objectName + ".hideAutocompleteDiv();return true;";
                x1.onmousedown = new Function(onClickFn);
                
                var x2 = document.createElement("span");
                x2.className = "srt";
                x2.appendChild(document.createTextNode(resultsObj.result[i][0]));
                
                var x3 = document.createElement("span");
                x3.className = "src";
                x3.appendChild(document.createTextNode(resultsObj.result[i][1] + ", " + resultsObj.result[i][2]));
				
				 
                x1.appendChild(x2);
                x1.appendChild(x3);
                
                output.appendChild(x1);
            }
        }
        requestSuggestions();
    }
    
    function keyupHandler (evt)
    {
        // make sure we have a valid event variable
        if(!evt && window.event) {
            evt = window.event;
        }
        var key = evt.keyCode;
        
        if (key < 32 || (key >= 33 && key <= 46) || (key >= 112 && key <= 123)) {
            //ignore
            } else {
            //request suggestions from the suggestion provider
            // Backspace key(8), Delete key (48)
            if (key != 8 && key != 48) {
                requestSuggestions();
            }
        }
        return true;
    }
    
    
    function keypressHandler (evt)
    {
        // don't do anything if the div is hidden
        var div = document.getElementById(autocompleteDivName);
        
        if (div.style.display == "none")
        return true;
        
        // make sure we have a valid event variable
        if(!evt && window.event) {
            evt = window.event;
        }
        var key = evt.keyCode;
        
        // if this key isn't one of the ones we care about, just return
        var KEYUP = 38;
        var KEYDOWN = 40;
        var KEYENTER = 13;
        var KEYTAB = 9;
        
        if ((key != KEYUP) && (key != KEYDOWN)  && (key != KEYENTER))
        {
            return true;
        }
        
        if (key == KEYUP){
            if ((selected-1) >= 0) {
                selected = selected -1;
                setValues();
                htmlFormat();
            }
        }
        
        if (key == KEYDOWN) {
            if ((selected+1) < 10) {
                selected = selected +1;
                setValues();
                htmlFormat();
            }
        }
        
        if (key == KEYENTER) {
            setValues();
            selectRange(99,99);
            hideAutocompleteDiv();
            return false;
        }
        
        return true;
    }
    
    function highlight(item, number) {
        selected = number;
        htmlFormat();
    }
    
    function setValues() {
        if (resultsObj) {
               var result = resultsObj.result[selected][0];
			   var userID = document.getElementById ('userID');         
               userID.value = resultsObj.result[selected][3];
               latestServerQuery = result.toLowerCase();
               queryField.value = result;
               selectRange(99,99);
        }
    }
    
    function showAutocompleteDiv(){
        if (document.layers) document.layers[autocompleteDivName].display="block";
        else document.getElementById(autocompleteDivName).style.display="block";
    }
    
    function hideAutocompleteDiv(){
        if (document.layers) document.layers[autocompleteDivName].display="none";
        else document.getElementById(autocompleteDivName).style.display="none";
    }
    
    function requestSuggestions() {
        var sTextboxValue =  query().toLowerCase();
        var aSuggestions = [];
        
        if (suggestionValues.length > 0){
            //search for matching states
            for (var i=0; i < suggestionValues.length; i++) {
                if (suggestionValues[i].toLowerCase().indexOf(sTextboxValue) == 0) {
                    aSuggestions.push(suggestionValues[i]);
                }
            }
            autosuggest(aSuggestions);
        }
    };
    
    
    
    function autosuggest(aSuggestions ) {
        //make sure there's at least one suggestion
        if (aSuggestions.length > 0) {
            typeAhead(aSuggestions[0]);
        }
    }
    
    function typeAhead(sSuggestion ) {
        
        var textbox = queryField;
        //check for support of typeahead functionality
        if (textbox.createTextRange || textbox.setSelectionRange){
            var iLen = query().length;
            textbox.value = sSuggestion;
            selectRange(iLen, sSuggestion.length);
        }
    };
    
    function selectRange(iStart, iLength ) {
        
        var textbox = queryField;
        //use text ranges for Internet Explorer
        if (textbox.createTextRange) {
            var oRange = textbox.createTextRange();
            oRange.moveStart("character", iStart);
            oRange.moveEnd("character", iLength - textbox.value.length);
            oRange.select();
            //use setSelectionRange() for Mozilla
            } else if (textbox.setSelectionRange) {
            textbox.setSelectionRange(iStart, iLength);
        }
        
        //set focus back to the textbox
        textbox.focus();
    };
    
    function trim (incoming) {
        if (incoming) {
            return incoming.replace(/^(\s+)?(.*\S)(\s+)?$/, '$2');
            } else {
            return incoming;
        }
    }
    
    
} 