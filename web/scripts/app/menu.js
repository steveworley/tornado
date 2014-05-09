/**
 * Simple menu function for jQuery.
 *
 * @author: Steve Worley <sj.worley88@gmail.com>
 */
(function($, window, undefined) {
  "use strict";

  $.fn.dropdown = function(options) {
    var $el = $(this),
        $that = $($el.data('dropdown')),
        opts = $.extend( {}, $.fn.dropdown.defaults, options);

    $el.on('click', function(evt) {
      $el.toggleClass(opts.openClass);
      if (typeof $that[opts.animation] == 'function') {
        $that[opts.animation]();
      }
      evt.preventDefault();
    });
  }

  $.fn.dropdown.defaults = {
    openClass: 'open',
    animation: 'fadeToggle',
  }

  $("[data-dropdown]").dropdown();
})(jQuery, this)
