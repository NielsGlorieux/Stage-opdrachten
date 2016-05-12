<!DOCTYPE html>
<html>
    <head>
        <title>Time calculator</title>
        <!--<script type="text/javascript" src="https://getfirebug.com/firebug-lite.js"></script>-->
        <link href="css/calc.css" rel="stylesheet">
        <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet">
        <script src="js/calc.js"></script>
        <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script> 
        <script type="text/javascript" src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/js/bootstrap.min.js"></script>    
        <script type="text/javascript" src="http://www.jeasyui.com/easyui/jquery.easyui.min.js"></script>     
        <script src="js/jquery.maskedinput.min.js" type="text/javascript"></script>
    </head>
    <body>          
        <div class="container">
            <div class="content">
                <div class="title"><h1>Time calculator</h1></div>  
            </div>
            <table border="4" class='content'>
                <tbody>
                <tr>
                    <td align="center" colspan="2"><b>Time Tracker</b></td>
                </tr>
                <tr>
                <!-- TIME STATISTICS -->
                    <td >
                        <font size="2">min: </font><input disabled type="text" name="timeMin" id="timeMin" size="10" ><br>
                        <font size="2">max: </font><input disabled type="text" name="timeMax" id="timeMax" size="10" ><br>
                        <font size="2">avg: </font><input disabled type="text" name="timeAvg" id="timeAvg" size="10" ><br>
                    </td>
                <!-- TIME TOTAL -->
                    <td align="center">
                        <font size="2"><b>total time:</b></font><br>
                        <input disabled type="text" name="timeTotal" id="total" size="10"><br>
                        <b id='aanduiding'>hr : min : sec</b>
                    </td>
                </tr>
                <tr>
                    <!-- TIME -->
                    <td align="center">
                        <p><b id='typeAanduiding'>hr : min : sec</b>
                        </p>
                    </td>
                    <!-- CUMULATIVE TIME -->
                    <td align="center">
                        <b><p>cumulative<br>time</p></b>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <table id="tableData" name="tableData" border="0" cellspacing="0" cellpadding="0">
                            <tr class="inputList">
                                <td align="center" class="comment">0.</td>
                                <td align="center">
                                    <input onkeypress='return event.charCode >= 48 && event.charCode <= 57' class='inputs' type="text" step="1" name="time0" id="time0" size="10" onchange="changeValue(0); cumulValue(0)">  
                                    <script>
                                    jQuery(function($){
                                        var maskFormat = document.querySelector('input[name = "inputFormat"]:checked').id;
                                        var maskPlace = document.querySelector('input[name = "inputFormat"]:checked').value;
                                        $("#time0").mask(maskFormat, {placeholder:maskPlace});
                                        });
                                    </script>
                                </td>
                            </tr>
                            <script type="text/javascript">
                                var teller = 1;                                                           
                            
                                $('body').on('keydown', '#tableData tr:last' , function(i){
                                        $('#tableData').append($("<tr class='inputList'><td align='center' class='comment'>" + teller + ".</td><td align='center'><input onkeypress='return event.charCode >= 48 && event.charCode <= 57' class='inputs' type='text'>").val($('#input_listName').val()));
                                                    
                                        $('.inputList input').attr('id', function(i) {
                                            return 'time'+(i);
                                        });
                                        
                                        $('.inputList input').attr('onchange', function(i) {
                                        
                                            return 'changeValue('+(i)+'); cumulValue('+ (i) +')';
                                        });
                                        $('.inputList').val($('#input_listName').val());
                                        var maskFormat = document.querySelector('input[name = "inputFormat"]:checked').id;
                                        var maskPlace = document.querySelector('input[name = "inputFormat"]:checked').value;
                                        $('#time'+teller).mask(maskFormat, {placeholder:maskPlace});
                                        teller += 1; 
                                });
                            </script> 
                        </table>
                    </td>
                    <td id='cumul' align="center">
                    <!-- CUMULATIVE TIME -->
                        <table id="tableCumul" name="tableCumul" border="0" cellspacing="0" cellpadding="0">
                            <tr class='cumulItem'><td><input disabled type="text" id="cumtime0" size="10"></td></tr>
                        </table>         
                    </td>
                    <script type="text/javascript">
                        $('body').on('keydown','#tableData tr:last',function(i){
                                $('#tableCumul').append($("<tr class='cumulItem'><td><input disabled type='text'></td>").val($('#input_listName').val()));
                                                        
                                $('#tableCumul input').attr('id', function(i) {
                                    return 'cumtime'+(i);
                                });
                    
                        });
                    </script> 
                </tr>
            </table>
        <button id='btnClear' tabindex='-1' value="" onclick="clearFields()">Clear fields</button>    
        <br>
        <h2>Rekenen</h2>
        <div class='rekenen'>
        <input onkeypress='return event.charCode >= 48 && event.charCode <= 57'  id="val1" class="add" type="text" step="1" size="10"  >
        <input onkeypress='return event.charCode >= 48 && event.charCode <= 57'  id="val2" class="add" type="text" step="1" size="10"  >
           <script>
                jQuery(function($){    
                    $("#val1, #val2").mask("99:99:99",{placeholder:"hh:mm:ss"});
                });
           </script>
           <br>
        <input type="button" value="+" onClick="add()" />
        <input type="button" value="-" onClick="af()" />
        <br>
        <label name='outputPlus' id='outputPlus'></label>  
        <br>
        <input onkeypress='return event.charCode >= 48 && event.charCode <= 57'  id="val3" class="add" type="text" step="1" size="10"  >
        <input onkeypress='return event.charCode >= 48 && event.charCode <= 57'  id="val4" class="add" type="text" step="1" size="10" placeholder='factor' >
        <script>
            jQuery(function($){  
                $("#val3").mask("99:99:99",{placeholder:"hh:mm:ss"});
            });
        </script>
        <br>
        <input type="button" value="*" onClick="maal()" />
        <input type="button" value="/" onClick="devide()" />
        <label name='outputMaal' id='outputMaal'></label>
         <br>
    
    </div> 
    <div>   
        <form id='choiceFormat'>
            <h3>Formatting</h3>
            <p>
                <label for="outputhms"><b>hr:min:sec</b></label>
                <input type="radio" name="outputFormat" id="outputhms" value="hh:mm:ss" onclick="changeValue()" checked="">
            </p>
            <p>
                <label for="outhm"><b>hr:min</b></label> 
                <input type="radio" name="outputFormat" id="outhm" value="hh:mm" onclick="changeValue()">
                <label for="outms"><b>min:sec</b></label>
                <input type="radio" name="outputFormat" id="outms" value="mm:ss" onclick="changeValue()">
            </p>
            <p>
                <label for="outh"><b>hr</b></label>
                <input type="radio" name="outputFormat" id="outh" value="hh" onclick="changeValue()">
                <label for="outm"><b>min</b></label>
                <input type="radio" name="outputFormat" id="outm" value="mm" onclick="changeValue()">
                <label for="outs"><b>sec</b></label>
                <input type="radio" name="outputFormat" id="outs" value="ss" onclick="changeValue()">
            </p>  
        </from>
    </div>
    <div id='choiceTyping'>
        <form id='typing' >
            <h3>Typing</h3>
            <p><label for="">
                <input type="radio" name="inputFormat" id="99:99:99" value='hh:mm:ss' checked=""  >
                <b>hr:min:sec</b>
                </label>
            </p>
            <p><label for="">
                <input type="radio" name="inputFormat" id="99:99" value='hh:mm'>
                <b>hr:min</b>
                </label> 
                <label for="">
                <input type="radio" name="inputFormat" id="99:99" value='mm:ss'>
                <b>min:sec</b>
                </label>
            </p>
            <p>
                <label for="">
                <input type="radio" name="inputFormat" id="99" value='hh'>
                <b>hr</b>
                </label>
                <label for="">
                <input type="radio" name="inputFormat" id="99" value='mm'>
                <b>min</b>
                </label>
                <label for="">
                <input type="radio" name="inputFormat" id="99" value='ss'>
                <b>sec</b>
                </label>
            </p>
        </from>

    </div>
        
    <script>
        var previous;
        $("input[name=inputFormat]").on("mousedown", function() {
            previous =  $('input[name=inputFormat]:checked').val();
            
        });

        jQuery(function($){
                $('input[name=inputFormat]').change(function() {  
                    var maskFormat = document.querySelector('input[name = "inputFormat"]:checked').id;
                    var maskPlace = document.querySelector('input[name = "inputFormat"]:checked').value;                                    
                    var cols = document.getElementById('tableData').getElementsByTagName('input'),
                    colslen = cols.length,
                    i = -1;
                    var maskPlace = document.querySelector('input[name = "inputFormat"]:checked').value;
                    while (++i < colslen) {
                        if (typeof cols[i] !== "undefined" && cols[i].value != '') {
                            var noMarkupSecs = hourConverter(previous, maskPlace, cols[i].value);
                            splt = noMarkupSecs.split(':');      
                            var negens1='', negens2='', negens3='';             
                            if(typeof splt[0] !== 'undefined'){
                                var eersteNegens = splt[0].length;
                                for(var x = 0; x < eersteNegens; x++){
                                    negens1 += '9';
                                }
                            }   
                            if(typeof splt[1] !== 'undefined'){   
                                var tweedeNegens = splt[1].length;
                                for(var y = 0; y < tweedeNegens; y++){
                                    negens2 += '9';
                                }
                            }        
                            if(typeof splt[2] !== 'undefined'){
                            
                                var derdeNegens = splt[2].length;
                                for(var z = 0; z < derdeNegens; z++){
                                    negens3 += '9';
                                }
                            }
                            if(negens3 != ''){
                                negens2 += ':'
                            }                          
                            if(negens2 != ''){
                                negens1 += ':'
                            }

                            specialFormat = negens1 + negens2 + negens3;
                            $("#time" + i).mask(specialFormat, {placeholder:'hh:mm:ss'});
                            console.log("#time" + i);
                            document.getElementById("time" + i).value = noMarkupSecs;       
                        }
                    }    
                    $(':text.inputs[value=""]').mask(maskFormat, {placeholder:maskPlace});
                });
        });
    </script>

</div> 

</body>
</html>