$(document).ready(function(){
    // Show the first tab content by default
    $('.tab-content').first().addClass('active');

    // Initialize Lottie animation
    var confettiAnimation = lottie.loadAnimation({
        container: document.getElementById('lottie-container'),
        renderer: 'svg',
        loop: false,
        autoplay: false,
        path: 'confetti.json' // Path to the Lottie JSON file
    });

    // Tab link click handler
    $('.tab-link').click(function() {
        // Remove active class from all tabs and contents
        $('.tab-link').removeClass('active');
        $('.tab-content').removeClass('active');

        // Add active class to the clicked tab and corresponding content
        var tabId = $(this).attr('data-tab');
        $(this).addClass('active');
        $('#' + tabId).addClass('active');

        // Play confetti animation
        confettiAnimation.goToAndPlay(0, true); // Restart the animation from the beginning
    });
});
