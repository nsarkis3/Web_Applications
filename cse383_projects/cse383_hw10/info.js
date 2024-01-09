// Function that displays value
var URL="https://api.clearllc.com/api/v2/math/";
var baseURL="https://api.clearllc.com/api/v2/math/";
var num1 = 0;
var num2 = 0;
var opperation = '';

function dis(val) {
    if(val == '+' || val == '-' || val == '*' || val == '/') {
        opperation = val;
        num1 = $('#result').val();
        $('#result').val("");   
        console.log("Display operaton: " + opperation);     
    } else { $('#result').val($('#result').val() + val); }
    
}

//API call for all oppeartions (+,-,*,/)
function solve() {
    console.log("Solve operaton: " + opperation);    
    num2 = $('#result').val();
    if(opperation == '+') {URL += "Add?api_key=bed859b37ac6f1dd59387829a18db84c22ac99c09ee0f5fb99cb708364858818&n1="+num1+"&n2="+num2;} 
    else if (opperation == '-') {URL += "Subtract?api_key=bed859b37ac6f1dd59387829a18db84c22ac99c09ee0f5fb99cb708364858818&n1="+num1+"&n2="+num2;} 
    else if (opperation == '/') {URL += "Divide?api_key=bed859b37ac6f1dd59387829a18db84c22ac99c09ee0f5fb99cb708364858818&n1="+num1+"&n2="+num2;}
    else if (opperation == '*') {URL += "Multiply?api_key=bed859b37ac6f1dd59387829a18db84c22ac99c09ee0f5fb99cb708364858818&n1="+num1+"&n2="+num2;} 

    //ajax code
    a=$.ajax({
        url: URL,
        method: "GET"
    }).done(function(data) {
        $('#result').val(data.result);
        num1 = data.result;
        opperation = '';
        URL = baseURL;
    }).fail(function(error) {
        console.log("error",error.statusText);      
    });

}

// resets all alues in the calculator for the next use
function clr() {
    $('#result').val("") ; 
    num1 = 0;
    num2 = 0;
    opperation = '';
}
