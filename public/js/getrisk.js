/**
 * getrisk.js
 * by Ted Morin
 *
 * manages the getrisk page
 *
 */

/**
 * a function to add another CUI to the form
 */
function addCUI(CUI) {
    if (riskfactors[CUI] == null) {   // this should NEVER happen
        // process CUI data into a form element
        var new_tr = '<tr id = "'+CUI+'row">';
        // removal button
        new_tr +=    '    <td style= "text-align:center;">';
        new_tr +=    '        <button id = "remove' + CUI + '" >-</button>'; //TODO
        new_tr +=    '    </td>';
        // CUI displayed w/link
        new_tr +=    '    <td style= "text-align:center;">';
        new_tr +=    '        <a href="riskfactor.php?CUI=' + CUI + '" >'+CUI+'</a>';
        new_tr +=    '    </td>';
        new_tr +=    '    <td style= "text-align:center;">No Data</td>';
        $('#riskfactors tr:last').after(new_tr);
    }
    else {
        // process CUI data into a form element
        var new_tr = '<tr id = "'+CUI+'row">';
        // removal button
        new_tr +=    '    <td style= "text-align:center;">';
        new_tr +=    '        <button id = "remove' + CUI + '" onclick=hideCUI("'+CUI+'") >-</button>'; //TODO
        new_tr +=    '    </td>';
        // name of risk factor CUI displayed w/link
        new_tr +=    '    <td style= "text-align:center;">';
        var riskname = toTitleCase(riskfactors[CUI]['name']);
        new_tr +=    '        <a href="CUIquery.php?CUI=' + CUI + '" >'+riskname+'</a>'; // TODO link destination
        new_tr +=    '    </td>';
        
        var inputdata = "";
        var units = "";
        // interpret the datatype and units
        if (CUI == 'C28421') { // Sex
            inputdata = 'type="radio" value="male" checked> Male</input>  <input type="radio" name="'+CUI+'" value="female"> Female<p></p';
        } else if (riskfactors[CUI]['datatype'].toLowerCase() == 'float') {
            inputdata = 'type="number" placeholder="Float" style="width:50px"';
            units += riskfactors[CUI]['units'];
        } else if (riskfactors[CUI]['datatype'].toLowerCase() == 'int' || riskfactors[CUI]['datatype'].toLowerCase() == 'integer') {
            inputdata = 'type="number" placeholder="Integer" style="width:50px';
            units += riskfactors[CUI]['units'];
        } else /*if (riskfactors[CUI]['datatype'].toUpperCase() == 'BOOL')*/ {
            inputdata = 'type = "checkbox" ';
        } 
        new_tr +=    '    <td style= "text-align:center;">';
        new_tr +=    '        <input name = "' + CUI + '" ' + inputdata + ' ></input> ';
        new_tr +=    '    </td>';
        new_tr +=    '    <td style= "text-align:center;">' + units + '</td>';
        new_tr +=    '</tr>';
        // insert the new row
        $.when($,$('#riskfactors tr:last').after(new_tr)).done( function() {
            // afterwards, put the default value on the input
            //$('#riskfactors [name='+CUI+']').val(100);           // val(riskfactors[CUI]['defaultvalue'] TODO
        });
    }
}

/**
 * a function to build the risk form
 */
function makeriskform() {
    // inserts the base form
    var base_form = '<table class="table-condensed" class="riskfactortable" style="margin-left:auto; margin-right:auto" >\n';
    base_form +=    '    <tr>\n';
    base_form +=    '        <td class="columntitle" ><b></b></td>\n';
    //base_form +=    '        <td class="columntitle" ><b>CUI</b></td>\n';
    base_form +=    '        <td class="columntitle" ><b>Risk Factor</b></td>\n';
    base_form +=    '        <td class="columntitle" ><b>Value</b></td>\n';
    base_form +=    '        <td class="columntitle" ><b>Units</b></td>\n';
    base_form +=    '        <td class="columntitle" ><b></b></td>\n';
    base_form +=    '    </tr>\n';
    base_form +=    '</table>\n' ;
    $('#riskfactors').html( base_form );
    
    // iterates through the keys
    var CUIs = Object.keys(riskfactors);
    var togetCUIs = [];  // CUIs which must be requested
    var doneCUIs = [];   // CUIs in the form already
    for (var i in CUIs) {
        var thisCUI = CUIs[i];
        if (riskfactors[thisCUI] == null) {
            // get data, then add to table
            togetCUIs.push(
                // this ajax request should be replaced by a new defered object class. May happen a lot...
                $.ajax({
                    url : "CUIquery.php", 
                    data : {"CUI":thisCUI},
                    success: function(reply) {
                        var thisdata = JSON.parse(reply);
                        var thisCUI = Object.keys(thisdata)[0];
                        riskfactors[thisCUI] = thisdata[thisCUI];
                    }
                })
            );
        }
        else {
            addCUI(thisCUI);
            doneCUIs.push(thisCUI);
        }
    }
    $.when.apply($, togetCUIs ).done( function() { // should get models on success or failure of the ajax requests
        var CUIs = Object.keys(riskfactors);
        for (i in CUIs) {
            var thisCUI = CUIs[i];
            if (doneCUIs.indexOf(thisCUI) == -1) { // CUI is not already in the 'done' list
                if (riskfactors[thisCUI] != null) {  // CUI data was actually added
                    addCUI(thisCUI);
                    console.log('fetched a good CUI: '+thisCUI);
                }
                else {
                    delete riskfactors[thisCUI];
                    badCUIs.push(thisCUI); 
                    console.log('fetched a bad CUI: '+thisCUI);// throw some kind of warning?
                }
            }
        }
        makemodeldiv();
    });
}

/**
 * a function to remove a CUI from the form
 */
function hideCUI(CUI) {
    hiddenCUIs[CUI] = riskfactors[CUI];
    delete riskfactors[CUI];
    makeriskform();
}

/**
 * a function to build the models div
 */
function makemodeldiv() {
    var riskfactorsbyCUI = getriskfactorsbyCUI();
    // ajax request to modelquery.php
    $.ajax({
        url : "modelquery.php", 
        data : riskfactorsbyCUI,
        // build the model table on reply
        success: function(reply) {
            // inserts the base of the div
            var base_form = '<table class="table-condensed" class="riskmodeltable" style="margin-left:auto; margin-right:auto" >\n';
            base_form +=    '    <tr>\n';
            //base_form +=    '        <td class="columntitle" ><b>DOI</b></td>\n';
            //base_form +=    '        <td class="columntitle" ><b>Paper</b></td>\n';
            base_form +=    '        <td class="columntitle" ><b>Model</b></td>\n';
            base_form +=    '        <td class="columntitle" ><b></b></td>\n';
            base_form +=    '        <td class="columntitle" ><b>Score</b></td>\n';
            base_form +=    '    </tr>\n';
            base_form +=    '</table>\n' ;
            $('#models').html(base_form);
            
            // inserts a row for each model
            var models = JSON.parse(reply);
            for (var i in models) {
                var model = models[i];
                // 
                var new_tr = '<tr id = "' + model['DOI'] + '" title="' + model['modeltitle']+'">';
                new_tr +=    '  <td style= "text-align:center;">';
                new_tr +=    '    <div>';
                new_tr +=    '      <a href="scorequery.php?id='+model['id']+'" >' + model['outcometime'].toString() + 'Y Risk of '+model['outcome']+'</a>';
                new_tr +=    '<br>from ' + model['authors'][0] + " et al's " + model['yearofpub'] +' Paper';
                new_tr +=    '    </div>';
                new_tr +=    '  </td>';
                new_tr +=    '  <td style= "text-align:center;">';
                new_tr +=    '    <button id = "calc' + model['id'] + '" onclick="getscore('+model['id']+')" > = </button>'; // calculation TODO
                new_tr +=    '  </td>';
                new_tr +=    '  <td style= "text-align:center;" id="score'+model['id']+'"></td>'; // score
                new_tr +=    '</tr>';
                $('#models tr:last').after(new_tr);
            }
        }
    });
}

/**
 * 
 */
function getscore(id) {
    var scoringdata = getriskfactorsbyCUI();
    scoringdata['id'] = id;
    $.ajax({
        url : 'scorequery.php',
        data : scoringdata,
        success : function(reply) {
            var data = JSON.parse(reply);
            var score = (parseFloat(data['score']) == NaN) ? data['score'] : parseFloat(data['score']) ; // NaN untested
            if (typeof(score) == "number") {
                score = (Math.round(score*1000)/10).toString() + "%";
            }
            var id = data['id'];
            $('#score'+id).html(score);
        }
    });
}

/**
 * on ready
 */
$(document).ready( function () {
    makeriskform();
    
    // prevent form submission on pressing enter (courtesy of Phil Carter and Simon on stackoverflow)
    $(window).keydown(function(event){
        if( (event.keyCode == 13) ){ //&& (true == false) ) { // change true to a validation system?
            event.preventDefault();
            return false;
        }
    });

    
    $('#riskfactors').change(function(){
        // clear scores? check against upper and lower bounds?
    });
    
    
    $('#newriskfactor').change( function(){
        // check if CUI is known to be invalid
        newCUI = $('#newriskfactor [name=search]').val() ;
        newCUI = newCUI.replace(/\s+/g, ''); // remove whitespace
        if (badCUIs.indexOf(newCUI) != -1) {        
            console.log("search has a known bad CUI" + newCUI);
        }
        // check if Name was changed to a valid name
        
        // 
    });
    
    $('#newriskfactor').submit( function(e) { // still only searches by CUI!
        e.preventDefault();
        // get the data
        var newCUI = $('#newriskfactor [name=search]').val() ;
        newCUI = newCUI.replace(/\s+/g, ''); // remove whitespace
        // blank CUI
        if (newCUI == "") {
            alert("Search field is empty!");           
            console.log("attempted a blank CUI");
            
        // if the CUI is known to be bad...
        } else if (badCUIs.indexOf(newCUI) != -1) {        
            console.log("attempted a bad CUI: " + newCUI);
        
        // if they already have this risk factor
        } else if (Object.keys(riskfactors).indexOf(newCUI) != -1) {
            console.log("attempted a repeat CUI: " + newCUI);
            
        } else {
            // add new risk factor    
            riskfactors[newCUI] = null;
            makeriskform();
        }
    });
    
    $('#newmodel').submit( function(e) {
        e.preventDefault();
        // get the data
        var newmodel = $('#newmodel [name=search]').val() ;
        newmodel = newmodel.replace(/\s+/g, ''); // remove whitespace
        $.ajax({
            url:"modelquery.php",
            data:{
                'byDOI':true,
                'DOI':newmodel,
                'byID':true,
                'ID':newmodel
            }, 
            success:function(reply){
                var newmodels = JSON.parse(reply);
                if (newmodels.length == 0) {
                    alert('Sorry, no models fit that DOI or ID!');
                }
                else {
                    var theseCUIs = [];
                    for (var i in newmodels) {
                        var inputCUIs = newmodels[i]['inpCUI'];
                        for (var j in inputCUIs) {
                            theseCUIs.push(inputCUIs[j]);
                        }
                    }
                    riskfactors = [];
                    for (var i in theseCUIs) {
                        riskfactors[theseCUIs[i]] = null;
                    }
                }
                makeriskform();
                console.log(reply);
            }
        });
        // blank model
        /*if (newmodel == "") {
            alert("Search field is empty!");           
            console.log("attempted a blank search.");
            
        // if the model is known to be bad...
        } else if (badmodels.indexOf(newmodel) != -1) {        
            console.log("attempted a bad model: " + newmodel);
        
        // if they already have this risk factor
        } else if (Object.keys(riskfactors).indexOf(newmodel) != -1) {
            console.log("attempted a repeat model: " + newmodel);
            
        } else {
            // add new risk factor    
            riskfactors[newmodel] = null;
            makeriskform();
        }*/
    });
    
});
