onload=changeValue;
function changeValue(sourceId) {
  
  //Aanduiding
    var idee = document.querySelector('input[name = "outputFormat"]:checked').id;
    var selector = 'label[for=' + idee + ']';
    
    var input = document.querySelector(selector).innerHTML;
    document.getElementById('aanduiding').innerHTML = input;
    
    //output aanpassen aan mask keuze
    var maskPlace = document.querySelector('input[name = "inputFormat"]:checked').value;  
    //loop
    var cols = document.getElementById('tableData').getElementsByTagName('input'),
    colslen = cols.length,
    i = -1;

    var min, max, avg, count=0, sum=0, tot =0, cumu=0;
    while (++i < colslen) {
        if (typeof cols[i] !== "undefined" && cols[i].value != '') {
            var noMarkupSecs = getRightFormat(maskPlace, cols[i].value);
            var valu = convertToSecs(noMarkupSecs);
                if (!isNaN(valu)) {
                    //voor totaal
                    tot += valu;
                    //voor min
                    if (min != null) {
                        if (min > valu) {
                            min = valu;
                        }
                    } else {
                        min = valu;
                    }
                    //voor max
                    if (max != null) {
                        if (max < valu) {
                            max = valu;
                        }
                    } else {
                            max = valu;
                    }
                    //voor average  
                    count++;
                    sum += valu;
                    //cumul probleem
                    cumu = cumulValue(i);
                    document.getElementById("cumtime" + i).value = makePrettyTime(cumu);  
              }  
          }        
      }
          
      avg = sum / count;
      
      if (!isNaN(tot)) {
          document.getElementById("total").value = makePrettyTime(tot);
      }
      if (!isNaN(min)) {
          document.getElementById("timeMin").value = makePrettyTime(min);
      }
      if (!isNaN(max)) {
          document.getElementById("timeMax").value = makePrettyTime(max);
      }
      if (!isNaN(avg)) {
          document.getElementById("timeAvg").value = makePrettyTime(avg);
      }   
}

function getRightFormat(place, input) {
  
    var noMarkupSecs = input;
    switch (place) {
        case 'hh:mm:ss': noMarkupSecs = input;
            break;
        case 'hh:mm': noMarkupSecs = input + ':00';
            break;
        case 'mm:ss': noMarkupSecs = '00:' + input;
            break;
        case 'hh': noMarkupSecs = input + ':00:00';
            break;
        case 'mm': noMarkupSecs = '00:' + input + ':00';
            break;
        case 'ss': noMarkupSecs = '00:00:' + input;
            break;
        default: noMarkupSecs = input;                   
    }
    return noMarkupSecs;
}

function hourConverter(previousMask, newMask ,input) {
    
    var output;
    switch (previousMask) {
        case 'hh:mm:ss':
            var str = input.split(':');
            var hh = str[0];
            var mm = str[1];
            var ss = str[2];
            var secs = (hh * 3600) + (mm * 60) + (ss * 1);
            output = makePrettyTime(secs, true);
            console.log(output);
            break;
        case 'hh:mm': 
            var str = input.split(':');
            var hh = str[0];
            var mm = str[1];
            var secs = (hh * 3600) + (mm * 60);     
            output = makePrettyTime(secs, true);
            break;
        case 'mm:ss':  
            var str = input.split(':');
            var mm = str[0];
            var ss = str[1];
            var secs = (mm * 60) + (ss * 1);
            output = makePrettyTime(secs, true);
            break;
        case 'hh':
            var hh = input;
            var secs = (hh * 3600);
            output = makePrettyTime(secs, true);
            break;
        case 'mm':
            var mm = input;
            var secs = (mm * 60);
            output = makePrettyTime(secs, true);
            break;
        case 'ss':
            var ss = input;
            var secs = (ss * 1);       
            output = makePrettyTime(secs, true);
    
    }
    return output;
}
function cumulValue(id) {
    var cols = document.getElementById('tableData').getElementsByTagName('input'),
    i = -1;
    var maskPlace = document.querySelector('input[name = "inputFormat"]:checked').value;
    var cum=0,valu=0;
    while (++i <= id) {
        var noMarkupSecs = getRightFormat(maskPlace, cols[i].value);
        valu = convertToSecs(noMarkupSecs);
        if (!isNaN(valu)) {
            cum += valu;
        }
    }
    document.getElementById("cumtime" + id).value = makePrettyTime(cum);
    return cum;
}


function convertToSecs(old) {
    var split = old.split(":");
    var hours = split[0]; 
    var minutes = split[1]; 
    var seconds = split[2]; 
    return (hours * 3600) + (minutes * 60) + (seconds * 1);  
}

function makePrettyTime(seconden, voorConverter) {
    var millis = seconden * 1000;
    var hh = Math.floor(millis / 36e5),
        mm = Math.floor((millis % 36e5) / 6e4),
        ss = Math.floor((millis % 6e4) / 1000);  
    
    var input;
    if (voorConverter == null) {
         input = document.querySelector('input[name = "outputFormat"]:checked').value;
    }
    else {
         input = document.querySelector('input[name = "inputFormat"]:checked').value;
    }
    var t;
    switch (input) {
        case "hh:mm:ss":
            if (hh < 10) { hh = "0" + hh; } 
            if (mm < 10) { mm = "0" + mm; }
            if (ss < 10) { ss = "0" + ss; }
            t = hh + ':' + mm + ':' + ss;  
            break;
        case "hh:mm":
            if (hh < 10) { hh = "0" + hh; } 
            if (mm < 10) { mm = "0" + (mm + Math.ceil((ss/60*100))/100); }else{mm = (mm + Math.ceil((ss/60*100))/100) + '';}
            t = hh + ':' + mm;  
            break;
        case "mm:ss":
            mm = mm + (hh * 60); 
            if (ss < 10) { ss = "0" + ss; } 
            t = mm + ':' + ss;  
            break;
        case "hh":
            if (hh < 10) { hh = "0" + Math.ceil((hh + (mm/60) + (ss/3600))*10000)/10000 ; } else{ hh =  Math.ceil((hh + (mm/60) + (ss/3600))*10000)/10000 + '' ;}
            
            t = hh;
            break;
        case "mm":
            mm = Math.ceil((mm + (hh*60) + (ss/60))*100)/100 + ''; 
            t =  mm; 
            break;
        case "ss":
            ss = Math.floor((ss + (hh*3600) + (mm*60))*100)/100 + '';
            t =  ss;
            break;
        default:
            t = hh + ':' + mm + ':' + ss;  
            break;
    }   
    return t;  
}

function add() {  
    var a = document.getElementById('val1').value;
    var seca = convertToSecs(a);
    var b = document.getElementById('val2').value;
    var secb = convertToSecs(b);
    var sum = seca + secb;
    document.getElementById("outputPlus").innerHTML = makePrettyTime(sum);
}

function af() {
    var a = document.getElementById('val1').value;
    var seca = convertToSecs(a);
    var b = document.getElementById('val2').value;
    var secb = convertToSecs(b);
    var min = seca - secb;  
    document.getElementById("outputPlus").innerHTML = makePrettyTime(min); 
}

function maal() {
    var a = document.getElementById('val3').value;
    var seca = convertToSecs(a);
    var secb = document.getElementById('val4').value;
    var multi = seca * secb;
    document.getElementById("outputMaal").innerHTML = makePrettyTime(multi);
}


function devide() {
    var a = document.getElementById('val3').value;
    var seca = convertToSecs(a);
    var secb = document.getElementById('val4').value;
    var dev = seca / secb;
    document.getElementById("outputMaal").innerHTML = makePrettyTime(dev); 
}

function clearFields() {
    $('.content input[type="text"]').val('');
    $('input[type="text"]').val('');
}

