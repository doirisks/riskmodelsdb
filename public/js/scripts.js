/**
 * scripts.js
 *
 * global DOI Risk Web Interface
 */

/**
 * function to get data on a CUI
 *
 * calls the onsuccessfunction with the CUI as a parameter 
 * USELESS UNTIL THERE IS A CLASS FOR WHEN.DONE() TODO
 */

/*function getCUIdata(CUI,onsuccess) {
    // ajax request to CUIquery.php
    $.ajax({
        url : "CUIquery.php", 
        data : {"CUI":CUI},
        success: function(reply) {
            var thisdata = JSON.parse(reply);
            var thisCUI = Object.keys(thisdata)[0];
            riskfactors[thisCUI] = thisdata[thisCUI];
            addCUI(thisCUI);
        }
    });
}*/

// toTitleCase(str) courtesy of Greg Dean on stackoverflow
function toTitleCase(str)
{ //
    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
}

/**
 * function to request an individual risk score
 */

function getriskscore (id, oncomplete) {
}

/**
 * function to request applicable models
 *
 * Assumes existence of:
 * 1) form with id "riskfactors" and inputs for all risk factors, named by CUI.
 * 2) div with id "models"
 */
function getmodels (onsuccess) {
    // get CUI values from form
    var riskfactorsbyCUI = {};
    $("#riskfactors").find(":input").each(function() {
        if (this.type == 'radio'){
            if (this.checked == true) {
                riskfactorsbyCUI[this.name] = $(this).val();
            }
        } else if (this.type == 'checkbox') {
            if (this.checked == true) {
                riskfactorsbyCUI[this.name] = true
            }
            else {
                riskfactorsbyCUI[this.name] = false
            }
        } else { //float entry
            if ($(this).val() == null) {
                riskfactorsbyCUI[this.name] = 0;
            }
            else {
                riskfactorsbyCUI[this.name] = $(this).val();
            }
        }
    });
    // ajax request to modelquery.php
    $.ajax({
        url : "modelquery.php", 
        data : riskfactorsbyCUI,
        success: function(reply) {
            onsuccess(reply);
        }
    });
}

/**
 * a function to get the riskfactors by CUI from the riskfactors form
 */

function getriskfactorsbyCUI() {
    // get CUI values from form
    var riskfactorsbyCUI = {};
    $("#riskfactors").find(":input").each(function() {
        if (this.name != "") {
            if (this.type == 'radio'){
                if (this.checked == true) {
                    riskfactorsbyCUI[this.name] = $(this).val();
                }
            } else if (this.type == 'checkbox') {
                if (this.checked == true) {
                    riskfactorsbyCUI[this.name] = true
                }
                else {
                    riskfactorsbyCUI[this.name] = false
                }
            } else { //float entry
                if ($(this).val() == null) {
                    riskfactorsbyCUI[this.name] = 0;
                }
                else {
                    riskfactorsbyCUI[this.name] = $(this).val();
                }
            }
        }
    });
    return riskfactorsbyCUI;
}
