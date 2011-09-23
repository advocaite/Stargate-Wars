var auto;
var autoString;   //will hold the POSTed data

function autoLoad(){
    var url="stats.php"; 
    autoRequest("GET",url,true);
    setTimeout(autoLoad,15000);
}

//event handler for XMLHttpRequest
function autoHandle(){
    if(auto.readyState == 4){
        if(auto.status == 200){
            var resp = auto.responseText;
            var obj = eval(resp);
			stylizeDiv(obj[6],document.getElementById("next"));
			stylizeDiv(obj[5],document.getElementById("messages"));
			stylizeDiv(obj[4],document.getElementById("time"));
			stylizeDiv(obj[3],document.getElementById("turns"));
			stylizeDiv(obj[2],document.getElementById("isRank"));
			stylizeDiv(obj[1],document.getElementById("inBank"));
			stylizeDiv(obj[0],document.getElementById("inHand"));			
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

