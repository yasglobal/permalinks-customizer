var regeneratePermalink = document.getElementById( 'regenerate_permalink' ),
    regenerateValue = document.getElementById( 'permalinks_customizer_regenerate_permalink' ),
    savePost = document.getElementById( 'save-post' );

function regenratePermalinkOption() {
    if ( savePost ) {
        var confirmBox = confirm( "Are you sure, you want to regenerete Permalink?" );
        if (confirmBox) {
            regenerateValue.value = "true";
            savePost.click();
        }
    } else {
        var bodyClasses = document.querySelector( 'body' );
        if ( bodyClasses && bodyClasses.classList.contains( 'term-php' ) ) {
            var saveTax = document.querySelector( 'body .edit-tag-actions input[type="submit"]' );
            var confirmBox = confirm( "Are you sure, you want to regenerete Permalink?" );
            if ( confirmBox ) {
                regenerateValue.value = "true";
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