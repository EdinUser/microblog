$(function () {
    // On Key Up - start building a slug
    $("#post_title").on('keyup', function () {
        buildSLug()
    });
    connectUploadButton();
    connectRemovePictureElement();
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

/**
 * Add event for AJAX upload
 */
function connectUploadButton() {
    $(".uploadButton").on("change", function () {
        doAjaxUpload(this.files[0], this);
    });
}

/**
 * Process the uploads
 * @param file
 * @param elem
 */
function doAjaxUpload(file, elem) {
    const formData = new FormData($('#post_form')[0]);
    $(elem).after('<progress value="0"></progress>');

    let containerName = ".uploadContainer";

    const getId = elem.name.match(/\[(.*)\]/);
    const getRealName = elem.name.match(/(.*?)\[/);

    const getCurrentName = getRealName[1];
    const getCurrentId = getId[1];

    formData.append('fetchedName', getCurrentName);
    formData.append('picid', getCurrentId);

    if ($("#watermark" + getCurrentId).prop('checked') === true) {
        formData.append('watermark[' + getCurrentId + ']', "1");
    }

    $.ajax({
        url: '/upload', //server script to process data
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        xhr: function () {  // custom xhr
            let myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) { // check if upload property exists
                myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // for handling the progress of the upload
            }
            return myXhr;
        },
        //Ajax events
        success: function (resp) {
            console.log(resp);
            if (getCurrentId) {
                $(containerName + getCurrentId).html(resp);
            } else {
                $(containerName).prepend(resp);
            }

            $('progress').remove();

            $(elem).val('');

            let elemId = $("#" + this.id);
            elemId.replaceWith(elemId.clone());
        },
        error: function (what) {
            console.log(what);
        }
    });
}

/**
 * Helped function to show slider
 * @param e
 */
function progressHandlingFunction(e) {
    if (e.lengthComputable) {
        $('progress').attr({
            value: e.loaded,
            max: e.total
        });
    }
}

/**
 * Attache event for removing a picture
 */
function connectRemovePictureElement() {
    $(".removeFromPost").on("click", function (e) {
        e.preventDefault();

        const data = $(this).data();
        const answer = confirm("WARNING!!!\nThis is irreversible. Continue?");

        if (answer) {
            removePicture(data, $(this))
        }
    });
}

/**
 * Execute the remove AJAX
 * @param picture {object}
 * @param picture.post_id {string} The post ID to be processed
 * @param picture.picture {string} The name of the picture to be removed
 * @param clickedElement
 */
function removePicture(picture, clickedElement) {
    $.ajax({
        url: '/picture/remove', //server script to process data
        type: 'POST',
        data: picture,
        success: function (resp) {
            console.log(resp);
            clickedElement.parent().fadeOut("slow", function () {
                $(this).remove();
            });
        },
        error: function (what) {
            alert(what);
        }
    });
}