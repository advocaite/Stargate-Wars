
		
<table border="0" cellpadding="0" cellspacing="0" width="800">

  <tr>
   <td><img src="spacer.gif" width="135" height="1" border="0" alt="" /></td>
   <td><img src="spacer.gif" width="520" height="1" border="0" alt="" /></td>
   <td><img src="spacer.gif" width="145" height="1" border="0" alt="" /></td>
   <td><img src="spacer.gif" width="1" height="1" border="0" alt="" /></td>
  </tr>

  <tr>
   <td align="left" valign="top"><table width="100%" border="0">
     <tr>
       <td colspan="2">Rank: </td>
       <td> <div id='isRank'></div></td>
     </tr>
     <tr>
       <td colspan="2">Action Turns:</td>
       <td><div id="turns"></div></td>
     </tr>
	 <tr>
	   <td>Messages:</td>
	   <td><a href="javascript:void(0)" onclick="sendData('messages','get','mainDisplay'); return false"><div id='messages'></div></a></td>
	 </tr>
	 <tr>
	 <td colspan="2"><center><a href='?logout=true'>Logout</a></center></td>
	 </tr>
   </table>   </td>
   <td align="center" valign="middle"><img src="images/logo.gif"></td>
   <td rowspan="2" align="left" valign="top"><table width="100%" border="0">
     <tr>
       <td width="27%"><a href="javascript:void(0)" onClick="sendData('bank','get','mainDisplay'); return false">Bank</a></td>
       <td width="73%"><div id='inBank'></div></td>
     </tr>
     <tr>
       <td>Naquadah:</td>
       <td><div id='inHand'></div></td>
     </tr>
     <tr>
       <td>Next Turn:</td>
       <td><div id='next'>&nbsp;</div></td>
     </tr>
     <tr>
       <td colspan="2"><div id='time'></div></td>
     </tr>
   </table></td>
   <td><img src="spacer.gif" width="1" height="99" border="0" alt="" /></td>
  </tr>
  <tr>
    <td background="../images/bar.jpg" colspan="2" align="center" valign="top">
      <a href="javascript:void(0)" onclick="sendData('base','get','mainDisplay'); return false">Base</a> | <a href="javascript:void(0)" onclick="sendData('rank','get','mainDisplay'); return false">Attack</a> | <a href="javascript:void(0)" onclick="sendData('armory','get','mainDisplay'); return false">Armory</a> | <a href="javascript:void(0)" onclick="sendData('train','get','mainDisplay'); return false">Train</a> | <a href="javascript:void(0)" onclick="sendData('technology','get','mainDisplay'); return false">Technology</a> | <a href="javascript:void(0)" onclick="sendData('logs','get','mainDisplay'); return false">Logs</a> | <a href="javascript:void(0)" onclick="sendData('market','get','mainDisplay'); return false">Market</a> | <a href="javascript:void(0)" onclick="sendData('faq','get','mainDisplay'); return false">F.A.Q.</a> | <a href="forums/" target="_blank">Forums</a> | Contact Us | Chat<br />
      <span>&quot;Because it is so clear it takes a long time to realise it. If you immediately know the candlelight is fire, the meal was cooked a long time ago.&quot;</span>    </td>
   <td rowspan="2"><img src="spacer.gif" width="1" height="27" border="0" alt="" /></td>
  </tr>
  <tr align="center">
    <td colspan="3" valign="top">
           <center><form name="form1" action="javascript:void(0);">
                <input id="keyword" name="keyword" style="WIDTH:150px" autocomplete="on" />
                <div class="autocompleteContainer">
				<div id="autocomplete" class="autocomplete"></div>
                </div>
				<input type="hidden" name="userID" id="userID" value="" />
				<input style="WIDTH:60px"type="button" value="GetInfo" onclick="sendData('user','get',userID.value); return false;"/>
            </form></center></td>
  </tr>
  <tr>
   <td colspan="3" align="left" valign="top"><div id='mainDisplay'></div></td>
   <td><img src="spacer.gif" width="1" height="474" border="0" alt="" /></td>
  </tr>
</table>

