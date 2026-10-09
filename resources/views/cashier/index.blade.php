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
            color: #3a3334;
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -.03em;
        }

        .pos-heading p { margin: 4px 0 0; color: #766d6f; font-size: 14px; }

        .transaction-chip {
            padding: 8px 11px;
            border: 1px solid #e9dada;
            border-radius: 8px;
            color: #51474a;
            background: #fff;
            font-size: 11px;
            text-align: right;
            white-space: nowrap;
        }

        .transaction-chip strong { display: block; margin-top: 2px; color: #8d2a22; font-size: 12px; }

        .pos-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(275px, .85fr);
            align-items: start;
            gap: 14px;
        }

        .pos-left, .pos-right { display: grid; min-width: 0; gap: 14px; }
        .pos-view .card { overflow: visible; border: 1px solid #e9dada; border-radius: 10px; background: #fff; box-shadow: 0 1px 3px rgba(57,32,33,.07); }
        .pos-view .card-header { padding: 14px 16px; border-bottom: 1px solid #eee5e4; color: #3a3334; background: #fff; font-size: 13px; font-weight: 700; }
        .pos-view .card-body { padding: 15px 16px; }
        .card-header-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .item-count { padding: 4px 8px; border-radius: 99px; color: #8d2a22; background: #f4e6e4; font-size: 10px; font-weight: 700; }
        .search-area { display: flex; gap: 9px; }
        .search-input { position: relative; flex: 1; min-width: 0; }
        .search-input > i { position: absolute; top: 50%; left: 12px; z-index: 1; transform: translateY(-50%); color: #766d6f; }
        .pos-view .form-control { width: 100%; height: 42px; padding: 0 11px; border: 1px solid #e5d8d7; border-radius: 8px; color: #3a3334; background: rgba(255,255,255,.96); font-size: 14px; }
        .pos-view .search-input .form-control { padding-left: 35px; }
        .pos-view .form-control:focus { border-color: #b94245; box-shadow: 0 0 0 3px rgba(185,66,69,.18); }
        .pos-view .btn { min-height: 42px; padding: 0 16px; border-radius: 8px; font-size: 13px; font-weight: 700; }
        .pos-view .btn-primary { color: #fff; background: #b94245; border-color: #b94245; }
        .pos-view .btn-primary:hover { color: #fff; background: #8d2a22; border-color: #8d2a22; }
        .product-results { position: absolute; z-index: 5; top: 43px; right: 0; left: 0; display: none; overflow: hidden; border: 1px solid #e9dada; border-radius: 8px; background: #fff; box-shadow: 0 8px 20px rgba(82,39,41,.12); }
        .product-item { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 12px; border-bottom: 1px solid #eee5e4; cursor: pointer; font-size: 12px; }
        .product-item:last-child { border-bottom: 0; }
        .product-item:hover { background: #faf6f5; }
        .product-name { color: #3a3334; font-size: 15px; font-weight: 700; }
        .product-meta { margin-top: 3px; color: #766d6f; font-size: 11px; }
        .product-price { color: #8d2a22; font-size: 14px; font-weight: 800; white-space: nowrap; }

        .field-label { display: block; margin-bottom: 5px; color: #65748b; font-size: 10px; font-weight: 700; }
        .table-wrap { width: calc(100% - 28px); margin: 0 auto; overflow-x: auto; }
        .pos-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .pos-table th { padding: 12px 12px; border: 0; border-bottom: 1px solid #e5d8d7; color: #51474a; background: linear-gradient(180deg, #f2e8e7, #e9dada); font-size: 12px; font-weight: 750; letter-spacing: .05em; text-align: left; text-transform: uppercase; white-space: nowrap; }
        .pos-table td { padding: 12px; border: 0; border-bottom: 1px solid #eee5e4; color: #51474a; font-size: 13px; white-space: nowrap; }
        .pos-table tbody tr:last-child td { border-bottom: 0; }
        .pos-table tbody tr:hover td { background: #faf6f5; }
        .pos-table th:last-child, .pos-table td:last-child { text-align: center; }
        .text-right { text-align: right; }
        .empty-cart { padding: 48px 10px !important; color: #8290a4 !important; text-align: center; white-space: normal !important; }
        .empty-cart i { display: block; margin-bottom: 8px; color: #aab6c5; font-size: 24px; }
        .qty-control { display: flex; align-items: center; justify-content: center; gap: 3px; }
        .qty-control button, .remove-btn { width: 25px; height: 25px; border: 1px solid #e5d8d7; border-radius: 5px; color: #8d2a22; background: #fff; cursor: pointer; }
        .qty-control input { width: 38px; height: 25px; border: 1px solid #e5d8d7; border-radius: 5px; text-align: center; font-size: 11px; }
        .remove-btn { color: #c23445; }
        .summary-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; min-height: 38px; color: #65748b; font-size: 13px; }
        .summary-row strong { color: #17243a; }
        .summary-row input { width: 98px; height: 30px; padding: 0 8px; border: 1px solid #e5d8d7; border-radius: 6px; color: #3a3334; background: #fff; text-align: right; font-size: 13px; }
        .total-box { margin-top: 10px; padding: 13px; border: 1px solid #e9dada; border-radius: 8px; background: linear-gradient(180deg, #f8efef, #f4e6e4); }
        .total-label { color: #738198; font-size: 10px; font-weight: 750; letter-spacing: .08em; }
        .total-value { margin-top: 4px; color: #8d2a22; font-size: 24px; font-weight: 800; }
        .payment-box { margin-top: 13px; padding: 12px; border: 1px solid #e9dada; border-radius: 8px; background: #fbf9f9; }
        .payment-label { margin-bottom: 6px; color: #536176; font-size: 10px; font-weight: 750; }
        .payment-input { width: 100%; height: 40px; padding: 0 10px; border: 1px solid #dda4a0; border-radius: 7px; outline: none; color: #3a3334; background: #fff; text-align: right; font-size: 18px; font-weight: 700; }
        .payment-input:focus { border-color: #b94245; box-shadow: 0 0 0 3px rgba(185,66,69,.18); }
        .payment-method { display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px; }
        .payment-method button { min-height: 29px; border: 1px solid #e5d8d7; border-radius: 5px; color: #51474a; background: #fff; cursor: pointer; font-size: 10px; }
        .payment-method button.active { border-color: #b94245; color: #fff; background: #b94245; }
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
        .held-transactions-card[hidden] { display: none; }
        .held-transactions-list { display: grid; gap: 8px; }
        .held-transaction {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 10px;
            border: 1px solid #e9dada;
            border-radius: 8px;
            background: #fbf9f9;
        }
        .held-transaction-info { min-width: 0; }
        .held-transaction-title { display: block; color: #3a3334; font-size: 12px; font-weight: 700; }
        .held-transaction-detail { display: block; margin-top: 3px; color: #766d6f; font-size: 11px; }
        .held-transaction-actions { display: flex; flex: 0 0 auto; gap: 5px; }
        .held-transaction-actions .btn { min-height: 32px; padding: 0 9px; font-size: 11px; }
        .hold-confirm-modal .modal-content { overflow: hidden; border: 1px solid #ecd8d5; border-radius: 16px; box-shadow: 0 22px 60px rgba(66, 32, 34, .2); }
        .hold-confirm-modal .modal-header { padding: 22px 24px 12px; border: 0; }
        .hold-confirm-modal .modal-body { padding: 4px 24px 22px; }
        .hold-confirm-modal .modal-footer { padding: 14px 24px 20px; border: 0; background: #fbf8f7; }
        .hold-confirm-icon { display: grid; width: 56px; height: 56px; margin: 0 auto 14px; place-items: center; border-radius: 16px; color: #8d2a22; background: linear-gradient(135deg, #fae7df, #f4d8c4); font-size: 25px; }
        .hold-confirm-title { color: #3a3334; font-size: 20px; font-weight: 800; text-align: center; }
        .hold-confirm-copy { max-width: 360px; margin: 8px auto 18px; color: #766d6f; font-size: 13px; line-height: 1.55; text-align: center; }
        .hold-confirm-summary { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px; border: 1px solid #ecd8d5; border-radius: 10px; background: linear-gradient(135deg, #fffafa, #f8eeee); color: #766d6f; font-size: 13px; }
        .hold-confirm-summary strong { color: #8d2a22; font-size: 16px; }
        .hold-confirm-modal .modal-footer .btn { min-height: 40px; border-radius: 8px; font-weight: 700; }
        .hold-notification { position: fixed; right: 22px; bottom: 22px; z-index: 1090; display: flex; align-items: center; gap: 12px; max-width: min(390px, calc(100vw - 32px)); padding: 14px 18px; border: 1px solid #d8e9dd; border-radius: 12px; color: #285a42; background: #f4fbf6; box-shadow: 0 12px 32px rgba(37, 64, 48, .17); opacity: 0; pointer-events: none; transform: translateY(12px); transition: opacity .2s ease, transform .2s ease; }
        .hold-notification.show { opacity: 1; transform: translateY(0); }
        .hold-notification-icon { display: grid; width: 34px; height: 34px; flex: 0 0 34px; place-items: center; border-radius: 50%; color: #fff; background: #3b805e; font-size: 17px; }
        .hold-notification strong { display: block; color: #234b37; font-size: 13px; }
        .hold-notification span { display: block; margin-top: 2px; color: #547563; font-size: 12px; }
        .checkout-confirm-modal .modal-content { overflow: hidden; border: 1px solid #ecd8d5; border-radius: 16px; box-shadow: 0 22px 60px rgba(66, 32, 34, .2); }
        .checkout-confirm-modal .modal-header { padding: 22px 24px 12px; border: 0; }
        .checkout-confirm-modal .modal-body { padding: 4px 24px 22px; }
        .checkout-confirm-modal .modal-footer { padding: 14px 24px 20px; border: 0; background: #fbf8f7; }
        .checkout-confirm-icon { display: grid; width: 56px; height: 56px; margin: 0 auto 14px; place-items: center; border-radius: 16px; color: #8d2a22; background: linear-gradient(135deg, #fae7df, #f4d8c4); font-size: 25px; }
        .checkout-confirm-title { color: #3a3334; font-size: 20px; font-weight: 800; text-align: center; }
        .checkout-confirm-copy { max-width: 360px; margin: 8px auto 18px; color: #766d6f; font-size: 13px; line-height: 1.55; text-align: center; }
        .checkout-confirm-details { display: grid; gap: 10px; padding: 14px; border: 1px solid #ecd8d5; border-radius: 10px; background: linear-gradient(135deg, #fffafa, #f8eeee); }
        .checkout-confirm-details div { display: flex; align-items: center; justify-content: space-between; gap: 12px; color: #766d6f; font-size: 13px; }
        .checkout-confirm-details strong { color: #3a3334; font-size: 14px; }
        .checkout-confirm-details .checkout-confirm-total { padding-top: 10px; border-top: 1px solid #ecd8d5; color: #8d2a22; font-weight: 700; }
        .checkout-confirm-details .checkout-confirm-total strong { color: #8d2a22; font-size: 18px; }
        .checkout-confirm-modal .modal-footer .btn { min-height: 40px; border-radius: 8px; font-weight: 700; }

        @media (max-width: 850px) {
            .pos-layout { grid-template-columns: 1fr; }
            .pos-right { grid-template-columns: 1fr 1fr; align-items: start; }
        }

        @media (max-width: 560px) {
            .pos-heading { align-items: flex-start; flex-direction: column; }
            .transaction-chip { width: 100%; text-align: left; }
            .pos-right { grid-template-columns: 1fr; }
            .search-area { flex-direction: column; }
            .search-area .btn { width: 100%; }
            .table-wrap { width: calc(100% - 20px); }
            .table-wrap { max-width: 100%; -webkit-overflow-scrolling: touch; overscroll-behavior-x: contain; }
            .pos-table { min-width: 610px; }
            .pos-view .card-body { padding: 13px 12px; }
            .pos-view .form-control { min-height: 44px; height: 44px; font-size: 16px; }
            .field-label { font-size: 12px; }
            .qty-control button, .remove-btn { width: 36px; height: 36px; }
            .qty-control input { width: 44px; height: 36px; font-size: 16px; }
            .summary-row { min-height: 44px; font-size: 13px; }
            .summary-row input { width: 120px; height: 40px; font-size: 16px; }
            .payment-input { height: 48px; font-size: 20px; }
            .payment-method { gap: 8px; }
            .payment-method button { min-height: 44px; font-size: 12px; }
            .action-area { gap: 8px; }
            .action-area .btn, .btn-pay { min-height: 44px; }
            .btn-pay { min-height: 50px !important; }
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
                <article class="card held-transactions-card" id="heldTransactionsCard" hidden>
                    <div class="card-header card-header-row">
                        <span>Transaksi Ditahan</span>
                        <span class="item-count" id="heldTransactionCount">0</span>
                    </div>
                    <div class="card-body">
                        <div class="held-transactions-list" id="heldTransactionsList"></div>
                    </div>
                </article>
            </aside>
        </main>
    </div>

    <script id="product-data" type="application/json">@json($products)</script>
@endsection

@push('modals')
    <div class="modal fade hold-confirm-modal" id="holdConfirmationModal" tabindex="-1" aria-labelledby="holdConfirmationTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="hold-confirm-icon" aria-hidden="true"><i class="bi bi-pause-circle"></i></div>
                    <h2 class="hold-confirm-title" id="holdConfirmationTitle">Tahan Transaksi Ini?</h2>
                    <p class="hold-confirm-copy">Transaksi akan disimpan sementara. Anda dapat melayani pelanggan berikutnya dan melanjutkan pembayaran ini nanti.</p>
                    <div class="hold-confirm-summary">
                        <span id="holdConfirmItemCount">0 item</span>
                        <strong id="holdConfirmTotal">Rp 0</strong>
                    </div>
                </div>
                <div class="modal-footer justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Kembali</button>
                    <button type="button" class="btn btn-primary px-4" id="confirmHoldButton">
                        <i class="bi bi-pause-fill me-1" aria-hidden="true"></i>Ya, Tahan
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade checkout-confirm-modal" id="payConfirmationModal" tabindex="-1" aria-labelledby="payConfirmationTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="checkout-confirm-icon" aria-hidden="true"><i class="bi bi-bag-check"></i></div>
                    <h2 class="checkout-confirm-title" id="payConfirmationTitle">Konfirmasi Pembayaran</h2>
                    <p class="checkout-confirm-copy">Pastikan jumlah pembayaran sudah sesuai sebelum transaksi disimpan.</p>
                    <div class="checkout-confirm-details">
                        <div><span>Jumlah item</span><strong id="payConfirmItemCount">0 item</strong></div>
                        <div class="checkout-confirm-total"><span>Total belanja</span><strong id="payConfirmTotal">Rp 0</strong></div>
                        <div><span>Uang dibayar</span><strong id="payConfirmPaid">Rp 0</strong></div>
                        <div><span>Kembalian</span><strong id="payConfirmChange">Rp 0</strong></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Periksa Lagi</button>
                    <button type="button" class="btn btn-primary px-4" id="confirmPaymentButton">
                        <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>Konfirmasi &amp; Bayar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade checkout-confirm-modal" id="receiptConfirmationModal" tabindex="-1" aria-labelledby="receiptConfirmationTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="checkout-confirm-icon" aria-hidden="true"><i class="bi bi-receipt-cutoff"></i></div>
                    <h2 class="checkout-confirm-title" id="receiptConfirmationTitle">Pembayaran Berhasil</h2>
                    <p class="checkout-confirm-copy">Transaksi sudah tersimpan. Apakah Anda ingin mencetak struk untuk pelanggan?</p>
                    <div class="checkout-confirm-details">
                        <div><span>No. Invoice</span><strong id="receiptConfirmInvoice">-</strong></div>
                        <div class="checkout-confirm-total"><span>Total belanja</span><strong id="receiptConfirmTotal">Rp 0</strong></div>
                        <div><span>Kembalian</span><strong id="receiptConfirmChange">Rp 0</strong></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" id="skipReceiptButton" data-bs-dismiss="modal">Selesai Tanpa Cetak</button>
                    <button type="button" class="btn btn-primary px-4" id="printReceiptButton">
                        <i class="bi bi-printer me-1" aria-hidden="true"></i>Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>
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
    <div class="hold-notification" id="holdNotification" role="status" aria-live="polite">
        <span class="hold-notification-icon" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
        <div>
            <strong>Transaksi berhasil ditahan</strong>
            <span>Silakan layani pelanggan berikutnya.</span>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        const products = JSON.parse(document.querySelector('#product-data').textContent);
        let cart = [];
        let checkoutInProgress = false;
        let activeHeldTransactionId = null;
        const searchInput = document.querySelector('#searchProduct');
        const resultsBox = document.querySelector('#productResults');
        const paymentInput = document.querySelector('#payment');
        const paymentButtons = [...document.querySelectorAll('.payment-method button')];
        const heldTransactionsCard = document.querySelector('#heldTransactionsCard');
        const heldTransactionsList = document.querySelector('#heldTransactionsList');
        const holdConfirmationElement = document.querySelector('#holdConfirmationModal');
        const holdConfirmationModal = bootstrap.Modal.getOrCreateInstance(holdConfirmationElement);
        const payConfirmationElement = document.querySelector('#payConfirmationModal');
        const payConfirmationModal = bootstrap.Modal.getOrCreateInstance(payConfirmationElement);
        const receiptConfirmationElement = document.querySelector('#receiptConfirmationModal');
        const receiptConfirmationModal = bootstrap.Modal.getOrCreateInstance(receiptConfirmationElement);
        let completedCheckout = null;
        let holdNotificationTimeout;
        const heldStorageKey = @json('held-sales-' . auth()->id());
        let heldTransactions = loadHeldTransactions();
        const currentPaymentMethod = () => paymentButtons.find(button => button.classList.contains('active')).dataset.method;
        const money = (value) => new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', maximumFractionDigits:0 }).format(value);
        const totalValue = () => { const subtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0); const fee = Number(document.querySelector('#otherFee').value) || 0; return subtotal + fee; };
        function loadHeldTransactions() {
            try {
                const saved = localStorage.getItem(heldStorageKey);
                if (!saved) return [];

                const parsed = JSON.parse(saved);
                if (!Array.isArray(parsed)) throw new Error('Data transaksi tertahan tidak valid.');

                return parsed;
            } catch (error) {
                console.error('Gagal membaca transaksi tertahan.', error);
                alert('Data transaksi tertahan tidak dapat dibaca. Hapus data situs yang rusak sebelum melanjutkan.');
                return [];
            }
        }
        function saveHeldTransactions() {
            try {
                localStorage.setItem(heldStorageKey, JSON.stringify(heldTransactions));
            } catch (error) {
                console.error('Gagal menyimpan transaksi tertahan.', error);
                alert('Transaksi tidak dapat ditahan di browser ini. Periksa ruang penyimpanan browser.');
                return false;
            }

            renderHeldTransactions();
            return true;
        }
        function resetActiveTransaction() {
            cart = [];
            activeHeldTransactionId = null;
            document.querySelector('#otherFee').value = 0;
            paymentInput.value = '';
            paymentButtons.forEach(button => button.classList.toggle('active', button.dataset.method === 'tunai'));
            paymentInput.readOnly = false;
            document.querySelector('#paymentLabel').textContent = 'Uang Dibayar';
            renderCart();
        }
        function renderHeldTransactions() {
            heldTransactionsCard.hidden = heldTransactions.length === 0;
            document.querySelector('#heldTransactionCount').textContent = heldTransactions.length;
            heldTransactionsList.replaceChildren();

            heldTransactions.forEach(transaction => {
                const row = document.createElement('div');
                row.className = 'held-transaction';

                const info = document.createElement('div');
                info.className = 'held-transaction-info';
                const title = document.createElement('span');
                title.className = 'held-transaction-title';
                title.textContent = `Transaksi ${new Date(transaction.createdAt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}`;
                const detail = document.createElement('span');
                detail.className = 'held-transaction-detail';
                detail.textContent = `${transaction.cart.reduce((sum, item) => sum + item.qty, 0)} item · ${money(transaction.cart.reduce((sum, item) => sum + item.price * item.qty, 0) + transaction.otherFee)}`;
                info.append(title, detail);

                const actions = document.createElement('div');
                actions.className = 'held-transaction-actions';
                const resumeButton = document.createElement('button');
                resumeButton.type = 'button';
                resumeButton.className = 'btn btn-primary';
                resumeButton.dataset.action = 'resume';
                resumeButton.dataset.id = transaction.id;
                resumeButton.textContent = 'Lanjutkan';
                const deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.className = 'btn btn-outline-danger';
                deleteButton.dataset.action = 'delete';
                deleteButton.dataset.id = transaction.id;
                deleteButton.setAttribute('aria-label', 'Hapus transaksi tertahan');
                deleteButton.textContent = 'Hapus';
                actions.append(resumeButton, deleteButton);
                row.append(info, actions);
                heldTransactionsList.append(row);
            });
        }
        function holdActiveTransaction() {
            if (!cart.length) return false;

            const existingIndex = heldTransactions.findIndex(transaction => transaction.id === activeHeldTransactionId);
            const transaction = {
                id: activeHeldTransactionId || `${Date.now()}-${Math.random().toString(36).slice(2)}`,
                createdAt: existingIndex >= 0 ? heldTransactions[existingIndex].createdAt : new Date().toISOString(),
                cart: cart.map(item => ({ ...item })),
                otherFee: Number(document.querySelector('#otherFee').value) || 0,
                payment: Number(paymentInput.value) || 0,
                paymentMethod: currentPaymentMethod(),
            };

            if (existingIndex >= 0) {
                heldTransactions[existingIndex] = transaction;
            } else {
                heldTransactions.unshift(transaction);
            }

            if (!saveHeldTransactions()) return false;

            resetActiveTransaction();
            return true;
        }
        function resumeHeldTransaction(id) {
            if (cart.length) {
                return alert('Tahan atau batalkan transaksi yang sedang aktif sebelum melanjutkan transaksi lain.');
            }

            const index = heldTransactions.findIndex(transaction => transaction.id === id);
            if (index < 0) return;
            const [transaction] = heldTransactions.splice(index, 1);
            if (!saveHeldTransactions()) {
                heldTransactions.splice(index, 0, transaction);
                return;
            }

            cart = transaction.cart;
            activeHeldTransactionId = transaction.id;
            document.querySelector('#otherFee').value = transaction.otherFee;
            paymentInput.value = transaction.payment;
            paymentButtons.forEach(button => button.classList.toggle('active', button.dataset.method === transaction.paymentMethod));
            paymentInput.readOnly = transaction.paymentMethod === 'qris';
            document.querySelector('#paymentLabel').textContent = transaction.paymentMethod === 'qris' ? 'Nominal QRIS' : 'Uang Dibayar';
            renderCart();
            paymentInput.value = transaction.payment;
            calculateChange();
        }
        function searchProduct() { const keyword = searchInput.value.toLowerCase().trim(); if (!keyword) { resultsBox.style.display = 'none'; return; } const matches = products.filter(product => `${product.name} ${product.category || 'Umum'} ${product.id}`.toLowerCase().includes(keyword)); resultsBox.innerHTML = matches.length ? matches.map(product => `<div class="product-item" data-product-id="${product.id}"><div><div class="product-name">${product.name}</div><div class="product-meta">${product.category || 'Umum'} - Stok ${product.stock}</div></div><div class="product-price">${money(product.price)}</div></div>`).join('') : '<div class="product-item">Barang tidak ditemukan</div>'; resultsBox.style.display = 'block'; }
        function addToCart(id) { const product = products.find(item => item.id === id); if (!product || product.stock < 1) return; const existing = cart.find(item => item.id === id); if (existing) { if (existing.qty < product.stock) existing.qty++; } else cart.push({ ...product, qty:1 }); searchInput.value = ''; resultsBox.style.display = 'none'; renderCart(); }
        function renderCart() { const body = document.querySelector('#cartBody'); if (!cart.length) { body.innerHTML = '<tr><td colspan="6" class="empty-cart"><i class="bi bi-basket2"></i>Keranjang masih kosong.<br>Silakan cari atau pilih barang.</td></tr>'; } else body.innerHTML = cart.map((item, index) => `<tr><td>${index + 1}</td><td><strong>${item.name}</strong><div class="product-meta">${item.category || 'Umum'}</div></td><td>${money(item.price)}</td><td><div class="qty-control"><button type="button" data-action="decrease" data-id="${item.id}">-</button><input type="number" min="1" max="${item.stock}" value="${item.qty}" data-action="quantity" data-id="${item.id}"><button type="button" data-action="increase" data-id="${item.id}">+</button></div></td><td class="text-right"><strong>${money(item.price * item.qty)}</strong></td><td><button class="remove-btn" type="button" data-action="remove" data-id="${item.id}">x</button></td></tr>`).join(''); calculateTotal(); }
        function calculateTotal() { const quantity = cart.reduce((sum, item) => sum + item.qty, 0); const subtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0); document.querySelector('#totalQty').textContent = quantity; document.querySelector('#itemCount').textContent = `${quantity} Item`; document.querySelector('#subtotal').textContent = money(subtotal); document.querySelector('#grandTotal').textContent = money(totalValue()); if (currentPaymentMethod() === 'qris') paymentInput.value = totalValue(); calculateChange(); }
        function calculateChange() { const difference = (Number(document.querySelector('#payment').value) || 0) - totalValue(); const box = document.querySelector('#changeBox'); const label = document.querySelector('#changeLabel'); box.classList.toggle('short-payment', difference < 0); label.textContent = difference < 0 ? 'UANG KURANG' : 'KEMBALIAN'; document.querySelector('#change').textContent = money(Math.abs(difference)); }
        searchInput.addEventListener('input', searchProduct); document.querySelector('#searchButton').addEventListener('click', searchProduct); paymentInput.addEventListener('input', calculateChange); document.querySelector('#otherFee').addEventListener('input', calculateTotal);
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
        document.querySelector('#cancelButton').addEventListener('click', () => {
            if (cart.length && !confirm('Batalkan transaksi ini?')) return;
            if (activeHeldTransactionId) {
                heldTransactions = heldTransactions.filter(transaction => transaction.id !== activeHeldTransactionId);
                saveHeldTransactions();
            }
            resetActiveTransaction();
        });
        function showHoldNotification() {
            const notification = document.querySelector('#holdNotification');
            notification.classList.add('show');
            clearTimeout(holdNotificationTimeout);
            holdNotificationTimeout = setTimeout(() => notification.classList.remove('show'), 4200);
        }
        document.querySelector('#holdButton').addEventListener('click', () => {
            if (!cart.length) {
                return alert('Tidak ada transaksi untuk ditahan.');
            }

            const itemCount = cart.reduce((sum, item) => sum + item.qty, 0);
            document.querySelector('#holdConfirmItemCount').textContent = `${itemCount} item`;
            document.querySelector('#holdConfirmTotal').textContent = money(totalValue());
            holdConfirmationModal.show();
        });
        document.querySelector('#confirmHoldButton').addEventListener('click', event => {
            const button = event.currentTarget;
            button.disabled = true;

            if (holdActiveTransaction()) {
                holdConfirmationModal.hide();
                showHoldNotification();
            }

            button.disabled = false;
        });
        heldTransactionsList.addEventListener('click', event => {
            const button = event.target.closest('[data-action]');
            if (!button) return;

            if (button.dataset.action === 'resume') {
                resumeHeldTransaction(button.dataset.id);
            } else if (button.dataset.action === 'delete' && confirm('Hapus transaksi yang ditahan ini?')) {
                const index = heldTransactions.findIndex(transaction => transaction.id === button.dataset.id);
                if (index < 0) return;
                const [transaction] = heldTransactions.splice(index, 1);
                if (!saveHeldTransactions()) heldTransactions.splice(index, 0, transaction);
            }
        });
        async function submitCheckout(button) {
            if (checkoutInProgress) {
                return;
            }

            const payment = Number(document.querySelector('#payment').value) || 0;

    if (!cart.length) {
        return alert('Keranjang masih kosong.');
    }

    if (payment < totalValue()) {
        return alert('Uang pembayaran masih kurang.');
    }

    checkoutInProgress = true;
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
                items: cart.map(item => ({
                    product_id: item.id,
                    quantity: item.qty
                })),
                customer_type: 'Umum',
                customer_contact: null,
                discount_percent: 0,
                discount_amount: 0,
                tax: 0,
                other_fee: Number(document.querySelector('#otherFee').value) || 0,
                paid: payment,
                payment_method: document.querySelector('.payment-method button.active').dataset.method
            })
        });

        const result = await response.json();

        if (!response.ok) {
            const errors = Object.values(result.errors || {}).flat();

            throw new Error(
                errors.join('\n') ||
                result.message ||
                'Transaksi gagal disimpan.'
            );
        }

        if (activeHeldTransactionId) {
            heldTransactions = heldTransactions.filter(transaction => transaction.id !== activeHeldTransactionId);
            activeHeldTransactionId = null;
            saveHeldTransactions();
        }

        completedCheckout = result;
        document.querySelector('#receiptConfirmInvoice').textContent = result.invoice;
        document.querySelector('#receiptConfirmTotal').textContent = money(result.total);
        document.querySelector('#receiptConfirmChange').textContent = money(result.change);
        receiptConfirmationModal.show();

    } catch (error) {

        alert(
            error.message ||
            'Transaksi gagal disimpan. Silakan coba lagi.'
        );

        checkoutInProgress = false;
        button.disabled = false;
    }
}

const qrisModalElement = document.querySelector('#qrisSimulationModal');
const qrisModal = qrisModalElement && typeof bootstrap !== 'undefined' ? bootstrap.Modal.getOrCreateInstance(qrisModalElement) : null;

document.querySelector('#payButton').addEventListener('click', event => {
    if (!cart.length) {
        return alert('Keranjang masih kosong.');
    }

    const payment = Number(paymentInput.value) || 0;

    if (payment < totalValue()) {
        return alert('Uang pembayaran masih kurang.');
    }

    document.querySelector('#payConfirmItemCount').textContent = `${cart.reduce((sum, item) => sum + item.qty, 0)} item`;
    document.querySelector('#payConfirmTotal').textContent = money(totalValue());
    document.querySelector('#payConfirmPaid').textContent = money(payment);
    document.querySelector('#payConfirmChange').textContent = money(Math.max(0, payment - totalValue()));
    payConfirmationModal.show();
});
document.querySelector('#confirmPaymentButton').addEventListener('click', event => {
    payConfirmationModal.hide();
    if (currentPaymentMethod() === 'qris') {
        paymentInput.value = totalValue();
        const amountEl = document.querySelector('#qrisSimulationAmount');
        if (amountEl) amountEl.textContent = money(totalValue());
        if (qrisModal) {
            qrisModal.show();
        } else {
            submitCheckout(event.currentTarget);
        }
        return;
    }

    submitCheckout(document.querySelector('#payButton'));
});
receiptConfirmationElement.addEventListener('hidden.bs.modal', () => {
    if (completedCheckout) {
        window.location.href = completedCheckout.history_url;
    }
});
document.querySelector('#printReceiptButton').addEventListener('click', async event => {
    const button = event.currentTarget;
    if (!completedCheckout) return;

    button.disabled = true;
    try {
        const printResponse = await fetch(
            @json(route('cashier.receipt.print', ['sale' => '__SALE_ID__']))
                .replace('__SALE_ID__', completedCheckout.sale_id),
            {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }
        );

        if (!printResponse.ok) {
            throw new Error('Transaksi berhasil, tetapi struk gagal dicetak. Periksa koneksi printer.');
        }

        receiptConfirmationModal.hide();
    } catch (error) {
        console.error('Gagal mencetak struk.', error);
        alert(error.message || 'Transaksi berhasil, tetapi struk gagal dicetak.');
        button.disabled = false;
    }
});
        const confirmQrisBtn = document.querySelector('#confirmQrisSimulation');
        if (confirmQrisBtn) {
            confirmQrisBtn.addEventListener('click', () => {
                if (qrisModal) qrisModal.hide();
                submitCheckout(document.querySelector('#payButton'));
            });
        }
        document.querySelector('#currentDate').textContent = new Date().toLocaleString('id-ID'); document.addEventListener('keydown', event => { if (event.key === 'F2') { event.preventDefault(); searchInput.focus(); } if (event.key === 'F4') { event.preventDefault(); document.querySelector('#payment').focus(); } if (event.key === 'Escape') document.querySelector('#cancelButton').click(); }); renderCart(); renderHeldTransactions();
    </script>
@endpush
