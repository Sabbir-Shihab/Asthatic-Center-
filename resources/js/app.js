import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.data('store', () => ({
  cart: JSON.parse(localStorage.getItem('skinoveda-cart') || '[]'),
  wishlist: JSON.parse(localStorage.getItem('skinoveda-wishlist') || '[]'),
  cartOpen: false, searchOpen: false, mobileOpen: false,
  checkoutOpen: false, checkoutBusy: false, checkoutMessage: '', successMessage: '', bookingOpen: false,
  paymentMethods: [],
  paymentMethodId: '',
  bookingStep: 1, bookingBusy: false,
  get cartCount() { return this.cart.reduce((sum, item) => sum + item.qty, 0); },
  get cartTotal() { return this.cart.reduce((sum, item) => sum + item.price * item.qty, 0); },
  get selectedPayment() {
    const methods = window.svPayments || this.paymentMethods || [];
    const id = this.paymentMethodId || methods[0]?.id || '';
    return methods.find(item => String(item.id) === String(id)) || null;
  },
  openCheckout() {
    const methods = window.svPayments || [];
    this.paymentMethods = methods;
    if (!this.paymentMethodId && methods[0]) this.paymentMethodId = String(methods[0].id);
    this.cartOpen = false;
    this.checkoutOpen = true;
  },
  add(product) { const found = this.cart.find(item => item.id === product.id); found ? found.qty++ : this.cart.push({...product, qty: 1}); this.save(); this.cartOpen = true; },
  remove(id) { this.cart = this.cart.filter(item => item.id !== id); this.save(); },
  toggleWish(id) { this.wishlist = this.wishlist.includes(id) ? this.wishlist.filter(x => x !== id) : [...this.wishlist, id]; localStorage.setItem('skinoveda-wishlist', JSON.stringify(this.wishlist)); },
  save() { localStorage.setItem('skinoveda-cart', JSON.stringify(this.cart)); },
  async checkout(form) {
    this.checkoutBusy = true; this.checkoutMessage = '';
    try {
      const response = await fetch(form.action, {method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content || form.querySelector('[name="_token"]')?.value},body:JSON.stringify({...Object.fromEntries(new FormData(form)),items:this.cart.map(({id,qty})=>({id,qty}))})});
      const raw = await response.text();
      let result;
      try { result = JSON.parse(raw); } catch { throw new Error('Order could not be submitted. Please refresh the page and try again.'); }
      if (!response.ok) throw new Error(result.message || Object.values(result.errors || {}).flat().join(' ') || window.svI18n?.checkoutError || 'Please check your details and try again.');
      this.cart = []; this.save(); this.checkoutMessage = `${result.message} Order ${result.order_number} · ${this.money(result.total)}`; this.successMessage = this.checkoutMessage; this.checkoutOpen = true; this.cartOpen = false;
    } catch (error) { this.checkoutMessage = error.message; }
    this.checkoutBusy = false;
  },
  money(n) { return '৳ ' + Number(n).toLocaleString('en-BD'); },
}));
Alpine.start();
document.documentElement.classList.add('alpine-ready');

const syncActiveNav = () => {
  const links = document.querySelectorAll('[data-nav]');
  if (!links.length) return;

  const path = window.location.pathname.replace(/\/+$/, '') || '/';
  const hash = window.location.hash.replace('#', '');
  let active = '';

  if (path.includes('/treatments')) active = 'treatments';
  else if (path.includes('/shop')) active = 'shop';
  else if (path.includes('/contact')) active = 'contact';
  else if (['treatments', 'wellness', 'about'].includes(hash)) active = hash;

  links.forEach((link) => {
    link.classList.toggle('is-active', link.dataset.nav === active);
  });
};

window.addEventListener('DOMContentLoaded', syncActiveNav);
window.addEventListener('hashchange', syncActiveNav);
document.addEventListener('click', (event) => {
  const link = event.target.closest('[data-nav]');
  if (link) setTimeout(syncActiveNav, 0);
});

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.shop-filter-panel').forEach((panel) => {
    const heading = panel.querySelector(':scope > div:first-child h2');
    if (!heading) return;
    heading.addEventListener('click', () => panel.classList.toggle('is-expanded'));
  });
});
