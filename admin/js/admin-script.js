function changePagination(e) {
    "use strict";

    console.log(e)
    if (e.keyCode == 13) {
        console.log("ent");
    }

    return false;
}

function adminContentLoaded() {
    "use strict";

    var pageSelector = document.getElementById("current-page-selector");
    pageSelector.addEventListener("keyup", changePagination);
}

document.addEventListener("DOMContentLoaded", adminContentLoaded);
