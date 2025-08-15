/* Production console silencing */
if (typeof window !== 'undefined' && !document.documentElement.hasAttribute('data-debug')) {
	/* no-op: keep console in dev; you can add flags later if needed */
}

// Telegram init
const tg = window.Telegram ? window.Telegram.WebApp : null;
if (tg) {
	tg.ready && tg.ready();
	tg.expand && tg.expand();
}

document.body.classList.add('telegram-theme');

// State
let cart = {};
let currentCategory = parseInt(document.body.getAttribute('data-first-category-id') || '1', 10);

// Category switching
window.switchCategory = function(categoryId, el) {
	// Hide all category contents
	document.querySelectorAll('.category-content').forEach(content => content.classList.add('d-none'));
	// Remove active class from all tabs
	document.querySelectorAll('.category-tab').forEach(tab => tab.classList.remove('active'));
	// Show selected category content
	document.getElementById(`cat-content-${categoryId}`).classList.remove('d-none');
	// Add active class to clicked tab
	if (el) el.classList.add('active');
	currentCategory = categoryId;
};

// Quantity management
window.changeQuantity = function(itemId, change) {
	const currentQty = cart[itemId] || 0;
	const newQty = Math.max(0, currentQty + change);
	if (newQty === 0) delete cart[itemId]; else cart[itemId] = newQty;
	document.getElementById(`qty-${itemId}`).textContent = newQty;
	updateCart();
};

function updateCart() {
	let totalPrice = 0; let totalItems = 0;
	Object.keys(cart).forEach(itemId => {
		const qty = cart[itemId];
		const price = parseFloat(document.querySelector(`[data-item-id="${itemId}"]`).dataset.price);
		totalPrice += qty * price; totalItems += qty;
	});
	document.getElementById('cart-total-price').textContent = totalPrice.toLocaleString();
	document.getElementById('cart-total-items').textContent = totalItems;
	const checkoutBtn = document.getElementById('checkout-btn');
	checkoutBtn.disabled = totalItems <= 0;
}

window.proceedToCheckout = function() {
	if (Object.keys(cart).length === 0) return;
	let checkoutHtml = ''; let totalPrice = 0;
	Object.keys(cart).forEach(itemId => {
		const qty = cart[itemId];
		const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
		const name = itemElement.querySelector('.menu-item-title').textContent;
		const price = parseFloat(itemElement.dataset.price);
		const itemTotal = qty * price; totalPrice += itemTotal;
		checkoutHtml += `<div class="d-flex justify-content-between align-items-center mb-2">
			<div><strong>${name}</strong><br><small class="text-muted">${qty} x ${price.toLocaleString()} so'm</small></div>
			<strong>${itemTotal.toLocaleString()} so'm</strong>
		</div>`;
	});
	document.getElementById('checkout-items').innerHTML = checkoutHtml;
	document.getElementById('checkout-total').textContent = totalPrice.toLocaleString() + " so'm";
	const modal = new bootstrap.Modal(document.getElementById('checkoutModal'));
	modal.show();
};

window.confirmOrder = function() {
	if (Object.keys(cart).length === 0) return;
	const botToken = document.querySelector('meta[name="bot-token"]').content || '';
	const orderData = {
		restaurant_id: parseInt(document.body.getAttribute('data-restaurant-id') || '0', 10) || undefined,
		items: Object.keys(cart).map(itemId => ({ menu_item_id: parseInt(itemId, 10), quantity: cart[itemId], price: parseFloat(document.querySelector(`[data-item-id="${itemId}"]`).dataset.price) })),
		total_amount: Object.keys(cart).reduce((total, id) => total + cart[id] * parseFloat(document.querySelector(`[data-item-id="${id}"]`).dataset.price), 0),
		telegram_chat_id: (tg && tg.initDataUnsafe && tg.initDataUnsafe.user && tg.initDataUnsafe.user.id) ? tg.initDataUnsafe.user.id : null,
		bot_token: botToken
	};
	fetch('/api/orders', {
		method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify(orderData)
	}).then(r => r.json()).then(data => {
		if (data.success) {
			cart = {}; updateCart();
			document.querySelectorAll('[id^="qty-"]').forEach(el => el.textContent = '0');
			bootstrap.Modal.getInstance(document.getElementById('checkoutModal')).hide();
			if (tg && tg.showAlert) tg.showAlert('Buyurtmangiz muvaffaqiyatli qabul qilindi! 🎉');
			if (tg && tg.sendData) tg.sendData(JSON.stringify({ action: 'order_placed', order_id: data.order_id }));
		} else {
			if (tg && tg.showAlert) tg.showAlert("Xatolik yuz berdi. Iltimos, qaytadan urinib ko'ring.");
		}
	}).catch(() => { if (tg && tg.showAlert) tg.showAlert("Xatolik yuz berdi. Iltimos, qaytadan urinib ko'ring."); });
};

function addSmoothScrolling() {
	const scrollToTopBtn = document.createElement('button');
	scrollToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
	scrollToTopBtn.className = 'scroll-to-top';
	scrollToTopBtn.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
	document.body.appendChild(scrollToTopBtn);
	window.addEventListener('scroll', () => { scrollToTopBtn.style.display = window.pageYOffset > 300 ? 'block' : 'none'; });
}

function addPerformanceMonitoring() {
	if (!('performance' in window)) return;
	try {
		const observer = new PerformanceObserver((list) => {
			list.getEntries().forEach(entry => { if (entry.entryType === 'resource' && entry.name.includes('img/menu')) { /* no-op */ } });
		});
		observer.observe({ entryTypes: ['resource'] });
	} catch (_e) { /* ignore */ }
}

function initializeLazyLoading() {
	if (!('IntersectionObserver' in window)) return;
	const imageObserver = new IntersectionObserver((entries, observer) => {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				const img = entry.target;
				if (img.dataset.src) {
					preloadImage(img.dataset.src).then(() => { img.src = img.dataset.src; img.removeAttribute('data-src'); observer.unobserve(img); }).catch(() => { img.src = img.dataset.src; img.removeAttribute('data-src'); observer.unobserve(img); });
				}
			}
		});
	}, { rootMargin: '50px 0px', threshold: 0.1 });
	document.querySelectorAll('img[data-src]').forEach(img => imageObserver.observe(img));
}

function preloadImage(src) { return new Promise((res, rej) => { const img = new Image(); img.onload = () => res(); img.onerror = () => rej(); img.src = src; }); }

function handleImageError(img) {
	img.style.display = 'none';
	const placeholder = img.nextElementSibling;
	if (placeholder && placeholder.classList.contains('menu-item-image-placeholder')) placeholder.style.display = 'flex';
	const container = img.closest('.image-container');
	if (container) {
		container.classList.remove('loaded');
		showImageError(container); addRetryButton(container, img);
		const fallback = container.querySelector('.menu-item-image-fallback'); if (fallback) fallback.style.display = 'block';
	}
}
window.handleImageError = handleImageError;

function addRetryButton(container, img) {
	let retryBtn = container.querySelector('.retry-button');
	if (!retryBtn) {
		retryBtn = document.createElement('button');
		retryBtn.className = 'retry-button';
		retryBtn.innerHTML = '<i class="fas fa-redo"></i> Qayta urinish';
		retryBtn.onclick = () => retryImageLoad(img);
		container.appendChild(retryBtn);
	}
	retryBtn.style.display = 'block';
}

function retryImageLoad(img) {
	const container = img.closest('.image-container');
	if (container) {
		container.classList.remove('loaded'); hideImageError(container);
		const retryBtn = container.querySelector('.retry-button'); if (retryBtn) retryBtn.style.display = 'none';
		if (img.dataset.src) img.src = img.dataset.src; else img.src = img.src;
	}
}
window.retryImageLoad = retryImageLoad;

function handleImageLoad(img) {
	img.style.display = 'block';
	const placeholder = img.nextElementSibling; if (placeholder && placeholder.classList.contains('menu-item-image-placeholder')) placeholder.style.display = 'none';
	const container = img.closest('.image-container');
	if (container) {
		container.classList.add('loaded');
		const fallback = container.querySelector('.menu-item-image-fallback'); if (fallback) fallback.style.display = 'none';
		hideImageError(container); hideRetryButton(container);
	}
}
window.handleImageLoad = handleImageLoad;

function hideRetryButton(container) { const retryBtn = container.querySelector('.retry-button'); if (retryBtn) retryBtn.style.display = 'none'; }
function cleanupImageHandling() { document.querySelectorAll('.image-container .menu-item-image').forEach(img => { img.removeEventListener('error', handleImageError); img.removeEventListener('load', handleImageLoad); }); }
window.addEventListener('beforeunload', cleanupImageHandling);

document.addEventListener('visibilitychange', function() { /* could pause/resume heavy ops if needed */ });

function showImageError(container) {
	let errorDiv = container.querySelector('.image-error');
	if (!errorDiv) { errorDiv = document.createElement('div'); errorDiv.className = 'image-error'; errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Rasm yuklanmadi'; container.appendChild(errorDiv); }
	errorDiv.style.display = 'block';
}
function hideImageError(container) { const errorDiv = container.querySelector('.image-error'); if (errorDiv) errorDiv.style.display = 'none'; }

function addFocusManagement() {
	const menuItems = document.querySelectorAll('.menu-item');
	menuItems.forEach(item => { item.setAttribute('tabindex', '0'); item.setAttribute('role', 'button'); item.setAttribute('aria-label', `Add ${item.querySelector('.menu-item-title').textContent} to cart`); });
	menuItems.forEach(item => {
		item.addEventListener('focus', function() { this.style.outline = `2px solid ${getComputedStyle(document.documentElement).getPropertyValue('--primary-color')}`; });
		item.addEventListener('blur', function() { this.style.outline = 'none'; });
	});
}

function addErrorBoundary() {
	window.addEventListener('error', function() { if (tg && tg.showAlert) tg.showAlert('Xatolik yuz berdi. Iltimos, sahifani yangilang.'); });
	window.addEventListener('unhandledrejection', function() { if (tg && tg.showAlert) tg.showAlert('Xatolik yuz berdi. Iltimos, sahifani yangilang.'); });
}

function addOfflineSupport() {
	window.addEventListener('online', function() { if (tg && tg.showAlert) tg.showAlert('Internet aloqasi tiklandi! 🌐'); });
	window.addEventListener('offline', function() { if (tg && tg.showAlert) tg.showAlert("Internet aloqasi yo'q! 📡"); });
}

function addServiceWorkerSupport() {
	if ('serviceWorker' in navigator) { navigator.serviceWorker.register('/sw.js').catch(() => {}); }
}

function addAnalytics() {
	document.addEventListener('click', function(e) {
		if (e.target.closest('.menu-item')) { /* track click */ }
		if (e.target.closest('.checkout-btn')) { /* track click */ }
	});
	let maxScroll = 0; window.addEventListener('scroll', function() { const scrollPercent = Math.round((window.pageYOffset / (document.body.scrollHeight - window.innerHeight)) * 100); if (scrollPercent > maxScroll) { maxScroll = scrollPercent; } });
}

document.addEventListener('DOMContentLoaded', function() {
	updateCart();
	initializeImageHandling();
	initializeLazyLoading();
	addPerformanceMonitoring();
	addSmoothScrolling();
	addKeyboardNavigation();
	addFocusManagement();
	addErrorBoundary();
	addOfflineSupport();
	addServiceWorkerSupport();
	addAnalytics();
	addFinalOptimizations();
});

function addKeyboardNavigation() {
	document.addEventListener('keydown', function(e) {
		if (e.key === 'Escape') {
			const modal = document.querySelector('.modal.show'); if (modal) { const modalInstance = bootstrap.Modal.getInstance(modal); if (modalInstance) modalInstance.hide(); }
		}
		if (e.key === 'Enter' && document.activeElement.classList.contains('menu-item')) {
			const itemId = document.activeElement.dataset.itemId; if (itemId) changeQuantity(parseInt(itemId, 10), 1);
		}
	});
}

function addFinalOptimizations() {
	const criticalImages = document.querySelectorAll('img[data-src]');
	criticalImages.forEach(img => { if (img.dataset.src) { const link = document.createElement('link'); link.rel = 'preload'; link.as = 'image'; link.href = img.dataset.src; document.head.appendChild(link); } });
	if (!document.querySelector('meta[name="viewport"]')) { const viewport = document.createElement('meta'); viewport.name = 'viewport'; viewport.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no'; document.head.appendChild(viewport); }
	if (!document.querySelector('meta[name="theme-color"]')) { const themeColor = document.createElement('meta'); themeColor.name = 'theme-color'; themeColor.content = getComputedStyle(document.documentElement).getPropertyValue('--primary-color'); document.head.appendChild(themeColor); }
}

function initializeImageHandling() {
	document.querySelectorAll('.menu-item-image').forEach(img => {
		img.addEventListener('error', function() { handleImageError(this); });
		img.addEventListener('load', function() { handleImageLoad(this); });
	});
} 