'use strict';

function carRemaining(target, value, max){

  let input = parseInt(value.length);
  let remain = max - input;

  let result =  `${remain} characters remaining`;
  let tag = $(`#${target}`);

  tag.css({
    fontWeight: 'bold', 
    fontSize: '14px', 
    display: 'block', 
    textAlign: 'end',
  });

  if(remain <= 20){
    tag.css('color', 'red');
  }else if(remain <= 50){
      tag.css('color', 'green');
  }else{
    tag.css('color', 'black');
  }

  tag.text(result);
}

let DURATION_IN_SECONDS = {

  epochs: ['year', 'month', 'day', 'hour', 'minute'],
  year: 31536000,
  month: 2592000,
  day: 86400,
  hour: 3600,
  minute: 60

};

function getDuration(seconds) {
    var epoch, interval;
    for (var i = 0; i < DURATION_IN_SECONDS.epochs.length; i++) {
        epoch = DURATION_IN_SECONDS.epochs[i];
        interval = Math.floor(seconds / DURATION_IN_SECONDS[epoch]);
        if (interval >= 1) {
        return {
            interval: interval,
            epoch: epoch
        };
        }
    }
};

function timeSince(date) {
  var seconds = Math.floor((new Date() - new Date(date)) / 1000);
  var duration = getDuration(seconds);
  var suffix = (duration.interval > 1 || duration.interval === 0) ? 's' : '';
  return duration.interval + ' ' + duration.epoch + suffix;
};


function showPassword(inputId, className) {

  let input = document.getElementById(inputId);
  let icon = document.getElementsByClassName(className);

  if(input.type === "password"){
    input.type = "text";
    $(icon).removeClass('fa fa-eye').addClass('fa fa-eye-slash');
  }else{
    input.type = "password";
    $(icon).removeClass('fa fa-eye-slash').addClass('fa fa-eye');
  }

}