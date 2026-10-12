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

for (let i = 0; i < navbarLinks.length; i++) {
  navbarLinks[i].addEventListener("click", function () {
    if (navbar && navbar.classList.contains("active")) {
      navbar.classList.toggle("active");
      if (menuToggleBtn) menuToggleBtn.classList.toggle("active");
    }
  });
}

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

  const orderPayload = {
    customer_name: name,
    customer_phone: phone,
    customer_type: customerType,
    pickup_type: pickupType,
    delivery_location: deliveryLocation || null,
    pickup_time_slot: pickupTime,
    payment_method: paymentMethod,
    notes: notes || null,
    items: cart.map(item => ({
      menu_id: item.id,
      quantity: item.qty,
      notes: null
    }))
  };

  // Button loading state
  if (submitBtn) submitBtn.disabled = true;
  if (submitBtnText) submitBtnText.textContent = "Memproses Pesanan...";

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
    if (result.success) {
      const data = result.data;

      // Populate Success Modal
      const orderNumEl = document.getElementById("successOrderNumber");
      const customerNameEl = document.getElementById("successCustomerName");
      const pickupTimeEl = document.getElementById("successPickupTime");
      const pickupTypeEl = document.getElementById("successPickupType");
      const totalAmountEl = document.getElementById("successTotalAmount");
      const paymentMethodEl = document.getElementById("successPaymentMethod");
      const paySnapBtn = document.getElementById("successPaySnapBtn");
      const waBtn = document.getElementById("successWhatsAppBtn");

      if (orderNumEl) orderNumEl.textContent = data.order_number;
      if (customerNameEl) customerNameEl.textContent = data.customer_name;
      if (pickupTimeEl) pickupTimeEl.textContent = data.pickup_time_slot;
      if (pickupTypeEl) pickupTypeEl.textContent = data.pickup_type === 'deliver_to_room' ? `Antar (${data.delivery_location || 'Ruang Guru'})` : 'Ambil di Stand Kantin';
      if (totalAmountEl) totalAmountEl.textContent = data.formatted_total;
      if (paymentMethodEl) {
        paymentMethodEl.textContent = (data.payment_method === 'cash')
          ? 'Bayar Tunai di Stand'
          : `Midtrans (${data.payment_method.toUpperCase()})`;
      }

      // WhatsApp message generator
      let waMsg = `*KONFIRMASI PESANAN KANTIN SMKN 1 CIOMAS*\n`;
      waMsg += `━━━━━━━━━━━━━━━━━━━━━\n`;
      waMsg += `🔖 *Kode Pesanan:* ${data.order_number}\n`;
      waMsg += `👤 *Pemesan:* ${data.customer_name} (${data.customer_type})\n`;
      waMsg += `⏰ *Jadwal Ambil:* ${data.pickup_time_slot}\n`;
      waMsg += `📦 *Pengambilan:* ${data.pickup_type === 'deliver_to_room' ? 'Antar ke ' + (data.delivery_location || 'Ruangan') : 'Ambil di Stand'}\n`;
      waMsg += `💳 *Metode Bayar:* ${data.payment_method.toUpperCase()}\n`;
      waMsg += `💰 *TOTAL BAYAR:* *${data.formatted_total}*\n`;
      waMsg += `━━━━━━━━━━━━━━━━━━━━━\n`;
      waMsg += `📋 *Rincian Menu:*\n`;
      cart.forEach((item, idx) => {
        waMsg += `${idx + 1}. ${item.name} (${item.qty}x) - ${item.stand}\n`;
      });
      if (notes) {
        waMsg += `📝 *Catatan:* ${notes}\n`;
      }
      waMsg += `━━━━━━━━━━━━━━━━━━━━━\n`;
      waMsg += `_Mohon disiapkan sebelum jam istirahat sekolah. Terima kasih!_ 🙏`;

      const kantinPhone = "6281234567890";
      if (waBtn) {
        waBtn.href = `https://api.whatsapp.com/send?phone=${kantinPhone}&text=${encodeURIComponent(waMsg)}`;
      }

      // Close checkout modal & cart
      window.closeCheckoutModal();
      cart = [];
      saveCart();
      renderCartDrawer();
      window.closeCart();

      // Configure Re-pay button if Snap token is present
      if (data.snap_token) {
        if (paySnapBtn) {
          paySnapBtn.style.display = "flex";
          paySnapBtn.onclick = function () {
            if (typeof window.snap !== 'undefined' && typeof window.snap.pay === 'function') {
              window.snap.pay(data.snap_token, {
                onSuccess: function (res) {
                  showToast("Pembayaran berhasil dikonfirmasi!");
                  if (paymentMethodEl) paymentMethodEl.textContent += " (LUNAS)";
                },
                onPending: function (res) {
                  showToast("Menunggu pembayaran diselesaikan.");
                },
                onError: function (err) {
                  alert("Pembayaran Midtrans gagal atau dibatalkan.");
                },
                onClose: function () {
                  console.log("Snap modal closed by user.");
                }
              });
            } else if (data.redirect_url) {
              window.open(data.redirect_url, '_blank');
            }
          };
        }

        // Auto popup Snap window if window.snap is loaded
        if (typeof window.snap !== 'undefined' && typeof window.snap.pay === 'function') {
          window.snap.pay(data.snap_token, {
            onSuccess: function (res) {
              console.log("Midtrans payment success:", res);
              window.openOrderSuccessModal();
              showToast("Pembayaran Midtrans Berhasil!");
              if (paymentMethodEl) paymentMethodEl.textContent += " (LUNAS)";
            },
            onPending: function (res) {
              console.log("Midtrans payment pending:", res);
              window.openOrderSuccessModal();
              showToast("Silakan selesaikan pembayaran sesuai petunjuk.");
            },
            onError: function (err) {
              console.error("Midtrans payment error:", err);
              window.openOrderSuccessModal();
              alert("Pembayaran belum berhasil diselesaikan.");
            },
            onClose: function () {
              window.openOrderSuccessModal();
            }
          });
          return;
        }
      } else {
        if (paySnapBtn) paySnapBtn.style.display = "none";
      }

      // Open Success Modal directly for cash or mock
      window.openOrderSuccessModal();
      showToast("Pesanan berhasil dibuat & tersimpan di database!");
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

// Notification & Bookmarks Bottom Sheets
window.openNotificationsSheet = function () {
  const sheet = document.getElementById("notifSheet");
  const backdrop = document.getElementById("notifSheetBackdrop");
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
  renderCartDrawer();

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