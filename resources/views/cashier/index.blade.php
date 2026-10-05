@extends('layouts.admin')

@section('title', 'Transaksi Baru')

@push('styles')
    <style>
        .pos-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
            margin-bottom: 18px;
        }

        .pos-heading h1 {
            margin: 0;
            color: #17243a;
            font-size: 23px;
            font-weight: 750;
            letter-spacing: -.03em;
        }

        .pos-heading p { margin: 4px 0 0; color: #738198; font-size: 12px; }

        .transaction-chip {
            padding: 8px 11px;
            border: 1px solid #e3e9f0;
            border-radius: 8px;
            color: #536176;
            background: #fff;
            font-size: 11px;
            text-align: right;
            white-space: nowrap;
        }

        .transaction-chip strong { display: block; margin-top: 2px; color: #07536a; font-size: 12px; }

        .pos-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(275px, .85fr);
            align-items: start;
            gap: 14px;
        }

        .pos-left, .pos-right { display: grid; min-width: 0; gap: 14px; }
        .pos-view .card { overflow: visible; border: 1px solid #e3e9f0; border-radius: 10px; background: #fff; box-shadow: 0 1px 3px rgba(19,39,67,.07); }
        .pos-view .card-header { padding: 14px 16px; border-bottom: 1px solid #edf1f5; color: #17243a; background: #fff; font-size: 13px; font-weight: 700; }
        .pos-view .card-body { padding: 15px 16px; }
        .card-header-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .item-count { padding: 4px 8px; border-radius: 99px; color: #07536a; background: #eef5f7; font-size: 10px; font-weight: 700; }
        .search-area { display: flex; gap: 9px; }
        .search-input { position: relative; flex: 1; min-width: 0; }
        .search-input > i { position: absolute; top: 50%; left: 12px; z-index: 1; transform: translateY(-50%); color: #738198; }
        .pos-view .form-control { width: 100%; height: 38px; padding: 0 11px; border: 1px solid #d7e0e9; border-radius: 7px; color: #17243a; background: #fff; font-size: 12px; }
        .pos-view .search-input .form-control { padding-left: 35px; }
        .pos-view .form-control:focus { border-color: #07536a; box-shadow: 0 0 0 3px rgba(7,83,106,.1); }
        .pos-view .btn { min-height: 38px; padding: 0 14px; border-radius: 7px; font-size: 12px; font-weight: 700; }
        .pos-view .btn-primary { background: #07536a; border-color: #07536a; }
        .pos-view .btn-primary:hover { background: #064456; border-color: #064456; }
        .product-results { position: absolute; z-index: 5; top: 43px; right: 0; left: 0; display: none; overflow: hidden; border: 1px solid #e3e9f0; border-radius: 8px; background: #fff; box-shadow: 0 8px 20px rgba(19,39,67,.12); }
        .product-item { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 12px; border-bottom: 1px solid #edf1f5; cursor: pointer; font-size: 12px; }
        .product-item:last-child { border-bottom: 0; }
        .product-item:hover { background: #f5f8fc; }
        .product-name { color: #17243a; font-weight: 700; }
        .product-meta { margin-top: 3px; color: #738198; font-size: 10px; }
        .product-price { color: #07536a; font-weight: 750; white-space: nowrap; }
        .customer-area { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 14px; }
        .field-label { display: block; margin-bottom: 5px; color: #65748b; font-size: 10px; font-weight: 700; }
        .table-wrap { width: calc(100% - 28px); margin: 0 auto; overflow-x: auto; }
        .pos-table { width: 100%; border: 1px solid #d4dfe7; border-collapse: collapse; font-size: 12px; }
        .pos-table th { padding: 11px 12px; border: 1px solid #d4dfe7; color: #34445b; background: #edf4f6; font-size: 11px; font-weight: 750; letter-spacing: .05em; text-align: left; text-transform: uppercase; white-space: nowrap; }
        .pos-table td { padding: 12px; border: 1px solid #d4dfe7; color: #34445b; white-space: nowrap; }
        .pos-table tbody tr:hover td { background: #f5f9fb; }
        .pos-table th:last-child, .pos-table td:last-child { text-align: center; }
        .text-right { text-align: right; }
        .empty-cart { padding: 48px 10px !important; color: #8290a4 !important; text-align: center; white-space: normal !important; }
        .empty-cart i { display: block; margin-bottom: 8px; color: #aab6c5; font-size: 24px; }
        .qty-control { display: flex; align-items: center; justify-content: center; gap: 3px; }
        .qty-control button, .remove-btn { width: 25px; height: 25px; border: 1px solid #dbe3ec; border-radius: 5px; color: #07536a; background: #fff; cursor: pointer; }
        .qty-control input { width: 38px; height: 25px; border: 1px solid #dbe3ec; border-radius: 5px; text-align: center; font-size: 11px; }
        .remove-btn { color: #c23445; }
        .summary-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; min-height: 35px; color: #65748b; font-size: 11px; }
        .summary-row strong { color: #17243a; }
        .summary-row input { width: 92px; height: 28px; padding: 0 8px; border: 1px solid #d7e0e9; border-radius: 6px; color: #17243a; background: #fff; text-align: right; font-size: 11px; }
        .total-box { margin-top: 10px; padding: 13px; border: 1px solid #e3e9f0; border-radius: 8px; background: #f5f8fc; }
        .total-label { color: #738198; font-size: 9px; font-weight: 750; letter-spacing: .08em; }
        .total-value { margin-top: 4px; color: #07536a; font-size: 22px; font-weight: 800; }
        .payment-box { margin-top: 13px; padding: 12px; border: 1px solid #dce7ef; border-radius: 8px; background: #f8fbfd; }
        .payment-label { margin-bottom: 6px; color: #536176; font-size: 10px; font-weight: 750; }
        .payment-input { width: 100%; height: 40px; padding: 0 10px; border: 1px solid #a9c5d6; border-radius: 7px; outline: none; color: #17243a; background: #fff; text-align: right; font-size: 18px; font-weight: 700; }
        .payment-input:focus { border-color: #07536a; box-shadow: 0 0 0 3px rgba(7,83,106,.1); }
        .payment-method { display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px; }
        .payment-method button { min-height: 29px; border: 1px solid #dbe3ec; border-radius: 5px; color: #536176; background: #fff; cursor: pointer; font-size: 10px; }
        .payment-method button.active { border-color: #07536a; color: #fff; background: #07536a; }
        .qris-simulation-mark {
            display: grid;
            width: 126px;
            height: 126px;
            margin: 0 auto 14px;
            place-items: center;
            border: 7px solid #17243a;
            border-radius: 8px;
            color: #17243a;
            background:
                repeating-conic-gradient(#17243a 0% 25%, #fff 0% 50%) 0 0 / 18px 18px;
            box-shadow: inset 0 0 0 4px #fff;
        }
        .qris-simulation-mark i { padding: 5px; border: 3px solid #17243a; border-radius: 4px; background: #fff; font-size: 42px; }
        .change-box { margin-top: 11px; padding: 10px; border-radius: 6px; background: #e8f6ee; }
        .change-box.short-payment { background: #fff0ef; }
        .change-label { color: #398064; font-size: 9px; font-weight: 800; }
        .short-payment .change-label { color: #b24640; }
        .change-value { margin-top: 2px; color: #11704c; font-size: 18px; font-weight: 800; }
        .short-payment .change-value { color: #c63d38; }
        .action-area { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-top: 13px; }
        .action-area .btn { min-height: 38px; }
        .btn-pay { grid-column: 1 / -1; min-height: 42px !important; }

        @media (max-width: 850px) {
            .pos-layout { grid-template-columns: 1fr; }
            .pos-right { grid-template-columns: 1fr 1fr; align-items: start; }
        }

        @media (max-width: 560px) {
            .pos-heading { align-items: flex-start; flex-direction: column; }
            .transaction-chip { text-align: left; }
            .customer-area, .pos-right { grid-template-columns: 1fr; }
            .search-area { flex-direction: column; }
            .search-area .btn { width: 100%; }
            .table-wrap { width: calc(100% - 20px); }
        }
    </style>
@endpush

@section('content')
    <div class="pos-view">
        <header class="pos-heading">
            <div>
                <h1>Transaksi Baru</h1>
                <p>Masukkan barang ke keranjang, lalu lanjutkan pembayaran.</p>
            </div>
            <div class="transaction-chip">
                No. Transaksi
                <strong id="transactionNumber">TRX-{{ now()->format('Ymd') }}-001</strong>
                <span id="currentDate"></span>
            </div>
        </header>

        <main class="pos-layout">
            <section class="pos-left">
                <article class="card">
                    <div class="card-header">Tambah Barang</div>
                    <div class="card-body">
                        @if($products->isEmpty())
                            <div class="alert alert-warning mb-3" role="status">
                                Belum ada barang aktif dengan stok tersedia. Pastikan barang berstatus aktif dan stoknya lebih dari 0 di Kelola Barang.
                            </div>
                        @endif
                        <div class="search-area">
                            <div class="search-input">
                                <i class="bi bi-search"></i>
                                <input class="form-control" id="searchProduct" type="text" placeholder="Cari nama barang / kategori..." autocomplete="off">
                                <div class="product-results" id="productResults"></div>
                            </div>
                            <button class="btn btn-primary" type="button" id="searchButton">Cari Barang</button>
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
                </article>

                <article class="card">
                    <div class="card-header card-header-row">
                        <span>Keranjang Belanja</span>
                        <span class="item-count" id="itemCount">0 Item</span>
                    </div>
                    <div class="table-wrap">
                        <table class="pos-table">
                            <thead>
                                <tr><th>No</th><th>Barang</th><th>Harga</th><th>Qty</th><th class="text-right">Subtotal</th><th></th></tr>
                            </thead>
                            <tbody id="cartBody">
                                <tr><td colspan="6" class="empty-cart"><i class="bi bi-basket2"></i>Keranjang masih kosong.<br>Silakan cari atau pilih barang.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>

            <aside class="pos-right">
                <article class="card">
                    <div class="card-header">Ringkasan Pembayaran</div>
                    <div class="card-body">
                        <div class="summary-row"><span>Total Item</span><strong id="totalQty">0</strong></div>
                        <div class="summary-row"><span>Subtotal</span><strong id="subtotal">Rp 0</strong></div>
                        <div class="summary-row"><span>Diskon (%)</span><input type="number" id="discountPercent" value="0" min="0" max="100"></div>
                        <div class="summary-row"><span>Diskon (Rp)</span><input type="number" id="discountAmount" value="0" min="0"></div>
                        <div class="summary-row"><span>Pajak / PPN</span><input type="number" id="tax" value="0" min="0"></div>
                        <div class="summary-row"><span>Biaya Lain</span><input type="number" id="otherFee" value="0" min="0"></div>

                        <div class="total-box">
                            <div class="total-label">TOTAL AKHIR</div>
                            <div class="total-value" id="grandTotal">Rp 0</div>
                        </div>

                        <div class="payment-box">
                            <div class="payment-label" id="paymentLabel">Uang Dibayar</div>
                            <input class="payment-input" id="payment" type="number" min="0" placeholder="0">
                            <div class="payment-label" style="margin-top:13px">Metode Pembayaran</div>
                            <div class="payment-method">
                                <button type="button" class="active" data-method="tunai">Tunai</button><button type="button" data-method="qris">QRIS</button><button type="button" data-method="transfer">Transfer</button>
                            </div>
                            <div class="change-box" id="changeBox">
                                <div class="change-label" id="changeLabel">KEMBALIAN</div>
                                <div class="change-value" id="change">Rp 0</div>
                            </div>
                        </div>

                        <div class="action-area">
                            <button class="btn btn-warning" type="button" id="holdButton">Tahan</button>
                            <button class="btn btn-danger" type="button" id="cancelButton">Batal</button>
                            <button class="btn btn-success btn-pay" type="button" id="payButton">BAYAR &amp; CETAK</button>
                        </div>
                    </div>
                </article>
            </aside>
        </main>
    </div>

    <script id="product-data" type="application/json">@json($products)</script>
@endsection

@push('modals')
    <div class="modal fade" id="qrisSimulationModal" tabindex="-1" aria-labelledby="qrisSimulationTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="qrisSimulationTitle">Simulasi Pembayaran QRIS</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="qris-simulation-mark" aria-hidden="true"><i class="bi bi-qr-code"></i></div>
                    <span class="badge text-bg-warning mb-2">SIMULASI - BUKAN PEMBAYARAN SUNGGUHAN</span>
                    <p class="mb-1">Nominal pembayaran</p>
                    <strong class="fs-4 text-primary" id="qrisSimulationAmount">Rp 0</strong>
                    <p class="small text-muted mt-3 mb-0">Kode ini tidak dapat dipindai. Tekan tombol konfirmasi untuk menyimulasikan pembayaran berhasil.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmQrisSimulation">
                        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>Simulasikan Pembayaran Berhasil
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        const products = JSON.parse(document.querySelector('#product-data').textContent);
        let cart = [];
        const searchInput = document.querySelector('#searchProduct');
        const resultsBox = document.querySelector('#productResults');
        const paymentInput = document.querySelector('#payment');
        const paymentButtons = [...document.querySelectorAll('.payment-method button')];
        const currentPaymentMethod = () => paymentButtons.find(button => button.classList.contains('active')).dataset.method;
        const money = (value) => new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', maximumFractionDigits:0 }).format(value);
        const totalValue = () => { const subtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0); const percent = Number(document.querySelector('#discountPercent').value) || 0; const discount = Number(document.querySelector('#discountAmount').value) || 0; const tax = Number(document.querySelector('#tax').value) || 0; const fee = Number(document.querySelector('#otherFee').value) || 0; return Math.max(0, subtotal - (subtotal * percent / 100) - discount + tax + fee); };
        function searchProduct() { const keyword = searchInput.value.toLowerCase().trim(); if (!keyword) { resultsBox.style.display = 'none'; return; } const matches = products.filter(product => `${product.name} ${product.category || 'Umum'} ${product.id}`.toLowerCase().includes(keyword)); resultsBox.innerHTML = matches.length ? matches.map(product => `<div class="product-item" data-product-id="${product.id}"><div><div class="product-name">${product.name}</div><div class="product-meta">${product.category || 'Umum'} - Stok ${product.stock}</div></div><div class="product-price">${money(product.price)}</div></div>`).join('') : '<div class="product-item">Barang tidak ditemukan</div>'; resultsBox.style.display = 'block'; }
        function addToCart(id) { const product = products.find(item => item.id === id); if (!product || product.stock < 1) return; const existing = cart.find(item => item.id === id); if (existing) { if (existing.qty < product.stock) existing.qty++; } else cart.push({ ...product, qty:1 }); searchInput.value = ''; resultsBox.style.display = 'none'; renderCart(); }
        function renderCart() { const body = document.querySelector('#cartBody'); if (!cart.length) { body.innerHTML = '<tr><td colspan="6" class="empty-cart"><i class="bi bi-basket2"></i>Keranjang masih kosong.<br>Silakan cari atau pilih barang.</td></tr>'; } else body.innerHTML = cart.map((item, index) => `<tr><td>${index + 1}</td><td><strong>${item.name}</strong><div class="product-meta">${item.category || 'Umum'}</div></td><td>${money(item.price)}</td><td><div class="qty-control"><button type="button" data-action="decrease" data-id="${item.id}">-</button><input type="number" min="1" max="${item.stock}" value="${item.qty}" data-action="quantity" data-id="${item.id}"><button type="button" data-action="increase" data-id="${item.id}">+</button></div></td><td class="text-right"><strong>${money(item.price * item.qty)}</strong></td><td><button class="remove-btn" type="button" data-action="remove" data-id="${item.id}">x</button></td></tr>`).join(''); calculateTotal(); }
        function calculateTotal() { const quantity = cart.reduce((sum, item) => sum + item.qty, 0); const subtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0); document.querySelector('#totalQty').textContent = quantity; document.querySelector('#itemCount').textContent = `${quantity} Item`; document.querySelector('#subtotal').textContent = money(subtotal); document.querySelector('#grandTotal').textContent = money(totalValue()); if (currentPaymentMethod() === 'qris') paymentInput.value = totalValue(); calculateChange(); }
        function calculateChange() { const difference = (Number(document.querySelector('#payment').value) || 0) - totalValue(); const box = document.querySelector('#changeBox'); const label = document.querySelector('#changeLabel'); box.classList.toggle('short-payment', difference < 0); label.textContent = difference < 0 ? 'UANG KURANG' : 'KEMBALIAN'; document.querySelector('#change').textContent = money(Math.abs(difference)); }
        searchInput.addEventListener('input', searchProduct); document.querySelector('#searchButton').addEventListener('click', searchProduct); paymentInput.addEventListener('input', calculateChange); document.querySelectorAll('.summary-row input').forEach(input => input.addEventListener('input', calculateTotal));
        resultsBox.addEventListener('click', event => { const item = event.target.closest('[data-product-id]'); if (item) addToCart(Number(item.dataset.productId)); });
        document.querySelector('#cartBody').addEventListener('click', event => { const button = event.target.closest('[data-action]'); if (!button) return; const item = cart.find(product => product.id === Number(button.dataset.id)); if (!item) return; if (button.dataset.action === 'increase') item.qty = Math.min(item.stock, item.qty + 1); if (button.dataset.action === 'decrease') item.qty--; if (button.dataset.action === 'remove' || item.qty < 1) cart = cart.filter(product => product.id !== item.id); renderCart(); });
        document.querySelector('#cartBody').addEventListener('change', event => { if (event.target.dataset.action !== 'quantity') return; const item = cart.find(product => product.id === Number(event.target.dataset.id)); if (item) item.qty = Math.max(1, Math.min(item.stock, Number(event.target.value) || 1)); renderCart(); });
        paymentButtons.forEach(button => button.addEventListener('click', () => {
            paymentButtons.forEach(item => item.classList.remove('active'));
            button.classList.add('active');
            const isQris = button.dataset.method === 'qris';
            paymentInput.readOnly = isQris;
            document.querySelector('#paymentLabel').textContent = isQris ? 'Nominal QRIS' : 'Uang Dibayar';
            if (isQris) paymentInput.value = totalValue();
            calculateChange();
        }));
        document.querySelector('#cancelButton').addEventListener('click', () => { if (!cart.length || confirm('Batalkan transaksi ini?')) { cart = []; document.querySelector('#payment').value = ''; renderCart(); } });
        document.querySelector('#holdButton').addEventListener('click', () => alert(cart.length ? 'Transaksi berhasil ditahan.' : 'Tidak ada transaksi untuk ditahan.'));
        async function submitCheckout(button) {
            const payment = Number(document.querySelector('#payment').value) || 0;
            if (!cart.length) return alert('Keranjang masih kosong.');
            if (payment < totalValue()) return alert('Uang pembayaran masih kurang.');

            button.disabled = true;
            try {
                const response = await fetch(@json(route('cashier.checkout')), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        items: cart.map(item => ({ product_id: item.id, quantity: item.qty })),
                        customer_type: document.querySelector('#customerType').value,
                        customer_contact: document.querySelector('#customerContact').value,
                        discount_percent: Number(document.querySelector('#discountPercent').value) || 0,
                        discount_amount: Number(document.querySelector('#discountAmount').value) || 0,
                        tax: Number(document.querySelector('#tax').value) || 0,
                        other_fee: Number(document.querySelector('#otherFee').value) || 0,
                        paid: payment,
                        payment_method: document.querySelector('.payment-method button.active').dataset.method
                    })
                });
                const result = await response.json();
                if (!response.ok) {
                    const errors = Object.values(result.errors || {}).flat();
                    throw new Error(errors.join('\n') || result.message || 'Transaksi gagal disimpan.');
                }

                alert(`Transaksi berhasil disimpan!\n\nInvoice: ${result.invoice}\nTotal: ${money(result.total)}\nKembali: ${money(result.change)}`);
                window.location.href = result.history_url;
            } catch (error) {
                alert(error.message || 'Transaksi gagal disimpan. Silakan coba lagi.');
                button.disabled = false;
            }
        }
        const qrisModalElement = document.querySelector('#qrisSimulationModal');
        const qrisModal = bootstrap.Modal.getOrCreateInstance(qrisModalElement);

        document.querySelector('#payButton').addEventListener('click', event => {
            if (currentPaymentMethod() === 'qris') {
                if (!cart.length) return alert('Keranjang masih kosong.');
                paymentInput.value = totalValue();
                document.querySelector('#qrisSimulationAmount').textContent = money(totalValue());
                qrisModal.show();
                return;
            }

            submitCheckout(event.currentTarget);
        });
        document.querySelector('#confirmQrisSimulation').addEventListener('click', () => {
            qrisModal.hide();
            submitCheckout(document.querySelector('#payButton'));
        });
        document.querySelector('#currentDate').textContent = new Date().toLocaleString('id-ID'); document.addEventListener('keydown', event => { if (event.key === 'F2') { event.preventDefault(); searchInput.focus(); } if (event.key === 'F4') { event.preventDefault(); document.querySelector('#payment').focus(); } if (event.key === 'Escape') document.querySelector('#cancelButton').click(); }); renderCart();
    </script>
@endpush
