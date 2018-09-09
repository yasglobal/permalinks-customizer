var regeneratePermalink = document.getElementById("regenerate_permalink");
var regenerateValue = document.getElementById("permalinks_customizer_regenerate_permalink");
var savePost = document.getElementById("save-post");
var getHomeURL = document.getElementById("permalinks_customizer_home_url");
var getPermalink = document.getElementById("permalinks_customizer");
var checkYoastSEO = document.getElementById("wpseo_meta");

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

if ( regeneratePermalink && regenerateValue ) {
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

function changeSEOLink () {
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

if (checkYoastSEO) {
    window.addEventListener("load", changeSEOLink);
}
if ( document.querySelector("#permalinks-customizer-edit-box .inside").innerHTML.trim() === "" ) {
    document.getElementById("permalinks-customizer-edit-box").style.display = "none";
}
