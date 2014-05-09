/**
 * Handle Ajax submission of the forms on the home page.
 *
 * @author: Steve Worley <sj.worley88@gmail.com>
 * @since: 1
 */
$(function() {

  var

  // The error div will be displayed if the $.ajax form submit does not complete.
  $errorDiv = $("<div>", {
    class: "alert alert-danger",
    content: ""
  }).html("<strong>Oh snap!</strong>: We could not process that file - please try again."),

  // Provide some basic options for the $.ajaxSubmit plugin.
  options = {

    // This is a DOM element - this is required to exist otherwise the ajax submit will not work.
    target: "#output",

    // Add a progress bar when files are uploaded.
    uploadProgress: function(event, position, total, percentComplete) {
      var $progressBar = $('#file-upload-progress .progress-bar');
      $progressBar.parent().removeClass('hidden').addClass('active');
      $progressBar.css({'width': percentComplete + '%'});
    },

    // What to do with a successful API interaction.
    success: function(data) {
      if (typeof data.id === 'undefined') {
        $('body').prepend($errorDiv);
        return;
      }

      window.location = '/r/' + data.id;
    },

    resetForm: true
  };

  $('#resource_file').bind('change', function() {
    $(this).parents('form').ajaxSubmit(options);
  });

  $('#send-code').bind('click', function() {
    $('#resource_code').val(window.editor.getSession().getValue());
    $('#resource_code').parents('form').ajaxSubmit(options);
  });

  $('#link-toggle').bind('click', function() {
    var $el = $(this),
        $toggle = $el.parents('section').find('[data-toggle]');

    $toggle.toggle();
    $el.text($toggle.filter(':visible').data('link'));
  });

});
