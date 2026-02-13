@extends('app')

@section('title', 'Status Occupancy')

@push('styles')
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

<style>
/* ========================================
   LOBBY DISPLAY - TIDAK MENUTUPI HEADER WEB
   ======================================== */

/* Content Header (bukan fullscreen header) */
.lobby-content-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px 30px;
    border-radius: 15px;
    color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    margin-bottom: 20px;
}

.content-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: white;
    margin: 0;
}

.content-subtitle {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.9);
    margin-top: 2px;
}

.header-time {
    font-size: 2rem;
    font-weight: 700;
    color: white;
    line-height: 1;
}

.header-date {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.9);
    margin-top: 3px;
}

.last-update {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.8);
    margin-top: 5px;
}

/* ========================================
   SWIPER CONTAINER (DALAM AREA CONTENT)
   ======================================== */
.swiper-container {
    width: 100%;
    height: calc(100vh - 350px); /* Dikurangi header web + navbar + content header */
    min-height: 500px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 15px;
    overflow: hidden;
}

.swiper-slide {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 25px;
}

/* ========================================
   AREA CARDS GRID (2 PER SLIDE - RESPONSIVE)
   ======================================== */
.areas-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    width: 100%;
    max-width: 1800px;
    height: 100%;
    padding: 20px;
}

/* ========================================
   AREA CARD - CLEAN & CLEAR
   ======================================== */
.area-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transition: transform 0.3s ease;
    height: 100%;
    min-height: 0;
}

.area-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.2);
}

/* Area Header - GRADIENT */
.area-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 18px 25px;
    color: white;
    flex-shrink: 0;
}

.area-name {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}

.area-location {
    font-size: 0.9rem;
    opacity: 0.95;
    margin-top: 5px;
}

/* Breakdown List */
.breakdown-list {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 15px;
    overflow-y: auto;
    min-height: 0;
}

/* Custom Scrollbar */
.breakdown-list::-webkit-scrollbar {
    width: 6px;
}

.breakdown-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.breakdown-list::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: 10px;
}

.breakdown-list::-webkit-scrollbar-thumb:hover {
    background: #764ba2;
}

/* ========================================
   BREAKDOWN ITEM - CLEAR SEPARATION
   ======================================== */
.breakdown-item {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 15px;
    border-left: 5px solid #667eea;
    transition: all 0.3s ease;
}

.breakdown-item:hover {
    border-left-width: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.breakdown-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.tipe-label {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* TIPE CODE - BADGE STYLE */
.tipe-code {
    font-weight: 800;
    font-size: 1.1rem;
    color: #667eea;
    letter-spacing: 0.5px;
    background: rgba(102, 126, 234, 0.1);
    padding: 4px 12px;
    border-radius: 8px;
}

.tipe-name {
    font-weight: 600;
    font-size: 1.05rem;
    color: #333;
}

/* VALUE - LARGE & BOLD */
.breakdown-value {
    font-size: 1.5rem;
    font-weight: 700;
}

/* ========================================
   PROGRESS BAR - CLEAR COLOR INDICATORS
   ======================================== */
.simple-progress {
    height: 35px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    border: 2px solid #dee2e6;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

.simple-progress-fill {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    color: white;
    transition: width 0.5s ease, background-color 0.5s ease;
    text-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

/* ========================================
   COLOR INDICATORS (CLEAR & DISTINCT)
   ======================================== */
/* GREEN: Available (0-79%) */
.status-available {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}
.text-available { 
    color: #28a745 !important; 
}

/* YELLOW: Almost Full (80-99%) */
.status-almost-full {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
}
.text-almost-full { 
    color: #ff8c00 !important; 
}

/* RED: Full (100%) */
.status-full {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
}
.text-full { 
    color: #dc3545 !important; 
}

/* ========================================
   SWIPER NAVIGATION
   ======================================== */
.swiper-button-next,
.swiper-button-prev {
    color: white;
    background: rgba(102, 126, 234, 0.8);
    width: 55px;
    height: 55px;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.swiper-button-next:after,
.swiper-button-prev:after {
    font-size: 24px;
    font-weight: 700;
}

.swiper-button-next:hover,
.swiper-button-prev:hover {
    background: rgba(102, 126, 234, 1);
    transform: scale(1.1);
}

.swiper-pagination {
    bottom: 15px !important;
}

.swiper-pagination-bullet {
    background: white;
    opacity: 0.5;
    width: 12px;
    height: 12px;
    margin: 0 6px !important;
}

.swiper-pagination-bullet-active {
    opacity: 1;
    background: white;
    width: 30px;
    border-radius: 6px;
}

/* ========================================
   AUTO-REFRESH INDICATOR
   ======================================== */
.refresh-indicator {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: rgba(255, 255, 255, 0.95);
    padding: 10px 20px;
    border-radius: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    font-size: 0.9rem;
    font-weight: 600;
    color: #333;
    z-index: 999;
    display: flex;
    align-items: center;
    gap: 8px;
}

.refresh-icon {
    animation: rotate 2s linear infinite;
    color: #667eea;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* ========================================
   EMPTY STATE
   ======================================== */
.empty-state {
    text-align: center;
    padding: 80px 40px;
    background: white;
    border-radius: 20px;
    max-width: 600px;
    margin: 0 auto;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.empty-state i {
    font-size: 5rem;
    color: #dc3545;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 1.8rem;
    color: #333;
    margin-bottom: 10px;
}

.empty-state p {
    font-size: 1.1rem;
    color: #666;
}

/* ========================================
   RESPONSIVE
   ======================================== */
@media (max-width: 1400px) {
    .areas-grid {
        gap: 20px;
    }
    
    .area-name {
        font-size: 1.4rem;
    }
    
    .breakdown-value {
        font-size: 1.3rem;
    }
}

@media (max-width: 1200px) {
    .lobby-content-header {
        padding: 15px 20px;
    }
    
    .content-title {
        font-size: 1.5rem;
    }
    
    .header-time {
        font-size: 1.7rem;
    }
    
    .swiper-container {
        height: calc(100vh - 320px);
        min-height: 400px;
    }
    
    .areas-grid {
        grid-template-columns: 1fr;
        padding: 15px;
    }
    
    .area-card {
        max-height: 70vh;
    }
}

@media (max-width: 768px) {
    .lobby-content-header {
        padding: 12px 20px;
    }
    
    .content-title {
        font-size: 1.3rem;
    }
    
    .swiper-container {
        height: calc(100vh - 300px);
        min-height: 350px;
    }
    
    .swiper-slide {
        padding: 15px;
    }
    
    .areas-grid {
        gap: 15px;
        padding: 10px;
    }
    
    .area-name {
        font-size: 1.2rem;
    }
    
    .breakdown-value {
        font-size: 1.2rem;
    }
    
    .refresh-indicator {
        bottom: 15px;
        right: 15px;
        padding: 8px 16px;
        font-size: 0.8rem;
    }
}
</style>
@endpush

@section('content')
<!-- ========================================
     LOBBY DISPLAY HEADER (DI DALAM CONTENT)
     ======================================== -->
<div class="lobby-content-header mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="content-title">
                <i class="fas fa-parking"></i> Status Occupancy
            </h1>
            <div class="content-subtitle">
                Real-time Parking Availability
            </div>
            <div class="last-update">
                <i class="fas fa-sync-alt me-1"></i>
                Last Update: <span id="lastUpdate">{{ now()->format('H:i:s') }}</span>
            </div>
        </div>
        <div class="text-end">
            <div class="header-time" id="currentTime">
                {{ now()->format('H:i') }}
            </div>
            <div class="header-date">
                {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </div>
        </div>
    </div>
</div>

<!-- ========================================
     SWIPER CONTAINER (SLIDES)
     ======================================== -->
    <div class="swiper-container">
        <div class="swiper-wrapper">
            @if(count($areas) > 0)
                @foreach($areas->chunk(2) as $chunkIndex => $areaChunk)
                <div class="swiper-slide">
                    <div class="areas-grid">
                        @foreach($areaChunk as $area)
                        <!-- ========================================
                             AREA CARD
                             ======================================== -->
                        <div class="area-card">
                            <!-- Area Header -->
                            <div class="area-header">
                                <div class="area-name">{{ $area['area_name'] }}</div>
                                <div class="area-location">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $area['area_lokasi'] }}
                                </div>
                            </div>

                            <!-- Breakdown List -->
                            <div class="breakdown-list">
                                @foreach($area['breakdown'] as $tipe)
                                <div class="breakdown-item">
                                    <!-- Header -->
                                    <div class="breakdown-header">
                                        <div class="tipe-label">
                                            <span class="tipe-code">{{ $tipe['kode_tipe'] }}</span>
                                            <span class="tipe-name">{{ $tipe['tipe'] }}</span>
                                        </div>
                                        <div class="breakdown-value text-{{ $tipe['status'] }}">
                                            {{ $tipe['terpakai'] }}/{{ $tipe['total'] }}
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="simple-progress">
                                        <div class="simple-progress-fill status-{{ $tipe['status'] }}" 
                                             style="width: {{ $tipe['rate'] }}%;">
                                            @if($tipe['status'] == 'full')
                                                <i class="fas fa-exclamation-circle me-2"></i>PENUH
                                            @elseif($tipe['status'] == 'almost-full')
                                                <i class="fas fa-exclamation-triangle me-2"></i>{{ $tipe['tersedia'] }} TERSISA
                                            @else
                                                <i class="fas fa-check-circle me-2"></i>{{ $tipe['tersedia'] }} SLOT TERSEDIA
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @else
                <!-- ========================================
                     EMPTY STATE
                     ======================================== -->
                <div class="swiper-slide">
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>Tidak Ada Data</h3>
                        <p>Belum ada konfigurasi area parkir yang tersedia</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Navigation Arrows -->
        @if(count($areas) > 2)
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
        @endif
    </div>

    <!-- ========================================
         AUTO-REFRESH INDICATOR
         ======================================== -->
    <div class="refresh-indicator">
        <i class="fas fa-sync-alt refresh-icon"></i>
        <span>Auto-refresh 30s</span>
    </div>
</div>
@endsection

@push('scripts')
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
(function() {
    'use strict';
    
    console.log('=== LOBBY DISPLAY INITIALIZED ===');
    
    // ========================================
    // SWIPER INITIALIZATION
    // ========================================
    const totalAreas = {{ count($areas) }};
    const hasMultipleSlides = totalAreas > 2;
    
    const swiper = new Swiper('.swiper-container', {
        slidesPerView: 1,
        spaceBetween: 0,
        loop: hasMultipleSlides,
        autoplay: hasMultipleSlides ? {
            delay: 10000, // 10 seconds per slide
            disableOnInteraction: false,
        } : false,
        navigation: hasMultipleSlides ? {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        } : false,
        pagination: hasMultipleSlides ? {
            el: '.swiper-pagination',
            clickable: true,
        } : false,
        effect: 'slide', // Smooth slide effect
        speed: 800, // Transition speed
    });
    
    console.log('Swiper initialized:', {
        totalAreas: totalAreas,
        slides: Math.ceil(totalAreas / 2),
        autoplay: hasMultipleSlides
    });
    
    // ========================================
    // REAL-TIME CLOCK UPDATE
    // ========================================
    function updateTime() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit'
        });
        
        const timeElement = document.getElementById('currentTime');
        if (timeElement) {
            timeElement.textContent = timeStr;
        }
    }
    
    // Update every second
    setInterval(updateTime, 1000);
    updateTime();
    
    // ========================================
    // AUTO-REFRESH DATA (Every 30 seconds)
    // ========================================
    let refreshInterval = setInterval(function() {
        console.log('Auto-refreshing data...');
        
        // Update last update timestamp
        const now = new Date();
        const lastUpdateElement = document.getElementById('lastUpdate');
        if (lastUpdateElement) {
            lastUpdateElement.textContent = now.toLocaleTimeString('id-ID');
        }
        
        console.log('Data refreshed at:', now.toLocaleTimeString('id-ID'));
        
        // Reload page to show updated data
        setTimeout(() => {
            location.reload();
        }, 500);
    }, 30000); // 30 seconds
    
    // ========================================
    // CLEANUP ON PAGE UNLOAD
    // ========================================
    window.addEventListener('beforeunload', function() {
        clearInterval(refreshInterval);
    });
    
    console.log('=== LOBBY DISPLAY READY ===');
})();
</script>
@endpush