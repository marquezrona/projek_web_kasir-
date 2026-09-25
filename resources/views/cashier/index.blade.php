<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir - Toko Sembako Jazzel</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <style>
        :root { --teal:#07536a; --teal-deep:#063b4d; --coral:#e96357; --ink:#17212b; --muted:#77848c; --line:#dfe7e8; --canvas:#f2f5f6; --green:#168354; --yellow:#f2b700; }
        * { box-sizing:border-box; }
        body { margin:0; color:var(--ink); background:var(--canvas); font-family:"Segoe UI",Tahoma,sans-serif; }
        button,input,select { font:inherit; }
        .pos-shell { min-height:100vh; max-width:1480px; margin:auto; padding:24px 30px 0; }
        .pos-header { display:flex; align-items:flex-end; justify-content:space-between; gap:20px; padding:0 10px 18px; }
        .store-name { color:var(--teal-deep); font-size:19px; font-weight:800; letter-spacing:.04em; }
        .store-info,.transaction-info { color:var(--muted); font-size:11px; }
        .transaction-info { text-align:right; }
        .transaction-number { color:var(--teal); font-size:14px; font-weight:800; }
        .pos-main { display:grid; grid-template-columns:minmax(0,1fr) 300px; gap:14px; padding:14px; background:#edf1f2; border:1px solid #e1e7e8; }
        .left-column { min-width:0; }
        .card { overflow:hidden; border:1px solid var(--line); border-radius:7px; background:#fff; box-shadow:0 2px 7px rgba(24,55,62,.04); }
        .card + .card { margin-top:14px; }
        .card-header { padding:13px 15px; border-bottom:1px solid var(--line); color:var(--teal-deep); font-size:13px; font-weight:800; }
        .card-header-row { display:flex; justify-content:space-between; gap:10px; }
        .card-body { padding:14px; }
        .search-area { display:flex; gap:9px; }
        .search-input { position:relative; flex:1; }
        .search-input i { position:absolute; top:50%; left:12px; transform:translateY(-50%); color:var(--muted); }
        .form-control { width:100%; height:36px; padding:0 11px; border:1px solid #cfdadb; border-radius:5px; outline:none; color:var(--ink); background:#fff; font-size:12px; }
        .search-input .form-control { padding-left:34px; }
        .form-control:focus { border-color:var(--teal); box-shadow:0 0 0 3px rgba(7,83,106,.1); }
        .btn { border:0; border-radius:5px; padding:0 16px; color:#fff; cursor:pointer; font-size:12px; font-weight:700; }
        .btn-primary { background:var(--teal); }
        .btn-warning { color:#4e3a00; background:var(--yellow); }
        .btn-danger { background:#d9384b; }
        .btn-success { background:var(--green); }
        .product-results { position:absolute; z-index:4; top:42px; right:0; left:0; display:none; overflow:hidden; border:1px solid var(--line); border-radius:5px; background:#fff; box-shadow:0 8px 20px rgba(0,0,0,.12); }
        .product-item { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:10px 12px; border-bottom:1px solid #edf1f1; cursor:pointer; font-size:12px; }
        .product-item:last-child { border-bottom:0; }
        .product-item:hover { background:#f0f7f7; }
        .product-name { font-weight:700; }
        .product-meta { margin-top:3px; color:var(--muted); font-size:10px; }
        .product-price { color:var(--teal); font-weight:800; white-space:nowrap; }
        .customer-area { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:13px; }
        .field-label { display:block; margin-bottom:5px; color:var(--muted); font-size:10px; font-weight:700; }
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; font-size:11px; }
        th { padding:10px 12px; color:var(--muted); background:#f8fafa; font-size:10px; text-align:left; }
        td { padding:11px 12px; border-top:1px solid #edf1f1; white-space:nowrap; }
        th:last-child,td:last-child { text-align:center; }
        .text-right { text-align:right; }
        .empty-cart { padding:54px 10px; color:var(--muted); text-align:center; white-space:normal; }
        .empty-cart i { display:block; margin-bottom:8px; color:#b6c5c7; font-size:25px; }
        .qty-control { display:flex; align-items:center; justify-content:center; gap:3px; }
        .qty-control button,.remove-btn { width:24px; height:24px; border:1px solid var(--line); border-radius:4px; color:var(--teal); background:#f7fafa; cursor:pointer; }
        .qty-control input { width:38px; height:24px; border:1px solid var(--line); border-radius:4px; text-align:center; font-size:11px; }
        .remove-btn { color:#d9384b; }
        .summary-row { display:flex; align-items:center; justify-content:space-between; gap:12px; min-height:34px; color:#5f6d73; font-size:11px; }
        .summary-row strong { color:var(--ink); }
        .summary-row input { width:92px; height:28px; padding:0 8px; border:1px solid #cfdadb; border-radius:4px; text-align:right; font-size:11px; }
        .total-box { margin-top:9px; padding:13px 12px; border-radius:5px; background:#f0f1f6; }
        .total-label { color:var(--muted); font-size:10px; font-weight:700; }
        .total-value { margin-top:4px; color:var(--teal-deep); font-size:22px; font-weight:800; }
        .payment-box { margin-top:14px; padding:12px; border:1px solid #bfd6e4; border-radius:5px; background:#eef7fd; }
        .payment-label { margin-bottom:6px; color:var(--teal-deep); font-size:10px; font-weight:800; }
        .payment-input { width:100%; height:40px; padding:0 10px; border:2px solid #62a9d4; border-radius:5px; outline:none; color:var(--ink); text-align:right; font-size:18px; font-weight:700; }
        .payment-method { display:grid; grid-template-columns:repeat(3,1fr); gap:5px; }
        .payment-method button { min-height:28px; border:1px solid #d5dfe2; border-radius:4px; color:#536269; background:#fff; cursor:pointer; font-size:10px; }
        .payment-method button.active { border-color:var(--teal); color:#fff; background:var(--teal); }
        .change-box { margin-top:12px; padding:10px; border-radius:4px; background:#d4f0e3; }
        .change-box.short-payment { background:#ffe1df; }
        .change-label { color:#398064; font-size:9px; font-weight:800; }
        .short-payment .change-label { color:#b24640; }
        .change-value { margin-top:2px; color:#11704c; font-size:19px; font-weight:800; }
        .short-payment .change-value { color:#c63d38; }
        .action-area { display:grid; grid-template-columns:1fr 1fr; gap:6px; margin-top:14px; }
        .action-area .btn { min-height:38px; }
        .btn-pay { grid-column:1 / -1; min-height:43px !important; letter-spacing:.05em; }
        .pos-footer { display:flex; justify-content:space-between; padding:8px 10px; color:rgba(255,255,255,.7); background:#20292d; font-size:10px; }
        @media (max-width:850px) { .pos-shell { padding:15px 10px 0; } .pos-main { grid-template-columns:1fr; } .right-column { display:grid; grid-template-columns:1fr 1fr; gap:14px; align-items:start; } .card + .card { margin-top:0; } }
        @media (max-width:560px) { .pos-header { align-items:flex-start; flex-direction:column; } .transaction-info { text-align:left; } .customer-area,.right-column { grid-template-columns:1fr; } .search-area { flex-direction:column; } .btn-primary { min-height:36px; } }
    </style>
</head>
<body>
    <div class="pos-shell">
        <header class="pos-header">
            <div>
                <div class="store-name">TOKO SEMBAKO JAZZEL</div>
                <div class="store-info">Jl. Contoh No. 123 - Jember - Telp. 0812-xxxx-xxxx</div>
            </div>
            <div class="transaction-info">
                <div>No. Transaksi</div>
                <div class="transaction-number" id="transactionNumber">TRX-{{ now()->format('Ymd') }}-001</div>
                <div id="currentDate"></div>
            </div>
        </header>
        <main class="pos-main">
            <section class="left-column">
                <div class="card">
                    <div class="card-header">Tambah Barang</div>
                    <div class="card-body">
                        <div class="search-area">
                            <div class="search-input">
                                <i class="bi bi-search"></i>
                                <input class="form-control" id="searchProduct" type="text" placeholder="Cari nama barang / kategori..." autocomplete="off">
                                <div class="product-results" id="productResults"></div>
                            </div>
                            <button class="btn btn-primary" type="button" id="searchButton">Cari</button>
                        </div>
                        <div class="customer-area">
                            <div>
                                <label class="field-label" for="customerType">Pelanggan</label>
                                <select class="form-control" id="customerType">
                                    <option>Umum</option>
                                    <option>Pelanggan Member</option>
                                    <option>Member VIP</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label" for="customerContact">No. Member / HP</label>
                                <input class="form-control" id="customerContact" type="text" placeholder="Opsional">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header card-header-row">
                        <span>Keranjang Belanja</span>
                        <span id="itemCount">0 Item</span>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr><th>No</th><th>Barang</th><th>Harga</th><th>Qty</th><th class="text-right">Subtotal</th><th></th></tr>
                            </thead>
                            <tbody id="cartBody">
                                <tr><td colspan="6" class="empty-cart"><i class="bi bi-basket2"></i>Keranjang masih kosong.<br>Silakan cari atau pilih barang.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <aside class="right-column">
                <div class="card"><div class="card-header">Ringkasan Pembayaran</div><div class="card-body">
                    <div class="summary-row"><span>Total Item</span><strong id="totalQty">0</strong></div><div class="summary-row"><span>Subtotal</span><strong id="subtotal">Rp 0</strong></div>
                    <div class="summary-row"><span>Diskon (%)</span><input type="number" id="discountPercent" value="0" min="0" max="100"></div><div class="summary-row"><span>Diskon (Rp)</span><input type="number" id="discountAmount" value="0" min="0"></div><div class="summary-row"><span>Pajak / PPN</span><input type="number" id="tax" value="0" min="0"></div><div class="summary-row"><span>Biaya Lain</span><input type="number" id="otherFee" value="0" min="0"></div>
                    <div class="total-box"><div class="total-label">TOTAL AKHIR</div><div class="total-value" id="grandTotal">Rp 0</div></div>
                    <div class="payment-box"><div class="payment-label">Uang Dibayar</div><input class="payment-input" id="payment" type="number" min="0" placeholder="0"><div class="payment-label" style="margin-top:13px">Metode Pembayaran</div><div class="payment-method"><button type="button" class="active">Tunai</button><button type="button">QRIS</button><button type="button">Debit</button><button type="button">Kredit</button><button type="button">E-Wallet</button><button type="button">Transfer</button></div><div class="change-box" id="changeBox"><div class="change-label" id="changeLabel">KEMBALIAN</div><div class="change-value" id="change">Rp 0</div></div></div>
                    <div class="action-area"><button class="btn btn-warning" type="button" id="holdButton">Tahan</button><button class="btn btn-danger" type="button" id="cancelButton">Batal</button><button class="btn btn-success btn-pay" type="button" id="payButton">BAYAR &amp; CETAK</button></div>
                </div></div>
            </aside>
        </main>
        <footer class="pos-footer"><span>Kasir: {{ auth()->user()->name }}</span><span>F2 Cari Barang - F4 Bayar - ESC Batal</span></footer>
    </div>
    <script id="product-data" type="application/json">@json($products)</script>
    <script>
        const products = JSON.parse(document.querySelector('#product-data').textContent);
        let cart = [];
        const searchInput = document.querySelector('#searchProduct');
        const resultsBox = document.querySelector('#productResults');
        const money = (value) => new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', maximumFractionDigits:0 }).format(value);
        const totalValue = () => { const subtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0); const percent = Number(document.querySelector('#discountPercent').value) || 0; const discount = Number(document.querySelector('#discountAmount').value) || 0; const tax = Number(document.querySelector('#tax').value) || 0; const fee = Number(document.querySelector('#otherFee').value) || 0; return Math.max(0, subtotal - (subtotal * percent / 100) - discount + tax + fee); };
        function searchProduct() { const keyword = searchInput.value.toLowerCase().trim(); if (!keyword) { resultsBox.style.display = 'none'; return; } const matches = products.filter(product => `${product.name} ${product.category || 'Umum'} ${product.id}`.toLowerCase().includes(keyword)); resultsBox.innerHTML = matches.length ? matches.map(product => `<div class="product-item" data-product-id="${product.id}"><div><div class="product-name">${product.name}</div><div class="product-meta">${product.category || 'Umum'} - Stok ${product.stock}</div></div><div class="product-price">${money(product.price)}</div></div>`).join('') : '<div class="product-item">Barang tidak ditemukan</div>'; resultsBox.style.display = 'block'; }
        function addToCart(id) { const product = products.find(item => item.id === id); if (!product || product.stock < 1) return; const existing = cart.find(item => item.id === id); if (existing) { if (existing.qty < product.stock) existing.qty++; } else cart.push({ ...product, qty:1 }); searchInput.value = ''; resultsBox.style.display = 'none'; renderCart(); }
        function renderCart() { const body = document.querySelector('#cartBody'); if (!cart.length) { body.innerHTML = '<tr><td colspan="6" class="empty-cart"><i class="bi bi-basket2"></i>Keranjang masih kosong.<br>Silakan cari atau pilih barang.</td></tr>'; } else body.innerHTML = cart.map((item, index) => `<tr><td>${index + 1}</td><td><strong>${item.name}</strong><div class="product-meta">${item.category || 'Umum'}</div></td><td>${money(item.price)}</td><td><div class="qty-control"><button type="button" data-action="decrease" data-id="${item.id}">-</button><input type="number" min="1" max="${item.stock}" value="${item.qty}" data-action="quantity" data-id="${item.id}"><button type="button" data-action="increase" data-id="${item.id}">+</button></div></td><td class="text-right"><strong>${money(item.price * item.qty)}</strong></td><td><button class="remove-btn" type="button" data-action="remove" data-id="${item.id}">x</button></td></tr>`).join(''); calculateTotal(); }
        function calculateTotal() { const quantity = cart.reduce((sum, item) => sum + item.qty, 0); const subtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0); document.querySelector('#totalQty').textContent = quantity; document.querySelector('#itemCount').textContent = `${quantity} Item`; document.querySelector('#subtotal').textContent = money(subtotal); document.querySelector('#grandTotal').textContent = money(totalValue()); calculateChange(); }
        function calculateChange() { const difference = (Number(document.querySelector('#payment').value) || 0) - totalValue(); const box = document.querySelector('#changeBox'); const label = document.querySelector('#changeLabel'); box.classList.toggle('short-payment', difference < 0); label.textContent = difference < 0 ? 'UANG KURANG' : 'KEMBALIAN'; document.querySelector('#change').textContent = money(Math.abs(difference)); }
        searchInput.addEventListener('input', searchProduct); document.querySelector('#searchButton').addEventListener('click', searchProduct); document.querySelector('#payment').addEventListener('input', calculateChange); document.querySelectorAll('.summary-row input').forEach(input => input.addEventListener('input', calculateTotal));
        resultsBox.addEventListener('click', event => { const item = event.target.closest('[data-product-id]'); if (item) addToCart(Number(item.dataset.productId)); });
        document.querySelector('#cartBody').addEventListener('click', event => { const button = event.target.closest('[data-action]'); if (!button) return; const item = cart.find(product => product.id === Number(button.dataset.id)); if (!item) return; if (button.dataset.action === 'increase') item.qty = Math.min(item.stock, item.qty + 1); if (button.dataset.action === 'decrease') item.qty--; if (button.dataset.action === 'remove' || item.qty < 1) cart = cart.filter(product => product.id !== item.id); renderCart(); });
        document.querySelector('#cartBody').addEventListener('change', event => { if (event.target.dataset.action !== 'quantity') return; const item = cart.find(product => product.id === Number(event.target.dataset.id)); if (item) item.qty = Math.max(1, Math.min(item.stock, Number(event.target.value) || 1)); renderCart(); });
        document.querySelectorAll('.payment-method button').forEach(button => button.addEventListener('click', () => { document.querySelectorAll('.payment-method button').forEach(item => item.classList.remove('active')); button.classList.add('active'); }));
        document.querySelector('#cancelButton').addEventListener('click', () => { if (!cart.length || confirm('Batalkan transaksi ini?')) { cart = []; document.querySelector('#payment').value = ''; renderCart(); } });
        document.querySelector('#holdButton').addEventListener('click', () => alert(cart.length ? 'Transaksi berhasil ditahan.' : 'Tidak ada transaksi untuk ditahan.'));
        document.querySelector('#payButton').addEventListener('click', () => { const total = totalValue(); const payment = Number(document.querySelector('#payment').value) || 0; if (!cart.length) return alert('Keranjang masih kosong.'); if (payment < total) return alert('Uang pembayaran masih kurang.'); alert(`Pembayaran berhasil!\n\nTotal: ${money(total)}\nBayar: ${money(payment)}\nKembali: ${money(payment - total)}`); });
        document.querySelector('#currentDate').textContent = new Date().toLocaleString('id-ID'); document.addEventListener('keydown', event => { if (event.key === 'F2') { event.preventDefault(); searchInput.focus(); } if (event.key === 'F4') { event.preventDefault(); document.querySelector('#payment').focus(); } if (event.key === 'Escape') document.querySelector('#cancelButton').click(); }); renderCart();
    </script>
</body>
</html>