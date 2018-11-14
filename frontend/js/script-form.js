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
    var gutenberg = 0;
    if (!savePost) {
        if (document.querySelector("#editor .editor-post-save-draft")) {
            savePost = document.querySelector("#editor .editor-post-save-draft");
            gutenberg = 1;
        } else if (document.querySelector("#editor .editor-post-publish-button")) {
            savePost = document.querySelector("#editor .editor-post-publish-button");
            gutenberg = 1;
        }
    }
    if (savePost) {
        if (confirmBox) {
            regenerateValue.value = "true";
            savePost.click();
            if (gutenberg === 1) {
                setInterval( function () {
                    if (document.querySelector(".components-notice.is-success.is-dismissible")) {
                        location.reload();
                    }
                }, 1000);
            }
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

if (regeneratePermalink && regenerateValue) {
    regeneratePermalink.addEventListener("click", regenratePermalinkOption);
    if (!savePost) {
        savePost = document.getElementById("publish");
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
        var snippetEditorSlug  = document.getElementById("snippet-editor-slug");
        var snippetEditorDesc  = document.getElementById("snippet-editor-meta-description");
        var snippetCite        = document.getElementById("snippet_cite");
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
 * Update Permalink Value in View Button
 */
function updateMetaBox() {
    "use strict";

    if (!editPost) {
      return;
    }

    isSaving = editPost.isSavingMetaBoxes();

    if (isSaving !== lastIsSaving && !isSaving) {
        lastIsSaving = isSaving;
        var postId = wp.data.select("core/editor").getEditedPostAttribute("id");
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var setPermlinks = JSON.parse(this.responseText);
                getPermalink.value = setPermlinks.permalink_customizer;
                document.getElementById("permalinks-customizer-post-slug").value = setPermlinks.permalink_customizer;
                document.getElementById("original_permalink").value = setPermlinks.original_permalink;
                document.querySelector("#view-post-btn a").href = getHomeURL.value + "/" + setPermlinks.permalink_customizer;
                if (document.getElementById("permalinks_customizer_add") && document.getElementById("permalinks_customizer_add").value == "add") {
                    document.getElementById("permalinks-customizer-edit-box").style.display = "";
                }
                if (document.querySelector(".components-notice__content a")) {
                    document.querySelector(".components-notice__content a").href = "/" + setPermlinks.permalink_customizer;
                }
            }
        };
        xhttp.open("GET", getHomeURL.value + "/wp-json/permalinks-customizer/v1/get-permalink/" + postId, true);
        xhttp.send();
    }

    lastIsSaving = isSaving;
}

function permalinkContentLoaded() {
    "use strict";

    editPost = wp.data.select("core/edit-post");
    if (checkYoastSEO) {
        window.addEventListener("load", changeSEOLink);
    }
    if ( document.querySelector("#permalinks-customizer-edit-box .inside").innerHTML.trim() === "" ) {
        document.getElementById("permalinks-customizer-edit-box").style.display = "none";
    }
    if (document.getElementById("permalinks_customizer_add") && document.getElementById("permalinks_customizer_add").value == "add") {
        document.getElementById("permalinks-customizer-edit-box").style.display = "none";
    }
    wp.data.subscribe(updateMetaBox);
}
document.addEventListener("DOMContentLoaded", permalinkContentLoaded);
