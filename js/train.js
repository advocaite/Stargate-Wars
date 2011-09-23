var train;

function trainthis(page,type,id){
    if (type == "post") { setQueryStringTrain(page); }
	var url = "modules/"+page+".php?id="+id+"&time=1";//+date.getTime();
    trainReq("POST",url,true);
}

//event handler for XMLtrainReq
function trainResp(){
    if(train.readyState == 4){
        if(train.status == 200){
           var doc = train.responseText;
           stylizeDiv(doc,document.getElementById("display"));
        } else {
            alert("A problem occurred with communicating between the XMLtrainReq object and the server program.");
        }
    }//end outer if
}

function trainInit(reqType,url,bool){
    train.onreadystatechange=trainResp;
    train.open(reqType,url,bool);
    train.setRequestHeader("Content-Type",
            "application/x-www-form-urlencoded; charset=UTF-8");
    train.send(queryString);
}

function trainReq(reqType,url,asynch){
    if(window.XMLHttpRequest){
        train = new XMLHttpRequest();
    } else if (window.ActiveXObject){
        train=new ActiveXObject("Msxml2.XMLHTTP");
        if (! train){
            train=new ActiveXObject("Microsoft.XMLHTTP");
        }
     }
    if(train){
       trainInit(reqType,url,asynch);
    }  else {
        alert("Your browser does not permit the use of all "+
        "of this application's features!");}
}

function setQueryStringTrain(page){
    queryString="";
	var frm = document.getElementById(page);
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