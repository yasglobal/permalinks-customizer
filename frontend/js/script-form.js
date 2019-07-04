var regeneratePermalink = document.getElementById("regenerate_permalink");
var regenerateValue = document.getElementById("permalinks_customizer_regenerate_permalink");
var savePost = document.getElementById("save-post");
var getHomeURL = document.getElementById("permalinks_customizer_home_url");
var getPermalink = document.getElementById("permalinks_customizer");
var checkYoastSEO = document.getElementById("wpseo_meta");
var editPost = "";
var isSaving = "";
var lastIsSaving = false;

function regenratePermalinkOption() {
    "use strict";

    var confirmBox = confirm("Are you sure, you want to regenerete Permalink?");
    if (!savePost) {
        if (document.querySelector("#editor .editor-post-save-draft")) {
            savePost = document.querySelector("#editor .editor-post-save-draft");
        } else if (document.querySelector("#editor .editor-post-publish-button")) {
            savePost = document.querySelector("#editor .editor-post-publish-button");
        }
    }
    if (savePost) {
        if (confirmBox) {
            regenerateValue.value = "true";
            savePost.click();
        }
    } else {
        var bodyClasses = document.querySelector("body");
        if ( bodyClasses && bodyClasses.classList.contains("term-php") ) {
            var saveTax = document.querySelector("body .edit-tag-actions input[type='submit']");
            if ( confirmBox ) {
                regenerateValue.value = "true";
                saveTax.click();
            }
        }
    }
}

function changeSEOLinkOnBlur() {
    "use strict";

    var snippetCiteBase = document.getElementById("snippet_citeBase");
    if (snippetCiteBase && getHomeURL && getHomeURL.value !== "" && getPermalink && getPermalink.value) {
        var i = 0;
        var urlChanged = setInterval( function() {
            i += 1;
            snippetCiteBase.innerHTML = getHomeURL.value + "/" + getPermalink.value;
            if (i === 5) {
                clearInterval(urlChanged);
            }
        }, 1000);
    }
}

function changeSEOLink() {
    "use strict";

    var snippetCiteBase = document.getElementById("snippet_citeBase");
    if (snippetCiteBase && getHomeURL && getHomeURL.value !== "" && getPermalink && getPermalink.value) {
        var i = 0;
        var urlChanged = setInterval( function() {
            i += 1;
            snippetCiteBase.innerHTML = getHomeURL.value + "/" + getPermalink.value;
            if (i === 5) {
                clearInterval(urlChanged);
            }
        }, 1000);
        var snippetEditorTitle = document.getElementById("snippet-editor-title");
        var snippetEditorSlug = document.getElementById("snippet-editor-slug");
        var snippetEditorDesc = document.getElementById("snippet-editor-meta-description");
        var snippetCite = document.getElementById("snippet_cite");
        if (snippetEditorTitle) {
            snippetEditorTitle.addEventListener("blur", changeSEOLinkOnBlur);
        }
        if (snippetEditorSlug) {
            snippetEditorSlug.addEventListener("blur", changeSEOLinkOnBlur);
        }
        if (snippetEditorDesc) {
            snippetEditorDesc.addEventListener("blur", changeSEOLinkOnBlur);
        }
        if (snippetCite) {
            snippetCite.style.display = "none";
        }
    }
}

/**
 * Change color of edit box on focus.
 */
function focusPermalinkField() {
    "use strict";

    var newPostSlug = document.getElementById("permalinks-customizer-post-slug");
    if (newPostSlug) {
        newPostSlug.style.color = "#000";
    }
}

/**
 * Change color of edit box on blur.
 */
function blurPermalinkField() {
    "use strict";

    var newPostSlug = document.getElementById("permalinks-customizer-post-slug");
    var originalPermalink = document.getElementById("original_permalink")
    if (!newPostSlug) {
        return;
    }
    document.getElementById("permalinks_customizer").value = newPostSlug.value;
    if ( newPostSlug.value == "" || newPostSlug.value == originalPermalink.value ) {
        newPostSlug.value = originalPermalink.value;
        newPostSlug.style.color = "#ddd";
    }
}

/**
 * Update Permalink Value in View Button
 */
function updateMetaBox() {
    "use strict";

    if (!editPost) {
        return;
    }

    var defaultPerm = document.getElementsByClassName("edit-post-post-link__preview-label");
    if (defaultPerm && defaultPerm[0]) {
        defaultPerm[0].parentNode.classList.add("pc-permalink-hidden");
    }
    isSaving = editPost.isSavingMetaBoxes();

    if (isSaving !== lastIsSaving && !isSaving) {
        lastIsSaving = isSaving;
        var postId = wp.data.select("core/editor").getEditedPostAttribute("id");
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var setPermlinks = JSON.parse(this.responseText);
                var permalinkAdd = document.getElementById("permalinks_customizer_add");
                getPermalink.value = setPermlinks.permalink_customizer;
                document.getElementById("permalinks-customizer-post-slug").value = setPermlinks.permalink_customizer;
                document.getElementById("original_permalink").value = setPermlinks.original_permalink;
                document.querySelector("#view-post-btn a").href = getHomeURL.value + "/" + setPermlinks.permalink_customizer;
                if (permalinkAdd && permalinkAdd.value == "add") {
                    document.getElementById("permalinks-customizer-edit-box").style.display = "";
                }
                if (document.querySelector(".components-notice__content a")) {
                    document.querySelector(".components-notice__content a").href = "/" + setPermlinks.permalink_customizer;
                }
            }
        };
        xhttp.open("GET", getHomeURL.value + "/wp-json/permalinks-customizer/v1/get-permalink/" + postId, true);
        xhttp.setRequestHeader("Cache-Control", "private, max-age=0, no-cache");
        xhttp.send();
    }

    lastIsSaving = isSaving;
}

/**
 * Hide default Permalink metabox
 */
function hideDefaultPermalink() {
    "use strict";

    var defaultPerm = document.getElementsByClassName("edit-post-post-link__preview-label");
    if (defaultPerm && defaultPerm[0]) {
        defaultPerm[0].parentNode.classList.add("pc-permalink-hidden");
    }
}

function permalinkContentLoaded() {
    "use strict";

    var permalinkEdit = document.getElementById("permalinks-customizer-edit-box");
    var defaultPerm = document.getElementsByClassName("edit-post-post-link__preview-label");
    var postSlug = document.getElementById("permalinks-customizer-post-slug");

    if (regeneratePermalink && regenerateValue) {
        regeneratePermalink.addEventListener("click", regenratePermalinkOption);
        if (!savePost) {
            savePost = document.getElementById("publish");
        }
    }

    if (postSlug) {
        postSlug.addEventListener("focus", focusPermalinkField);
        postSlug.addEventListener("blur", blurPermalinkField);
    }

    if (checkYoastSEO) {
        window.addEventListener("load", changeSEOLink);
    }
    if (document.querySelector("#permalinks-customizer-edit-box .inside").innerHTML.trim() === "") {
        permalinkEdit.style.display = "none";
    }
    if (wp.data) {
        var permalinkAdd = document.getElementById("permalinks_customizer_add");
        var sidebar = document.querySelectorAll(".edit-post-sidebar .components-panel__header");
        var i = 0;
        var totalTabs = sidebar.length;
        if (permalinkAdd && permalinkAdd.value == "add") {
            permalinkEdit.style.display = "none";
        }
        editPost = wp.data.select("core/edit-post");
        wp.data.subscribe(updateMetaBox);
        if (defaultPerm && defaultPerm[0]) {
            defaultPerm[0].parentNode.classList.add("pc-permalink-hidden");
        }
        if (permalinkEdit.classList.contains("closed")) {
            permalinkEdit.classList.remove("closed")
        }
        if (sidebar && totalTabs > 0) {
            while (i < totalTabs) {
                sidebar[i].addEventListener("click", hideDefaultPermalink);
                i += 1;
            }
        }
    }
}
document.addEventListener("DOMContentLoaded", permalinkContentLoaded);
