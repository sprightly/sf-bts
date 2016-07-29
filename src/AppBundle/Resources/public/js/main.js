var AppBundle = {
    init: function () {
        this.initCommentEditClick();
        this.initCancelButton();
    },
    initCommentEditClick: function () {
        jQuery('.edit-comment').click(function () {
            var container = jQuery(this).parents('.panel');
            var commentBodyInput = jQuery('#comment_body');

            var text = container.find('.panel-body').text();
            var id = container.data('id');

            commentBodyInput.val(text);
            jQuery('#comment_id').val(id);
            commentBodyInput.focus();

            jQuery('.cancel-edit').show();

            return false;
        });
    },
    initCancelButton: function () {
        jQuery('.cancel-edit').click(function () {
            jQuery(this).hide();
            jQuery('#comment_body').val('');
            jQuery('#comment_id').val('');
            return false;
        });
    }
};

jQuery(document).ready(function () {
    AppBundle.init();
});