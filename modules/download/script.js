var download_time = 0;
function initDownloadList(id) {
  var patt = /download_([0-9]+)/;
  forEach($G(id).elems('a'), function () {
    if (patt.test(this.id)) {
      callClick(this, doDownloadClick);
    }
  });
}
var doDownloadClick = function () {
  var req = new GAjax({
    asynchronous: false
  });
  req.send(WEB_URL + 'index.php/download/model/download/action', 'action=download&id=' + this.id);
  var ds = req.responseText.toJSON();
  if (ds) {
    if (ds.confirm) {
      if (confirm(ds.confirm)) {
        req.send(WEB_URL + 'index.php/download/model/download/action', 'action=downloading&id=' + this.id);
        ds = req.responseText.toJSON();
        if (ds.downloads) {
          $E('downloads_' + ds.id).innerHTML = ds.downloads;
        }
        if (ds.id) {
          this.href = ds.href;
          return true;
        }
      }
    }
    if (ds.alert) {
      alert(ds.alert);
    }
  } else if (req.responseText != '') {
    alert(req.responseText);
  }
  return false;
};