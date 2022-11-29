$(function () {
    // On Key Up - start building a slug
    $("#post_title").on('keyup', function () {
        buildSLug()
    })
})

/**
 * Build a slug by title
 */
function buildSLug() {
    const currentTitle = $("#post_title").val();

    $("#post_slug").val(currentTitle
        .replace(/[\W\s]+/gm, "_")
        .toLowerCase()
    );
}