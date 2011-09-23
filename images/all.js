var request;
var auto;
var queryString;   //will hold the POSTed data

function sendData(burst){
    var url = "s_pages/"+burst+".php";
    httpRequest("GET",url,true);
	
}

//event handler for XMLHttpRequest
function handleResponse(){
    if(request.readyState == 4){
        if(request.status == 200){
           var doc = request.responseText;
           stylizeDiv(doc,document.getElementById("mainDisplay"));
        } else {
            alert("A problem occurred with communicating between the XMLHttpRequest object and the server program.");
        }
    }//end outer if
}


/* Initialize a Request object that is already constructed */
function initReq(reqType,url,bool){
    /* Specify the function that will handle the HTTP response */
    request.onreadystatechange=handleResponse;
    request.open(reqType,url,bool);
    request.setRequestHeader("Content-Type",
            "application/x-www-form-urlencoded; charset=UTF-8");
    /* Only works in Mozilla-based browsers */
    //request.overrideMimeType("text/XML");
    request.send(queryString);
}

/* Wrapper function for constructing a Request object.
 Parameters:
  reqType: The HTTP request type such as GET or POST.
  url: The URL of the server program.
  asynch: Whether to send the request asynchronously or not. */
function httpRequest(reqType,url,asynch){
    //Mozilla-based browsers
    if(window.XMLHttpRequest){
        request = new XMLHttpRequest();
    } else if (window.ActiveXObject){
        request=new ActiveXObject("Msxml2.XMLHTTP");
        if (! request){
            request=new ActiveXObject("Microsoft.XMLHTTP");
        }
     }
    //the request could still be null if neither ActiveXObject
    //initializations succeeded
    if(request){
       initReq(reqType,url,asynch);
    }  else {
        alert("Your browser does not permit the use of all "+
        "of this application's features!");}
}

function stylizeDiv(bdyTxt,div){
    //reset DIV content
    div.innerHTML="";
    div.innerHTML = bdyTxt;
}

var auto;
var autoString;   //will hold the POSTed data

function autoLoad(){
    var url="stats.php"; 
    autoRequest("POST",url,true);
    setTimeout(autoLoad,15000);
}

//event handler for XMLHttpRequest
function autoHandle(){
    if(auto.readyState == 4){
        if(auto.status == 200){
            var resp = auto.responseText; //Retrieve PHP Output
			//alert(resp);
            var obj = eval(resp); //Evaluates the PHP Output as an Arrary
			//Sets the DIV s To the autoloaded stuff every (thenumberyouenters / 1000) seconds
			autoDiv(obj[0],document.getElementById("inHand"));
			autoDiv(obj[1],document.getElementById("inBank"));
			autoDiv(obj[2],document.getElementById("isRank"));
			autoDiv(obj[3],document.getElementById("turns"));
			autoDiv(obj[4],document.getElementById("time"));
			autoDiv(obj[5],document.getElementById("messages"));
        } else {
            alert("A problem occurred with communicating between the XMLHttpRequest object and the server program .//Auto Problem");
        }
    }//end outer if
}

/* Initialize a Request object that is already constructed */
function autoReq(reqType,url,bool){
    /* Specify the function that will handle the HTTP response */
    auto.onreadystatechange=autoHandle;
    auto.open(reqType,url,bool);
    auto.setRequestHeader("Content-Type",
            "application/x-www-form-urlencoded; charset=UTF-8");
    auto.send(autoString);
}

/* Wrapper function for constructing a Request object.
 Parameters:
  reqType: The HTTP auto type such as GET or POST.
  url: The URL of the server program.
  asynch: Whether to send the auto asynchronously or not. */
function autoRequest(reqType,url,asynch){
    //Mozilla-based browsers
    if(window.XMLHttpRequest){
        auto = new XMLHttpRequest();
    } else if (window.ActiveXObject){
        auto=new ActiveXObject("Msxml2.XMLHTTP");
        if (! auto){
            auto=new ActiveXObject("Microsoft.XMLHTTP");
        }
     }
    //the auto could still be null if neither ActiveXObject
    //initializations succeeded
    if(auto){
       autoReq(reqType,url,asynch);
    }  else {
        alert("Your browser does not permit the use of all "+
        "of this application's features!");}
}

function setQueryString(){
    autoString="";
    var frm = document.forms[0];
    var numberElements =  frm.elements.length;
    for(var i = 0; i < numberElements; i++)  {
            if(i < numberElements-1)  {
                autoString += frm.elements[i].name+"="+
                               encodeURIComponent(frm.elements[i].value)+"&";
            } else {
                autoString += frm.elements[i].name+"="+
                               encodeURIComponent(frm.elements[i].value);
            }

    }
}

function autoDiv(bdyTxt,div){
    //reset DIV content
    div.innerHTML=" ";
    div.innerHTML = bdyTxt;

}
