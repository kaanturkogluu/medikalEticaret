@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-lg" style="border-radius: 15px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="card-header bg-dark text-white border-0 py-3" style="border-radius: 15px 15px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i> n11 Sipariş Listesi (API Test)</h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 10px;">
                        <i class="fas fa-info-circle me-2"></i> n11 API'den gelen ham yanıt aşağıda listelenmiştir.
                    </div>

                    @if(isset($orders) && count($orders) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sipariş No</th>
                                        <th>Paket ID</th>
                                        <th>Durum</th>
                                        <th class="text-end">Tutar</th>
                                        <th>Son Güncelleme</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // n11 Rest API returns a direct list of items in the collection
                                        $orderList = $orders;
                                    @endphp
                                    
                                    @forelse($orderList as $package)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $package['orderNumber'] ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $package['customerfullName'] ?? 'Müşteri Belirtilmemiş' }}</small>
                                            </td>
                                            <td>{{ $package['id'] ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $status = $package['shipmentPackageStatus'] ?? 'Unknown';
                                                    $badgeClass = match($status) {
                                                        'Created' => 'bg-info',
                                                        'Picking' => 'bg-warning text-dark',
                                                        'Shipped' => 'bg-primary',
                                                        'Delivered' => 'bg-success',
                                                        'Cancelled' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }} rounded-pill px-3">
                                                    {{ $status }}
                                                </span>
                                            </td>
                                            <td class="fw-bold text-end">{{ number_format($package['totalAmount'] ?? 0, 2, ',', '.') }} TL</td>
                                            <td>
                                                @if(isset($package['lastModifiedDate']))
                                                    {{ date('d.m.Y H:i', $package['lastModifiedDate'] / 1000) }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-dark view-raw" data-json="{{ json_encode($package) }}">
                                                    <i class="fas fa-eye me-1"></i> Detay
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
                                                Henüz sipariş bulunamadı veya API boş döndü.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 p-3 bg-light rounded shadow-sm">
                            <h6><i class="fas fa-terminal me-2"></i> Full API Response:</h6>
                            <pre class="bg-dark text-success p-3 rounded" style="max-height: 400px; overflow-y: auto;">{{ json_encode($orders, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                            <p>API'den veri alınamadı ya da boş yanıt geldi.</p>
                            <pre class="bg-dark text-danger p-3 rounded text-start mt-3">@if(isset($orders)){{ json_encode($orders, JSON_PRETTY_PRINT) }}@else Yanıt Yok @endif</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Raw JSON -->
<div class="modal fade" id="rawJsonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title">Sipariş Detay (JSON)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="jsonViewer" class="bg-light p-3 rounded"></pre>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.view-raw');
    const modal = new bootstrap.Modal(document.getElementById('rawJsonModal'));
    const viewer = document.getElementById('jsonViewer');

    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            const data = JSON.parse(this.getAttribute('data-json'));
            viewer.textContent = JSON.stringify(data, null, 4);
            modal.show();
        });
    });
});
</script>

<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }
    .card {
        transition: transform 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection
