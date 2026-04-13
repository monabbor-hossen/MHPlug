document.addEventListener('DOMContentLoaded', function() {
    var btnYes = document.getElementById('mh-age-gate-yes');
    var btnNo  = document.getElementById('mh-age-gate-no');
    var overlay = document.getElementById('mh-age-gate-overlay');

    if (btnYes) {
        btnYes.addEventListener('click', function() {
            var cookieDays = 30; // default
            if (typeof mhAgeGate !== 'undefined' && mhAgeGate.cookieDays) {
                cookieDays = parseInt(mhAgeGate.cookieDays, 10);
            }
            
            var date = new Date();
            date.setTime(date.getTime() + (cookieDays * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toUTCString();
            document.cookie = "mh_plug_age_verified=true" + expires + "; path=/";
            
            if (overlay) {
                // Fade out softly
                overlay.style.transition = 'opacity 0.4s ease';
                overlay.style.opacity = '0';
                setTimeout(function() {
                    overlay.style.display = 'none';
                }, 400);
            }
        });
    }

    if (btnNo) {
        btnNo.addEventListener('click', function() {
            if (typeof mhAgeGate !== 'undefined' && mhAgeGate.redirectUrl) {
                window.location.href = mhAgeGate.redirectUrl;
            } else {
                // Fallback action if no redirect url is provided
                alert("You are not old enough to view this content.");
                window.history.back();
            }
        });
    }
});
