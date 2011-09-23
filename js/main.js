var request;
var queryString;   //will hold the POSTed data
var a;
function autocomplete(sender,ev) {
if (( ev.keyCode >= 48 && ev.keyCode <= 57 ) 
  ||  ( ev.keyCode >= 65 && ev.keyCode <= 90 )) {
	var sent = sender.value;
    // Prepare a server request:
      if(window.XMLHttpRequest){
        httpreq = new XMLHttpRequest();
    } else if (window.ActiveXObject){
        httpreq=new ActiveXObject("Msxml2.XMLHTTP");
        if (! httpreq){
            httpreq=new ActiveXObject("Microsoft.XMLHTTP");
        }
     }
    var url = "userlist.php?val="+sent;
    httpreq.open("GET", url, true);

    var original_text = sender.value;

    // Response function:
    httpreq.onreadystatechange = function () {
      if (httpreq.readyState == 4) {
		var resp = httpreq.responseText;
	  	var obj = eval(resp);
        var suggestion = obj[0];
		var userID = document.getElementById ('userID2');         
		userID.value = obj[1];
        var toUser = document.getElementById ('toUser1');         

        if ((suggestion) && (toUser.value == original_text)) {
	   // Firefox and Opera
           if(window.XMLHttpRequest) {
              var initial_len = toUser.value.length;
              toUser.value = suggestion;
              toUser.selectionStart = initial_len;
              toUser.selectionEnd   = toUser.value.length;            
           }
           // Internet Explorer
           else if (window.ActiveXObject) {
	   		  var leg =  suggestion.replace(original_text,"");
              var sel = document.selection.createRange ();
			  sel.text = leg;
    		  sel.move ("character", -suggestion.length); 
              sel.findText (leg);
              sel.select ();
           }
        }
      }
    }
    httpreq.send (null);
	}
}
function toggle_visible (elName) {

    var el = document.getElementById (elName);
    var isVisible = (el.style.visibility == "hidden") ? true : false;

    el.style.visibility = isVisible ? "visible" : "hidden";
    el.style.display = isVisible ? "inline" : "none";
}
function sendData(page,type,id,atype,subject,message){
	bb_save_state();
	date = new Date();
	if (type =="post")
	{
	setQueryString();
    var url = "modules/"+page+".php?id="+id+"&time="+date.getTime()+"&atype="+atype;
    httpRequest("POST",url,true);
	}else{
    var url = "modules/"+page+".php?id="+id+"&time="+date.getTime()+"&atype="+atype;
    httpRequest("GET",url,true);
	}
}

function mainUpdate(page,text)
{
	date = new Date();
    var url = "indexpages/"+page+".php?time="+date.getTime();
    httpRequest("GET",url,true);
	a = text;
}

function rollUpDate(text)
{
	stylizeDiv(text,document.getElementById("rollover"));
}

function autoclear()
{
	stylizeDiv(a,document.getElementById("rollover"));
}

//event handler for XMLHttpRequest
function handleResponse(){
    if(request.readyState == 4){
        if(request.status == 200){
           var doc = request.responseText;
            stylizeDiv(doc,document.getElementById("mainDisplay"));
			 queryString="";
        } else {
            alert("A problem occurred with communicating between the XMLHttpRequest object and the server program.");
        }
    }//end outer if
	
	
}

function initReq(reqType,url,bool){
    request.onreadystatechange=handleResponse;
    request.open(reqType,url,bool);
    request.setRequestHeader("Content-Type",
            "application/x-www-form-urlencoded; charset=UTF-8");
    request.send(queryString);
	queryString=null;
}

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

function setQueryString(){
    queryString="";
    var frm = document.forms[1];
    var numberElements =  frm.elements.length;
    for(var i = 0; i < numberElements; i++)  {
            if(i < numberElements-1)  {
                queryString += frm.elements[i].name+"="+
                               encodeURIComponent(frm.elements[i].value)+"&";
            } else {
                queryString += frm.elements[i].name+"="+
                               encodeURIComponent(frm.elements[i].value);
            }

    }
	
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function disableFormElements(formD)
{
    formD = document.getElementById(formD);
    for (var i=0;i<formD.elements.length;i++)
   {
      var e = document.formD.elements[i];
      e.disabled=true;
   }
}

function disableFormElementsAfterSubmit(in_Name)
{
   setTimeout("disableFormElements(" + in_Name + ")", 10);
}
