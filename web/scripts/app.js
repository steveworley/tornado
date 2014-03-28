require.config({
  baseUrl:  window.location.protocol + "//" + window.location.host
          + window.location.pathname.split("/").slice(0, -1).join("/"),
  paths: {
    ace: 'scripts/lib/ace',
  }
});

require(['ace/ace'], function(ace) {
  window.editor = ace.edit('editor');
  editor.setTheme('ace/theme/github');
  editor.getSession().setMode('ace/mode/php');
});
