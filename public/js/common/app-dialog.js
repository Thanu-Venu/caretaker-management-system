(function () {
  "use strict";

  var STYLE_ID = "app-dialog-inline-style";
  var ROOT_ID = "appDialogRoot";
  var queue = [];
  var active = false;
  var dom = null;
  var nativeAlert = window.alert ? window.alert.bind(window) : null;

  function ensureStyles() {
    if (document.getElementById(STYLE_ID)) {
      return;
    }

    var style = document.createElement("style");
    style.id = STYLE_ID;
    style.textContent = [
      ".app-dialog-overlay{position:fixed;inset:0;z-index:99999;background:rgba(2,6,23,.45);display:none;align-items:center;justify-content:center;padding:16px;}",
      ".app-dialog-overlay.is-open{display:flex;}",
      ".app-dialog-box{width:min(420px,100%);background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 16px 48px rgba(15,23,42,.24);overflow:hidden;}",
      ".app-dialog-head{padding:14px 16px;border-bottom:1px solid #eef2f7;display:flex;align-items:center;gap:10px;}",
      ".app-dialog-icon{width:30px;height:30px;border-radius:999px;background:#eaf3ff;color:#1e88e5;display:inline-flex;align-items:center;justify-content:center;font-weight:700;}",
      ".app-dialog-title{margin:0;font-size:15px;font-weight:700;color:#0f172a;}",
      ".app-dialog-body{padding:16px;color:#334155;font-size:14px;line-height:1.5;white-space:pre-wrap;}",
      ".app-dialog-actions{padding:0 16px 16px;display:flex;justify-content:flex-end;gap:8px;}",
      ".app-dialog-btn{border:1px solid #d0d9e6;border-radius:10px;padding:8px 14px;font-size:13px;font-weight:600;background:#fff;color:#334155;cursor:pointer;}",
      ".app-dialog-btn:hover{background:#f8fafc;}",
      ".app-dialog-btn.primary{border-color:#1e88e5;background:#1e88e5;color:#fff;}",
      ".app-dialog-btn.primary:hover{background:#1976d2;border-color:#1976d2;}",
      ".app-dialog-btn:focus{outline:2px solid rgba(30,136,229,.35);outline-offset:1px;}"
    ].join("");
    document.head.appendChild(style);
  }

  function ensureDom() {
    if (dom && document.body.contains(dom.overlay)) {
      return dom;
    }

    ensureStyles();

    var existingRoot = document.getElementById(ROOT_ID);
    if (existingRoot) {
      existingRoot.remove();
    }

    var root = document.createElement("div");
    root.id = ROOT_ID;
    root.innerHTML =
      '<div class="app-dialog-overlay" role="presentation">' +
      '  <div class="app-dialog-box" role="dialog" aria-modal="true" aria-labelledby="appDialogTitle">' +
      '    <div class="app-dialog-head">' +
      '      <span class="app-dialog-icon" aria-hidden="true">!</span>' +
      '      <h3 class="app-dialog-title" id="appDialogTitle">Notice</h3>' +
      "    </div>" +
      '    <div class="app-dialog-body" id="appDialogBody"></div>' +
      '    <div class="app-dialog-actions" id="appDialogActions">' +
      '      <button type="button" class="app-dialog-btn" data-action="cancel">Cancel</button>' +
      '      <button type="button" class="app-dialog-btn primary" data-action="ok">OK</button>' +
      "    </div>" +
      "  </div>" +
      "</div>";
    document.body.appendChild(root);

    dom = {
      root: root,
      overlay: root.querySelector(".app-dialog-overlay"),
      title: root.querySelector("#appDialogTitle"),
      body: root.querySelector("#appDialogBody"),
      actions: root.querySelector("#appDialogActions"),
      okBtn: root.querySelector('[data-action="ok"]'),
      cancelBtn: root.querySelector('[data-action="cancel"]')
    };

    dom.overlay.addEventListener("click", function (event) {
      if (event.target === dom.overlay) {
        closeCurrent(false);
      }
    });

    dom.actions.addEventListener("click", function (event) {
      var btn = event.target.closest("button[data-action]");
      if (!btn) {
        return;
      }
      closeCurrent(btn.getAttribute("data-action") === "ok");
    });

    document.addEventListener("keydown", function (event) {
      if (!active || !dom.overlay.classList.contains("is-open")) {
        return;
      }
      if (event.key === "Escape") {
        event.preventDefault();
        closeCurrent(false);
      } else if (event.key === "Enter") {
        event.preventDefault();
        closeCurrent(true);
      }
    });

    return dom;
  }

  function showNext() {
    if (active || queue.length === 0) {
      return;
    }

    active = true;
    var ctx = queue[0];
    var ui = ensureDom();

    ui.title.textContent = ctx.title;
    ui.body.textContent = ctx.message;

    if (ctx.type === "alert") {
      ui.cancelBtn.style.display = "none";
    } else {
      ui.cancelBtn.style.display = "";
    }

    ui.okBtn.textContent = ctx.okText || "OK";
    ui.cancelBtn.textContent = ctx.cancelText || "Cancel";
    ui.overlay.classList.add("is-open");

    if (ctx.type === "alert") {
      ui.okBtn.focus();
    } else {
      ui.cancelBtn.focus();
    }
  }

  function closeCurrent(confirmed) {
    if (!active || queue.length === 0) {
      return;
    }

    var ctx = queue.shift();
    active = false;

    if (dom) {
      dom.overlay.classList.remove("is-open");
    }

    if (ctx.type === "confirm") {
      ctx.resolve(Boolean(confirmed));
    } else {
      ctx.resolve();
    }

    if (queue.length > 0) {
      showNext();
    }
  }

  function enqueue(item) {
    queue.push(item);
    showNext();
  }

  function alertDialog(message, options) {
    options = options || {};
    return new Promise(function (resolve) {
      enqueue({
        type: "alert",
        title: options.title || "Notice",
        message: String(message == null ? "" : message),
        okText: options.okText || "OK",
        resolve: resolve
      });
    });
  }

  function confirmDialog(message, options) {
    options = options || {};
    return new Promise(function (resolve) {
      enqueue({
        type: "confirm",
        title: options.title || "Confirm action",
        message: String(message == null ? "" : message),
        okText: options.okText || "OK",
        cancelText: options.cancelText || "Cancel",
        resolve: resolve
      });
    });
  }

  function upgradeInlineConfirms() {
    var nodes = document.querySelectorAll("[onclick]");
    nodes.forEach(function (node) {
      var onclick = node.getAttribute("onclick");
      if (!onclick || onclick.indexOf("confirm(") === -1) {
        return;
      }

      var match = onclick.match(/return\s+confirm\((['"])([\s\S]*?)\1\)\s*;?/i);
      if (!match) {
        return;
      }

      node.removeAttribute("onclick");
      node.setAttribute("data-app-confirm", match[2]);
    });
  }

  function bindDataConfirms() {
    document.addEventListener("click", function (event) {
      var target = event.target.closest("[data-app-confirm]");
      if (!target) {
        return;
      }

      var message = target.getAttribute("data-app-confirm") || "Are you sure?";
      var href = target.getAttribute("href");
      var tag = target.tagName.toLowerCase();

      event.preventDefault();
      event.stopPropagation();

      confirmDialog(message).then(function (ok) {
        if (!ok) {
          return;
        }

        if (tag === "a" && href) {
          window.location.href = href;
          return;
        }

        if ((tag === "button" || tag === "input") && target.form) {
          target.form.submit();
          return;
        }

        if (target.form) {
          target.form.submit();
        }
      });
    });
  }

  function boot() {
    window.appDialog = {
      alert: alertDialog,
      confirm: confirmDialog,
      nativeAlert: nativeAlert
    };

    window.alert = function (message) {
      alertDialog(message);
    };

    upgradeInlineConfirms();
    bindDataConfirms();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
