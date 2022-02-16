define(['jquery'], function($){
    return function(config, element){
        var timer = $(element);
        // refresh every 1 second the timer
        var x = setInterval(function() {
            if(window.countDownDate){
                var countDownDate = window.countDownDate || undefined;
                var countDownDate = new Date(countDownDate["y"],countDownDate["m"]-1,countDownDate["d"]).getTime() || undefined;
                // this will give you current date and time
                var now = new Date().getTime();

                // calculate how much time from now on the count down date
                var distance = countDownDate - now;

                // Time in days, hours, minutes and seconds
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // The result goes to the element"
                timer.html(days + "d : " + hours + "h : " + minutes + "m : " + seconds + "s");
                timer.show();
            }
            $('.sidewide-message').show(); // Show the countdown timer

            // If the count down is over, write some text
            //        if (distance < 0) {
            //            clearInterval(x);
            //            timer.html("EXPIRED");
            //        }
        }, 1000);
    }
});
