'use strict';

/**
 * navbar toggle on mobile
 */
const navbar = document.querySelector("[data-navbar]");
const navbarLinks = document.querySelectorAll("[data-nav-link]");
const menuToggleBtn = document.querySelector("[data-menu-toggle-btn]");

if (menuToggleBtn && navbar) {
  menuToggleBtn.addEventListener("click", function () {
    navbar.classList.toggle("active");
    this.classList.toggle("active");
  });
}

/**
 * NAVBAR ACTIVE SCROLLSPY & SMOOTH CLICK HANDLING (ZERO FLICKER)
 */
const navLinks = document.querySelectorAll(".navbar-link[href^='#'], [data-nav-link][href^='#']");
let isClickScrolling = false;
let clickScrollTimer = null;

function getTrackedSections() {
  const ids = ["home", "about", "food-menu", "blog", "footer"];
  const list = [];
  ids.forEach(id => {
    const el = document.getElementById(id);
    if (el) list.push(el);
  });
  return list;
}

function setActiveNavById(sectionId) {
  if (!sectionId) return;

  navLinks.forEach(link => {
    const targetHash = link.getAttribute("href");
    if (targetHash === `#${sectionId}`) {
      link.classList.add("active");
    } else {
      link.classList.remove("active");
    }
  });

  // Sync bottom dock item smoothly
  const dockHome = document.querySelector('.dock-item[data-nav-target="home"]');
  const dockMenu = document.querySelector('.dock-item[data-nav-target="search"]');
  if (dockHome && dockMenu) {
    if (sectionId === "food-menu") {
      dockMenu.classList.add("active");
      dockHome.classList.remove("active");
    } else if (sectionId === "home" || sectionId === "about") {
      dockHome.classList.add("active");
      dockMenu.classList.remove("active");
    }
  }
}

function updateActiveNavLink() {
  if (isClickScrolling) return; // Prevent intermediate buttons from flickering during smooth scroll!

  const trackedSections = getTrackedSections();
  if (!trackedSections.length) return;

  // Center sweet-spot (roughly 35% from the top of viewport)
  const focusLine = window.scrollY + (window.innerHeight * 0.35);
  let currentSectionId = "";

  // If scrolled to near the bottom of document, activate footer (Kontak)
  if ((window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - 90)) {
    currentSectionId = "footer";
  } else {
    for (let i = 0; i < trackedSections.length; i++) {
      const sec = trackedSections[i];
      const top = sec.offsetTop;
      const height = sec.offsetHeight;
      if (focusLine >= top && focusLine < top + height) {
        currentSectionId = sec.getAttribute("id");
        break;
      }
    }
    if (!currentSectionId && window.scrollY < 200) {
      currentSectionId = "home";
    }
  }

  if (currentSectionId) {
    setActiveNavById(currentSectionId);
  }
}

// Click navigation with lock to eliminate "ngeilir" hopping across intermediate buttons
navLinks.forEach(link => {
  link.addEventListener("click", function (e) {
    const targetHref = this.getAttribute("href");
    if (targetHref && targetHref.startsWith("#")) {
      const targetId = targetHref.substring(1);

      if (navbar && navbar.classList.contains("active")) {
        navbar.classList.remove("active");
        if (menuToggleBtn) menuToggleBtn.classList.remove("active");
      }

      // 1. Immediately highlight clicked button without waiting
      setActiveNavById(targetId);

      const targetSec = document.getElementById(targetId);
      if (targetSec) {
        targetSec.classList.add("section-target-highlight");
        setTimeout(() => targetSec.classList.remove("section-target-highlight"), 1200);
      }

      // 2. Lock scrollspy for 800ms so it does NOT animate through intermediate buttons while scrolling
      isClickScrolling = true;
      clearTimeout(clickScrollTimer);
      clickScrollTimer = setTimeout(() => {
        isClickScrolling = false;
        updateActiveNavLink();
      }, 850);
    }
  });
});

// Scroll listener with RAF throttling
let isScrollSpyTicking = false;
window.addEventListener("scroll", function () {
  if (isClickScrolling) return; // Silent during click smooth scroll
  if (!isScrollSpyTicking) {
    window.requestAnimationFrame(function () {
      updateActiveNavLink();
      isScrollSpyTicking = false;
    });
    isScrollSpyTicking = true;
  }
}, { passive: true });

// Listen on hashchange and initial load
window.addEventListener("DOMContentLoaded", updateActiveNavLink);
window.addEventListener("hashchange", updateActiveNavLink);
window.addEventListener("load", updateActiveNavLink);
updateActiveNavLink();

/**
 * header sticky & back to top
 */
const header = document.querySelector("[data-header]");
const backTopBtn = document.querySelector("[data-back-top-btn]");

window.addEventListener("scroll", function () {
  if (window.scrollY >= 100) {
    if (header) header.classList.add("active");
    if (backTopBtn) backTopBtn.classList.add("active");
  } else {
    if (header) header.classList.remove("active");
    if (backTopBtn) backTopBtn.classList.remove("active");
  }
});

/**
 * search box toggle
 */
const searchBtn = document.querySelector("[data-search-btn]");
const searchContainer = document.querySelector("[data-search-container]");
const searchSubmitBtn = document.querySelector("[data-search-submit-btn]");
const searchCloseBtn = document.querySelector("[data-search-close-btn]");

const searchBoxElems = [searchBtn, searchSubmitBtn, searchCloseBtn];

for (let i = 0; i < searchBoxElems.length; i++) {
  if (searchBoxElems[i] && searchContainer) {
    searchBoxElems[i].addEventListener("click", function () {
      searchContainer.classList.toggle("active");
      document.body.classList.toggle("active");
    });
  }
}

/**
 * move cycle on scroll
 */
const deliveryBoy = document.querySelector("[data-delivery-boy]");
let deliveryBoyMove = -80;
let lastScrollPos = 0;

window.addEventListener("scroll", function () {
  let deliveryBoyTopPos = deliveryBoy ? deliveryBoy.getBoundingClientRect().top : 0;

  if (deliveryBoy && deliveryBoyTopPos < 500 && deliveryBoyTopPos > -250) {
    let activeScrollPos = window.scrollY;
    if (lastScrollPos < activeScrollPos) {
      deliveryBoyMove += 1;
    } else {
      deliveryBoyMove -= 1;
    }
    lastScrollPos = activeScrollPos;
    deliveryBoy.style.transform = `translateX(${deliveryBoyMove}px)`;
  }
});


// ==========================================
// KANTIN SKANIC - CATEGORY FILTER & SEARCH
// ==========================================

const filterBtns = document.querySelectorAll("[data-filter-btn]");
const menuItems = document.querySelectorAll("[data-filter-item]");
const menuSearchInput = document.querySelector(".search-input");

let currentCategory = 'all';
let currentSearch = '';

function filterMenu() {
  const items = document.querySelectorAll("[data-filter-item]");
  items.forEach(item => {
    const itemCat = item.getAttribute("data-category") || '';
    const itemName = (item.getAttribute("data-name") || '').toLowerCase();
    const itemStand = (item.getAttribute("data-stand") || '').toLowerCase();

    const matchesCat = currentCategory === 'all' || itemCat === currentCategory;
    const matchesSearch = currentSearch === '' || itemName.includes(currentSearch) || itemStand.includes(currentSearch);

    if (matchesCat && matchesSearch) {
      item.style.display = "";
    } else {
      item.style.display = "none";
    }
  });
}

if (filterBtns.length > 0) {
  filterBtns.forEach(btn => {
    btn.addEventListener("click", function () {
      filterBtns.forEach(b => b.classList.remove("active"));
      this.classList.add("active");
      currentCategory = this.getAttribute("data-filter-btn");
      filterMenu();
    });
  });
}

if (menuSearchInput) {
  menuSearchInput.addEventListener("input", function (e) {
    currentSearch = e.target.value.toLowerCase().trim();
    filterMenu();
  });
}


// ==========================================
// KANTIN SKANIC - CART & CHECKOUT ENGINE
// ==========================================

let cart = [];
try {
  const savedCart = localStorage.getItem('skanic_canteen_cart');
  if (savedCart) {
    cart = JSON.parse(savedCart);
  }
} catch (e) {
  cart = [];
}

function saveCart() {
  try {
    localStorage.setItem('skanic_canteen_cart', JSON.stringify(cart));
  } catch (e) { }
}

function formatRupiah(num) {
  return 'Rp ' + Number(num).toLocaleString('id-ID');
}

function showToast(msg) {
  let container = document.querySelector(".toast-container");
  if (!container) {
    container = document.createElement("div");
    container.className = "toast-container";
    container.style.cssText = "position:fixed; top:24px; right:24px; z-index:9999; display:flex; flex-direction:column; gap:10px; pointer-events:none;";
    document.body.appendChild(container);
  }
  const toast = document.createElement("div");
  toast.className = "toast-msg";
  toast.style.cssText = "background:#0f172a; color:#ffffff; padding:12px 20px; border-radius:12px; font-size:1.35rem; font-weight:500; box-shadow:0 10px 25px rgba(0,0,0,0.2); display:flex; align-items:center; gap:10px; pointer-events:auto; transition:all 0.3s ease;";
  toast.innerHTML = `<ion-icon name="bag-check-outline" style="font-size:2rem; color:#16a34a;"></ion-icon> <span>${msg}</span>`;
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(50px)';
    setTimeout(() => toast.remove(), 300);
  }, 2500);
}

function updateCartBadges() {
  const totalCount = cart.reduce((sum, item) => sum + item.qty, 0);
  const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);

  const counters = document.querySelectorAll("[data-header-cart-count], [data-cart-counter]");
  counters.forEach(c => c.textContent = totalCount);

  const totals = document.querySelectorAll("[data-cart-total]");
  totals.forEach(t => t.textContent = formatRupiah(totalPrice));
}

function renderCartDrawer() {
  updateCartBadges();
  const cartList = document.querySelector("[data-cart-items-list]");
  const cartEmpty = document.querySelector("[data-cart-empty]");
  const formSection = document.getElementById("cartOrderFormSection");
  const cartSubtotalElem = document.querySelector("[data-cart-subtotal]");
  const serviceFeeElem = document.querySelector("[data-cart-service-fee]");
  const cartTotalElem = document.querySelector("[data-cart-final-total]");
  const cartBtnTotalElem = document.querySelector("[data-cart-btn-total]");
  const countPillElem = document.querySelector("[data-header-cart-count-pill]");
  const checkoutBtn = document.querySelector("[data-checkout-btn]");
  const btnOpenCheckout = document.getElementById("btnOpenCheckout");

  if (!cartList) return;

  const totalCount = cart.reduce((sum, item) => sum + item.qty, 0);
  const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
  const serviceFee = cart.length > 0 ? 500 : 0;
  const grandTotal = subtotal + serviceFee;

  if (cart.length === 0) {
    if (cartEmpty) cartEmpty.style.display = "block";
    if (formSection) formSection.style.display = "none";
    cartList.innerHTML = "";
    if (cartSubtotalElem) cartSubtotalElem.textContent = "Rp 0";
    if (serviceFeeElem) serviceFeeElem.textContent = "Rp 0";
    if (cartTotalElem) cartTotalElem.textContent = "Rp 0";
    if (cartBtnTotalElem) cartBtnTotalElem.textContent = "Rp 0";
    if (countPillElem) countPillElem.textContent = "0 Menu";
    if (checkoutBtn) checkoutBtn.disabled = true;
    if (btnOpenCheckout) {
      btnOpenCheckout.disabled = true;
      btnOpenCheckout.classList.add("disabled");
    }
    return;
  }

  if (cartEmpty) cartEmpty.style.display = "none";
  if (formSection) formSection.style.display = "block";
  if (serviceFeeElem) serviceFeeElem.textContent = formatRupiah(serviceFee);
  if (countPillElem) countPillElem.textContent = `${totalCount} Menu`;
  if (cartBtnTotalElem) cartBtnTotalElem.textContent = formatRupiah(grandTotal);
  if (checkoutBtn) checkoutBtn.disabled = false;
  if (btnOpenCheckout) {
    btnOpenCheckout.disabled = false;
    btnOpenCheckout.classList.remove("disabled");
  }

  let html = '';
  cart.forEach((item, index) => {
    const itemTotal = item.price * item.qty;
    html += `
      <div class="cart-item">
        <img src="${item.img}" alt="${item.name}" class="cart-item-img">
        <div class="cart-item-info">
          <h4 class="cart-item-name">${item.name}</h4>
          <span class="cart-item-stand"><ion-icon name="storefront-outline"></ion-icon> ${item.stand}</span>
          <div class="cart-item-price">${formatRupiah(itemTotal)}</div>
        </div>
        <div class="cart-qty-ctrl">
          <button type="button" class="btn-qty" onclick="changeCartQty(${index}, -1)" aria-label="Kurang">-</button>
          <span class="cart-qty-num">${item.qty}</span>
          <button type="button" class="btn-qty" onclick="changeCartQty(${index}, 1)" aria-label="Tambah">+</button>
        </div>
        <button type="button" class="btn-remove-item" onclick="removeCartItem(${index})" title="Hapus item">
          <ion-icon name="trash-outline"></ion-icon>
        </button>
      </div>
    `;
  });

  cartList.innerHTML = html;
  if (cartSubtotalElem) cartSubtotalElem.textContent = formatRupiah(subtotal);
  if (cartTotalElem) cartTotalElem.textContent = formatRupiah(grandTotal);
}

window.addToCart = function (id, name, price, stand, img) {
  const existingIndex = cart.findIndex(item => item.id == id);
  if (existingIndex > -1) {
    cart[existingIndex].qty += 1;
  } else {
    cart.push({
      id: Number(id),
      name: name,
      price: Number(price),
      stand: stand,
      img: img,
      qty: 1
    });
  }
  saveCart();
  renderCartDrawer();
  showToast(`<strong>${name}</strong> masuk keranjang!`);
};

window.addToCartFromButton = function (btn) {
  if (!btn) return;
  const id = btn.dataset.menuId;
  const name = btn.dataset.menuName;
  const price = btn.dataset.menuPrice;
  const stand = btn.dataset.menuStand;
  const img = btn.dataset.menuImg;
  window.addToCart(id, name, price, stand, img);
};

window.changeCartQty = function (index, delta) {
  if (cart[index]) {
    cart[index].qty += delta;
    if (cart[index].qty <= 0) {
      cart.splice(index, 1);
    }
    saveCart();
    renderCartDrawer();
  }
};

window.removeCartItem = function (index) {
  if (cart[index]) {
    const removedName = cart[index].name;
    cart.splice(index, 1);
    saveCart();
    renderCartDrawer();
    showToast(`${removedName} dihapus dari keranjang`);
  }
};

window.openCart = function () {
  const drawer = document.querySelector("[data-cart-drawer]");
  const backdrop = document.querySelector("[data-cart-backdrop]");
  if (drawer && backdrop) {
    drawer.classList.add("active");
    backdrop.classList.add("active");
    renderCartDrawer();
  }
};

window.closeCart = function () {
  const drawer = document.querySelector("[data-cart-drawer]");
  const backdrop = document.querySelector("[data-cart-backdrop]");
  if (drawer && backdrop) {
    drawer.classList.remove("active");
    backdrop.classList.remove("active");
  }
};

window.openCheckoutModal = function () {
  if (cart.length === 0) {
    alert("Keranjang belanja masih kosong! Silakan pilih menu terlebih dahulu.");
    return;
  }
  window.closeCart();

  const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
  const serviceFee = 500;
  const grandTotal = subtotal + serviceFee;
  const checkoutTotalElem = document.getElementById("checkoutFinalTotal");
  if (checkoutTotalElem) {
    checkoutTotalElem.textContent = formatRupiah(grandTotal);
  }

  const checkoutBackdrop = document.getElementById("checkoutModalBackdrop");
  if (checkoutBackdrop) {
    checkoutBackdrop.classList.add("active");
  }
};

window.closeCheckoutModal = function () {
  const checkoutBackdrop = document.getElementById("checkoutModalBackdrop");
  if (checkoutBackdrop) {
    checkoutBackdrop.classList.remove("active");
  }
};

window.selectPaymentMethod = function (method, element) {
  document.querySelectorAll('.payment-card-option').forEach(el => el.classList.remove('selected'));
  if (element) {
    element.classList.add('selected');
    const radio = element.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
  }
};

window.toggleDeliveryLocationInput = function (val) {
  const group = document.getElementById("groupDeliveryLocation");
  if (group) {
    group.style.display = val === 'deliver_to_room' ? 'block' : 'none';
  }
};

// ==========================================
// ORDER RECAP & HISTORY HELPERS
// ==========================================
function escapeHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function saveOrderToHistory(orderData) {
  try {
    let orders = JSON.parse(localStorage.getItem('cafetariansa_orders') || '[]');
    const existingIdx = orders.findIndex(o => o.order_number === orderData.order_number);
    const enriched = {
      ...orderData,
      saved_at: new Date().toISOString()
    };
    if (existingIdx >= 0) {
      orders[existingIdx] = { ...orders[existingIdx], ...enriched };
    } else {
      orders.unshift(enriched);
    }
    if (orders.length > 20) orders = orders.slice(0, 20);
    localStorage.setItem('cafetariansa_orders', JSON.stringify(orders));
    updateOrdersBadge();
  } catch (e) {
    console.error("Failed to save order to localStorage:", e);
  }
}

function updateOrdersBadge() {
  try {
    const orders = JSON.parse(localStorage.getItem('cafetariansa_orders') || '[]');
    const badge = document.getElementById("myOrdersCountBadge");
    if (badge) badge.textContent = orders.length;
  } catch (e) {}
}

function renderSuccessOrderRecap(orderData) {
  const orderNumEl = document.getElementById("successOrderNumber");
  const customerNameEl = document.getElementById("successCustomerName");
  const pickupTimeEl = document.getElementById("successPickupTime");
  const pickupTypeEl = document.getElementById("successPickupType");
  const totalAmountEl = document.getElementById("successTotalAmount");
  const paymentMethodEl = document.getElementById("successPaymentMethod");
  const paymentBadgeEl = document.getElementById("successPaymentStatusBadge");
  const subtotalEl = document.getElementById("successSubtotal");
  const serviceFeeEl = document.getElementById("successServiceFee");
  const itemCountEl = document.getElementById("successItemCount");
  const notesRowEl = document.getElementById("successNotesRow");
  const notesTextEl = document.getElementById("successOrderNotes");
  const itemsListEl = document.getElementById("successOrderItemsList");
  const waBtn = document.getElementById("successWhatsAppBtn");
  const paySnapBtn = document.getElementById("successPaySnapBtn");

  if (orderNumEl) orderNumEl.textContent = orderData.order_number || '-';
  if (customerNameEl) customerNameEl.textContent = orderData.customer_name || '-';
  if (pickupTimeEl) pickupTimeEl.textContent = orderData.pickup_time_slot || '-';
  if (pickupTypeEl) {
    pickupTypeEl.textContent = (orderData.pickup_type === 'deliver_to_room')
      ? `Antar (${orderData.delivery_location || 'Ruang Guru/Kelas'})`
      : 'Ambil di Stand Kantin';
  }
  if (totalAmountEl) totalAmountEl.textContent = orderData.formatted_total || `Rp ${Number(orderData.total_amount).toLocaleString('id-ID')}`;

  if (paymentMethodEl) {
    paymentMethodEl.textContent = (orderData.payment_method === 'cash')
      ? 'Bayar Tunai di Stand'
      : `Midtrans (${(orderData.payment_method || 'QRIS').toUpperCase()})`;
  }

  const isPaid = orderData.order_status === 'confirmed' || orderData.payment_status === 'paid' || orderData.is_paid;
  if (paymentBadgeEl) {
    if (isPaid && orderData.payment_method !== 'cash') {
      paymentBadgeEl.textContent = "🟢 LUNAS (Midtrans)";
      paymentBadgeEl.style.background = "#dcfce7";
      paymentBadgeEl.style.color = "#15803d";
    } else if (orderData.payment_method === 'cash') {
      paymentBadgeEl.textContent = "💵 Bayar Tunai di Stand";
      paymentBadgeEl.style.background = "#fef3c7";
      paymentBadgeEl.style.color = "#b45309";
    } else {
      paymentBadgeEl.textContent = "🟡 Menunggu Pembayaran";
      paymentBadgeEl.style.background = "#fee2e2";
      paymentBadgeEl.style.color = "#b91c1c";
    }
  }

  if (subtotalEl) subtotalEl.textContent = orderData.formatted_subtotal || `Rp ${Number(orderData.subtotal || 0).toLocaleString('id-ID')}`;
  if (serviceFeeEl) serviceFeeEl.textContent = orderData.formatted_service_fee || `Rp ${Number(orderData.service_fee || 500).toLocaleString('id-ID')}`;

  const items = orderData.items || [];
  if (itemCountEl) {
    itemCountEl.textContent = items.reduce((sum, it) => sum + (it.quantity || it.qty || 1), 0);
  }

  if (notesRowEl && notesTextEl) {
    if (orderData.notes) {
      notesRowEl.style.display = "block";
      notesTextEl.textContent = orderData.notes;
    } else {
      notesRowEl.style.display = "none";
    }
  }

  // Render items list inside recap
  if (itemsListEl) {
    itemsListEl.innerHTML = "";
    if (items.length === 0) {
      itemsListEl.innerHTML = `<div style="text-align:center; color:#94a3b8; font-size:1.3rem; padding:10px;">Tidak ada rincian item.</div>`;
    } else {
      items.forEach(it => {
        const itemName = it.menu_name || it.name || "Menu Kantin";
        const standName = it.stand_name || it.stand || "Stand Kantin";
        const qty = it.quantity || it.qty || 1;
        const unitPrice = it.unit_price || it.price || 0;
        const itemSubtotal = it.subtotal || (qty * unitPrice);

        const row = document.createElement("div");
        row.style.cssText = "display:flex; justify-content:space-between; align-items:flex-start; padding:8px 0; border-bottom:1px dashed #f1f5f9; font-size:1.3rem;";
        row.innerHTML = `
          <div style="flex:1; padding-right:10px;">
            <div style="font-weight:600; color:#1e293b; line-height:1.3;">${escapeHtml(itemName)}</div>
            <div style="font-size:1.15rem; color:#64748b; margin-top:2px;">
              <span style="color:var(--deep-saffron); font-weight:600;">${escapeHtml(standName)}</span> &bull; 
              <span>${qty}x @ Rp ${Number(unitPrice).toLocaleString('id-ID')}</span>
            </div>
            ${it.notes ? `<div style="font-size:1.1rem; color:#ea580c; font-style:italic;">"${escapeHtml(it.notes)}"</div>` : ''}
          </div>
          <div style="font-weight:700; color:#0f172a; white-space:nowrap;">
            Rp ${Number(itemSubtotal).toLocaleString('id-ID')}
          </div>
        `;
        itemsListEl.appendChild(row);
      });
    }
  }

  // WhatsApp message generator
  let waMsg = `*KONFIRMASI PESANAN KANTIN SMKN 1 CIOMAS*\n`;
  waMsg += `━━━━━━━━━━━━━━━━━━━━━\n`;
  waMsg += `🔖 *Kode Pesanan:* ${orderData.order_number}\n`;
  waMsg += `👤 *Pemesan:* ${orderData.customer_name} (${orderData.customer_type || 'Siswa'})\n`;
  waMsg += `⏰ *Jadwal Ambil:* ${orderData.pickup_time_slot}\n`;
  waMsg += `📦 *Pengambilan:* ${orderData.pickup_type === 'deliver_to_room' ? 'Antar ke ' + (orderData.delivery_location || 'Ruangan') : 'Ambil di Stand'}\n`;
  waMsg += `💳 *Metode Bayar:* ${(orderData.payment_method || '').toUpperCase()} (${isPaid ? 'LUNAS' : 'MENUNGGU PEMBAYARAN'})\n`;
  waMsg += `💰 *TOTAL BAYAR:* *${orderData.formatted_total || 'Rp ' + Number(orderData.total_amount).toLocaleString('id-ID')}*\n`;
  waMsg += `━━━━━━━━━━━━━━━━━━━━━\n`;
  waMsg += `📋 *Rincian Rekap Menu:*\n`;
  items.forEach((it, idx) => {
    const qty = it.quantity || it.qty || 1;
    const name = it.menu_name || it.name;
    const stand = it.stand_name || it.stand || 'Stand';
    const sub = it.subtotal || (qty * (it.unit_price || it.price || 0));
    waMsg += `${idx + 1}. ${name} (${qty}x) - ${stand} [Rp ${Number(sub).toLocaleString('id-ID')}]\n`;
  });
  if (orderData.notes) {
    waMsg += `📝 *Catatan:* ${orderData.notes}\n`;
  }
  waMsg += `━━━━━━━━━━━━━━━━━━━━━\n`;
  waMsg += isPaid 
    ? `_Pembayaran telah lunas melalui Midtrans. Mohon disiapkan sebelum jam istirahat. Terima kasih!_ 🙏` 
    : `_Pesanan tercatat, pembayaran akan diselesaikan saat pengambilan di stand._ 🙏`;

  const kantinPhone = "6281234567890";
  if (waBtn) {
    waBtn.href = `https://api.whatsapp.com/send?phone=${kantinPhone}&text=${encodeURIComponent(waMsg)}`;
  }

  if (paySnapBtn) {
    if (!isPaid && orderData.snap_token) {
      paySnapBtn.style.display = "flex";
      paySnapBtn.onclick = function() {
        if (typeof window.snap !== 'undefined' && typeof window.snap.pay === 'function') {
          window.snap.pay(orderData.snap_token, {
            onSuccess: function(res) {
              handleMidtransSuccess(orderData, res);
            },
            onPending: function(res) {
              showToast("Menunggu pembayaran diselesaikan.");
            },
            onError: function(err) {
              alert("Pembayaran Midtrans gagal atau dibatalkan.");
            },
            onClose: function() {
              showToast("Pembayaran belum diselesaikan.");
            }
          });
        }
      };
    } else {
      paySnapBtn.style.display = "none";
    }
  }
}

function handleMidtransSuccess(orderData, snapResult) {
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

  // Synchronize payment status with backend API
  fetch(`/api/orders/${orderData.order_number}/confirm-payment`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify(snapResult || {})
  })
  .then(res => res.json())
  .then(result => {
    const finalData = (result && result.data) ? result.data : orderData;
    if (!finalData.items || finalData.items.length === 0) {
      finalData.items = orderData.items;
    }
    finalData.is_paid = true;
    finalData.order_status = 'confirmed';
    finalData.payment_status = 'paid';

    // Clear cart now that payment is confirmed
    cart = [];
    saveCart();
    renderCartDrawer();
    window.closeCart();
    window.closeCheckoutModal();

    saveOrderToHistory(finalData);
    renderSuccessOrderRecap(finalData);
    window.openOrderSuccessModal();
    showToast("Pembayaran Midtrans Berhasil! Pesanan sedang disiapkan.");
  })
  .catch(err => {
    console.error("Failed to sync confirm payment:", err);
    // Client fallback
    orderData.is_paid = true;
    orderData.order_status = 'confirmed';
    orderData.payment_status = 'paid';
    cart = [];
    saveCart();
    renderCartDrawer();
    window.closeCart();
    window.closeCheckoutModal();
    saveOrderToHistory(orderData);
    renderSuccessOrderRecap(orderData);
    window.openOrderSuccessModal();
    showToast("Pembayaran Midtrans Berhasil!");
  });
}

// ==========================================
// SUBMIT ORDER TO LARAVEL DATABASE API
// ==========================================
window.submitCanteenOrder = function (event) {
  if (event) event.preventDefault();

  if (cart.length === 0) {
    alert("Keranjang belanja masih kosong! Silakan pilih menu terlebih dahulu.");
    return;
  }

  const nameInput = document.getElementById("orderCustomerName");
  const phoneInput = document.getElementById("orderCustomerPhone");
  const typeInput = document.getElementById("orderCustomerType");
  const pickupInput = document.getElementById("orderPickupType");
  const locationInput = document.getElementById("orderDeliveryLocation");
  const timeInput = document.getElementById("orderPickupTime");
  const notesInput = document.getElementById("orderNotesText");
  const submitBtn = document.getElementById("btnSubmitOrder");
  const submitBtnText = document.getElementById("btnSubmitOrderText");

  const name = nameInput ? nameInput.value.trim() : "";
  const phone = phoneInput ? phoneInput.value.trim() : "";
  const customerType = typeInput ? typeInput.value : "student";
  const pickupType = pickupInput ? pickupInput.value : "pickup_at_stand";
  const deliveryLocation = locationInput ? locationInput.value.trim() : "";
  const pickupTime = timeInput ? timeInput.value : "Istirahat 1 (09:45 - 10:15 WIB)";

  const selectedRadio = document.querySelector('input[name="payment_method_choice"]:checked');
  const paymentMethod = selectedRadio ? selectedRadio.value : "qris";
  const notes = notesInput ? notesInput.value.trim() : "";

  if (!name) {
    alert("Mohon isi Nama Pemesan (Nama Siswa / Guru)!");
    if (nameInput) nameInput.focus();
    return;
  }

  if (!phone) {
    alert("Mohon masukkan Nomor WhatsApp aktif untuk konfirmasi pesanan!");
    if (phoneInput) phoneInput.focus();
    return;
  }

  if (pickupType === 'deliver_to_room' && !deliveryLocation) {
    alert("Mohon masukkan nama Ruang Kelas atau Ruang Guru untuk lokasi pengantaran!");
    if (locationInput) locationInput.focus();
    return;
  }

  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

  // Take snapshot of cart items for reliable recap
  const cartSnapshot = cart.map(item => ({
    menu_id: item.id,
    name: item.name,
    menu_name: item.name,
    stand_name: item.stand,
    unit_price: item.price,
    quantity: item.qty,
    qty: item.qty,
    subtotal: item.price * item.qty,
    notes: null
  }));

  const orderPayload = {
    customer_name: name,
    customer_phone: phone,
    customer_type: customerType,
    pickup_type: pickupType,
    delivery_location: deliveryLocation || null,
    pickup_time_slot: pickupTime,
    payment_method: paymentMethod,
    notes: notes || null,
    items: cartSnapshot.map(item => ({
      menu_id: item.menu_id,
      quantity: item.quantity,
      notes: null
    }))
  };

  // Button loading state
  if (submitBtn) submitBtn.disabled = true;
  if (submitBtnText) submitBtnText.textContent = (paymentMethod === 'cash') ? "Membuat Pesanan..." : "Menghubungkan Midtrans...";

  fetch('/api/orders', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify(orderPayload)
  })
  .then(response => {
    if (!response.ok) {
      return response.json().then(err => { throw err; });
    }
    return response.json();
  })
  .then(result => {
    if (!result.success) {
      throw new Error(result.message || "Gagal membuat pesanan.");
    }

    const data = result.data;
    if (!data.items || data.items.length === 0) {
      data.items = cartSnapshot;
    }

    // ===============================================
    // JIKA PEMBAYARAN TUNAI (CASH DI STAND)
    // ===============================================
    if (paymentMethod === 'cash') {
      // 1. Kosongkan keranjang
      cart = [];
      saveCart();
      renderCartDrawer();
      window.closeCart();
      window.closeCheckoutModal();

      // 2. Simpan riwayat pesanan
      saveOrderToHistory({ ...data, is_paid: false, order_status: 'confirmed' });

      // 3. Tampilkan modal sukses struk & rekap
      renderSuccessOrderRecap(data);
      window.openOrderSuccessModal();
      showToast("Pesanan berhasil dibuat! Silakan bayar tunai saat pengambilan.");
      return;
    }

    // ===============================================
    // JIKA PEMBAYARAN ONLINE (MIDTRANS: QRIS, VA)
    // JANGAN KOSONGKAN KERANJANG DULU & JANGAN BUKA MODAL SUKSES DULU!
    // TUNGGU SAMPAI USER SELESAI BAYAR!
    // ===============================================
    if (data.snap_token) {
      if (typeof window.snap !== 'undefined' && typeof window.snap.pay === 'function') {
        window.snap.pay(data.snap_token, {
          onSuccess: function (res) {
            console.log("Midtrans payment success:", res);
            handleMidtransSuccess(data, res);
          },
          onPending: function (res) {
            console.log("Midtrans payment pending:", res);
            // Simpan sebagai pesanan menunggu bayar di history
            saveOrderToHistory({ ...data, is_paid: false, order_status: 'pending_payment' });
            alert("Pesanan tercatat dengan status Menunggu Pembayaran. Silakan selesaikan pembayaran agar pesanan segera dibuat oleh stand kantin.");
            if (submitBtn) submitBtn.disabled = false;
            if (submitBtnText) submitBtnText.textContent = "Konfirmasi & Pesan";
          },
          onError: function (err) {
            console.error("Midtrans payment error:", err);
            if (data && data.order_number) {
              fetch(`/api/orders/${data.order_number}/cancel`, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': csrfToken
                }
              }).catch(e => console.error("Cancel order error:", e));
            }
            alert("Pembayaran Midtrans dibatalkan atau gagal. Keranjang belanja Anda tetap aman.");
            if (submitBtn) submitBtn.disabled = false;
            if (submitBtnText) submitBtnText.textContent = "Konfirmasi & Pesan";
          },
          onClose: function () {
            console.log("Snap modal closed by user without paying.");
            if (data && data.order_number) {
              fetch(`/api/orders/${data.order_number}/cancel`, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': csrfToken
                }
              }).catch(e => console.error("Cancel order error:", e));
            }
            alert("⚠️ Pembayaran dibatalkan. Pesanan yang belum dibayar telah otomatis dibatalkan agar tidak menumpuk di dashboard. Keranjang belanja masih tersimpan, silakan pilih metode lain (seperti BCA / Tunai) jika ingin melanjutkan.");
            if (submitBtn) submitBtn.disabled = false;
            if (submitBtnText) submitBtnText.textContent = "Konfirmasi & Pesan";
          }
        });
      } else if (data.redirect_url) {
        window.open(data.redirect_url, '_blank');
      } else {
        alert("Gagal memuat gateway pembayaran Midtrans.");
      }
    } else {
      alert("Token pembayaran tidak ditemukan. Silakan coba kembali.");
    }
  })
  .catch(error => {
    let msg = "Terjadi kesalahan saat memproses pesanan.";
    if (error && error.errors) {
      msg = Object.values(error.errors).flat().join('\n');
    } else if (error && error.message) {
      msg = error.message;
    }
    alert(msg);
  })
  .finally(() => {
    if (submitBtn) submitBtn.disabled = false;
    if (submitBtnText) submitBtnText.textContent = "Konfirmasi & Pesan";
  });
};

window.openOrderSuccessModal = function () {
  const backdrop = document.getElementById("orderSuccessBackdrop");
  if (backdrop) {
    backdrop.classList.add("active");
  }
};

window.closeOrderSuccessModal = function () {
  const backdrop = document.getElementById("orderSuccessBackdrop");
  if (backdrop) {
    backdrop.classList.remove("active");
  }
};

// ==========================================
// NOTIFICATIONS & REKAP PESANAN BOTTOM SHEET
// ==========================================
window.switchOrdersSheetTab = function (tabName) {
  const ordersTab = document.getElementById("tabContentMyOrders");
  const infoTab = document.getElementById("tabContentCanteenInfo");
  const btnOrders = document.getElementById("tabBtnMyOrders");
  const btnInfo = document.getElementById("tabBtnCanteenInfo");

  if (tabName === 'orders') {
    if (ordersTab) ordersTab.style.display = "block";
    if (infoTab) infoTab.style.display = "none";
    if (btnOrders) {
      btnOrders.style.background = "var(--deep-saffron)";
      btnOrders.style.color = "#fff";
    }
    if (btnInfo) {
      btnInfo.style.background = "#f1f5f9";
      btnInfo.style.color = "#64748b";
    }
  } else {
    if (ordersTab) ordersTab.style.display = "none";
    if (infoTab) {
      infoTab.style.display = "flex";
    }
    if (btnOrders) {
      btnOrders.style.background = "#f1f5f9";
      btnOrders.style.color = "#64748b";
    }
    if (btnInfo) {
      btnInfo.style.background = "var(--deep-saffron)";
      btnInfo.style.color = "#fff";
    }
  }
};

window.renderRecentOrders = function () {
  const container = document.getElementById("recentOrdersListContainer");
  const badge = document.getElementById("myOrdersCountBadge");
  if (!container) return;

  let orders = [];
  try {
    orders = JSON.parse(localStorage.getItem('cafetariansa_orders') || '[]');
  } catch (e) {
    orders = [];
  }

  if (badge) badge.textContent = orders.length;

  if (orders.length === 0) {
    container.innerHTML = `
      <div style="text-align:center; padding:30px 10px; color:#64748b;">
        <div style="width:64px; height:64px; border-radius:50%; background:#f8fafc; border:1px dashed #cbd5e1; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; color:#94a3b8; font-size:2.8rem;">
          <ion-icon name="bag-handle-outline"></ion-icon>
        </div>
        <p style="font-size:1.5rem; font-weight:700; color:#1e293b; margin-bottom:4px;">Belum Ada Riwayat Pesanan</p>
        <p style="font-size:1.25rem; color:#64748b; margin-bottom:14px;">Kamu belum melakukan pemesanan makanan atau minuman hari ini.</p>
        <button type="button" class="btn" onclick="closeNotificationsSheet(); window.location.hash='#food-menu';" style="margin:0 auto; padding:8px 18px; border-radius:30px; font-size:1.3rem;">
          Pesan Menu Kantin Sekarang
        </button>
      </div>
    `;
    return;
  }

  container.innerHTML = "";
  orders.forEach((ord, index) => {
    const isPaid = ord.order_status === 'confirmed' || ord.payment_status === 'paid' || ord.is_paid;
    const isCash = ord.payment_method === 'cash';
    let statusText = "🟢 Lunas & Disiapkan";
    let statusBg = "#dcfce7";
    let statusColor = "#15803d";

    if (isCash) {
      statusText = "💵 Bayar Tunai di Stand";
      statusBg = "#fef3c7";
      statusColor = "#b45309";
    } else if (!isPaid) {
      statusText = "🟡 Menunggu Pembayaran";
      statusBg = "#fee2e2";
      statusColor = "#b91c1c";
    }

    const items = ord.items || [];
    let itemsSummaryHtml = "";
    items.forEach(it => {
      const name = it.menu_name || it.name || "Menu";
      const qty = it.quantity || it.qty || 1;
      const sub = it.subtotal || (qty * (it.unit_price || it.price || 0));
      itemsSummaryHtml += `
        <div style="display:flex; justify-content:space-between; font-size:1.25rem; color:#334155; margin-bottom:2px;">
          <span>${qty}x ${escapeHtml(name)}</span>
          <span style="font-weight:600;">Rp ${Number(sub).toLocaleString('id-ID')}</span>
        </div>
      `;
    });

    const card = document.createElement("div");
    card.style.cssText = "background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:14px; box-shadow:0 2px 6px rgba(0,0,0,0.03);";
    card.innerHTML = `
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; border-bottom:1px solid #f1f5f9; padding-bottom:6px;">
        <div>
          <span style="font-family:monospace; font-weight:700; font-size:1.35rem; color:var(--deep-saffron);">${escapeHtml(ord.order_number)}</span>
          <div style="font-size:1.15rem; color:#64748b;">${escapeHtml(ord.pickup_time_slot || '')}</div>
        </div>
        <span style="font-size:1.15rem; font-weight:700; padding:2px 8px; border-radius:12px; background:${statusBg}; color:${statusColor};">
          ${statusText}
        </span>
      </div>

      <div style="margin:8px 0; background:#f8fafc; border-radius:8px; padding:8px 10px;">
        ${itemsSummaryHtml}
        <div style="border-top:1px dashed #cbd5e1; margin-top:6px; padding-top:6px; display:flex; justify-content:space-between; font-weight:700; font-size:1.35rem; color:#0f172a;">
          <span>Total:</span>
          <span style="color:var(--cinnabar);">${ord.formatted_total || 'Rp ' + Number(ord.total_amount).toLocaleString('id-ID')}</span>
        </div>
      </div>

      <div style="display:flex; gap:8px; margin-top:10px;">
        ${(!isPaid && !isCash && ord.snap_token) ? `
          <button type="button" class="btn" onclick="retrySnapPayment('${escapeHtml(ord.order_number)}')" style="flex:1; padding:6px 10px; font-size:1.2rem; border-radius:20px; background:#ff9d2d; color:#fff;">
            💳 Bayar Sekarang
          </button>
        ` : ''}
        <button type="button" class="btn" onclick="viewOrderRecapDetail(${index})" style="flex:1; padding:6px 10px; font-size:1.2rem; border-radius:20px; background:#f1f5f9; color:#475569;">
          📋 Rekap Lengkap
        </button>
      </div>
    `;
    container.appendChild(card);
  });
};

window.viewOrderRecapDetail = function (index) {
  try {
    const orders = JSON.parse(localStorage.getItem('cafetariansa_orders') || '[]');
    if (orders[index]) {
      closeNotificationsSheet();
      renderSuccessOrderRecap(orders[index]);
      window.openOrderSuccessModal();
    }
  } catch (e) {
    console.error(e);
  }
};

window.retrySnapPayment = function (orderNumber) {
  try {
    const orders = JSON.parse(localStorage.getItem('cafetariansa_orders') || '[]');
    const ord = orders.find(o => o.order_number === orderNumber);
    if (ord && ord.snap_token && typeof window.snap !== 'undefined' && typeof window.snap.pay === 'function') {
      window.snap.pay(ord.snap_token, {
        onSuccess: function (res) {
          handleMidtransSuccess(ord, res);
        },
        onPending: function (res) {
          showToast("Menunggu pembayaran diselesaikan.");
        },
        onError: function (err) {
          alert("Pembayaran gagal atau dibatalkan.");
        },
        onClose: function () {
          showToast("Pembayaran belum diselesaikan.");
        }
      });
    } else {
      alert("Token pembayaran tidak ditemukan. Silakan buat pesanan baru.");
    }
  } catch (e) {
    console.error(e);
  }
};

window.openNotificationsSheet = function () {
  const sheet = document.getElementById("notifSheet");
  const backdrop = document.getElementById("notifSheetBackdrop");
  renderRecentOrders();
  if (sheet && backdrop) {
    sheet.classList.add("active");
    backdrop.classList.add("active");
  }
};

window.closeNotificationsSheet = function () {
  const sheet = document.getElementById("notifSheet");
  const backdrop = document.getElementById("notifSheetBackdrop");
  if (sheet && backdrop) {
    sheet.classList.remove("active");
    backdrop.classList.remove("active");
  }
};

window.openBookmarksSheet = function () {
  const sheet = document.getElementById("bookmarkSheet");
  const backdrop = document.getElementById("bookmarkSheetBackdrop");
  if (sheet && backdrop) {
    sheet.classList.add("active");
    backdrop.classList.add("active");
  }
};

window.closeBookmarksSheet = function () {
  const sheet = document.getElementById("bookmarkSheet");
  const backdrop = document.getElementById("bookmarkSheetBackdrop");
  if (sheet && backdrop) {
    sheet.classList.remove("active");
    backdrop.classList.remove("active");
  }
};

// Bind on load
document.addEventListener("DOMContentLoaded", function () {
  initAppPreloader();
  initScrollReveal();
  renderCartDrawer();
  updateOrdersBadge();

  // Bind all cart toggle buttons
  const cartToggleBtns = document.querySelectorAll("[data-cart-toggle-btn], [data-cart-open-btn]");
  cartToggleBtns.forEach(btn => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      openCart();
    });
  });

  const cartCloseBtns = document.querySelectorAll("[data-cart-close-btn]");
  cartCloseBtns.forEach(btn => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      closeCart();
    });
  });

  const dockLinks = document.querySelectorAll(".dock-item:not(.dock-item-cart)");
  dockLinks.forEach(btn => {
    btn.addEventListener("click", function () {
      dockLinks.forEach(b => b.classList.remove("active"));
      this.classList.add("active");
    });
  });
});

/**
 * ========================================================
 * PRELOADER & INITIAL PAGE ENTRANCE ANIMATION
 * ========================================================
 */
function initAppPreloader() {
  const preloader = document.getElementById("appPreloader");
  if (!preloader) {
    document.body.classList.add("page-loaded");
    return;
  }

  const statusText = document.getElementById("preloaderStatusText");
  const counterEl = document.getElementById("preloaderCounter");
  const fillEl = document.getElementById("preloaderProgressFill");

  const statuses = [
    "Menghubungkan ke server Kantin SKANIC...",
    "Menyiapkan seblak, gurilem & takoyaki hangat...",
    "Menyegarkan pop ice blender & aneka es...",
    "Memeriksa stok stand makanan kantin...",
    "Kantin SMKN 1 Ciomas siap melayani!"
  ];
  let statusIndex = 0;

  const statusInterval = setInterval(() => {
    statusIndex = (statusIndex + 1) % statuses.length;
    if (statusText) {
      statusText.style.opacity = "0.3";
      setTimeout(() => {
        statusText.textContent = statuses[statusIndex];
        statusText.style.opacity = "1";
      }, 150);
    }
  }, 480);

  // Progressive Percentage Counter & Progress Fill
  let currentPercent = 0;
  const targetPercent = 94;
  const progressTimer = setInterval(() => {
    if (currentPercent < targetPercent) {
      currentPercent += Math.floor(Math.random() * 6) + 4;
      if (currentPercent > targetPercent) currentPercent = targetPercent;
      if (counterEl) counterEl.textContent = currentPercent + "%";
      if (fillEl) fillEl.style.width = currentPercent + "%";
    }
  }, 50);

  const minLoadTime = 1350;
  const startTime = Date.now();
  let dismissed = false;

  function dismissPreloader() {
    if (dismissed) return;
    dismissed = true;
    clearInterval(progressTimer);
    clearInterval(statusInterval);

    // Fast finish to 100%
    if (counterEl) counterEl.textContent = "100%";
    if (fillEl) fillEl.style.width = "100%";
    if (statusText) statusText.textContent = "Selamat Datang di Kantin SKANIC!";

    const elapsed = Date.now() - startTime;
    const remaining = Math.max(200, minLoadTime - elapsed);

    setTimeout(() => {
      preloader.classList.add("fade-out");
      document.body.classList.add("page-loaded");

      // Auto reveal hero section
      const hero = document.getElementById("home");
      if (hero) hero.classList.add("is-revealed");

      setTimeout(() => {
        if (preloader.parentNode) {
          preloader.style.display = "none";
        }
      }, 700);
    }, remaining);
  }

  if (document.readyState === "complete") {
    dismissPreloader();
  } else {
    window.addEventListener("load", dismissPreloader);
    setTimeout(dismissPreloader, 2800);
  }
}

/**
 * ========================================================
 * SCROLL REVEAL & SECTION TRANSITIONS ENGINE
 * ========================================================
 */
function initScrollReveal() {
  // 1. Top reading progress bar tracking
  const scrollProgressBar = document.getElementById("scrollProgressLine");
  if (scrollProgressBar) {
    const updateProgress = () => {
      const scrollTop = window.scrollY || document.documentElement.scrollTop;
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      const progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
      scrollProgressBar.style.width = `${Math.min(100, Math.max(0, progress))}%`;
    };
    window.addEventListener("scroll", updateProgress, { passive: true });
    updateProgress();
  }

  // 2. Ensure all sections are revealed immediately
  const revealElements = document.querySelectorAll(
    "[data-section-reveal], [data-reveal-left], [data-reveal-right], [data-reveal-zoom]"
  );

  revealElements.forEach(el => el.classList.add("is-revealed"));

  if ("IntersectionObserver" in window) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-revealed");
          observer.unobserve(entry.target);
        }
      });
    }, {
      root: null,
      threshold: 0,
      rootMargin: "50px 0px 50px 0px"
    });

    revealElements.forEach(el => revealObserver.observe(el));
  }
}