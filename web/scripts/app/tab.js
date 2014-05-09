/**
 * Tab.js
 *
 * @author: Steve Worley <sj.worley88@gmail.com>
 * @since: 1
 */
(function($) {
  $.fn.tabs = function() {
    $(this).bind('click', function(evt) {
      var $el = $(this);

      $('a[data-tabs]').removeClass('active');
      $el.addClass('active');
      $('[data-tabs-content]').removeClass('active');
      $($el.data('tabs')).addClass('active');
      console.log('clicked');
    });
  }

  $('a[data-tabs]').tabs();
})(jQuery)
