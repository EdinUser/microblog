const firstPass = $("#admin_pass");
const secondPass = $("#admin_pass_repeat");
$(function () {
    firstPass.on('keyup blur', () => {
        comparePasswords();
    });
    secondPass.on('keyup blur', () => {
        comparePasswords();
    });
})

function comparePasswords() {
    if (firstPass.val() !== secondPass.val()) {
        showWarning('show');
    } else {
        showWarning('hide');
    }
}

function showWarning(doWhat) {
    const warningContainer = $("#warningPass");
    if (doWhat === 'show') {
        if (warningContainer.hasClass('d-none')) {
            warningContainer.removeClass('d-none');
        }
        $("#savePassword").attr('disabled', true)
    } else {
        if (!warningContainer.hasClass('d-none')) {
            warningContainer.addClass('d-none');
        }
        $("#savePassword").attr('disabled', false)
    }
}