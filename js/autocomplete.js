/*
 * GAutoComplete
 *
 * @filesource js/autocomplete.js
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */
(function () {
  'use strict';
  window.GAutoComplete = GClass.create();
  GAutoComplete.prototype = {
    initialize: function (id, o) {
      var options = {
        className: 'gautocomplete',
        itemClass: 'item',
        prepare: $K.emptyFunction,
        callBack: $K.emptyFunction,
        get: $K.emptyFunction,
        populate: $K.emptyFunction,
        onRequest: $K.emptyFunction,
        loadingClass: 'wait',
        url: false,
        interval: 300
      };
      for (var property in o) {
        options[property] = o[property];
      }
      var cancleEvent = false;
      var showing = false;
      var listindex = 0;
      var list = new Array();
      var input = $G(id);
      var req = new GAjax();
      var self = this;
      if (!$E('gautocomplete_div')) {
        var div = document.createElement('div');
        document.body.appendChild(div);
        div.id = 'gautocomplete_div';
      }
      var display = $G('gautocomplete_div');
      display.className = options.className;
      display.style.left = '-100000px';
      display.style.position = 'absolute';
      display.style.display = 'table';
      display.style.zIndex = 9999;
      function _movehighlight(id) {
        listindex = Math.max(0, id);
        listindex = Math.min(list.length - 1, listindex);
        forEach(list, function (item, index) {
          if (listindex == this.itemindex) {
            item.addClass('select');
          } else {
            item.removeClass('select');
          }
        });
      }
      var _mouseclick = function () {
        if (showing) {
          _hide();
          options.callBack.call(this.datas);
        }
      };
      var _mousemove = function () {
        _movehighlight(this.itemindex);
      };
      function _populateitems(datas) {
        display.innerHTML = '';
        list = new Array();
        var f, ret = options.prepare.call(datas);
        if (ret && ret != '') {
          var p = ret.toDOM();
          display.appendChild(p);
        }
        for (var i in datas) {
          ret = options.populate.call(datas[i]);
          if (ret && ret != '') {
            p = ret.toDOM();
            f = p.firstChild;
            $G(f).className = options.itemClass;
            f.datas = datas[i];
            f.addEvent('mousedown', _mouseclick);
            f.addEvent('mousemove', _mousemove);
            f.itemindex = list.length;
            list.push(f);
            display.appendChild(p);
          }
        }
        _movehighlight(0);
      }
      function _hide() {
        input.removeClass(options.loadingClass);
        display.style.left = '-100000px';
        showing = false;
      }
      var _dokeyup = function () {
        window.clearTimeout(self.timer);
        req.abort();
        if (!cancleEvent) {
          var q = options.get.call(this);
          if (options.url && q && q != '') {
            input.addClass(options.loadingClass);
            self.timer = window.setTimeout(function () {
              req.send(options.url, q, function (xhr) {
                input.removeClass(options.loadingClass);
                if (xhr.responseText !== '') {
                  var datas = xhr.responseText.toJSON();
                  var ret = options.onRequest.call(self, datas);
                  if (ret) {
                    datas = ret;
                  }
                  listindex = 0;
                  if (datas) {
                    _populateitems(datas);
                  } else {
                    display.setValue(xhr.responseText);
                  }
                  display.style.left = input.getLeft() + 'px';
                  display.style.top = (input.getTop() + input.getHeight() + 5) + 'px';
                  showing = true;
                } else {
                  _hide();
                }
              });
            }, options.interval);
          } else {
            _hide();
          }
        }
        cancleEvent = false;
      };
      function _dokeydown(evt) {
        var key = GEvent.keyCode(evt);
        if (key == 40) {
          _movehighlight(listindex + 1);
          cancleEvent = true;
        } else if (key == 38) {
          _movehighlight(listindex - 1);
          cancleEvent = true;
        } else if (key == 13) {
          cancleEvent = true;
          forEach(list, function () {
            if (this.itemindex == listindex) {
              _mouseclick.call(this);
            }
          });
        }
        if (cancleEvent) {
          GEvent.stop(evt);
        }
      }
      input.addEvent('keyup', _dokeyup);
      input.addEvent('keydown', _dokeydown);
      input.addEvent('blur', function () {
        _hide();
      });
      $G(document.body).addEvent('click', function () {
        _hide();
      });
    }
  };
}());