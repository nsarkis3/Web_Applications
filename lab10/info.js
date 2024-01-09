var URL="https://ceclnx01.cec.miamioh.edu/~johnsok9/cse383/ajax/index.php";
var loadCount = 0;
var errorCount = 0;
var usersCount = 0;
var networkCount = 0;
var preTXBytes = 0;
var preRXBytes = 0;

var dfCounter=0;
var errorCounter=0;


$(document).ready(function(){	
	getLoadAvg();
	getNetwork();
	getDisk();
});

function getDisk() {

	a=$.ajax({
		url: URL + '/api/v1/ps',
		method: "GET"
	}).done(function(data) {
		dfCounter++;
		//clear out old data
		$("#processRun").html(dfCounter);
		$("#processes").html("");
		$("#processes").append("<tr><th>User </th><th>Pid</th><th>runtime</th><th>Command</th></tr>");
		len = data.ps.length;
		for (i=0;i<len;i++) {
			$("#processes").append("<tr><td>" + data.ps[i].user+"</td><td>" + data.ps[i].pid + "</td><td>" + data.ps[i].runTime + "</td><td>" + data.ps[i].cmd +"</td></tr>");
		}
		setTimeout(getDisk,5000);
	}).fail(function(error) {
		errorCounter++;
		$("#logRun").html(errorCounter);
		console.log("error",error.statusText);
		$("#log").prepend("df error "+new Date()+"<br>");
		
		setTimeout(getDisk,5000);
	});
}



	function getLoadAvg() {
		a=$.ajax({url : URL + '/api/v1/loadavg', method: "GET"}).done(function(data) {
			loadCount++;
			$("#loadRun").html(loadCount);
	        $("#onemin").html("");
	        $("#onemin").append(data.loadavg.OneMinAvg);
	        $("#fivemin").html("");
	        $("#fivemin").append(data.loadavg.FiveMinAvg);
	        $("#fifteenmin").html("");
	        $("#fifteenmin").append(data.loadavg.FifteenMinAvg);
	        $("#numRunning").html("");
	        $("#numRunning").append(data.loadavg.NumRunning);
	        $("#ttlProc").html("");
	        $("#ttlProc").append(data.loadavg.TtlProcesses);
	        setTimeout(getLoadAvg, 5000);   
		}).fail(function(error) {
				errorCount++;
				$("#logRun").html(errorCount);
				$("#log").prepend("loadavg error "+ new Date() +"<br>");
		    	setTimeout(getLoadAvg,5000);
		});
	}

	function getNetwork() {
        var date = new Date();
        var time = date.getTime();
        a = $.ajax({url: URL + '/vi/api/network',method: "GET"}).done(function(data){
        networkCount++;
        d = new Date();
        t = d.getTime();
        diff = (t - time)/1000;

        tByteDiff = Math.abs(parseInt(data.network.txbytes) - preTXBytes);
        rByteDiff = Math.abs(parseInt(data.network.rxbytes) - preRXBytes);
        preTXBytes = data.network.txbytes;
        preRXBytes = data.network.rxbytes;
     
        $("#networkRun").html(networkCount);
        $("#txbytes").html("");
        $("#rxbytes").html("");
        $("#txbytes").append(data.network.txbytes);
        $("#rxbytes").append(data.network.rxbytes);
        $("#txavg").html("");
        $("#rxavg").html("");
        $("#txavg").append(Math.round(tByteDiff/diff));
        $("#rxavg").append(Math.round(rByteDiff/diff));
        setTimeout(getNetwork, 5000);
		}).fail(function(error) {
        	errorCount++;
			$("#logRun").html(errorCount);
			$("#log").prepend("network error " + new Date() + "<br>");
			setTimeout(getNetwork, 5000);
		});
	}

