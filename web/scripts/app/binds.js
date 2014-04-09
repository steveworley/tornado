/**
 * This should be used to trigger events on another DOM element
 * when the same event is triggered on the bound element.
 *
 * @author: Steve Worley <sj.worley88@gmail.com>
 */

(function($, window, undefined) {
  "use strict";

  // A list of Javascript events to listen to.
  var _evts = ['click', 'mouseOut', 'mouseIn', 'focusIn', 'focusOut'];

  $.each(_evts, function(idx, evnt) {
    $("[data-bindto]").bind(evnt, function(ev) {
      // Trigger the event on the element this element is bound to.
      $('#' + $(this).data('bindto')).trigger(evnt);
      ev.preventDefault();
    });
  });
})(jQuery, this);

