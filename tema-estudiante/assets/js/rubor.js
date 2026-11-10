/* RUBOR — interacciones del tema (la bolsa y los pagos los maneja WooCommerce) */
(function ($) {
  "use strict";

  var DATA = window.RUBOR || {};
  var SHADES = DATA.shades || [];
  var TONE_LABEL = { calido: "Subtono cálido", neutro: "Subtono neutro", frio: "Subtono frío" };

  var q = function (s, c) { return (c || document).querySelector(s); };
  var qa = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  /* ---------- Filtros del catálogo de la portada ---------- */
  function setFilter(cat) {
    qa("[data-filter]").forEach(function (b) { b.setAttribute("aria-selected", String(b.dataset.filter === cat)); });
    qa(".products .product").forEach(function (el, i) {
      var show = cat === "todos" || el.dataset.cat === cat;
      el.hidden = !show;
      if (show) { el.style.animation = "none"; void el.offsetHeight; el.style.animation = ""; el.style.animationDelay = i * 0.04 + "s"; }
    });
  }
  qa("[data-filter]").forEach(function (b) {
    b.addEventListener("click", function () { setFilter(b.dataset.filter); });
  });

  /* ---------- Estudio de tonos ---------- */
  var list = q("[data-shade-list]");
  if (list && SHADES.length) {
    var current = SHADES[0];
    var info = q(".shades__info");
    var section = q(".shades");
    var addBtn = q("[data-shade-add]");

    list.innerHTML = SHADES.map(function (s, i) {
      return '<li><button class="shade-btn" data-shade="' + i + '" aria-pressed="' + (i === 0) + '">' +
        '<span class="shade-btn__dot" style="--c:' + s.hex + '"></span>' +
        "<span>" + s.name + "<small>N.º " + s.num + "</small></span></button></li>";
    }).join("");

    var pick = function (i) {
      current = SHADES[i];
      qa(".shade-btn").forEach(function (b) { b.setAttribute("aria-pressed", String(+b.dataset.shade === i)); });
      q("[data-shade-swatch]").style.setProperty("--shade", current.hex);
      section.style.setProperty("--shade-glow", current.hex);
      q("[data-shade-num]").textContent = "N.º " + current.num;
      q("[data-shade-name]").textContent = current.name;
      q("[data-shade-desc]").textContent = current.desc;
      q("[data-shade-tone]").textContent = TONE_LABEL[current.tone];
      if (current.price) q("[data-shade-price]").textContent = current.price;
      if (addBtn && current.id) {
        // WooCommerce lee el ID del producto con jQuery.data(), así que se actualizan ambos.
        addBtn.setAttribute("data-product_id", current.id);
        addBtn.setAttribute("href", "?add-to-cart=" + current.id);
        if ($) $(addBtn).data("product_id", current.id);
      }
      info.classList.remove("swap"); void info.offsetHeight; info.classList.add("swap");
    };

    list.addEventListener("click", function (e) {
      var b = e.target.closest("[data-shade]");
      if (b) pick(+b.dataset.shade);
    });

    qa("[data-undertone]").forEach(function (b) {
      b.addEventListener("click", function () {
        var t = b.dataset.undertone;
        qa("[data-undertone]").forEach(function (x) { x.setAttribute("aria-checked", String(x === b)); });
        var first = -1;
        qa(".shade-btn").forEach(function (btn) {
          var s = SHADES[+btn.dataset.shade];
          var match = t === "todos" || s.tone === t;
          btn.classList.toggle("is-dim", !match);
          if (match && first < 0) first = +btn.dataset.shade;
        });
        if (t !== "todos" && current.tone !== t && first >= 0) pick(first);
      });
    });
  }

  /* ---------- Bolsa (mini carrito de WooCommerce) ---------- */
  var drawer = q("[data-cart]");
  var scrim = q("[data-cart-scrim]");
  var toastEl = q("[data-toast]");
  var toastTimer;

  function toast(msg) {
    if (!toastEl) return;
    toastEl.textContent = msg;
    toastEl.classList.add("is-on");
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { toastEl.classList.remove("is-on"); }, 2600);
  }

  function openCart() {
    if (!drawer) { if (DATA.cartUrl) location.href = DATA.cartUrl; return; }
    scrim.hidden = false;
    drawer.classList.add("is-open");
    drawer.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
    q("[data-cart-close]").focus();
  }
  function closeCart() {
    if (!drawer) return;
    scrim.hidden = true;
    drawer.classList.remove("is-open");
    drawer.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
  }

  qa("[data-cart-open]").forEach(function (b) { b.addEventListener("click", openCart); });
  if (drawer) {
    q("[data-cart-close]").addEventListener("click", closeCart);
    scrim.addEventListener("click", closeCart);
  }

  if ($) {
    // Evento que dispara WooCommerce cuando un producto entra a la bolsa por AJAX.
    $(document.body).on("added_to_cart", function (e, fragments, hash, $button) {
      var row = $button && $button.closest(".product, .hero__tag, .shades__info");
      var name = row && row.find(".product__name, strong, [data-shade-name]").first().text();
      qa(".bag-btn__count, .bag-mobile__count").forEach(function (el) {
        el.classList.remove("bump"); void el.offsetHeight; el.classList.add("bump");
      });
      toast((name ? name.trim() : "Producto") + " está en tu bolsa");
      if ($button) $button.siblings(".added_to_cart").remove();
    });
  }

  /* ---------- Menú móvil ---------- */
  var menu = q("[data-menu]");
  function closeMenu() { if (menu && !menu.hidden) { menu.hidden = true; document.body.style.overflow = ""; } }
  if (menu) {
    q("[data-menu-open]").addEventListener("click", function () { menu.hidden = false; document.body.style.overflow = "hidden"; });
    q("[data-menu-close]").addEventListener("click", closeMenu);
    qa("a", menu).forEach(function (a) { a.addEventListener("click", closeMenu); });
  }

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") { closeCart(); closeMenu(); }
  });

  /* ---------- Header que se esconde al bajar ---------- */
  var header = q("[data-header]");
  var lastY = window.scrollY;
  window.addEventListener("scroll", function () {
    var y = window.scrollY;
    header.classList.toggle("is-scrolled", y > 10);
    header.classList.toggle("is-hidden", y > lastY && y > 400 && !document.body.classList.contains("woocommerce-checkout"));
    lastY = y;
  }, { passive: true });

  /* ---------- Revelado al hacer scroll ---------- */
  var items = qa(".reveal");
  if ("IntersectionObserver" in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add("is-in"); io.unobserve(en.target); }
      });
    }, { threshold: 0.12, rootMargin: "0px 0px -40px 0px" });
    items.forEach(function (el, i) {
      el.style.transitionDelay = (i % 4) * 0.08 + "s";
      io.observe(el);
    });
  } else {
    items.forEach(function (el) { el.classList.add("is-in"); });
  }
})(window.jQuery);
