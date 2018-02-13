var regeneratePermalink = document.getElementById( 'regenerate_permalink' ),
    regenerateValue     = document.getElementById( 'permalinks_customizer_regenerate_permalink' ),
    savePost            = document.getElementById( 'save-post' ),
    getHomeURL          = document.getElementById( 'permalinks_customizer_home_url' ),
    getPermalink        = document.getElementById( 'permalinks_customizer' ),
    checkYoastSEO       = document.getElementById( 'wpseo_meta' );

function regenratePermalinkOption() {
    if ( savePost ) {
        var confirmBox = confirm( 'Are you sure, you want to regenerete Permalink?' );
        if (confirmBox) {
            regenerateValue.value = 'true';
            savePost.click();
        }
    } else {
        var bodyClasses = document.querySelector( 'body' );
        if ( bodyClasses && bodyClasses.classList.contains( 'term-php' ) ) {
            var saveTax = document.querySelector( 'body .edit-tag-actions input[type="submit"]' );
            var confirmBox = confirm( 'Are you sure, you want to regenerete Permalink?' );
            if ( confirmBox ) {
                regenerateValue.value = 'true';
                saveTax.click();
            }
        }
    }
}

if ( regeneratePermalink && regenerateValue ) {
    if ( savePost ) {
        regeneratePermalink.addEventListener( 'click', regenratePermalinkOption, false );
    } else {
        savePost = document.getElementById( 'publish' );
        regeneratePermalink.addEventListener( 'click', regenratePermalinkOption, false );
    }
}

function changeSEOLinkOnBlur () {
    var snippetCiteBase = document.getElementById( 'snippet_citeBase' );
    if ( snippetCiteBase && getHomeURL && getHomeURL.value != "" && getPermalink && getPermalink.value ) {
        var i = 0;
        var urlChanged = setInterval( function() {
            i++;
            snippetCiteBase.innerHTML = getHomeURL.value + '/' + getPermalink.value;
            if (i === 5) {
                clearInterval(urlChanged);
            }
        }, 1000);
    }
}

function changeSEOLink () {
    var snippetCiteBase = document.getElementById( 'snippet_citeBase' );
    if ( snippetCiteBase && getHomeURL && getHomeURL.value != '' && getPermalink && getPermalink.value ) {
        var i = 0;
        var urlChanged = setInterval( function() {
            i++;
            snippetCiteBase.innerHTML = getHomeURL.value + '/' + getPermalink.value;
            if (i === 5) {
                clearInterval(urlChanged);
            }
        }, 1000);
        var snippetEditorTitle = document.getElementById( 'snippet-editor-title' ),
            snippetEditorSlug  = document.getElementById( 'snippet-editor-slug' ),
            snippetEditorDesc  = document.getElementById( 'snippet-editor-meta-description' ),
            snippetCite        = document.getElementById( 'snippet_cite' );
        if ( snippetEditorTitle ) {
            snippetEditorTitle.addEventListener('blur', changeSEOLinkOnBlur, false);
        }
        if ( snippetEditorSlug ) {
            snippetEditorSlug.addEventListener('blur', changeSEOLinkOnBlur, false);
        }
        if ( snippetEditorDesc ) {
            snippetEditorDesc.addEventListener('blur', changeSEOLinkOnBlur, false);
        }
        if ( snippetCite ) {
            snippetCite.style.display = 'none';
        }
    }
}

if ( checkYoastSEO ) {
    window.addEventListener('load', changeSEOLink, false);
}
