/**
 * jQuery
 */

$(document).ready(function () {
    var clock = 0;

    var update = function update(){

        clearTimeout(clock);
        var curTime = new Date();
        // console.log(curTime);
        // var curYear = curTime.getFullYear();
        // var curMon  = curTime.getMonth();
        // console.log(curMon);
        // var curDay  = curTime.getDate();
        // console.log(curDay);
        // var curHour = curTime.getHours() < 10 ? '0' + curTime.getHours() : curTime.getHours();
        // var curMin  = curTime.getMinutes() < 10 ? '0' + curTime.getMinutes() : curTime.getMinutes();
        // var curSec  = curTime.getSeconds() < 10 ? '0' + curTime.getSeconds() : curTime.getSeconds();
        //
        // var dateStr = curDay+'.'+curMon+'.'+curYear;
        // var timeStr = curHour+':'+curMin+':'+curSec;

        $('#today').html(curTime.toLocaleDateString('de-DE', {day: '2-digit', month: '2-digit', year: 'numeric'}));
        $('#clock').html(curTime.toLocaleTimeString('de-DE', {hour: '2-digit', minute: '2-digit'}));
        clock = setTimeout(update,1000);
    };
    clock = setTimeout(update,1000);
});