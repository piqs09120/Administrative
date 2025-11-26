<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Visitor Logs & Analytics - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @vite(['resources/css/soliera.css'])
  <style>
    /* Responsive chart containers */
    .chart-container {
      position: relative;
      height: 250px;
      width: 100%;
    }
    
    @media (max-width: 1366px) {
      .chart-container {
        height: 200px;
      }
    }
    
    @media (max-width: 768px) {
      .chart-container {
        height: 180px;
      }
    }
    
    /* Ensure charts don't overflow */
    canvas {
      max-width: 100% !important;
      height: auto !important;
    }
    
    /* Duration badge styling - responsive and accessible */
    .duration-display, .live-duration {
      cursor: help;
      transition: all 0.2s ease;
      display: inline-flex;
      max-width: none;
      width: auto;
    }
    
    .duration-display:hover, .live-duration:hover {
      transform: scale(1.05);
    }
    
    /* Duration pill styling */
    .duration-pill {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 9999px;
      padding: 0.125rem 0.625rem;
      font-size: 0.75rem;
      font-weight: 500;
      white-space: nowrap;
      font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
      font-variant-numeric: tabular-nums;
      border: 1px solid;
    }
    
    /* Duration pill color variants */
    .duration-pill--short {
      background-color: #f0fdf4;
      color: #166534;
      border-color: #bbf7d0;
    }
    
    .duration-pill--medium {
      background-color: #fffbeb;
      color: #92400e;
      border-color: #fed7aa;
    }
    
    .duration-pill--long {
      background-color: #fef2f2;
      color: #991b1b;
      border-color: #fecaca;
    }
    
    .duration-pill--error {
      background-color: #fef2f2;
      color: #991b1b;
      border-color: #fecaca;
    }
    
    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
      .duration-pill--short {
        background-color: #064e3b;
        color: #a7f3d0;
        border-color: #065f46;
      }
      
      .duration-pill--medium {
        background-color: #78350f;
        color: #fde68a;
        border-color: #92400e;
      }
      
      .duration-pill--long {
        background-color: #7f1d1d;
        color: #fecaca;
        border-color: #991b1b;
      }
      
      .duration-pill--error {
        background-color: #7f1d1d;
        color: #fecaca;
        border-color: #991b1b;
      }
    }
    
    /* Responsive sizing */
    .duration-pill {
      font-size: 0.625rem; /* 10px on mobile */
      padding: 0.125rem 0.5rem;
    }
    
    @media (min-width: 640px) {
      .duration-pill {
        font-size: 0.75rem; /* 12px on small screens */
        padding: 0.125rem 0.625rem;
      }
    }
    
    @media (min-width: 768px) {
      .duration-pill {
        font-size: 0.875rem; /* 14px on medium+ screens */
        padding: 0.125rem 0.75rem;
      }
    }
    
    /* Responsive text shortening for very small screens */
    @media (max-width: 480px) {
      .duration-pill {
        font-size: 0.625rem;
        padding: 0.125rem 0.375rem;
      }
    }
    
    /* Checkout time display styling */
    .checkout-time-display {
      cursor: help;
      transition: all 0.2s ease;
      font-weight: 500;
    }
    
    .checkout-time-display:hover {
      transform: scale(1.05);
      color: #3b82f6;
    }
    
    /* Still in badge styling */
    .badge-primary {
      background-color: #3b82f6 !important;
      color: white !important;
      font-weight: 500;
    }
    
    /* Checkout icon button (arrow with vertical bar) */
    .checkout-icon-btn {
      padding: 2px 4px;
      border-radius: 4px;
      background-color: #fff5ed;
      border: 1px solid #f97316;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .checkout-icon {
      position: relative;
      width: 14px;
      height: 14px;
    }

    .checkout-icon::before {
      content: '';
      position: absolute;
      left: 1px;
      top: 2px;
      bottom: 2px;
      width: 2px;
      background-color: #f97316;
      border-radius: 999px;
    }

    .checkout-icon::after {
      content: '';
      position: absolute;
      left: 6px;
      top: 3px;
      width: 0;
      height: 0;
      border-top: 4px solid transparent;
      border-bottom: 4px solid transparent;
      border-left: 6px solid #f97316;
    }
    
    /* Table column sizing for duration */
    .duration-column {
      min-width: 90px;
      width: 110px;
      max-width: 160px;
    }
    
    /* Ensure duration cell doesn't truncate */
    .duration-cell {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      min-width: 0;
      overflow: visible;
    }
    
    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
      .duration-display, .live-duration {
        transition: none;
      }
      
      .duration-display:hover, .live-duration:hover {
        transform: none;
      }
    }

    /* Reports & Analytics Animations */
    .reports-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
    }

    .reports-card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .progress-bar {
      transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .progress-bar::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }

    .stat-number {
      transition: all 0.3s ease;
      display: inline-block;
    }

    .stat-number.updating {
      color: #3b82f6;
      transform: scale(1.1);
    }

    .department-item, .purpose-item {
      transition: all 0.3s ease;
      cursor: pointer;
      border-radius: 8px;
      padding: 8px;
    }

    .department-item:hover, .purpose-item:hover {
      background-color: #f8fafc;
      transform: translateX(4px);
    }

    .badge-pulse {
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.7; }
    }

    .chart-container {
      position: relative;
      overflow: hidden;
    }

    .chart-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
      animation: chartLoad 2s ease-in-out;
    }

    @keyframes chartLoad {
      0% { left: -100%; }
      100% { left: 100%; }
    }

    .summary-section {
      transition: all 0.3s ease;
    }

    .summary-section:hover {
      transform: translateY(-2px);
    }

    .highlight-item {
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .highlight-item:hover {
      background-color: rgba(34, 197, 94, 0.1);
      transform: translateX(4px);
    }

    .improvement-item {
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .improvement-item:hover {
      background-color: rgba(251, 146, 60, 0.1);
      transform: translateX(4px);
    }

    .recommendation-item {
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .recommendation-item:hover {
      background-color: rgba(59, 130, 246, 0.1);
      transform: translateX(4px);
    }

    .interactive-row {
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    .interactive-row:hover td {
      background-color: #ffecec !important;
    }

    .interactive-row.active td {
      background-color: #ffd9d9 !important;
    }

    .visitor-panel-container {
      position: fixed;
      inset: 0;
      display: flex;
      justify-content: flex-end;
      align-items: stretch;
      pointer-events: none;
      z-index: 70;
    }

    .visitor-panel-overlayVisitor {
      position: absolute;
      inset: 0;
      background-color: rgba(15, 23, 42, 0.55);
      cursor: pointer;
      pointer-events: auto;
    }

    .visitor-details-panel {
      position: relative;
      width: 100%;
      max-width: 360px;
      background-color: #ffffff;
      height: 100%;
      box-shadow: -12px 0 35px rgba(15, 23, 42, 0.2);
      padding: 24px 22px 32px;
      display: flex;
      flex-direction: column;
      animation: slideIn 0.25s ease forwards;
      pointer-events: auto;
    }

    @keyframes slideIn {
      from { transform: translateX(30px); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    .visitor-detail-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }

    .visitor-detail-header h3 {
      font-size: 18px;
      font-weight: 600;
      color: #e6731b;
    }

    .visitor-detail-card {
      display: flex;
      flex-direction: column;
      gap: 16px;
      flex: 1;
      overflow-y: auto;
    }

    .visitor-profile-block {
      display: flex;
      align-items: center;
      gap: 16px;
      background: #ffffff;
      padding: 18px;
      border-radius: 18px;
      border: 1px solid rgba(148, 163, 184, 0.2);
      box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
    }

    .visitor-profile-block img,
    .visitor-profile-block .avatar-fallback {
      width: 58px;
      height: 58px;
      border-radius: 50%;
      object-fit: cover;
      background-color: #e2e8f0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      color: #0f172a;
    }

    .visitor-profile-block h4 {
      font-size: 20px;
      font-weight: 600;
      color: #0f172a;
    }

    .visitor-profile-block p {
      font-size: 13px;
      color: #94a3b8;
    }

    .visitor-detail-section {
      background: #ffffff;
      border-radius: 18px;
      padding: 18px 20px;
      margin-bottom: 18px;
      border: 1px solid rgba(15, 23, 42, 0.05);
    }

    .visitor-detail-section h4 {
      font-size: 12px;
      font-weight: 600;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 12px;
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      padding: 6px 0;
      font-size: 14px;
      color: #0f172a;
    }

    .detail-row span:first-child {
      font-weight: 600;
      color: #a0aec0;
      text-transform: uppercase;
      font-size: 11px;
    }

    .detail-row span:last-child {
      font-weight: 500;
    }

    .visitor-panel-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 2px 12px;
      border-radius: 999px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
    }

    .visitor-panel-badge--pending {
      background: rgba(245, 158, 11, 0.15);
      color: #d97706;
    }

    .visitor-panel-badge--approved {
      background: rgba(16, 185, 129, 0.15);
      color: #059669;
    }

    .visitor-panel-badge--rejected {
      background: rgba(248, 113, 113, 0.2);
      color: #b91c1c;
    }

    body.visitor-panel-open {
      overflow: hidden;
    }

    /* --- Visitor Details Redesign --- */
    .visitor-panel-container {
      position: fixed;
      inset: 0;
      display: flex;
      justify-content: flex-end;
      align-items: stretch;
      pointer-events: none;
      z-index: 70;
    }

    .visitor-panel-overlayVisitor {
      position: absolute;
      inset: 0;
      background-color: rgba(15, 23, 42, 0.55);
      pointer-events: auto;
      cursor: pointer;
    }

    .visitor-details-panel {
      width: clamp(520px, 45vw, 560px);
      height: 100%;
      background: #ffffff;
      padding: 24px 26px 32px;
      box-shadow: -18px 0 45px rgba(15, 23, 42, 0.2);
      display: flex;
      flex-direction: column;
      gap: 18px;
      pointer-events: auto;
      transform: translateX(100%);
      animation: visitor-panel-slide-in 0.28s ease forwards;
    }

    @keyframes visitor-panel-slide-in {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    .visitor-panel__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      color: #e47a1d;
      padding-bottom: 12px;
      border-bottom: 1px solid rgba(226, 232, 240, 0.9);
    }

    .visitor-panel__header h3 {
      font-weight: 600;
      font-size: 1.05rem;
    }

    .visitor-panel__close {
      background: transparent;
      border: none;
      font-size: 1.4rem;
      color: #9ca3af;
      cursor: pointer;
    }

    .visitor-panel__stack {
      display: flex;
      flex-direction: column;
      gap: 14px;
      flex: 1;
      overflow-y: auto;
      align-items: center;
      padding-right: 6px;
    }

    .visitor-panel__card {
      background: #f8f9fc;
      border-radius: 20px;
      border: 1px solid rgba(149, 156, 175, 0.15);
      box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
      padding: 18px 20px;
      width: 100%;
      max-width: 520px;
    }

    .visitor-panel__card--profile {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .visitor-panel__profile-header {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .visitor-panel__avatar,
    .visitor-panel__avatar img {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      object-fit: cover;
      background: #e2e8f0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      color: #0f172a;
      font-size: 1.1rem;
    }

    .visitor-panel__profile-meta {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .visitor-panel__profile-meta .name {
      font-size: 1rem;
      font-weight: 600;
      color: #111827;
    }

    .visitor-panel__profile-meta .meta {
      font-size: 0.82rem;
      color: #6b7280;
      line-height: 1.2;
    }

    .visitor-panel__profile-meta .note {
      font-size: 0.72rem;
      color: #c7cfe8;
      letter-spacing: 0.04em;
    }

    .visitor-panel__profile-name {
      font-size: 1rem;
      font-weight: 600;
      color: #111827;
    }

    .visitor-panel__section-heading {
      font-size: 0.72rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: #9ca3af;
      font-weight: 600;
      margin-bottom: 8px;
      padding-bottom: 6px;
      border-bottom: 1px solid rgba(226, 232, 240, 0.8);
      position: relative;
    }

    .visitor-panel__section-heading::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: -1px;
      width: 80px;
      height: 3px;
      background: #e97b20;
      border-radius: 999px;
    }

    .visitor-panel__kv {
      display: flex;
      flex-direction: column;
      gap: 2px;
      margin-bottom: 8px;
    }

    .visitor-panel__kv-label {
      font-size: 0.68rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #9ca3af;
      line-height: 1.1;
    }

    .visitor-panel__kv-value {
      font-size: 0.92rem;
      font-weight: 600;
      color: #111827;
      line-height: 1.25;
    }

    .visitor-panel__kv--split {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
    }

    .visitor-panel__kv--status {
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 4px;
    }

    .visitor-panel__badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 2px 12px;
      border-radius: 999px;
      font-size: 0.75rem;
      text-transform: uppercase;
      font-weight: 600;
    }

    .visitor-panel__badge--pending {
      background: rgba(245, 158, 11, 0.18);
      color: #d97706;
    }

    .visitor-panel__badge--approved {
      background: rgba(16, 185, 129, 0.18);
      color: #059669;
    }

    .visitor-panel__badge--rejected {
      background: rgba(248, 113, 113, 0.25);
      color: #b91c1c;
    }

    .table-footer {
      border-top: none;
      margin-top: 18px;
      padding-top: 12px;
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      align-items: center;
      justify-content: space-between;
      font-size: 0.9rem;
      color: #6b7280;
      border-bottom: 1px solid rgba(209, 213, 219, 0.8);
      padding-bottom: 6px;
    }
    @media (max-width: 640px) {
      .visitor-details-panel {
        width: 100%;
        border-radius: 0;
        padding: 24px 20px 32px;
      }

      .visitor-panel__stack {
        align-items: stretch;
        padding-right: 0;
      }

      .visitor-panel__card {
        max-width: none;
      }

      .table-footer {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
</head>
<body class="bg-base-100">
  <div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    @include('partials.sidebarr')
    
    <!-- Main content -->
    <div class="flex flex-col flex-1 overflow-hidden">
      <!-- Header -->
      @include('partials.navbar')

      <!-- Main content area -->
      <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6">
        @if(session('success'))
          <div class="alert alert-success mb-6">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span>{{ session('success') }}</span>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-error mb-6">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <span>{{ session('error') }}</span>
          </div>
        @endif

        <!-- Page Header -->
        <div class="mb-8">
          <div class="mb-4">
            <h1 class="text-3xl font-bold text-gray-800 mb-2" style="color: var(--color-charcoal-ink);">Visitor Logs & Analytics</h1>
            <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Comprehensive visitor tracking, analytics, and reporting</p>
          </div>
          <!-- underline divider (matches other modules) -->
          <div class="border-b border-gray-200 mb-6"></div>
        </div>

        <!-- Main Content Tabs -->
        <div class="bg-white rounded-xl shadow-lg">
          <!-- Clickable Breadcrumb Navigation -->
          <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <nav class="flex items-center space-x-2 text-sm">
              <button id="nav-logs" class="text-blue-600 hover:text-blue-800 font-medium flex items-center transition-colors duration-200 {{ $activeTab==='logs' ? 'text-blue-800 font-semibold' : '' }}" onclick="showTab('logs')">
                <i data-lucide="list" class="w-4 h-4 mr-1"></i>
                Detailed Logs
              </button>
              <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
              <button id="nav-reports" class="text-gray-600 hover:text-blue-600 font-medium flex items-center transition-colors duration-200 {{ $activeTab==='reports' ? 'text-blue-600 font-semibold' : '' }}" onclick="showTab('reports')">
                <i data-lucide="bar-chart-3" class="w-4 h-4 inline mr-1"></i>
                Reports & Analytics
              </button>
            </nav>
          </div>

          <!-- Tab Content -->
          <div class="p-4 sm:p-6">
            <!-- Detailed Logs Tab -->
            <div id="logs-content" class="tab-content">
              <!-- Logs Table -->
              <x-table-card :title="'Visitor Logs'">
                <!-- Search and Filter Controls inside the table card, below the blue banner -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                  <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                    <div class="flex flex-1 gap-3 items-center w-full sm:w-auto">
                      <!-- Search Input -->
                      <div class="relative flex-1 sm:flex-initial sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input 
                          type="text" 
                          id="logs-search-input" 
                          placeholder="Search..." 
                          class="w-full pl-10 pr-3 py-2.5 text-sm rounded-md border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                      </div>
                      
                      <!-- Date Range Selector -->
                      <div class="relative">
                        <button 
                          type="button" 
                          id="logs-date-range-btn"
                          class="px-4 py-2.5 text-sm font-medium rounded-md border border-transparent hover:scale-105 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                          style="background: linear-gradient(135deg, #F7A923 0%, #E6940F 100%); color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25);"
                          onmouseover="this.style.background='linear-gradient(135deg, #E6940F 0%, #D2840E 100%)'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'"
                          onmouseout="this.style.background='linear-gradient(135deg, #F7A923 0%, #E6940F 100%)'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'"
                          onclick="toggleDateRangeDropdown()"
                        >
                        <span id="logs-date-range-text">All Time</span>
                          <i data-lucide="chevron-down" class="w-4 h-4 inline-block ml-1" style="color: #1f2937;"></i>
                        </button>
                        
                        <!-- Date Range Dropdown -->
                        <div id="logs-date-range-dropdown" class="hidden absolute z-10 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 py-1">
                          <button onclick="setDateRange('all')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All Time</button>
                          <button onclick="setDateRange('today')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Today</button>
                          <button onclick="setDateRange('week')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Last 7 Days</button>
                          <button onclick="setDateRange('month')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Last 30 Days</button>
                          <button onclick="setDateRange('quarter')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Last 90 Days</button>
                          <button onclick="setDateRange('year')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Last Year</button>
                          <button onclick="setDateRange('custom')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 border-t border-gray-200">Custom Range</button>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-4 items-center">
                      <!-- Column Button -->
                      <div class="flex flex-col items-center">
                        <button 
                          type="button"
                          id="logs-column-btn"
                          class="w-12 h-12 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                          onclick="openColumnSettings()"
                          title="Column Settings"
                        >
                          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                          </svg>
                        </button>
                        <span class="mt-1 text-xs text-gray-500">Column</span>
                      </div>
                      
                      <!-- Export Button -->
                      <div class="flex flex-col items-center">
                        <button 
                          type="button"
                          id="logs-export-btn"
                          class="w-12 h-12 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                          onclick="exportVisitorLogs()"
                          title="Export Logs"
                        >
                          <i data-lucide="download" class="w-5 h-5 text-gray-600"></i>
                        </button>
                        <span class="mt-1 text-xs text-gray-500">Export</span>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Hidden date inputs for date range functionality -->
                  <input type="date" id="logs-start-date" class="hidden" />
                  <input type="date" id="logs-end-date" class="hidden" />
                </div>

                <table class="table table-zebra w-full">
                  <thead>
                    <tr class="bg-gray-50">
                    <th class="text-left py-3 px-4 font-medium text-gray-700" data-column="visitor_name">Visitor Name</th>
                    <th class="text-center py-3 px-4 font-medium text-gray-700" data-column="contact_number">Contact Number</th>
                    <th class="text-center py-3 px-4 font-medium text-gray-700" data-column="purpose">Purpose</th>
                    <th class="text-center py-3 px-4 font-medium text-gray-700" data-column="checkin_checkout">Check-In / Check-Out</th>
                    <th class="text-center py-3 px-4 font-medium text-gray-700" data-column="approval_status">Approval Status</th>
                    <th class="text-right py-3 px-4 font-medium text-gray-700 duration-column" data-column="duration">Duration</th>
                    <th class="text-center py-3 px-4 font-medium text-gray-700" data-column="id_number">ID Number</th>
                    <th class="text-center py-3 px-4 font-medium text-gray-700" data-column="actions">Action</th>
                    </tr>
                  </thead>
                  <tbody id="logs-table-body">
                    @forelse($visitors as $visitor)
                      <tr class="hover:bg-gray-50 transition-colors interactive-row"
                        data-id="{{ $visitor->id }}"
                        data-name="{{ $visitor->name ?? 'Visitor' }}"
                        data-email="{{ $visitor->email ?? 'N/A' }}"
                        data-phone="{{ $visitor->contact ?? 'N/A' }}"
                        data-host="{{ $visitor->host_employee ?? $visitor->host ?? 'N/A' }}"
                        data-source="{{ $visitor->source ?? 'N/A' }}"
                        data-checkin="{{ $visitor->time_in ?? '' }}"
                        data-checkout="{{ $visitor->time_out ?? '' }}"
                        data-purpose="{{ $visitor->purpose ?? 'N/A' }}"
                        data-status="{{ strtoupper($visitor->approval_status ?? ($visitor->status === 'active' ? 'APPROVED' : ($visitor->time_out ? 'COMPLETED' : 'PENDING'))) }}"
                        data-avatar="{{ $visitor->profile_photo_url ?? '' }}"
                        data-pass-id="{{ $visitor->pass_id ?? 'N/A' }}"
                        data-company="{{ $visitor->company ?? '' }}"
                        data-comment="{{ $visitor->rating_comment ?? ($visitor->special_instructions ?? ($visitor->company ?? '')) }}"
                      >
                        <td class="py-3 px-4" data-column="visitor_name">
                          <div class="flex items-center space-x-3">
                            <div class="relative">
                              @if(!empty($visitor->profile_photo_url))
                                <div class="w-10 h-10 rounded-full overflow-hidden ring-2 ring-white shadow">
                                  <img src="{{ $visitor->profile_photo_url }}" alt="{{ $visitor->name }}" class="object-cover w-full h-full">
                                </div>
                              @else
                                <div class="w-10 h-10 rounded-full bg-blue-900 flex items-center justify-center text-white font-semibold ring-2 ring-white shadow">
                                  {{ strtoupper(substr($visitor->name ?? 'V', 0, 1)) }}
                                </div>
                              @endif
                              @if(!empty($visitor->nationality_flag))
                                <img src="{{ $visitor->nationality_flag }}" alt="Flag" class="w-4 h-4 absolute -right-2 -bottom-1">
                              @endif
                            </div>
                            <div>
                              <div class="font-medium text-gray-900">{{ $visitor->name ?? 'Visitor' }}</div>
                              <div class="text-xs text-gray-500">{{ $visitor->email ?? 'No email' }}</div>
                            </div>
                          </div>
                        </td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600" data-column="contact_number">{{ $visitor->contact ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-center" data-column="purpose">
                          <span class="badge badge-outline badge-sm">{{ $visitor->purpose ?? 'N/A' }}</span>
                        </td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600" data-column="checkin_checkout">
                          @php
                            $checkInDt = $visitor->time_in ? \Carbon\Carbon::parse($visitor->time_in)->setTimezone('Asia/Manila') : null;
                            $checkOutDt = $visitor->time_out ? \Carbon\Carbon::parse($visitor->time_out)->setTimezone('Asia/Manila') : null;
                          @endphp
                          @if($checkInDt)
                            <div class="text-emerald-500 font-medium">
                              {{ $checkInDt->format('M d, Y, h:i:s A') }}
                            </div>
                          @else
                            <div class="text-gray-400">N/A</div>
                          @endif
                          @if($checkOutDt)
                            <div class="text-orange-500 text-xs mt-1">
                              {{ $checkOutDt->format('M d, Y, h:i:s A') }}
                            </div>
                          @endif
                        </td>
                        @php
                          $approvalStatus = strtoupper(
                            $visitor->approval_status
                              ?? ($visitor->status === 'active'
                                    ? 'APPROVED'
                                    : ($visitor->time_out ? 'COMPLETED' : 'PENDING'))
                          );
                          $statusColor = match ($approvalStatus) {
                            'COMPLETED' => 'text-emerald-600 bg-emerald-50',
                            'APPROVED' => 'text-emerald-600 bg-emerald-50',
                            'REJECTED' => 'text-red-600 bg-red-50',
                            default => 'text-amber-600 bg-amber-50'
                          };
                        @endphp
                        <td class="py-3 px-4 text-center text-sm font-semibold" data-column="approval_status">
                          <span class="px-3 py-1 rounded-full {{ $statusColor }}">{{ $approvalStatus }}</span>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600 duration-cell" data-column="duration">
                          @if($visitor->time_out)
                            @php
                              $checkIn = \Carbon\Carbon::parse($visitor->time_in);
                              $checkOut = \Carbon\Carbon::parse($visitor->time_out);
                              
                              // Check for data error (check out before check in)
                              if ($checkOut->lt($checkIn)) {
                                $durationMinutes = 0;
                                $isDataError = true;
                              } else {
                                $durationMinutes = $checkIn->diffInMinutes($checkOut);
                                $isDataError = false;
                              }
                              
                              // Calculate human-readable format
                              if ($isDataError) {
                                $displayText = '—';
                                $tooltipText = 'Data error: Check out before check in';
                                $colorClass = 'badge-error';
                              } else {
                                $days = floor($durationMinutes / (24 * 60));
                                $hours = floor(($durationMinutes % (24 * 60)) / 60);
                                $mins = $durationMinutes % 60;
                                
                                // Build compact display
                                $parts = [];
                                if ($days > 0) $parts[] = $days . 'd';
                                if ($hours > 0) $parts[] = $hours . 'h';
                                if ($mins > 0) $parts[] = $mins . 'm';
                                
                                $displayText = empty($parts) ? '0m' : implode(' ', $parts);
                                
                                // Build verbose tooltip
                                $tooltipParts = [];
                                if ($days > 0) $tooltipParts[] = $days . ' day' . ($days > 1 ? 's' : '');
                                if ($hours > 0) $tooltipParts[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
                                if ($mins > 0) $tooltipParts[] = $mins . ' minute' . ($mins > 1 ? 's' : '');
                                
                                $tooltipText = empty($tooltipParts) ? '0 minutes' : implode(', ', $tooltipParts);
                                
                                // Determine color class based on duration
                                if ($durationMinutes < 8 * 60) {
                                  $colorClass = 'badge-success'; // < 8h
                                } elseif ($durationMinutes < 72 * 60) {
                                  $colorClass = 'badge-warning'; // 8h-72h
                                } else {
                                  $colorClass = 'badge-error'; // > 72h
                                }
                              }
                            @endphp
                            <div class="duration-display" 
                                 data-duration-minutes="{{ $durationMinutes }}"
                                 data-tooltip="{{ $tooltipText }}"
                                 title="{{ $tooltipText }}"
                                 aria-label="{{ $tooltipText }}">
                              <span class="duration-pill duration-pill--{{ $durationMinutes < 480 ? 'short' : ($durationMinutes < 4320 ? 'medium' : 'long') }}">{{ $displayText }}</span>
                            </div>
                          @else
                            <div class="live-duration" 
                                 data-checkin="{{ $visitor->time_in }}" 
                                 data-visitor-id="{{ $visitor->id }}"
                                 data-duration-minutes="0"
                                 title="Still in building - duration being calculated"
                                 aria-label="Still in building - duration being calculated">
                              <span class="duration-pill duration-pill--short">Still in</span>
                            </div>
                          @endif
                        </td>
                        <td class="py-3 px-4 text-center text-sm text-gray-600 font-mono" data-column="id_number">{{ $visitor->pass_id ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-center" data-column="actions">
                          <div class="flex items-center justify-center gap-2">
                            <button class="btn btn-ghost btn-xs" title="View details" onclick="event.stopPropagation(); viewVisitorDetails({{ $visitor->id }})">
                              <i data-lucide="info" class="w-4 h-4"></i>
                            </button>
                            <button class="btn btn-ghost btn-xs" title="Print pass" onclick="event.stopPropagation(); printVisitorPass({{ $visitor->id }})">
                              <i data-lucide="printer" class="w-4 h-4 text-emerald-600"></i>
                            </button>
                            <button class="checkout-icon-btn" title="Check-out" onclick="event.stopPropagation(); openCheckoutRatingModal({{ $visitor->id }})">
                              <span class="checkout-icon"></span>
                            </button>
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="8" class="text-center py-12">
                          <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                              <i data-lucide="users" class="w-10 h-10 text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">No Visitor Logs Found</h3>
                            <p class="text-gray-500 text-sm">No visitor logs available for the selected criteria.</p>
                          </div>
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>

                <div class="table-footer">
                  <div class="flex items-center gap-2 text-sm text-gray-600">
                    <span>Items per page:</span>
                    <select 
                      id="logs-per-page" 
                      class="bg-transparent border-0 border-b border-gray-300 focus:border-gray-500 focus:ring-0 text-blue-600 font-medium cursor-pointer"
                      onchange="changePerPage(this.value)"
                    >
                      <option value="10" {{ ($visitors->perPage() ?? 10) == 10 ? 'selected' : '' }}>10</option>
                      <option value="20" {{ ($visitors->perPage() ?? 10) == 20 ? 'selected' : '' }}>20</option>
                      <option value="50" {{ ($visitors->perPage() ?? 10) == 50 ? 'selected' : '' }}>50</option>
                      <option value="100" {{ ($visitors->perPage() ?? 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                  </div>

                  <div class="text-sm text-gray-600 font-medium">
                    <span id="logs-pagination-range">
                      @if($visitors->total() > 0)
                        {{ $visitors->firstItem() }}-{{ $visitors->lastItem() }} of {{ $visitors->total() }}
                      @else
                        0 of 0
                      @endif
                    </span>
                  </div>

                  <div class="flex items-center gap-3 text-gray-500">
                    <button 
                      id="logs-prev-btn"
                      onclick="goToPage({{ $visitors->currentPage() - 1 }})"
                      class="p-1 disabled:opacity-30 disabled:cursor-not-allowed hover:text-gray-700 transition-colors"
                      {{ $visitors->onFirstPage() ? 'disabled' : '' }}
                      aria-label="Previous page"
                    >
                      <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <button 
                      id="logs-next-btn"
                      onclick="goToPage({{ $visitors->currentPage() + 1 }})"
                      class="p-1 disabled:opacity-30 disabled:cursor-not-allowed hover:text-gray-700 transition-colors"
                      {{ !$visitors->hasMorePages() ? 'disabled' : '' }}
                      aria-label="Next page"
                    >
                      <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                  </div>
                </div>
              </x-table-card>
            </div>



            <!-- Reports & Analytics Tab -->
            <div id="reports-content" class="tab-content hidden">
              <!-- Header with Export Button -->
              <div class="flex justify-between items-center mb-6">
                <div>
                  <h2 class="text-2xl font-bold text-gray-800" style="color: var(--color-charcoal-ink);">Reports & Analytics</h2>
                  <p class="text-gray-600" style="color: var(--color-charcoal-ink); opacity: 0.8;">Comprehensive visitor management insights and statistics</p>
                  </div>
                <div class="flex items-center gap-3">
                  <div class="flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                    <select class="select select-bordered select-sm" id="analytics-time-range" onchange="refreshAnalytics()">
                      <option value="today">Today</option>
                      <option value="week">This Week</option>
                      <option value="month">This Month</option>
                      <option value="year">This Year</option>
                    </select>
                  </div>
                  <button onclick="refreshAnalytics()" class="btn btn-sm" style="background: linear-gradient(135deg, #F7A923 0%, #E6940F 100%); color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25); border: none;" onmouseover="this.style.background='linear-gradient(135deg, #E6940F 0%, #D2840E 100%)'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'" onmouseout="this.style.background='linear-gradient(135deg, #F7A923 0%, #E6940F 100%)'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                    <i data-lucide="refresh-cw" class="w-4 h-4 mr-1" style="color: #1f2937; fill: none;"></i>Refresh
                  </button>
                  <button onclick="exportReport()" class="btn btn-sm" style="background: linear-gradient(135deg, #F7A923 0%, #E6940F 100%); color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25); border: none;" onmouseover="this.style.background='linear-gradient(135deg, #E6940F 0%, #D2840E 100%)'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'" onmouseout="this.style.background='linear-gradient(135deg, #F7A923 0%, #E6940F 100%)'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                    <i data-lucide="download" class="w-4 h-4 mr-1" style="color: #1f2937; fill: none;"></i>Export Report
                  </button>
                </div>
              </div>

              <!-- Summary Statistics Row -->
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Visitors -->
                <div class="reports-card bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                      <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                  </div>
                    <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full badge-pulse">+12% from last week</span>
                  </div>
                  <h3 class="text-3xl font-bold text-gray-900 mb-1 stat-number">147</h3>
                  <p class="text-sm text-gray-600">Total Visitors</p>
                </div>

                <!-- Average Visit Duration -->
                <div class="reports-card bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                      <i data-lucide="clock" class="w-5 h-5 text-green-600"></i>
                  </div>
                    <span class="text-xs font-medium text-gray-500">Optimal for productivity</span>
                  </div>
                  <h3 class="text-3xl font-bold text-gray-900 mb-1 stat-number">2h 34m</h3>
                  <p class="text-sm text-gray-600">Avg. Visit Duration</p>
                </div>

                <!-- Peak Capacity -->
                <div class="reports-card bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                      <i data-lucide="bar-chart-3" class="w-5 h-5 text-orange-600"></i>
                  </div>
                    <span class="text-xs font-medium text-gray-500">Tuesday 3:00 PM</span>
                </div>
                  <h3 class="text-3xl font-bold text-gray-900 mb-1 stat-number">85%</h3>
                  <p class="text-sm text-gray-600">Peak Capacity</p>
              </div>

                <!-- Security Incidents -->
                <div class="reports-card bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                      <i data-lucide="shield-check" class="w-5 h-5 text-red-600"></i>
                </div>
                    <span class="text-xs font-medium text-green-600">All clear this week</span>
                </div>
                  <h3 class="text-3xl font-bold text-gray-900 mb-1 stat-number">0</h3>
                  <p class="text-sm text-gray-600">Security Incidents</p>
                </div>
              </div>

              <!-- Detailed Analytics Row -->
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Peak Visiting Hours -->
                <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="clock" class="w-5 h-5 text-blue-600"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Peak Visiting Hours</h3>
                  </div>
                  <p class="text-sm text-gray-600 mb-4">Visitor traffic throughout the day</p>
                  <div id="peak-hours-content" class="space-y-3">
                    <!-- Dynamic content will be loaded here -->
                    <div class="text-center py-8 text-gray-500">
                      <i data-lucide="clock" class="w-8 h-8 mx-auto mb-2"></i>
                      <p>Loading peak hours data...</p>
                </div>
              </div>
            </div>

                <!-- Departments by visitor count -->
                <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="building" class="w-5 h-5 text-blue-600"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Departments with most visitors</h3>
                  </div>
                  <p class="text-sm text-gray-600 mb-4">All departments ranked by visitor count</p>
                  <div id="hosts-departments-content" class="space-y-3">
                    <!-- Dynamic content will be loaded here -->
                    <div class="text-center py-8 text-gray-500">
                      <i data-lucide="building" class="w-8 h-8 mx-auto mb-2"></i>
                      <p>Loading departments data...</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Visit Purposes & Weekly Summary -->
              <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Visit Purposes -->
                <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="target" class="w-5 h-5 text-blue-600"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Visit Purposes</h3>
                  </div>
                  <p class="text-sm text-gray-600 mb-4">Breakdown of visit reasons</p>
                  <div id="visitor-types-content" class="space-y-3">
                    <!-- Dynamic content will be loaded here -->
                    <div class="text-center py-8 text-gray-500">
                      <i data-lucide="target" class="w-8 h-8 mx-auto mb-2"></i>
                      <p>Loading visit purposes data...</p>
                    </div>
                  </div>
                </div>

                <!-- Weekly Activity Summary -->
                <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                  <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 text-blue-600"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Weekly Activity Summary</h3>
                  </div>
                  <p class="text-sm text-gray-600 mb-4">Key insights and recommendations for the past week</p>
                  
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Highlights -->
                    <div class="bg-green-50 rounded-lg p-4 summary-section">
                      <h4 class="text-sm font-semibold text-green-800 mb-3">Highlights</h4>
                      <ul class="space-y-2 text-sm text-green-700">
                        <li class="flex items-start gap-2 highlight-item">
                          <span class="text-green-500 mt-1">•</span>
                          <span>12% increase in visitor volume</span>
                        </li>
                        <li class="flex items-start gap-2 highlight-item">
                          <span class="text-green-500 mt-1">•</span>
                          <span>Zero security incidents reported</span>
                        </li>
                        <li class="flex items-start gap-2 highlight-item">
                          <span class="text-green-500 mt-1">•</span>
                          <span>100% badge return compliance</span>
                        </li>
                      </ul>
                    </div>

                    <!-- Areas for Improvement -->
                    <div class="bg-orange-50 rounded-lg p-4 summary-section">
                      <h4 class="text-sm font-semibold text-orange-800 mb-3">Areas for Improvement</h4>
                      <ul class="space-y-2 text-sm text-orange-700">
                        <li class="flex items-start gap-2 improvement-item">
                          <span class="text-orange-500 mt-1">•</span>
                          <span>15% visitors exceed expected duration</span>
                        </li>
                        <li class="flex items-start gap-2 improvement-item">
                          <span class="text-orange-500 mt-1">•</span>
                          <span>Peak hours causing capacity strain</span>
                        </li>
                        <li class="flex items-start gap-2 improvement-item">
                          <span class="text-orange-500 mt-1">•</span>
                          <span>Some hosts not promptly notified</span>
                        </li>
                      </ul>
                    </div>

                    <!-- Recommendations -->
                    <div class="bg-blue-50 rounded-lg p-4 summary-section">
                      <h4 class="text-sm font-semibold text-blue-800 mb-3">Recommendations</h4>
                      <ul class="space-y-2 text-sm text-blue-700">
                        <li class="flex items-start gap-2 recommendation-item">
                          <span class="text-blue-500 mt-1">•</span>
                          <span>Implement visit duration reminders</span>
                        </li>
                        <li class="flex items-start gap-2 recommendation-item">
                          <span class="text-blue-500 mt-1">•</span>
                          <span>Consider staggered appointment times</span>
                        </li>
                        <li class="flex items-start gap-2 recommendation-item">
                          <span class="text-blue-500 mt-1">•</span>
                          <span>Enhance host notification system</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Report Generation Section -->
              <div class="mt-8 bg-gray-50 rounded-xl p-6">
                <div class="flex items-center gap-2 mb-4">
                  <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                  <h3 class="text-lg font-semibold text-gray-800">Generate Custom Report</h3>
                </div>
                
                <form id="report-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Report Type:</label>
                      <select name="report_type" class="select select-bordered w-full">
                        <option value="daily">Daily Summary</option>
                        <option value="weekly">Weekly Summary</option>
                        <option value="monthly">Monthly Summary</option>
                        <option value="custom">Custom Report</option>
                      </select>
                    </div>
                    <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date:</label>
                    <input type="date" name="start_date" class="input input-bordered w-full" value="{{ now()->subDays(7)->format('Y-m-d') }}">
                    </div>
                    <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date:</label>
                    <input type="date" name="end_date" class="input input-bordered w-full" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Format:</label>
                      <select name="format" class="select select-bordered w-full">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                        <option value="csv">CSV</option>
                      </select>
                    </div>
                  <div class="md:col-span-2 lg:col-span-4 flex justify-end">
                    <button type="submit" class="btn btn-primary">
                      <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                      Generate Report
                    </button>
                  </div>
                  </form>
              </div>
            </div>


          </div>
        </div>
      </main>
    </div>
  </div>

  @php
    $columnOptions = [
      ['id' => 'visitor_name', 'label' => 'Visitor Name'],
      ['id' => 'contact_number', 'label' => 'Contact Number'],
      ['id' => 'purpose', 'label' => 'Purpose'],
      ['id' => 'checkin_checkout', 'label' => 'Check-In / Check-Out'],
      ['id' => 'approval_status', 'label' => 'Approval Status'],
      ['id' => 'duration', 'label' => 'Duration'],
      ['id' => 'id_number', 'label' => 'ID Number'],
      ['id' => 'actions', 'label' => 'Action'],
    ];
  @endphp

  <!-- Column Settings Modal -->
  <div id="columnSettingsModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 px-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-[#E0761C]">Table Columns</h3>
        <button type="button" onclick="closeColumnSettings()" class="text-gray-400 hover:text-gray-600">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      <div class="px-6 py-4">
        <p class="text-sm text-gray-600 mb-4">Select columns to appear in table</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="columnCheckboxContainer">
          @foreach($columnOptions as $option)
            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
              <input 
                type="checkbox" 
                class="checkbox checkbox-warning column-visibility-checkbox" 
                data-column-id="{{ $option['id'] }}" 
                checked
              >
              <span>{{ $option['label'] }}</span>
            </label>
          @endforeach
        </div>
      </div>
      <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
        <button type="button" class="btn btn-ghost btn-sm" onclick="closeColumnSettings()">Cancel</button>
        <button type="button" class="btn btn-sm" style="background: linear-gradient(135deg, #F7A923 0%, #E6940F 100%); color: #1f2937;" onclick="saveColumnSettings()">
          <i data-lucide="layout" class="w-4 h-4 mr-1"></i>Save
        </button>
      </div>
    </div>
  </div>

  <div id="visitorDetailsPanel" class="visitor-panel-container hidden">
    <div class="visitor-panel-overlayVisitor"></div>
    <div class="visitor-details-panel">
      <div class="visitor-panel__header">
        <h3>Visitor Details</h3>
        <button type="button" class="visitor-panel__close" onclick="closeVisitorDetailsPanel()">&times;</button>
      </div>
      <div class="visitor-panel__stack">
        <section class="visitor-panel__card visitor-panel__card--profile">
          <div class="visitor-panel__profile-header">
            <div class="visitor-panel__avatar" id="detailAvatar">V</div>
            <div class="visitor-panel__profile-meta">
              <p class="visitor-panel__profile-name" id="detailNameDisplay">Visitor Name</p>
            </div>
          </div>

          <div class="visitor-panel__section-heading">Profile Details</div>

          <div class="visitor-panel__kv">
            <p class="visitor-panel__kv-label">Name</p>
            <p class="visitor-panel__kv-value" id="detailName">Visitor Name</p>
          </div>

          <div class="visitor-panel__kv">
            <p class="visitor-panel__kv-label">Email</p>
            <p class="visitor-panel__kv-value" id="detailEmail">email@example.com</p>
          </div>

          <div class="visitor-panel__kv">
            <p class="visitor-panel__kv-label">Phone Number</p>
            <p class="visitor-panel__kv-value" id="detailPhone">+639...</p>
          </div>

          <div class="visitor-panel__kv">
            <p class="visitor-panel__kv-label">Pass ID</p>
            <p class="visitor-panel__kv-value" id="detailPassId">PASS-XXX</p>
          </div>

          <div class="visitor-panel__kv visitor-panel__kv--split">
            <div>
              <p class="visitor-panel__kv-label">Check-in Date &amp; Time</p>
              <p class="visitor-panel__kv-value text-emerald-600" id="detailCheckin">N/A</p>
            </div>
            <div>
              <p class="visitor-panel__kv-label">Check-out Date &amp; Time</p>
              <p class="visitor-panel__kv-value text-orange-500" id="detailCheckout">—</p>
            </div>
          </div>

          <div class="visitor-panel__kv">
            <p class="visitor-panel__kv-label">Host</p>
            <p class="visitor-panel__kv-value" id="detailHost">--</p>
          </div>

          <div class="visitor-panel__kv">
            <p class="visitor-panel__kv-label">Purpose</p>
            <p class="visitor-panel__kv-value" id="detailPurpose">N/A</p>
          </div>

          <div class="visitor-panel__kv">
            <p class="visitor-panel__kv-label">Comment</p>
            <p class="visitor-panel__kv-value" id="detailComment">—</p>
          </div>

          <div class="visitor-panel__kv visitor-panel__kv--status">
            <p class="visitor-panel__kv-label">Approval Status</p>
            <span id="detailStatus" class="visitor-panel__badge visitor-panel__badge--pending">Pending</span>
          </div>
        </section>
      </div>
    </div>
  </div>

  <!-- Print Pass / Pass Code Style Modal -->
  <!-- Print Pass Modal -->
  <div id="printPassModal" class="modal" onclick="closePrintPassModal()">
    <div class="modal-box w-full max-w-lg" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">Print Pass</h3>
        <button class="text-gray-400 hover:text-gray-600" onclick="closePrintPassModal()">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- Inner Pass Card -->
      <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-lg overflow-hidden">
        <!-- Top Section - Gradient -->
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-600 px-6 py-8 text-center">
          <!-- Avatar -->
          <div class="flex justify-center mb-3">
            <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white/30 bg-white/20 backdrop-blur-sm flex items-center justify-center">
              <img id="printPassPhoto" src="" alt="Visitor photo" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
              <div class="w-full h-full bg-white/20 backdrop-blur-sm flex items-center justify-center hidden">
                <i data-lucide="user" class="w-12 h-12 text-white"></i>
              </div>
            </div>
          </div>
          <!-- Name -->
          <h4 class="text-xl font-bold text-white mb-2" id="printPassName">Visitor Name</h4>
          <!-- Status Badge -->
          <span id="printPassStatus" class="inline-block px-4 py-1 rounded-full text-xs font-semibold bg-red-500 text-white">EXPIRED</span>
        </div>

        <!-- Middle Section - White -->
        <div class="px-6 py-6 space-y-4">
          <!-- Email -->
          <div class="flex items-center gap-3">
            <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
            <div>
              <span class="text-xs text-gray-500 uppercase">Email</span>
              <p class="text-sm font-medium text-gray-900" id="printPassEmail">visitor@example.com</p>
            </div>
          </div>

          <!-- Phone -->
          <div class="flex items-center gap-3">
            <i data-lucide="phone" class="w-5 h-5 text-gray-400"></i>
            <div>
              <span class="text-xs text-gray-500 uppercase">Phone</span>
              <p class="text-sm font-medium text-gray-900" id="printPassPhone">+63...</p>
            </div>
          </div>

          <!-- Check In -->
          <div class="flex items-center gap-3">
            <i data-lucide="calendar" class="w-5 h-5 text-gray-400"></i>
            <div>
              <span class="text-xs text-gray-500 uppercase">Check In</span>
              <p class="text-sm font-medium text-gray-900" id="printPassInviteDate">—</p>
            </div>
          </div>

          <!-- Expires At -->
          <div class="flex items-center gap-3">
            <i data-lucide="clock" class="w-5 h-5 text-gray-400"></i>
            <div>
              <span class="text-xs text-gray-500 uppercase">Expires At</span>
              <p class="text-sm font-medium text-gray-900" id="printPassExpiresAt">—</p>
            </div>
          </div>

          <!-- Pass Code -->
          <div class="flex items-center gap-3">
            <i data-lucide="key" class="w-5 h-5 text-gray-400"></i>
            <div>
              <span class="text-xs text-gray-500 uppercase">Pass Code</span>
              <p class="text-sm font-medium text-gray-900" id="printPassCode">—</p>
            </div>
          </div>

          <!-- QR Code -->
          <div class="flex flex-col items-center pt-4 border-t border-gray-200">
            <img id="printPassQR" src="" alt="QR Code" class="w-32 h-32 mb-2 border-2 border-gray-200 rounded-lg">
            <p class="text-xs text-gray-500 mb-4">Scan to verify</p>
            <div class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-center py-3 rounded-lg font-semibold text-sm">
              Please present this pass at the reception
            </div>
          </div>

          <!-- Show More Details Toggle -->
          <div class="pt-4 border-t border-gray-200">
            <button type="button" id="toggleMoreDetails" onclick="toggleMoreDetails()" class="w-full flex items-center justify-between text-sm text-gray-600 hover:text-gray-900 transition-colors">
              <span class="font-medium">Show more details</span>
              <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" id="moreDetailsChevron"></i>
            </button>
            
            <!-- Additional Details (Hidden by default) -->
            <div id="moreDetailsContent" class="hidden mt-4 space-y-3 pt-4 border-t border-gray-100">
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <span class="text-xs text-gray-500 uppercase">Purpose</span>
                  <p class="text-sm font-medium text-gray-900 mt-1" id="printPassPurpose">—</p>
                </div>
                <div>
                  <span class="text-xs text-gray-500 uppercase">Host</span>
                  <p class="text-sm font-medium text-gray-900 mt-1" id="printPassHost">—</p>
                </div>
                <div>
                  <span class="text-xs text-gray-500 uppercase">Department</span>
                  <p class="text-sm font-medium text-gray-900 mt-1" id="printPassDepartment">—</p>
                </div>
                <div>
                  <span class="text-xs text-gray-500 uppercase">ID Type</span>
                  <p class="text-sm font-medium text-gray-900 mt-1" id="printPassIdType">—</p>
                </div>
                <div>
                  <span class="text-xs text-gray-500 uppercase">Pass ID</span>
                  <p class="text-sm font-medium text-gray-900 mt-1 font-mono" id="printPassPassId">—</p>
                </div>
                <div>
                  <span class="text-xs text-gray-500 uppercase">Registered At</span>
                  <p class="text-sm font-medium text-gray-900 mt-1" id="printPassRegisteredAt">—</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Buttons -->
      <div class="mt-6 flex justify-end gap-3">
        <button type="button" class="btn btn-ghost btn-sm" onclick="closePrintPassModal()">Skip</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="handlePrintPass()">Print</button>
      </div>
    </div>
  </div>

  <!-- Checkout Rating Modal -->
  <div id="checkoutRatingModal" class="modal" onclick="closeCheckoutRatingModal()">
    <div class="modal-box w-full max-w-xl" onclick="event.stopPropagation()">
      <h3 class="text-lg font-semibold mb-4">Rate Visitor Experience</h3>
      <div class="flex items-center gap-4 mb-4">
        <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
          <img id="ratingPhoto" src="" alt="Visitor photo" class="w-full h-full object-cover">
        </div>
        <div>
          <p class="font-semibold" id="ratingName">Visitor Name</p>
          <p class="text-sm text-gray-500" id="ratingPhone">+63...</p>
        </div>
      </div>

      <div class="bg-indigo-50 rounded-2xl py-8 px-6 mb-4 flex flex-col items-center">
        <div class="flex gap-4 mb-4" id="ratingStars">
          <!-- stars injected by JS -->
        </div>
        <p class="text-sm text-gray-600" id="ratingLabel">You have rated :: 0/5</p>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Leave a comment</label>
        <textarea id="ratingComment" class="textarea textarea-bordered w-full" rows="3" placeholder="Leave a comment"></textarea>
      </div>

      <div class="modal-action mt-2 flex justify-end gap-3">
        <button type="button" class="btn btn-ghost btn-sm" onclick="closeCheckoutRatingModal()">Cancel</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="submitCheckoutRating()">Submit</button>
      </div>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    const columnDefinitions = [
      { id: 'visitor_name', label: 'Visitor Name' },
      { id: 'contact_number', label: 'Contact Number' },
      { id: 'purpose', label: 'Purpose' },
      { id: 'checkin_checkout', label: 'Check-In / Check-Out' },
      { id: 'approval_status', label: 'Approval Status' },
      { id: 'duration', label: 'Duration' },
      { id: 'id_number', label: 'ID Number' },
      { id: 'actions', label: 'Action' },
    ];

    let columnVisibility = {};

    function initializeColumnVisibility() {
      try {
        const stored = localStorage.getItem('visitorLogsColumns');
        columnVisibility = stored ? JSON.parse(stored) : {};
      } catch (e) {
        columnVisibility = {};
      }

      columnDefinitions.forEach(def => {
        if (typeof columnVisibility[def.id] === 'undefined') {
          columnVisibility[def.id] = true;
        }
      });
    }

    function applyColumnVisibility() {
      columnDefinitions.forEach(def => {
        const isVisible = columnVisibility[def.id] !== false;
        document.querySelectorAll(`[data-column="${def.id}"]`).forEach(el => {
          el.classList.toggle('hidden', !isVisible);
        });
      });
    }

    function openColumnSettings() {
      const modal = document.getElementById('columnSettingsModal');
      if (!modal) return;
      modal.classList.remove('hidden');
      modal.classList.add('flex');

      document.querySelectorAll('.column-visibility-checkbox').forEach(checkbox => {
        const columnId = checkbox.getAttribute('data-column-id');
        checkbox.checked = columnVisibility[columnId] !== false;
      });
    }

    function closeColumnSettings() {
      const modal = document.getElementById('columnSettingsModal');
      if (!modal) return;
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    function saveColumnSettings() {
      const checkboxes = document.querySelectorAll('.column-visibility-checkbox');
      const checkedColumns = [];

      checkboxes.forEach(checkbox => {
        const columnId = checkbox.getAttribute('data-column-id');
        const isChecked = checkbox.checked;
        columnVisibility[columnId] = isChecked;
        if (isChecked) checkedColumns.push(columnId);
      });

      if (checkedColumns.length === 0) {
        alert('At least one column must be visible.');
        return;
      }

      localStorage.setItem('visitorLogsColumns', JSON.stringify(columnVisibility));
      applyColumnVisibility();
      closeColumnSettings();
    }

    document.addEventListener('click', function(event) {
      const modal = document.getElementById('columnSettingsModal');
      if (!modal || modal.classList.contains('hidden')) return;
      const modalContent = modal.querySelector('.bg-white');
      if (modalContent && !modalContent.contains(event.target) && !event.target.closest('#logs-column-btn')) {
        closeColumnSettings();
      }
    });

    function escapeAttr(value) {
      return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }

    function attachRowClickHandlers() {
      const rows = document.querySelectorAll('.interactive-row');
      rows.forEach(row => {
        if (row._hasClickHandler) return;
        row.addEventListener('click', function() {
          const isAlreadyActive = row.classList.contains('active');
          rows.forEach(r => r.classList.remove('active'));
          if (isAlreadyActive) {
            closeVisitorDetailsPanel();
            return;
          }

          row.classList.add('active');
          const data = {
            name: row.dataset.name || 'Visitor',
            email: row.dataset.email || 'N/A',
            phone: row.dataset.phone || 'N/A',
            host: row.dataset.host || 'N/A',
            source: row.dataset.source || 'N/A',
            checkin: row.dataset.checkin || '',
            checkout: row.dataset.checkout || '',
            purpose: row.dataset.purpose || 'N/A',
            status: row.dataset.status || 'PENDING',
            avatar: row.dataset.avatar || '',
            passId: row.dataset.passId || 'N/A',
            comment: row.dataset.comment || row.dataset.company || ''
          };
          openVisitorDetailsPanel(data);
        });
        row._hasClickHandler = true;
      });
    }

    function openVisitorDetailsPanel(data) {
      const panel = document.getElementById('visitorDetailsPanel');
      if (!panel) return;

      const avatarEl = document.getElementById('detailAvatar');
      avatarEl.innerHTML = '';
      avatarEl.className = 'visitor-panel__avatar';
      if (data.avatar) {
        avatarEl.innerHTML = `<img src="${data.avatar}" alt="${data.name}" class="w-14 h-14 rounded-full object-cover">`;
      } else {
        const initial = (data.name || 'V').charAt(0).toUpperCase();
        avatarEl.textContent = initial;
      }

      setDetailText('detailNameDisplay', data.name || 'Visitor');
      setDetailText('detailName', data.name || 'Visitor');
      setDetailText('detailEmail', data.email || 'N/A');
      setDetailText('detailPhone', data.phone || 'N/A');
      setDetailText('detailHost', data.host || 'N/A');
      setDetailText('detailPurpose', data.purpose || 'N/A');
      // Only show comment when visitor has checked out; otherwise keep it empty
      const hasCheckout = !!data.checkout;
      setDetailText('detailComment', hasCheckout ? (data.comment || '—') : '—');
      setDetailText('detailPassId', data.passId || 'N/A');
      document.getElementById('detailCheckin').textContent = data.checkin ? formatDateTime(data.checkin) : 'N/A';
      document.getElementById('detailCheckout').textContent = data.checkout ? formatDateTime(data.checkout) : '—';
      applyStatusBadge(data.status || 'PENDING');

      panel.classList.remove('hidden');
      panel.classList.add('flex');
      document.body.classList.add('visitor-panel-open');
    }

    function setDetailText(id, value) {
      const el = document.getElementById(id);
      if (el) {
        el.textContent = value;
      }
    }

    function applyStatusBadge(statusText) {
      const badge = document.getElementById('detailStatus');
      if (!badge) return;
      const normalized = (statusText || '').toLowerCase();
      badge.textContent = statusText;
      badge.className = 'visitor-panel__badge';
      if (normalized === 'pending') {
        badge.classList.add('visitor-panel__badge--pending');
      } else if (normalized === 'approved' || normalized === 'completed') {
        badge.classList.add('visitor-panel__badge--approved');
      } else if (normalized === 'rejected') {
        badge.classList.add('visitor-panel__badge--rejected');
      }
    }

    function closeVisitorDetailsPanel() {
      const panel = document.getElementById('visitorDetailsPanel');
      if (!panel) return;
      panel.classList.add('hidden');
      panel.classList.remove('flex');
      document.querySelectorAll('.interactive-row').forEach(row => row.classList.remove('active'));
      document.body.classList.remove('visitor-panel-open');
    }

    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeVisitorDetailsPanel();
      }
    });

    const panelOverlay = document.querySelector('#visitorDetailsPanel .visitor-panel-overlayVisitor');
    if (panelOverlay) {
      panelOverlay.addEventListener('click', closeVisitorDetailsPanel);
    }

    initializeColumnVisibility();

    // Global variables
    let currentTab = 'logs';
    let dailyTrendsChart = null;
    let visitorTypesChart = null;
    let peakHoursChart = null;

    // Tab functionality
    function showTab(tabName) {
      currentTab = tabName;
      
      // Hide all tab contents
      document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
      });
      
      // Reset all navigation buttons
      const nav1 = document.getElementById('nav-logs');
      const nav2 = document.getElementById('nav-reports');
      
      [nav1, nav2].forEach(btn => {
        if (btn) {
          btn.classList.remove('text-blue-600', 'text-blue-800', 'font-semibold');
          btn.classList.add('text-gray-600');
        }
      });
      
      // Show selected tab content
      document.getElementById(tabName + '-content').classList.remove('hidden');
      
      // Update active navigation button
      if (tabName === 'logs' && nav1) {
        nav1.classList.remove('text-gray-600');
        nav1.classList.add('text-blue-800', 'font-semibold');
        // Reflect in URL
        try {
          const url = new URL(window.location.href);
          url.searchParams.delete('tab');
          window.history.replaceState({}, '', url);
        } catch(e) {}
      } else if (tabName === 'reports' && nav2) {
        nav2.classList.remove('text-gray-600');
        nav2.classList.add('text-blue-600', 'font-semibold');
        // Reflect in URL
        try {
          const url = new URL(window.location.href);
          url.searchParams.set('tab', 'reports');
          window.history.replaceState({}, '', url);
        } catch(e) {}
      }
      
      // Load data for the selected tab
      loadTabData(tabName);
      
      // Initialize Reports & Analytics if switching to reports tab
      if (tabName === 'reports') {
        setTimeout(() => {
          loadAnalyticsData(); // Load data first
          initializeReportsAnalytics();
        }, 200);
      }
    }

    // Load data for specific tabs
    function loadTabData(tabName) {
      switch(tabName) {
        case 'logs':
          loadLogsData();
          break;
        case 'reports':
          loadReportsData();
          break;
      }
    }

    // Analytics functions
    function loadAnalyticsData() {
      // Always load analytics data - don't check if tab is visible
      console.log('Loading analytics data...');
      console.log('Route URL:', '{{ route("visitor.logs.analytics") }}');
      
      // Show loading state
      showAnalyticsLoading();
      
      // Load analytics data from backend
      fetch('{{ route("visitor.logs.analytics") }}?time_range=today', {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
        .then(response => {
          console.log('Analytics response status:', response.status);
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          console.log('Analytics data loaded:', data);
          console.log('Total visitors from API:', data.statistics?.total_visitors);
          // Create charts with real data only
          createDailyTrendsChart(data.daily_trends || []);
          createVisitorTypesChart(data.visitor_types || {});
          createHostsDepartmentsChart(data.hosts_departments || []);
          createPeakHoursChart(data.peak_hours || []);
          updateAnalyticsStats(data);
        })
        .catch(error => {
          console.error('Error loading analytics data:', error);
          showNotification('Error loading analytics data: ' + error.message, 'error');
          // Show empty state instead of static data
          showEmptyAnalyticsState();
        });
    }

    function showAnalyticsLoading() {
      // Show loading state for charts
      const chartContainers = document.querySelectorAll('.chart-container');
      chartContainers.forEach(container => {
        container.innerHTML = '<div class="flex items-center justify-center h-full"><div class="loading loading-spinner loading-md"></div></div>';
      });
    }

    function showEmptyAnalyticsState() {
      // Show empty state for analytics when data fails to load
      const statNumbers = document.querySelectorAll('#reports-content .stat-number');
      statNumbers.forEach((stat, index) => {
        stat.textContent = '0';
      });
      
      // Clear charts
      const chartContainers = document.querySelectorAll('.chart-container');
      chartContainers.forEach(container => {
        container.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500">No data available</div>';
      });
    }

    function refreshAnalytics() {
      // Get selected time range
      const timeRange = document.getElementById('analytics-time-range').value || 'today';
      
      // Show loading state
      showAnalyticsLoading();
      
      // Load fresh analytics data
      loadAnalyticsDataForRange(timeRange);
      
      showNotification('Analytics data refreshed!', 'success');
    }

    function createDailyTrendsChart(data = null) {
      const canvas = document.getElementById('dailyTrendsChart');
      if (!canvas) {
        console.error('Daily trends chart canvas not found');
        return;
      }
      
      const ctx = canvas.getContext('2d');
      
      if (dailyTrendsChart) {
        dailyTrendsChart.destroy();
      }
      
      // Use real data only - no fallback to static data
      let chartData = data || [];
      
      dailyTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: chartData.map(item => item.label || item.date),
          datasets: [{
            label: 'Visitors',
            data: chartData.map(item => item.count || 0),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          },
          layout: {
            padding: {
              top: 10,
              bottom: 10,
              left: 10,
              right: 10
            }
          }
        }
      });
    }

    // Update Visitor Types HTML content with real data
    function updateVisitorTypesHTML(data = null) {
      const container = document.getElementById('visitor-types-content');
      if (!container) return;
      
      if (!data || Object.keys(data).length === 0) {
        container.innerHTML = `
          <div class="text-center py-8 text-gray-500">
            <i data-lucide="building" class="w-8 h-8 mx-auto mb-2"></i>
            <p>No visitor types data available</p>
          </div>
        `;
        return;
      }
      
      // Calculate total visitors for percentage calculation
      const totalVisitors = Object.values(data).reduce((sum, count) => sum + count, 0);
      
      // Generate HTML for each visitor type
      const colors = ['bg-blue-500', 'bg-green-500', 'bg-orange-500', 'bg-purple-500', 'bg-red-500', 'bg-yellow-500', 'bg-indigo-500', 'bg-pink-500'];
      const textColors = ['text-blue-600', 'text-green-600', 'text-orange-600', 'text-purple-600', 'text-red-600', 'text-yellow-600', 'text-indigo-600', 'text-pink-600'];
      const bgColors = ['bg-blue-100', 'bg-green-100', 'bg-orange-100', 'bg-purple-100', 'bg-red-100', 'bg-yellow-100', 'bg-indigo-100', 'bg-pink-100'];
      
      const html = Object.entries(data).map(([purpose, count], index) => {
        const percentage = totalVisitors > 0 ? Math.round((count / totalVisitors) * 100) : 0;
        const colorClass = colors[index % colors.length];
        const textColorClass = textColors[index % textColors.length];
        const bgColorClass = bgColors[index % bgColors.length];
        
        return `
          <div class="flex items-center justify-between department-item">
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 ${colorClass} rounded-full"></div>
              <span class="text-sm font-medium text-gray-700">${purpose}</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">${count} visitor${count !== 1 ? 's' : ''}</span>
              <span class="text-xs font-medium ${textColorClass} ${bgColorClass} px-2 py-1 rounded-full">${percentage}%</span>
            </div>
          </div>
        `;
      }).join('');
      
      container.innerHTML = html;
      
      // Re-initialize Lucide icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    }

    // Update Hosts/Departments HTML content with real data
    function updateHostsDepartmentsHTML(data = null) {
      const container = document.getElementById('hosts-departments-content');
      if (!container) return;
      
      if (!data || data.length === 0) {
        container.innerHTML = `
          <div class="text-center py-8 text-gray-500">
            <i data-lucide="users" class="w-8 h-8 mx-auto mb-2"></i>
            <p>No hosts/departments data available</p>
          </div>
        `;
        return;
      }
      
      // Calculate total visitors for percentage calculation
      const totalVisitors = data.reduce((sum, item) => sum + item.count, 0);
      
      // Generate HTML for each host/department
      const colors = ['bg-blue-500', 'bg-green-500', 'bg-orange-500', 'bg-purple-500', 'bg-red-500', 'bg-yellow-500', 'bg-indigo-500', 'bg-pink-500'];
      const textColors = ['text-blue-600', 'text-green-600', 'text-orange-600', 'text-purple-600', 'text-red-600', 'text-yellow-600', 'text-indigo-600', 'text-pink-600'];
      const bgColors = ['bg-blue-100', 'bg-green-100', 'bg-orange-100', 'bg-purple-100', 'bg-red-100', 'bg-yellow-100', 'bg-indigo-100', 'bg-pink-100'];
      
      const html = data.map((item, index) => {
        const percentage = totalVisitors > 0 ? Math.round((item.count / totalVisitors) * 100) : 0;
        const colorClass = colors[index % colors.length];
        const textColorClass = textColors[index % textColors.length];
        const bgColorClass = bgColors[index % bgColors.length];
        const typeIcon = 'building';
        const typeLabel = 'Department';
        
        return `
          <div class="flex items-center justify-between department-item">
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 ${colorClass} rounded-full"></div>
              <div class="flex items-center gap-1">
                <i data-lucide="${typeIcon}" class="w-3 h-3 text-gray-500"></i>
                <span class="text-sm font-medium text-gray-700">${item.name}</span>
                <span class="text-xs text-gray-500">(${typeLabel})</span>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">${item.count} visitor${item.count !== 1 ? 's' : ''}</span>
              <span class="text-xs font-medium ${textColorClass} ${bgColorClass} px-2 py-1 rounded-full">${percentage}%</span>
            </div>
          </div>
        `;
      }).join('');
      
      container.innerHTML = html;
      
      // Re-initialize Lucide icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    }

    function createHostsDepartmentsChart(data = null) {
      // Update the HTML content with real data
      updateHostsDepartmentsHTML(data);
      
      const canvas = document.getElementById('visitorTypesChart');
      if (!canvas) {
        console.error('Hosts/Departments chart canvas not found');
        return;
      }
      
      const ctx = canvas.getContext('2d');
      
      if (visitorTypesChart) {
        visitorTypesChart.destroy();
      }
      
      // Use real data only - no fallback to static data
      let chartData = data || [];
      
      const labels = chartData.map(item => `${item.name}`);
      const values = chartData.map(item => item.count);
      const colors = [
        'rgb(59, 130, 246)',
        'rgb(16, 185, 129)',
        'rgb(245, 158, 11)',
        'rgb(239, 68, 68)',
        'rgb(139, 92, 246)',
        'rgb(236, 72, 153)',
        'rgb(34, 197, 94)',
        'rgb(251, 146, 60)'
      ];
      
      visitorTypesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: values,
            backgroundColor: colors.slice(0, labels.length),
            borderWidth: 2,
            borderColor: '#ffffff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 20,
                usePointStyle: true
              }
            }
          }
        }
      });
    }

    function createVisitorTypesChart(data = null) {
      // Update the HTML content with real data
      updateVisitorTypesHTML(data);
      
      const canvas = document.getElementById('visitorTypesChart');
      if (!canvas) {
        console.error('Visitor types chart canvas not found');
        return;
      }
      
      const ctx = canvas.getContext('2d');
      
      if (visitorTypesChart) {
        visitorTypesChart.destroy();
      }
      
      // Use real data only - no fallback to static data
      let chartData = data || {};
      
      const labels = Object.keys(chartData);
      const values = Object.values(chartData);
      const colors = [
        'rgb(59, 130, 246)',
        'rgb(16, 185, 129)',
        'rgb(245, 158, 11)',
        'rgb(239, 68, 68)',
        'rgb(139, 92, 246)',
        'rgb(236, 72, 153)',
        'rgb(34, 197, 94)',
        'rgb(251, 146, 60)'
      ];
      
      visitorTypesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: values,
            backgroundColor: colors.slice(0, labels.length)
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 10,
                usePointStyle: true
              }
            }
          },
          layout: {
            padding: {
              top: 10,
              bottom: 10,
              left: 10,
              right: 10
            }
          }
        }
      });
    }

    // Update Peak Hours HTML content with real data
    function updatePeakHoursHTML(data = null) {
      const container = document.getElementById('peak-hours-content');
      if (!container) return;
      
      if (!data || data.length === 0) {
        container.innerHTML = `
          <div class="text-center py-8 text-gray-500">
            <i data-lucide="clock" class="w-8 h-8 mx-auto mb-2"></i>
            <p>No peak hours data available</p>
          </div>
        `;
        return;
      }
      
      // Find the maximum count for percentage calculation
      const maxCount = Math.max(...data.map(item => item.count || 0));
      
      // Generate HTML for each hour
      const html = data.map(item => {
        const percentage = maxCount > 0 ? Math.round((item.count / maxCount) * 100) : 0;
        const hour = String(item.hour).padStart(2, '0');
        return `
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700">${hour}:00</span>
            <div class="flex-1 mx-3">
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full progress-bar" style="width: ${percentage}%"></div>
              </div>
            </div>
            <span class="text-sm font-medium text-gray-700">${item.count || 0}</span>
          </div>
        `;
      }).join('');
      
      container.innerHTML = html;
      
      // Re-initialize Lucide icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    }

    function createPeakHoursChart(data = null) {
      // Update the HTML content with real data
      updatePeakHoursHTML(data);
      
      const canvas = document.getElementById('peakHoursChart');
      if (!canvas) {
        console.error('Peak hours chart canvas not found');
        return;
      }
      
      const ctx = canvas.getContext('2d');
      
      if (peakHoursChart) {
        peakHoursChart.destroy();
      }
      
      // Use real data only - no fallback to static data
      let chartData = data || [];
      
      peakHoursChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: chartData.map(item => `${item.hour}:00`),
          datasets: [{
            label: 'Visitors',
            data: chartData.map(item => item.count || 0),
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true
            },
            x: {
              ticks: {
                maxTicksLimit: 12
              }
            }
          }
        }
      });
    }

    function updateAnalyticsStats(data = null) {
      console.log('updateAnalyticsStats called with data:', data);
      // Update analytics statistics with real data
      if (data && data.statistics) {
        const stats = data.statistics;
        console.log('Updating stats with:', stats);
        
        // Update main statistics cards in Reports & Analytics section
        const statNumbers = document.querySelectorAll('#reports-content .stat-number');
        console.log('Found stat numbers elements:', statNumbers.length);
        if (statNumbers.length >= 4) {
          // Total Visitors
          statNumbers[0].textContent = stats.total_visitors || 0;
          console.log('Updated total visitors to:', stats.total_visitors || 0);
          // Avg. Visit Duration
          statNumbers[1].textContent = stats.average_duration || '0h';
          // Peak Capacity (calculate from current vs total)
          const peakCapacity = stats.total_visitors > 0 ? Math.round((stats.currently_in / stats.total_visitors) * 100) : 0;
          statNumbers[2].textContent = peakCapacity + '%';
          // Security Incidents (always 0 for now)
          statNumbers[3].textContent = '0';
        }
        
        // Update detailed analytics
        if (data.peak_hours && Array.isArray(data.peak_hours)) {
          // Find peak hour
          const peakHour = data.peak_hours.reduce((max, hour) => hour.count > max.count ? hour : max, {count: 0});
          if (peakHour.count > 0) {
            const nextHour = peakHour.hour + 1;
            const peakHoursText = `${peakHour.hour.toString().padStart(2, '0')}:00 - ${nextHour.toString().padStart(2, '0')}:00`;
            const peakHoursElement = document.getElementById('peakHoursDetail');
            if (peakHoursElement) {
              peakHoursElement.textContent = peakHoursText;
            }
          }
        }
        
        const mostVisitedElement = document.getElementById('mostVisitedFacility');
        if (mostVisitedElement) {
          mostVisitedElement.textContent = data.most_visited_facility || 'N/A';
        }
        
        const returnVisitorsElement = document.getElementById('returnVisitors');
        if (returnVisitorsElement) {
          returnVisitorsElement.textContent = (data.return_visitors || 0) + '%';
        }
      } else {
        // Show empty state for missing data
        const statNumbers = document.querySelectorAll('#reports-content .stat-number');
        statNumbers.forEach((stat, index) => {
          stat.textContent = '0';
        });
      }
    }

    // Time range functions
    function setTimeRange(range) {
      // Remove active class from all time range buttons
      document.querySelectorAll('.time-range-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      
      // Add active class to clicked button
      event.target.classList.add('active');
      
      // Show/hide custom date range
      const customRange = document.getElementById('custom-date-range');
      if (range === 'custom') {
        customRange.classList.remove('hidden');
        customRange.classList.add('flex', 'flex-wrap');
      } else {
        customRange.classList.add('hidden');
        customRange.classList.remove('flex', 'flex-wrap');
        // Load data for the selected time range
        loadAnalyticsDataForRange(range);
      }
    }

    function loadAnalyticsDataForRange(timeRange) {
      // Always load analytics data - don't check if tab is visible
      console.log('Loading analytics data for range:', timeRange);
      
      // Show loading state
      showAnalyticsLoading();
      
      // Load analytics data from backend with time range
      fetch(`{{ route("visitor.logs.analytics") }}?time_range=${timeRange}`, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
        .then(response => {
          console.log('Analytics response status for range:', response.status);
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          console.log('Analytics data loaded for range:', timeRange, data);
          createDailyTrendsChart(data.daily_trends || []);
          createVisitorTypesChart(data.visitor_types || {});
          createHostsDepartmentsChart(data.hosts_departments || []);
          createPeakHoursChart(data.peak_hours || []);
          updateAnalyticsStats(data);
        })
        .catch(error => {
          console.error('Error loading analytics data:', error);
          showNotification('Error loading analytics data: ' + error.message, 'error');
          // Show empty state instead of static data
          showEmptyAnalyticsState();
        });
    }

    function applyCustomRange() {
      // Always load analytics data - don't check if tab is visible
      console.log('Applying custom range...');
      
      const startDate = document.getElementById('start-date').value;
      const endDate = document.getElementById('end-date').value;
      
      if (startDate && endDate) {
        // Show loading state
        showAnalyticsLoading();
        
        // Load analytics data from backend with custom date range
        fetch(`{{ route("visitor.logs.analytics") }}?time_range=custom&start_date=${startDate}&end_date=${endDate}`, {
          method: 'GET',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        })
          .then(response => {
            console.log('Analytics response status for custom range:', response.status);
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            console.log('Analytics data loaded for custom range:', data);
            createDailyTrendsChart(data.daily_trends || []);
            createVisitorTypesChart(data.visitor_types || {});
            createHostsDepartmentsChart(data.hosts_departments || []);
            createPeakHoursChart(data.peak_hours || []);
            updateAnalyticsStats(data);
          })
          .catch(error => {
            console.error('Error loading analytics data:', error);
            showNotification('Error loading analytics data: ' + error.message, 'error');
            // Show empty state instead of static data
            showEmptyAnalyticsState();
          });
      } else {
        showNotification('Please select both start and end dates', 'error');
      }
    }

    // Search functionality
    let searchTimeout;
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('logs-search-input');
      if (searchInput) {
        searchInput.addEventListener('input', function(e) {
          clearTimeout(searchTimeout);
          const searchTerm = e.target.value.trim();
          searchTimeout = setTimeout(() => {
            filterLogsBySearch(searchTerm);
          }, 300);
        });
      }
    });

    function filterLogsBySearch(searchTerm) {
      const tbody = document.getElementById('logs-table-body');
      if (!tbody) return;
      
      const rows = tbody.querySelectorAll('tr');
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const matches = searchTerm === '' || text.includes(searchTerm.toLowerCase());
        row.style.display = matches ? '' : 'none';
      });
    }

    // Date Range Dropdown
    function toggleDateRangeDropdown() {
      const dropdown = document.getElementById('logs-date-range-dropdown');
      if (dropdown) {
        dropdown.classList.toggle('hidden');
      }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const btn = document.getElementById('logs-date-range-btn');
      const dropdown = document.getElementById('logs-date-range-dropdown');
      if (btn && dropdown && !btn.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
      }
    });

    function setDateRange(range) {
      const today = new Date();
      const startDateInput = document.getElementById('logs-start-date');
      const endDateInput = document.getElementById('logs-end-date');
      const rangeText = document.getElementById('logs-date-range-text');
      const dropdown = document.getElementById('logs-date-range-dropdown');
      
      let startDate = '';
      let endDate = '';
      let label = 'All Time';
      let shouldFetch = true;
      
      switch(range) {
        case 'all':
          label = 'All Time';
          break;
        case 'today':
          startDate = endDate = today.toISOString().split('T')[0];
          label = 'Today';
          break;
        case 'week': {
          const weekAgo = new Date(today);
          weekAgo.setDate(today.getDate() - 6);
          startDate = weekAgo.toISOString().split('T')[0];
          endDate = today.toISOString().split('T')[0];
          label = 'Last 7 Days';
          break;
        }
        case 'month': {
          const monthAgo = new Date(today);
          monthAgo.setDate(today.getDate() - 29);
          startDate = monthAgo.toISOString().split('T')[0];
          endDate = today.toISOString().split('T')[0];
          label = 'Last 30 Days';
          break;
        }
        case 'quarter': {
          const quarterAgo = new Date(today);
          quarterAgo.setDate(today.getDate() - 89);
          startDate = quarterAgo.toISOString().split('T')[0];
          endDate = today.toISOString().split('T')[0];
          label = 'Last 90 Days';
          break;
        }
        case 'year': {
          const yearAgo = new Date(today);
          yearAgo.setFullYear(today.getFullYear() - 1);
          startDate = yearAgo.toISOString().split('T')[0];
          endDate = today.toISOString().split('T')[0];
          label = 'Last Year';
          break;
        }
        case 'custom': {
          const start = prompt('Enter start date (YYYY-MM-DD):', startDateInput?.value || today.toISOString().split('T')[0]);
          const end = prompt('Enter end date (YYYY-MM-DD):', endDateInput?.value || today.toISOString().split('T')[0]);
          if (start && end) {
            startDate = start;
            endDate = end;
            label = `${start} → ${end}`;
          } else {
            shouldFetch = false;
          }
          break;
        }
        default:
          shouldFetch = false;
      }
      
      if (startDateInput && endDateInput) {
        startDateInput.value = startDate;
        endDateInput.value = endDate;
      }
      
      if (rangeText) {
        rangeText.textContent = label;
      }
      
      if (dropdown) {
        dropdown.classList.add('hidden');
      }
      
      if (shouldFetch) {
        currentPage = 1;
        fetchLogs(startDate, endDate, 1, perPage);
      }
    }

    // Export Logs
    function exportVisitorLogs() {
      const startDate = document.getElementById('logs-start-date')?.value || '';
      const endDate = document.getElementById('logs-end-date')?.value || '';
      const searchTerm = document.getElementById('logs-search-input')?.value || '';
      
      // Build export URL
      const url = new URL(`{{ route('visitor.logs.export') }}`, window.location.origin);
      if (startDate) url.searchParams.set('start_date', startDate);
      if (endDate) url.searchParams.set('end_date', endDate);
      if (searchTerm) url.searchParams.set('search', searchTerm);
      
      // Open in new window to trigger download
      window.open(url.toString(), '_blank');
      
      showNotification('Exporting visitor logs...', 'success');
    }

    // Logs functions
    function loadLogsData() {
      const startDate = document.getElementById('logs-start-date')?.value || '';
      const endDate = document.getElementById('logs-end-date')?.value || '';
      
      // Initialize pagination from server-side data
      @if(isset($visitors))
        currentPage = {{ $visitors->currentPage() }};
        perPage = {{ $visitors->perPage() }};
        totalItems = {{ $visitors->total() }};
        updatePaginationControls();
      @endif
      
      fetchLogs(startDate, endDate, currentPage, perPage);
    }

    function formatDate(dtStr) {
      if (!dtStr) return 'N/A';
      try { const d = new Date(dtStr); return d.toLocaleDateString(undefined, { month:'short', day:'2-digit', year:'numeric' }); } catch { return 'N/A'; }
    }
    function formatTime(dtStr) {
      if (!dtStr) return '—';
      try { const d = new Date(dtStr); return d.toLocaleTimeString(undefined, { hour:'2-digit', minute:'2-digit' }); } catch { return '—'; }
    }
    function formatDateTime(dtStr) {
      if (!dtStr) return '';
      try {
        const d = new Date(dtStr);
        return d.toLocaleString(undefined, {
          month: 'short',
          day: '2-digit',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit'
        });
      } catch {
        return '';
      }
    }

    function getStatusColorClass(status) {
      switch (status) {
        case 'COMPLETED':
        case 'APPROVED':
          return 'text-emerald-600 bg-emerald-50';
        case 'REJECTED':
          return 'text-red-600 bg-red-50';
        default:
          return 'text-amber-600 bg-amber-50';
      }
    }

    function renderLogsTable(rows = []) {
      const tbody = document.getElementById('logs-table-body');
      if (!tbody) return;
      if (!Array.isArray(rows) || rows.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-12">No Visitor Logs Found</td></tr>';
        return;
      }
      const html = rows.map(v => {
        const idNumberEl = document.createElement('div');
        idNumberEl.innerHTML = v.pass_id ?? 'N/A';
        const idNumberText = idNumberEl.textContent.trim() || 'N/A';
        const checkInText = formatDateTime(v.time_in);
        const checkOutText = formatDateTime(v.time_out);
        const approvalStatus = (
          v.approval_status
            || (v.status === 'active'
                  ? 'APPROVED'
                  : (v.time_out ? 'COMPLETED' : 'PENDING'))
        ).toUpperCase();
        const statusColor = getStatusColorClass(approvalStatus);
        const initial = (v.name || 'V').charAt(0).toUpperCase();
        const avatarHtml = v.profile_photo_url
          ? `<div class="w-10 h-10 rounded-full overflow-hidden ring-2 ring-white shadow"><img src="${escapeAttr(v.profile_photo_url)}" alt="${escapeAttr(v.name ?? 'Visitor')}" class="object-cover w-full h-full"></div>`
          : `<div class="w-10 h-10 rounded-full bg-blue-900 flex items-center justify-center text-white font-semibold ring-2 ring-white shadow">${initial}</div>`;
        return `
          <tr class="hover:bg-gray-50 transition-colors interactive-row"
            data-id="${v.id}"
            data-name="${escapeAttr(v.name ?? 'Visitor')}"
            data-email="${escapeAttr(v.email ?? 'N/A')}"
            data-phone="${escapeAttr(v.contact ?? 'N/A')}"
            data-host="${escapeAttr(v.host_employee ?? v.host ?? 'N/A')}"
            data-source="${escapeAttr(v.source ?? 'N/A')}"
            data-checkin="${escapeAttr(v.time_in ?? '')}"
            data-checkout="${escapeAttr(v.time_out ?? '')}"
            data-purpose="${escapeAttr(v.purpose ?? 'N/A')}"
            data-status="${escapeAttr(approvalStatus)}"
            data-avatar="${escapeAttr(v.profile_photo_url ?? '')}"
            data-pass-id="${escapeAttr(idNumberText)}"
            data-company="${escapeAttr(v.company ?? '')}"
            data-comment="${escapeAttr(v.rating_comment ?? v.special_instructions ?? v.company ?? '')}"
          >
            <td class="py-3 px-4" data-column="visitor_name">
              <div class="flex items-center space-x-3">
                <div class="relative">
                  ${avatarHtml}
                </div>
                <div><div class="font-medium text-gray-900">${v.name ?? ''}</div></div>
              </div>
            </td>
            <td class="py-3 px-4 text-center text-sm text-gray-600" data-column="contact_number">${v.contact ?? 'N/A'}</td>
            <td class="py-3 px-4 text-center" data-column="purpose"><span class="badge badge-outline badge-sm">${v.purpose ?? 'N/A'}</span></td>
            <td class="py-3 px-4 text-center text-sm text-gray-600" data-column="checkin_checkout">
              ${checkInText ? `<div class="text-emerald-500 font-medium">${checkInText}</div>` : '<div class="text-gray-400">N/A</div>'}
              ${v.time_out ? `<div class="text-orange-500 text-xs mt-1">${checkOutText}</div>` : ''}
            </td>
            <td class="py-3 px-4 text-center text-sm font-semibold" data-column="approval_status">
              <span class="px-3 py-1 rounded-full ${statusColor}">${approvalStatus}</span>
            </td>
            <td class="py-3 px-4 text-sm text-gray-600 duration-cell" data-column="duration">—</td>
            <td class="py-3 px-4 text-center text-sm text-gray-600 font-mono" data-column="id_number">${v.pass_id ?? 'N/A'}</td>
            <td class="py-3 px-4 text-center" data-column="actions">
              <div class="flex items-center justify-center gap-2">
                <button class="btn btn-ghost btn-xs" title="View details" onclick="event.stopPropagation(); viewVisitorDetails(${v.id})">
                  <i data-lucide="info" class="w-4 h-4"></i>
                </button>
                <button class="btn btn-ghost btn-xs" title="Print pass" onclick="event.stopPropagation(); printVisitorPass(${v.id})">
                  <i data-lucide="printer" class="w-4 h-4 text-emerald-600"></i>
                </button>
                <button class="checkout-icon-btn" title="Check-out" onclick="event.stopPropagation(); openCheckoutRatingModal(${v.id})">
                  <span class="checkout-icon"></span>
                </button>
              </div>
            </td>
          </tr>
        `;
      }).join('');
      tbody.innerHTML = html;
      if (window.lucide && window.lucide.createIcons) { window.lucide.createIcons(); }
      applyColumnVisibility();
      attachRowClickHandlers();
    }

    // Pagination state
    let currentPage = 1;
    let perPage = 10;
    let totalItems = 0;

    function fetchLogs(startDate, endDate, page = 1, itemsPerPage = null) {
      const url = new URL(`{{ route('visitor.logs.logs') }}` , window.location.origin);
      if (startDate) url.searchParams.set('start_date', startDate);
      if (endDate) url.searchParams.set('end_date', endDate);
      url.searchParams.set('page', page);
      if (itemsPerPage) {
        url.searchParams.set('per_page', itemsPerPage);
      } else {
        url.searchParams.set('per_page', perPage);
      }
      
      fetch(url.toString(), { headers: { 'Accept':'application/json' } })
        .then(r => r.json())
        .then(data => {
          const rows = Array.isArray(data?.data) ? data.data : (Array.isArray(data) ? data : []);
          renderLogsTable(rows);
          
          // Update pagination state
          if (data.current_page) {
            currentPage = data.current_page;
            totalItems = data.total || 0;
            perPage = data.per_page || 10;
            updatePaginationControls();
          }
        })
        .catch(() => { 
          renderLogsTable([]);
          updatePaginationControls();
        });
    }

    function changePerPage(newPerPage) {
      perPage = parseInt(newPerPage);
      currentPage = 1;
      const startDate = document.getElementById('logs-start-date')?.value || '';
      const endDate = document.getElementById('logs-end-date')?.value || '';
      fetchLogs(startDate, endDate, 1, perPage);
    }

    function goToPage(page) {
      if (page < 1) return;
      currentPage = page;
      const startDate = document.getElementById('logs-start-date')?.value || '';
      const endDate = document.getElementById('logs-end-date')?.value || '';
      fetchLogs(startDate, endDate, page, perPage);
    }

    function updatePaginationControls() {
      // Update per page selector
      const perPageSelect = document.getElementById('logs-per-page');
      if (perPageSelect) {
        perPageSelect.value = perPage;
      }
      
      // Update range display
      const rangeDisplay = document.getElementById('logs-pagination-range');
      if (rangeDisplay && totalItems > 0) {
        const start = ((currentPage - 1) * perPage) + 1;
        const end = Math.min(currentPage * perPage, totalItems);
        rangeDisplay.textContent = `${start}-${end} of ${totalItems}`;
      } else if (rangeDisplay) {
        rangeDisplay.textContent = '0 of 0';
      }
      
      // Update navigation buttons
      const prevBtn = document.getElementById('logs-prev-btn');
      const nextBtn = document.getElementById('logs-next-btn');
      
      if (prevBtn) {
        prevBtn.disabled = currentPage <= 1;
        if (currentPage <= 1) {
          prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
          prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
      }
      
      if (nextBtn) {
        const totalPages = Math.ceil(totalItems / perPage);
        nextBtn.disabled = currentPage >= totalPages;
        if (currentPage >= totalPages) {
          nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
          nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
      }
    }

    function applyLogsFilters() {
      const startDate = document.getElementById('logs-start-date').value;
      const endDate = document.getElementById('logs-end-date').value;
      currentPage = 1; // Reset to first page when filters change
      fetchLogs(startDate, endDate, 1, perPage);
    }

    // Reports functions
    function loadReportsData() {
      // Load reports data
      console.log('Loading reports data...');
      initializeReportsAnalytics();
    }

    // Initialize Reports & Analytics
    function initializeReportsAnalytics() {
      // Load real analytics data first
      loadAnalyticsData();
      
      // Animate statistics cards
      animateStatisticsCards();
      
      // Initialize peak hours chart
      initializePeakHoursChart();
      
      // Initialize department distribution
      initializeDepartmentChart();
      
      // Initialize visit purposes
      initializeVisitPurposes();
      
      // Start real-time updates
      startRealTimeUpdates();
      
      // Add click interactions
      addClickInteractions();
    }

    // Animate statistics cards
    function animateStatisticsCards() {
      const cards = document.querySelectorAll('#reports-content .bg-white.rounded-xl');
      cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
          card.style.transition = 'all 0.6s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 100);
      });
    }

    // Initialize peak hours chart with animation
    function initializePeakHoursChart() {
      const progressBars = document.querySelectorAll('#reports-content .bg-blue-500');
      progressBars.forEach((bar, index) => {
        const width = bar.style.width;
        bar.style.width = '0%';
        
        setTimeout(() => {
          bar.style.transition = 'width 1.5s ease-in-out';
          bar.style.width = width;
        }, index * 100 + 500);
      });
    }

    // Initialize department distribution with animation
    function initializeDepartmentChart() {
      const departmentItems = document.querySelectorAll('#reports-content .space-y-3 > div');
      departmentItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
          item.style.transition = 'all 0.5s ease';
          item.style.opacity = '1';
          item.style.transform = 'translateX(0)';
        }, index * 150 + 800);
      });
    }

    // Initialize visit purposes with animation
    function initializeVisitPurposes() {
      const purposeItems = document.querySelectorAll('#reports-content .space-y-3 > div');
      purposeItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
          item.style.transition = 'all 0.5s ease';
          item.style.opacity = '1';
          item.style.transform = 'translateX(0)';
        }, index * 150 + 1000);
      });
    }

    // Start real-time updates
    function startRealTimeUpdates() {
      // Update statistics every 30 seconds
      // Removed auto updates; manual refresh only
      
      // Update peak hours every minute
      // Removed auto updates; manual refresh only
      
      // Add hover effects
      addHoverEffects();
    }

    // Update statistics with animation
    function updateStatistics() {
      const statNumbers = document.querySelectorAll('#reports-content .text-3xl.font-bold');
      statNumbers.forEach(number => {
        const currentValue = parseInt(number.textContent);
        const newValue = currentValue + Math.floor(Math.random() * 3) - 1; // Random change -1 to +1
        
        if (newValue !== currentValue) {
          animateNumberChange(number, currentValue, newValue);
        }
      });
    }

    // Animate number changes
    function animateNumberChange(element, from, to) {
      const duration = 1000;
      const start = Date.now();
      
      function update() {
        const elapsed = Date.now() - start;
        const progress = Math.min(elapsed / duration, 1);
        const current = Math.round(from + (to - from) * progress);
        
        element.textContent = current;
        
        if (progress < 1) {
          requestAnimationFrame(update);
        }
      }
      
      requestAnimationFrame(update);
    }

    // Update peak hours with animation
    function updatePeakHours() {
      const progressBars = document.querySelectorAll('#reports-content .bg-blue-500');
      progressBars.forEach((bar, index) => {
        const currentWidth = parseInt(bar.style.width);
        const newWidth = Math.max(0, Math.min(100, currentWidth + (Math.random() - 0.5) * 10));
        
        bar.style.transition = 'width 0.8s ease';
        bar.style.width = newWidth + '%';
        
        // Update the count number
        const countElement = bar.closest('.flex').querySelector('.text-sm.font-medium:last-child');
        if (countElement) {
          const newCount = Math.round((newWidth / 100) * 30); // Assuming max 30 visitors
          countElement.textContent = newCount;
        }
      });
    }

    // Add hover effects
    function addHoverEffects() {
      // Card hover effects
      const cards = document.querySelectorAll('#reports-content .bg-white.rounded-xl');
      cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-5px)';
          this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
        });
        
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0)';
          this.style.boxShadow = '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)';
        });
      });

      // Progress bar hover effects
      const progressBars = document.querySelectorAll('#reports-content .bg-blue-500');
      progressBars.forEach(bar => {
        bar.addEventListener('mouseenter', function() {
          this.style.height = '8px';
          this.style.transition = 'height 0.3s ease';
        });
        
        bar.addEventListener('mouseleave', function() {
          this.style.height = '8px';
        });
      });
    }

    // Add click interactions
    function addClickInteractions() {
      // Statistics cards click to drill down
      const statCards = document.querySelectorAll('#reports-content .bg-white.rounded-xl');
      statCards.forEach(card => {
        card.addEventListener('click', function() {
          this.style.transform = 'scale(0.98)';
          setTimeout(() => {
            this.style.transform = 'scale(1)';
          }, 150);
          
          // Show detailed view (placeholder)
          showNotification('Detailed view coming soon!', 'info');
        });
      });

      // Department items click to filter
      const departmentItems = document.querySelectorAll('#reports-content .space-y-3 > div');
      departmentItems.forEach(item => {
        item.addEventListener('click', function() {
          const department = this.querySelector('.text-sm.font-medium').textContent;
          showNotification(`Filtering by ${department}`, 'info');
        });
      });
    }

    // Export Report Function
    function exportReport() {
      const timeRange = document.querySelector('#reports-content select').value || 'today';
      
      // Show loading state
      const button = event.target.closest('button');
      const originalText = button.innerHTML;
      button.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-1 animate-spin"></i>Exporting PDF...';
      button.disabled = true;
      if (typeof lucide !== 'undefined') { lucide.createIcons(); }

      // Derive date range for the selected timeRange
      const today = new Date();
      const fmt = (d) => d.toISOString().split('T')[0];
      let reportType = 'custom';
      let startDate = fmt(today);
      let endDate = fmt(today);
      if (timeRange === 'today') { reportType = 'daily'; }
      if (timeRange === 'week') {
        reportType = 'weekly';
        const wk = new Date(today); wk.setDate(today.getDate() - 7); startDate = fmt(wk); endDate = fmt(today);
      }
      if (timeRange === 'month') {
        reportType = 'monthly';
        const mo = new Date(today); mo.setMonth(today.getMonth() - 1); startDate = fmt(mo); endDate = fmt(today);
      }

      // Build and submit a real form to let the browser handle the download (avoids blob/mixed-content issues)
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '{{ route('visitor.logs.generate-report') }}';
      form.target = '_blank';
      form.style.display = 'none';

      const add = (name, value) => { const i = document.createElement('input'); i.type='hidden'; i.name=name; i.value=value; form.appendChild(i); };
      add('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
      add('report_type', reportType);
      add('start_date', startDate);
      add('end_date', endDate);
      add('format', 'pdf');

      document.body.appendChild(form);
      form.submit();
      document.body.removeChild(form);

      // Reset button state shortly after submit
      setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        if (typeof lucide !== 'undefined') { lucide.createIcons(); }
      }, 500);
    }

    // Get current statistics for export
    function getCurrentStatistics() {
      const stats = {};
      const statCards = document.querySelectorAll('#reports-content .stat-number');
      
      statCards.forEach((card, index) => {
        const label = card.closest('.bg-white').querySelector('p').textContent;
        const value = card.textContent;
        stats[label] = value;
      });
      
      return stats;
    }

    // Get current analytics for export
    function getCurrentAnalytics() {
      const analytics = {
        peakHours: [],
        departments: [],
        purposes: [],
        summary: {
          highlights: [],
          improvements: [],
          recommendations: []
        }
      };
      
      // Get peak hours data
      const peakHourItems = document.querySelectorAll('#reports-content .space-y-3 > div');
      peakHourItems.forEach(item => {
        const time = item.querySelector('.text-sm.font-medium:first-child')?.textContent;
        const count = item.querySelector('.text-sm.font-medium:last-child')?.textContent;
        if (time && count) {
          analytics.peakHours.push({ time, count: parseInt(count) });
        }
      });
      
      // Get department data
      const departmentItems = document.querySelectorAll('#reports-content .department-item');
      departmentItems.forEach(item => {
        const name = item.querySelector('.text-sm.font-medium').textContent;
        const count = item.querySelector('.text-sm.text-gray-600').textContent;
        const percentage = item.querySelector('.text-xs.font-medium').textContent;
        analytics.departments.push({ name, count, percentage });
      });
      
      // Get purpose data
      const purposeItems = document.querySelectorAll('#reports-content .purpose-item');
      purposeItems.forEach(item => {
        const name = item.querySelector('.text-sm.font-medium').textContent;
        const count = item.querySelector('.text-sm.text-gray-600').textContent;
        const percentage = item.querySelector('.text-xs.font-medium').textContent;
        analytics.purposes.push({ name, count, percentage });
      });
      
      // Get summary data
      const highlightItems = document.querySelectorAll('#reports-content .highlight-item');
      highlightItems.forEach(item => {
        analytics.summary.highlights.push(item.textContent.trim());
      });
      
      const improvementItems = document.querySelectorAll('#reports-content .improvement-item');
      improvementItems.forEach(item => {
        analytics.summary.improvements.push(item.textContent.trim());
      });
      
      const recommendationItems = document.querySelectorAll('#reports-content .recommendation-item');
      recommendationItems.forEach(item => {
        analytics.summary.recommendations.push(item.textContent.trim());
      });
      
      return analytics;
    }



    // Utility functions
    function viewVisitorDetails(visitorId) {
      // Open visitor details modal or redirect to details page
      console.log('Viewing visitor details:', visitorId);
    }

    async function printVisitorPass(visitorId) {
      const row = document.querySelector(`tr[data-id="${visitorId}"]`);
      if (!row) {
        showNotification('Unable to load visitor info for printing.', 'error');
        return;
      }

      const modal = document.getElementById('printPassModal');
      if (!modal) return;

      // Show loading state
      modal.classList.add('modal-open');
      document.body.classList.add('modal-open');

      try {
        // Fetch full visitor details
        const response = await fetch(`{{ url('/visitor') }}/${visitorId}/details`, {
          headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) throw new Error('Failed to load visitor details');
        const visitor = await response.json();

        // Populate modal fields
        const photoEl = document.getElementById('printPassPhoto');
        const nameEl = document.getElementById('printPassName');
        const emailEl = document.getElementById('printPassEmail');
        const phoneEl = document.getElementById('printPassPhone');
        const inviteDateEl = document.getElementById('printPassInviteDate');
        const expiresAtEl = document.getElementById('printPassExpiresAt');
        const passCodeEl = document.getElementById('printPassCode');
        const qrEl = document.getElementById('printPassQR');
        const statusEl = document.getElementById('printPassStatus');

        // Photo
        if (photoEl) {
          if (visitor.profile_photo_url) {
            photoEl.src = visitor.profile_photo_url;
            photoEl.style.display = 'block';
            const fallback = photoEl.nextElementSibling;
            if (fallback) fallback.style.display = 'none';
          } else {
            photoEl.src = '';
            photoEl.style.display = 'none';
            const fallback = photoEl.nextElementSibling;
            if (fallback) fallback.style.display = 'flex';
          }
        }

        // Name
        if (nameEl) nameEl.textContent = visitor.name || 'Visitor';

        // Email
        if (emailEl) emailEl.textContent = visitor.email || '—';

        // Phone
        if (phoneEl) phoneEl.textContent = visitor.contact || visitor.phone || '—';

        // Invite Date (check-in time or registered_at)
        if (inviteDateEl) {
          const inviteDate = visitor.time_in || visitor.registered_at;
          inviteDateEl.textContent = inviteDate ? formatDateTime(inviteDate) : '—';
        }

        // Expires At
        if (expiresAtEl) {
          const expiresAt = visitor.pass_valid_until || visitor.expected_time_out;
          if (expiresAt) {
            expiresAtEl.textContent = formatDateTime(expiresAt);
          } else {
            expiresAtEl.textContent = '—';
          }
        }

        // Pass Code (access_code)
        if (passCodeEl) {
          passCodeEl.textContent = visitor.access_code || '—';
        }

        // QR Code
        if (qrEl) {
          if (visitor.qr_code) {
            qrEl.src = visitor.qr_code;
            qrEl.style.display = 'block';
          } else {
            qrEl.style.display = 'none';
          }
        }

        // Status Badge
        if (statusEl) {
          const now = new Date();
          const expires = visitor.pass_valid_until ? new Date(visitor.pass_valid_until) : null;
          const isExpired = expires && expires < now;
          const isActive = visitor.status === 'active' && !visitor.time_out;

          if (isExpired) {
            statusEl.textContent = 'EXPIRED';
            statusEl.className = 'inline-block px-4 py-1 rounded-full text-xs font-semibold bg-red-500 text-white';
          } else if (isActive) {
            statusEl.textContent = 'ACTIVE';
            statusEl.className = 'inline-block px-4 py-1 rounded-full text-xs font-semibold bg-green-500 text-white';
          } else {
            statusEl.textContent = 'PENDING';
            statusEl.className = 'inline-block px-4 py-1 rounded-full text-xs font-semibold bg-yellow-500 text-white';
          }
        }

        // Additional Details
        const purposeEl = document.getElementById('printPassPurpose');
        const hostEl = document.getElementById('printPassHost');
        const deptEl = document.getElementById('printPassDepartment');
        const idTypeEl = document.getElementById('printPassIdType');
        const passIdEl = document.getElementById('printPassPassId');
        const registeredEl = document.getElementById('printPassRegisteredAt');

        if (purposeEl) purposeEl.textContent = visitor.purpose || '—';
        if (hostEl) hostEl.textContent = visitor.host_employee || visitor.host || '—';
        if (deptEl) deptEl.textContent = visitor.department || (visitor.facility?.name || '—');
        if (idTypeEl) {
          const idType = visitor.id_type || '';
          idTypeEl.textContent = idType ? idType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : '—';
        }
        if (passIdEl) passIdEl.textContent = visitor.pass_id || '—';
        if (registeredEl) {
          registeredEl.textContent = visitor.registered_at ? formatDateTime(visitor.registered_at) : '—';
        }

        // Reset more details section
        const moreDetailsContent = document.getElementById('moreDetailsContent');
        if (moreDetailsContent) {
          moreDetailsContent.classList.add('hidden');
        }
        const chevron = document.getElementById('moreDetailsChevron');
        if (chevron) {
          chevron.style.transform = 'rotate(0deg)';
        }

        // Re-initialize Lucide icons
        if (window.lucide && window.lucide.createIcons) {
          window.lucide.createIcons();
        }
      } catch (error) {
        console.error('Error loading visitor details:', error);
        showNotification('Failed to load visitor details for printing.', 'error');
        closePrintPassModal();
      }
    }

    function closePrintPassModal() {
      const modal = document.getElementById('printPassModal');
      if (modal) {
        modal.classList.remove('modal-open');
      }
      document.body.classList.remove('modal-open');
    }

    function handlePrintPass() {
      window.print();
    }

    function toggleMoreDetails() {
      const content = document.getElementById('moreDetailsContent');
      const chevron = document.getElementById('moreDetailsChevron');
      
      if (content && chevron) {
        const isHidden = content.classList.contains('hidden');
        if (isHidden) {
          content.classList.remove('hidden');
          chevron.style.transform = 'rotate(180deg)';
        } else {
          content.classList.add('hidden');
          chevron.style.transform = 'rotate(0deg)';
        }
        
        // Re-initialize Lucide icons
        if (window.lucide && window.lucide.createIcons) {
          window.lucide.createIcons();
        }
      }
    }

    // ---------- Checkout Rating ----------
    let currentRatingVisitorId = null;
    let currentRatingValue = 0;

    function openCheckoutRatingModal(visitorId) {
      currentRatingVisitorId = visitorId;
      currentRatingValue = 0;

      const row = document.querySelector(`tr[data-id="${visitorId}"]`);
      if (!row) return;

      const name = row.dataset.name || 'Visitor';
      const phone = row.dataset.phone || '';
      const avatar = row.dataset.avatar || '';

      document.getElementById('ratingName').textContent = name;
      document.getElementById('ratingPhone').textContent = phone;
      const photoEl = document.getElementById('ratingPhoto');
      if (photoEl) {
        photoEl.src = avatar || '';
      }

      buildRatingStars();
      updateRatingLabel();

      const modal = document.getElementById('checkoutRatingModal');
      modal.classList.add('modal-open');
      document.body.classList.add('modal-open');
    }

    function closeCheckoutRatingModal() {
      const modal = document.getElementById('checkoutRatingModal');
      if (modal) modal.classList.remove('modal-open');
      document.body.classList.remove('modal-open');
      currentRatingVisitorId = null;
      currentRatingValue = 0;
    }

    function buildRatingStars() {
      const container = document.getElementById('ratingStars');
      if (!container) return;
      container.innerHTML = '';
      for (let i = 1; i <= 5; i++) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'text-4xl px-1 transition-transform';
        btn.innerHTML = '★';
        btn.style.color = i <= currentRatingValue ? '#f97316' : '#d1d5db';
        btn.addEventListener('mouseenter', () => highlightStars(i));
        btn.addEventListener('mouseleave', () => highlightStars(currentRatingValue));
        btn.addEventListener('click', () => {
          currentRatingValue = i;
          highlightStars(currentRatingValue);
          updateRatingLabel();
        });
        container.appendChild(btn);
      }
    }

    function highlightStars(value) {
      const container = document.getElementById('ratingStars');
      if (!container) return;
      [...container.children].forEach((child, index) => {
        child.style.color = (index + 1) <= value ? '#f97316' : '#d1d5db';
      });
    }

    function updateRatingLabel() {
      const label = document.getElementById('ratingLabel');
      if (!label) return;
      label.textContent = `You have rated :: ${currentRatingValue}/5`;
    }

    function submitCheckoutRating() {
      if (!currentRatingVisitorId) return;
      const comment = document.getElementById('ratingComment').value || '';

      fetch(`{{ url('/visitor') }}/${currentRatingVisitorId}/rate`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          rating: currentRatingValue || null,
          comment: comment || null
        })
      })
        .then(res => res.json())
        .then(data => {
          if (!data.success) throw new Error(data.message || 'Unable to save rating');
          closeCheckoutRatingModal();
          showNotification('Feedback saved successfully.', 'success');
          // Reload logs to reflect checkout/comment
          loadLogsData();
        })
        .catch(err => {
          console.error(err);
          showNotification('Error saving feedback. Please try again.', 'error');
        });
    }

    function goToVisitorRequest(visitorId) {
      console.log('Navigating to visitor request:', visitorId);
      showNotification('Opening visitor request...', 'info');
    }

    function exportVisitorLog(visitorId) {
      // Export individual visitor log
      console.log('Exporting visitor log:', visitorId);
    }

    // Recent Reports Management
    function addToRecentReports(reportType, startDate, format) {
      const recentReportsContainer = document.getElementById('recent-reports');
      
      // Remove the "no reports" message if it exists
      const noReportsMsg = recentReportsContainer.querySelector('.text-center');
      if (noReportsMsg) {
        noReportsMsg.remove();
      }
      
      // Create new report entry
      const reportEntry = document.createElement('div');
      reportEntry.className = 'flex items-center justify-between p-3 bg-white rounded-lg';
      reportEntry.innerHTML = `
        <div>
          <p class="font-medium text-gray-900">${formatReportTitle(reportType, startDate)}</p>
          <p class="text-sm text-gray-500">Generated just now</p>
        </div>
        <div class="flex gap-2">
          <button class="btn btn-sm btn-outline" title="Download" onclick="downloadReport('${reportType}', '${startDate}', '${format}')">
            <i data-lucide="download" class="w-4 h-4"></i>
          </button>
          <button class="btn btn-sm btn-ghost text-red-600" title="Delete" onclick="deleteReport(this)">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
          </button>
        </div>
      `;
      
      // Add to the top of the list
      recentReportsContainer.insertBefore(reportEntry, recentReportsContainer.firstChild);
      
      // Limit to 5 recent reports
      const reports = recentReportsContainer.querySelectorAll('.flex.items-center.justify-between');
      if (reports.length > 5) {
        reports[reports.length - 1].remove();
      }
    }

    function formatReportTitle(reportType, startDate) {
      const date = new Date(startDate);
      const formattedDate = date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
      });
      
      switch(reportType) {
        case 'daily':
          return `Daily Summary - ${formattedDate}`;
        case 'weekly':
          return `Weekly Report - ${formattedDate}`;
        case 'monthly':
          return `Monthly Report - ${formattedDate}`;
        case 'custom':
          return `Custom Report - ${formattedDate}`;
        default:
          return `Report - ${formattedDate}`;
      }
    }

    function downloadReport(reportType, startDate, format) {
      // This would trigger a re-download of the report
      showNotification('Report download initiated', 'info');
    }

    function deleteReport(button) {
      if (confirm('Are you sure you want to delete this report?')) {
        button.closest('.flex.items-center.justify-between').remove();
        
        // Check if no reports left
        const recentReportsContainer = document.getElementById('recent-reports');
        const reports = recentReportsContainer.querySelectorAll('.flex.items-center.justify-between');
        
        if (reports.length === 0) {
          recentReportsContainer.innerHTML = `
            <div class="text-center py-8 text-gray-500">
              <i data-lucide="file-text" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
              <p>No recent reports generated yet</p>
              <p class="text-sm">Generate your first report using the form on the left</p>
            </div>
          `;
        }
        
        showNotification('Report deleted', 'success');
      }
    }

    // Auto-set date ranges based on report type
    document.querySelector('select[name="report_type"]').addEventListener('change', function() {
      const reportType = this.value;
      const startDateInput = document.querySelector('input[name="start_date"]');
      const endDateInput = document.querySelector('input[name="end_date"]');
      const today = new Date();
      
      switch(reportType) {
        case 'daily':
          startDateInput.value = today.toISOString().split('T')[0];
          endDateInput.value = today.toISOString().split('T')[0];
          break;
        case 'weekly':
          const weekAgo = new Date(today);
          weekAgo.setDate(today.getDate() - 7);
          startDateInput.value = weekAgo.toISOString().split('T')[0];
          endDateInput.value = today.toISOString().split('T')[0];
          break;
        case 'monthly':
          const monthAgo = new Date(today);
          monthAgo.setMonth(today.getMonth() - 1);
          startDateInput.value = monthAgo.toISOString().split('T')[0];
          endDateInput.value = today.toISOString().split('T')[0];
          break;
        case 'custom':
          // Don't auto-set for custom
          break;
      }
    });

    // Form submissions
    document.getElementById('report-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const form = e.currentTarget;
      const reportType = form.querySelector('[name="report_type"]').value;
      const startDate = form.querySelector('[name="start_date"]').value;
      const endDate = form.querySelector('[name="end_date"]').value;
      const format = form.querySelector('[name="format"]').value;
      
      if (!reportType || !startDate || !endDate || !format) {
        showNotification('Please fill in all required fields', 'error');
        return;
      }
      if (new Date(startDate) > new Date(endDate)) {
        showNotification('Start date cannot be after end date', 'error');
        return;
      }

      // Create a real POST submit to open/download directly (works for custom too)
      const tmp = document.createElement('form');
      tmp.method = 'POST';
      tmp.action = '{{ route('visitor.logs.generate-report') }}';
      tmp.target = '_blank';
      tmp.style.display = 'none';
      const add = (n,v)=>{ const i=document.createElement('input'); i.type='hidden'; i.name=n; i.value=v; tmp.appendChild(i); };
      add('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
      add('report_type', reportType);
      add('start_date', startDate);
      add('end_date', endDate);
      add('format', format);
      document.body.appendChild(tmp);
      tmp.submit();
      document.body.removeChild(tmp);

      // Optional: toast + recent list entry
      showNotification('Report generation started...', 'success');
      addToRecentReports(reportType, startDate, format);
    });





    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.className = `alert alert-${type === 'error' ? 'error' : type === 'success' ? 'success' : 'info'} fixed bottom-4 right-4 z-50 max-w-sm`;
      notification.innerHTML = `
        <i data-lucide="${type === 'error' ? 'alert-circle' : type === 'success' ? 'check-circle' : 'info'}" class="w-5 h-5"></i>
        <span>${message}</span>
      `;
      
      document.body.appendChild(notification);
      
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
      
      setTimeout(() => {
        notification.remove();
      }, 3000);
    }

    // Live duration calculation for visitors still in building
    function updateLiveDurations() {
      const liveDurationElements = document.querySelectorAll('.live-duration');
      
      liveDurationElements.forEach(element => {
        const checkInTime = element.getAttribute('data-checkin');
        if (checkInTime) {
          const checkIn = new Date(checkInTime);
          const now = new Date();
          const diffMs = now - checkIn;
          
          if (diffMs > 0) {
            const totalMinutes = Math.floor(diffMs / (1000 * 60));
            
            // Calculate days, hours, minutes
            const days = Math.floor(totalMinutes / (24 * 60));
            const hours = Math.floor((totalMinutes % (24 * 60)) / 60);
            const mins = totalMinutes % 60;
            
            // Build compact display
            const parts = [];
            if (days > 0) parts.push(`${days}d`);
            if (hours > 0) parts.push(`${hours}h`);
            if (mins > 0) parts.push(`${mins}m`);
            
            const displayText = parts.length > 0 ? parts.join(' ') : '0m';
            
            // Build verbose tooltip
            const tooltipParts = [];
            if (days > 0) tooltipParts.push(`${days} day${days > 1 ? 's' : ''}`);
            if (hours > 0) tooltipParts.push(`${hours} hour${hours > 1 ? 's' : ''}`);
            if (mins > 0) tooltipParts.push(`${mins} minute${mins > 1 ? 's' : ''}`);
            
            const tooltipText = tooltipParts.length > 0 ? tooltipParts.join(', ') : '0 minutes';
            
            // Determine color class based on duration
            let colorClass = 'badge-primary';
            if (totalMinutes < 8 * 60) {
              colorClass = 'badge-success'; // < 8h
            } else if (totalMinutes < 72 * 60) {
              colorClass = 'badge-warning'; // 8h-72h
            } else {
              colorClass = 'badge-error'; // > 72h
            }
            
            // Update element with new data
            element.setAttribute('data-duration-minutes', totalMinutes);
            element.setAttribute('data-tooltip', tooltipText);
            element.setAttribute('title', tooltipText);
            element.setAttribute('aria-label', tooltipText);
            
            // Determine pill class based on duration
            let pillClass = 'duration-pill--short'; // < 8h: green
            if (totalMinutes >= 480 && totalMinutes < 4320) { // 8h-72h: amber
              pillClass = 'duration-pill--medium';
            } else if (totalMinutes >= 4320) { // > 72h: red
              pillClass = 'duration-pill--long';
            }
            
            element.innerHTML = `<span class="duration-pill ${pillClass}">${displayText}</span>`;
          } else {
            element.innerHTML = '<span class="duration-pill duration-pill--error">Just arrived</span>';
          }
        }
      });
    }

    // Set up event listeners
    function setupEventListeners() {
      // Time range buttons
      document.querySelectorAll('.time-range-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          const range = this.dataset.range;
          setTimeRange(range);
        });
      });
      
      // Custom range apply button
      const applyCustomBtn = document.getElementById('apply-custom-range');
      if (applyCustomBtn) {
        applyCustomBtn.addEventListener('click', applyCustomRange);
      }
      // Date inputs are now hidden but still used for filtering via date range selector
      // No need for change listeners since they're set programmatically
      
      // Tab switching - load analytics when analytics tab is clicked
      const analyticsTabBtn = document.querySelector('[data-tab="analytics"]');
      if (analyticsTabBtn) {
        analyticsTabBtn.addEventListener('click', function() {
          // Small delay to ensure tab is visible before loading charts
          setTimeout(() => {
            loadAnalyticsData();
          }, 50);
        });
      }
    }

    // Check for reduced motion preference
    function shouldRespectReducedMotion() {
      return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
      // Set up event listeners first
      setupEventListeners();
      attachRowClickHandlers();
      applyColumnVisibility();
      
      // Initialize pagination state from server-side data
      @if(isset($visitors))
        currentPage = {{ $visitors->currentPage() }};
        perPage = {{ $visitors->perPage() }};
        totalItems = {{ $visitors->total() }};
        updatePaginationControls();
      @endif
      
      // Load initial data with a small delay to ensure DOM is ready
      setTimeout(() => {
        loadLogsData();
        
        // Always load analytics data on page load (regardless of active tab)
        loadAnalyticsData();
      }, 100);
      
      // Also load analytics data after a longer delay to ensure it's loaded
      setTimeout(() => {
        loadAnalyticsData();
      }, 1000);
      
      // Initialize all Lucide icons
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
      
      // Re-initialize icons after a short delay to ensure all dynamic elements are rendered
      setTimeout(() => {
        if (window.lucide && window.lucide.createIcons) {
          window.lucide.createIcons();
        }
      }, 500);

      // Start live duration updates (respect reduced motion)
      updateLiveDurations();
      
      if (!shouldRespectReducedMotion()) {
        // Removed auto updates; manual refresh only
      } else {
        // For users with reduced motion preference, update less frequently
        // Removed auto updates; manual refresh only
      }
      
      // Add accessibility attributes to duration elements
      const durationElements = document.querySelectorAll('.duration-display, .live-duration');
      durationElements.forEach((element, index) => {
        const tooltipId = `duration-tooltip-${index}`;
        element.setAttribute('aria-describedby', tooltipId);
        element.setAttribute('role', 'text');
        element.setAttribute('aria-label', element.getAttribute('data-tooltip') || 'Duration information');
      });
      
      // Add accessibility attributes to checkout time elements
      const checkoutTimeElements = document.querySelectorAll('.checkout-time-display');
      checkoutTimeElements.forEach((element, index) => {
        const tooltipId = `checkout-tooltip-${index}`;
        element.setAttribute('aria-describedby', tooltipId);
        element.setAttribute('role', 'text');
        element.setAttribute('aria-label', element.getAttribute('title') || 'Check out time information');
      });
    });
  </script>
</body>
</html>
