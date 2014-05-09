/**
 * Application.js
 *
 * Set up the autoload functionality for Javascript files. When App
 * is initialized it will attempt to locate the required libraries and
 * using Require.js it will include them as required.
 *
 *  - data-libraries: Is required on the script tag that includes app.js.
 *
 * @author: Steve Worley <sj.worley88@gmail.com>
 */

require.config({
  baseUrl:  window.location.protocol + "//" + window.location.host
          + window.location.pathname.split("/").slice(0, -1).join("/"),
  paths: {
    ace: '/scripts/lib/ace',
    lib:  '/scripts/app',
  }
});

(function($, window, undefined) {
  "use strict";

  var
    App = function() {
      return this;
    };

  /**
   * Initialize our App.
   *
   * Initializing the app will call any methods that are required for the
   * bootstrap process.
   *
   * @return App.
   */
  App.prototype.initialize = function() {
    this.loadLibraries();

    return this;
  }

  /**
   * Autoload the libraries required for this page.
   *
   * Given a value for "data-libraries" on the script tag including app.js we
   * will attempt to use Require.js to include the file when the DOM is ready.
   * Thus asynchronously loading JS files.
   *
   * After a library has been loaded we will trigger a "library:included" event
   * which will allow methods to be called after the file has been included.
   *
   * @return bool
   */
  App.prototype.loadLibraries = function() {
    var
      $script = $('script[src*="app.js"]'),
      libraries = $script.data('libraries').split(',');

    $.each(libraries, function(i, library) {

      // With all found libraries attempt to use Require.js to load the
      // require files. Then trigger an event which can be listened to
      // to perform additional tasks.
      require([library], function() {

        // Events will look like [path:to:library:included].
        $(document).trigger(library.replace('/', ':') + ':included', [arguments, library]);
      });

      // Return a boolean depending on if any files have been included.
      return libraries.length > 0;
    });
  }

  $(function() {
    // Start the applications Javascript.
    new App().initialize();

    $(document).bind('ace:ace:included', function(evt, args, library) {

      // When ace is included we will attempt to use it to render the code
      // block on the north side of the coast.
      var ace = args[0];

      window.editor = ace.edit('editor');
      editor.setTheme('ace/theme/tomorrow_night_eighties');
      editor.getSession().setMode('ace/mode/php');
    });
  })

})(jQuery, this);
