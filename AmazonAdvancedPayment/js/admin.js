/*!
  * --------------------------------------------------------------
  * Amazon Advanced Payment APIs Modul  V2.00
  * admin.js 2014-06-03
  *
  * alkim media
  * http://www.alkim.de
  *
  * patworx multimedia GmbH
  * http://www.patworx.de/
  *
  * Released under the GNU General Public License
  * --------------------------------------------------------------
*/
var blockHistoryReload;
var blockActionReload;
var ajaxHandler;
var lastHistory;
var lastActions;
var lastSummary;
$(document).ready(function(){
    ajaxHandler = $('.amzAjaxHandler').val();
    $('.amzAdminWr').each(function(){
        amzReloadOrder($(this));
        amzReloadLoop($(this));
    });

});

$(document).on('click', '.amzAjaxLink', function(e){
    e.preventDefault();
    var action = $(this).attr('data-action');
    var authId = $(this).attr('data-authid');
    var captureId = $(this).attr('data-captureid');
    var orderRef = $(this).attr('data-orderRef');
    var amount = $(this).attr('data-amount');
    if(action == 'captureAmountFromAuth'){
        var amount = parseFloat($(this).parent().find('.amzAmountField').val().replace(',', '.'));
    }else if(action == 'refundAmountFromField'){
        var amount = parseFloat($(this).parent().find('.amzAmountField').val().replace(',', '.'));
        action = 'refundAmount';
    }
    else if(action == 'authorizeAmountFromField'){
        var amount = parseFloat($(this).parent().find('.amzAmountField').val().replace(',', '.'));
        action = 'authorizeAmount';
    }

    $.post(ajaxHandler, {action:action, authId:authId, amount:amount, orderRef:orderRef, captureId:captureId}, function(data){
        var responseDiv = $('<div style="display:none;"/>').html(data);
        $('body').append(responseDiv);
        responseDiv.dialog();
        amzRefresh();

    });

});
function amzReloadLoop(wr){
    setTimeout(function(){amzReloadOrder(wr); amzReloadLoop(wr);}, 5000);
}
function amzReloadOrder(wr){
    var orderRef = wr.attr('data-orderRef');
    amzReloadHistory(orderRef, wr.find('.amzAdminOrderHistory'));
    amzReloadActions(orderRef, wr.find('.amzAdminOrderActions'));
    amzReloadSummary(orderRef, wr.find('.amzAdminOrderSummary'));
}

function amzReloadHistory(orderRef, target){
    $.post(ajaxHandler, {action:'getHistory', orderRef:orderRef}, function(data){
        if(lastHistory != data){
            target.html(data);
            lastHistory = data;
        }
        target.closest('.amzAdminWr').css('opacity', 1);
    });
}

function amzReloadActions(orderRef, target){
    $.post(ajaxHandler, {action:'getActions', orderRef:orderRef}, function(data){
        if(lastActions != data){
            target.html(data);
            lastActions = data;
        }
        target.closest('.amzAdminWr').css('opacity', 1);
    });
}
function amzReloadSummary(orderRef, target){
    $.post(ajaxHandler, {action:'getSummary', orderRef:orderRef}, function(data){
        if(lastSummary != data){
            target.html(data);
            lastSummary = data;
        }
        target.closest('.amzAdminWr').css('opacity', 1);
    });
}

function amzRefresh(){
    $('.amzAdminWr').each(function(){
        $(this).css('opacity', 0.6);
        amzReloadOrder($(this));
    });
}
