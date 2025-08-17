<!DOCTYPE html>
<html lang="uz">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
	<title>Buyurtmani tasdiqlash</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
		:root { --primary: #667eea; --accent: #ff6b35; --text: #2c3e50; --bg:#fff; }
		body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif; background:#f6f7fb; color:var(--text); padding-bottom: calc(68px + env(safe-area-inset-bottom)); margin: 0; }
		.footer-actions { padding-bottom: calc( env(safe-area-inset-bottom) + 8px ); }
		.container-sm { max-width: 440px; }
		.card { border:none; border-radius:14px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
		.card-header { background: linear-gradient(135deg, var(--primary), #764ba2); color:#fff; border-radius:14px 14px 0 0; padding:0.75rem 1rem; }
		.card-header h5 { margin:0; font-weight:700; font-size:1rem; }
		.form-label { font-weight:600; font-size:0.9rem; }
		.form-control, .form-select { font-size:0.95rem; padding:0.5rem 0.75rem; border-radius:8px; }
		.summary { background:#f8fafc; border:1px solid #e5e7eb; border-radius:10px; padding:10px; }
		.summary .row { font-size:0.9rem; }
		.btn { font-weight:600; }
		.btn-primary { background: var(--primary); border:none; }
		.btn-primary:hover { filter: brightness(1.05); }
		.btn-secondary { background:#6c757d; border:none; }
		.item { display:flex; justify-content:space-between; align-items:center; font-size:0.9rem; margin-bottom:6px; }
		.small { color:#64748b; }
		/* Sticky footer actions */
		.footer-actions { position: fixed; left: 0; right: 0; bottom: 0; background: #fff; border-top: 1px solid #e5e7eb; padding: 8px 12px; display: flex; gap: 8px; box-shadow: 0 -8px 24px rgba(0,0,0,0.06); }
		.footer-actions .btn { padding: 0.55rem 0.75rem; font-size: 0.95rem; border-radius: 10px; }
	</style>
</head>
<body>
	<div class="container-sm py-3">
		<div class="card">
			<div class="card-header">
				<h5>Buyurtmani tasdiqlash</h5>
			</div>
			<div class="card-body">
				<div id="items"></div>
				<div class="summary mt-2">
					<div class="row"><div class="col">Jami</div><div class="col-auto" id="total">0 so'm</div></div>
				</div>
				<hr>
				<form id="orderForm" class="mt-2">
					<div class="mb-2">
						<label class="form-label">Ismingiz</label>
						<input class="form-control" id="name" required>
					</div>
					<div class="mb-2">
						<label class="form-label">Telefon</label>
						<input class="form-control" id="phone" required>
					</div>
					<div class="mb-2">
						<label class="form-label">Manzil</label>
						<textarea class="form-control" id="address" rows="2" required></textarea>
					</div>
					<div class="mb-2">
						<label class="form-label">To'lov usuli</label>
						<select id="payment" class="form-select">
							<option value="cash" selected>Naqd</option>
							<option value="card">Karta</option>
							<option value="click">Click</option>
							<option value="payme">Payme</option>
						</select>
					</div>
					<div class="mb-2">
						<label class="form-label">Izoh (ixtiyoriy)</label>
						<textarea class="form-control" id="notes" rows="2"></textarea>
					</div>
					<div class="d-flex gap-2 mt-2 d-none d-md-flex">
						<button type="button" class="btn btn-secondary w-50" onclick="window.close()">Bekor</button>
						<button type="submit" class="btn btn-primary w-50">Tasdiqlash</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Sticky footer actions for mobile -->
	<div class="footer-actions d-md-none" style="padding-bottom: calc(env(safe-area-inset-bottom) + 8px);">
		<button type="button" class="btn btn-secondary flex-fill" onclick="window.close()">Bekor</button>
		<button type="button" class="btn btn-primary flex-fill" onclick="document.getElementById('orderForm').requestSubmit()">Tasdiqlash</button>
	</div>
	<script>
	(function(){
		const openerOrigin = window.opener ? window.opener.location.origin : null;
		let preview = null;
		try { preview = JSON.parse(localStorage.getItem('checkout_preview')||'{}'); } catch(e) { preview = {}; }
		const botToken = new URLSearchParams(location.search).get('bot_token') || preview.bot_token || '';
		const itemsDiv = document.getElementById('items');
		const totalDiv = document.getElementById('total');
		function fmt(n){ return (n||0).toLocaleString() + " so'm"; }
		let subtotal = 0;
		if (Array.isArray(preview.items)) {
			preview.items.forEach(it => {
				subtotal += (it.price||0)*(it.quantity||0);
				const row = document.createElement('div');
				row.className = 'item';
				row.innerHTML = `<div><strong>${it.name||''}</strong><div class=\"small\">${it.quantity||0} x ${(it.price||0).toLocaleString()} so'm</div></div><div><strong>${((it.price||0)*(it.quantity||0)).toLocaleString()} so'm</strong></div>`;
				itemsDiv.appendChild(row);
			});
		}
		totalDiv.textContent = fmt(subtotal);
		document.getElementById('orderForm').addEventListener('submit', function(e){
			e.preventDefault();
			const payload = {
				restaurant_id: preview.restaurant_id,
				items: (preview.items||[]).map(x=>({ menu_item_id: x.menu_item_id, quantity: x.quantity, price: x.price })),
				total_amount: preview.total_amount,
				telegram_chat_id: preview.telegram_chat_id||null,
				bot_token: botToken,
				customer_name: document.getElementById('name').value,
				customer_phone: document.getElementById('phone').value,
				customer_address: document.getElementById('address').value,
				customer_notes: document.getElementById('notes').value,
				payment_method: document.getElementById('payment').value
			};
			fetch('/api/orders', { method:'POST', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify(payload) })
			.then(r=>r.json()).then(data=>{
				if (data && data.success) {
					try { window.opener && openerOrigin && window.opener.postMessage({ type:'order_placed', order_id:data.order_id }, openerOrigin); } catch(_){}
					window.close();
				} else {
					alert("Xatolik yuz berdi. Iltimos, qayta urinib ko'ring.");
				}
			}).catch(()=>alert("Xatolik yuz berdi. Iltimos, qayta urinib ko'ring."));
		});
	})();
	</script>
</body>
</html> 