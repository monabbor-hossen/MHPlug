document.addEventListener('DOMContentLoaded', function() {
    var btnYes = document.getElementById('mh-age-gate-yes');
    var btnNo = document.getElementById('mh-age-gate-no');
    var overlay = document.getElementById('mh-age-gate-overlay');

    if (btnYes) {
        btnYes.addEventListener('click', function() {
            var date = new Date();
            date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toUTCString();
            document.cookie = "mh_plug_age_verified=true" + expires + "; path=/";
            
            if (overlay) {
                overlay.style.display = 'none';
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
