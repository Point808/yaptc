    function doAjaxGet(dataSource) {
       if(navigator.appName == "Microsoft Internet Explorer") {
         objHTTP = new ActiveXObject("Microsoft.XMLHTTP");
       } else {
         objHTTP = new XMLHttpRequest();
       }
       objHTTP.open("POST", dataSource, true);
       objHTTP.onreadystatechange = function() 
       { 
       if (objHTTP.readyState == 4 && objHTTP.status == 200) {
           document.getElementById('oClock').innerHTML = objHTTP.responseText;
         }
       }
         objHTTP.send('null'); 
       }
    function startclock() {
     doAjaxGet('time.inc.php');
      setTimeout('startclock()',1000);
     }
