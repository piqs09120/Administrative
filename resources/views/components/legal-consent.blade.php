@props(['open' => false])

<style>
  /* Modal Backdrop */
  #legalConsentModal::backdrop {
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    animation: fadeIn 0.2s ease-out;
  }

  /* Modal Animation */
  #legalConsentModal {
    animation: modalEnter 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    border: none;
    background: transparent;
    padding: 0;
    margin: 0;
  }

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  @keyframes modalEnter {
    from {
      opacity: 0;
      transform: scale(0.95) translateY(-10px);
    }
    to {
      opacity: 1;
      transform: scale(1) translateY(0);
    }
  }

  /* Scrollbar Styling */
  .policy-scroll-area::-webkit-scrollbar {
    width: 10px;
  }

  .policy-scroll-area::-webkit-scrollbar-track {
    background: #f1f5f9;
  }

  .policy-scroll-area::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 5px;
  }

  .policy-scroll-area::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }

  /* Policy Content Typography */
  .policy-content {
    font-size: 0.9375rem;
    line-height: 1.8;
    color: #374151;
  }

  .policy-content h4 {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1f2937;
    margin-top: 1.75rem;
    margin-bottom: 0.875rem;
  }

  .policy-content h4:first-of-type {
    margin-top: 0;
  }

  .policy-content p {
    margin-bottom: 1rem;
    color: #4b5563;
  }

  .policy-content ul {
    list-style: none;
    padding-left: 0;
    margin: 1rem 0;
  }

  .policy-content ul li {
    padding-left: 1.75rem;
    position: relative;
    margin-bottom: 0.75rem;
    line-height: 1.7;
  }

  .policy-content ul li::before {
    content: '▸';
    position: absolute;
    left: 0;
    color: #3b82f6;
    font-weight: bold;
  }

  /* Consent Footer Text */
  .consent-text {
    font-size: 0.9375rem;
    line-height: 1.75;
    color: #78350f;
    font-weight: 500;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }

  .consent-text strong {
    color: #92400e;
    font-weight: 700;
  }

  .consent-text a {
    color: #1e40af;
    text-decoration: underline;
    font-weight: 600;
    transition: color 0.2s ease;
  }

  .consent-text a:hover {
    color: #3b82f6;
  }

  /* Buttons */
  .btn-agree-continue {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    color: white;
    font-weight: 700;
    padding: 0.875rem 2.25rem;
    border-radius: 0.5rem;
    border: none;
    transition: all 0.2s ease;
    box-shadow: 0 4px 6px rgba(217, 119, 6, 0.3);
    font-size: 0.9375rem;
    letter-spacing: 0.025em;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    white-space: nowrap;
  }

  .btn-agree-continue:hover:not(:disabled) {
    background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
    box-shadow: 0 6px 12px rgba(217, 119, 6, 0.4);
    transform: translateY(-1px);
  }

  .btn-agree-continue:active:not(:disabled) {
    transform: translateY(0);
  }

  .btn-agree-continue:disabled {
    background: #d1d5db;
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
    opacity: 0.6;
  }

  .btn-agree-continue:focus {
    outline: 2px solid #f59e0b;
    outline-offset: 2px;
  }

  .btn-cancel-ghost {
    background: transparent;
    color: #6b7280;
    font-weight: 600;
    padding: 0.875rem 1.75rem;
    border-radius: 0.5rem;
    border: 1.5px solid #d1d5db;
    transition: all 0.2s ease;
    cursor: pointer;
    white-space: nowrap;
  }

  .btn-cancel-ghost:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    color: #374151;
  }

  .btn-cancel-ghost:focus {
    outline: 2px solid #6b7280;
    outline-offset: 2px;
  }

  /* Prevent body scroll */
  body.modal-open {
    overflow: hidden;
  }
</style>

<div id="legalConsentContainer" style="display: none;">
  <dialog 
    id="legalConsentModal" 
    class="modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-content-title"
    style="max-width: 80rem; width: 95vw; max-height: 86vh; padding: 0; margin: auto; background: transparent; border: none;"
  >
    <!-- Modal Container -->
    <div class="relative max-w-5xl w-full max-h-[86vh] flex flex-col overflow-hidden rounded-2xl shadow-2xl bg-white">
      <!-- Blue Header -->
      <div class="bg-gradient-to-b from-blue-900 via-blue-800 to-blue-700 text-white p-6 lg:p-8 relative flex-shrink-0">
        <div class="flex items-center justify-between">
          <div>
            <h1 id="modal-title" class="text-xl lg:text-2xl xl:text-3xl font-extrabold leading-tight uppercase tracking-wide">
              Full Privacy Policy And Terms Of Use
            </h1>
            <p class="mt-2 text-white/80 text-sm font-medium">Soliera Hotel • Philippines</p>
          </div>
          @if(empty($mustAcceptPolicies) || !$mustAcceptPolicies)
            <button 
              onclick="closeLegalConsentModal()" 
              class="btn btn-sm btn-circle bg-white/20 hover:bg-white/30 border-0 text-white"
              aria-label="Close modal"
            >
              <i data-lucide="x" class="w-5 h-5"></i>
            </button>
          @endif
        </div>
      </div>

      <!-- Content Panel -->
      <div class="bg-white grid grid-rows-[auto_1fr_auto] flex-1 min-h-0 relative">
        <!-- Content Header -->
        <div class="p-6 lg:p-8 pb-4 border-b border-gray-200 flex-shrink-0">
          <h2 id="modal-content-title" class="text-xl lg:text-2xl font-semibold text-slate-800 mb-2">Data Privacy Consent</h2>
          <p class="text-sm text-gray-600">Please read and accept the following policies to continue accessing the Legal Management module.</p>
        </div>

        <!-- Scrollable Body -->
        <div class="policy-scroll-area overflow-y-auto p-6 lg:p-8 pt-4 flex-1 min-h-0" id="policiesContainer">
          <div class="flex flex-col items-center justify-center min-h-[300px]">
            <div class="loading loading-spinner loading-lg text-blue-600"></div>
            <p class="text-gray-500 mt-4 font-medium">Loading policies...</p>
          </div>
        </div>

        <!-- Sticky Footer -->
        <div class="sticky bottom-0 bg-amber-50/90 backdrop-blur border-t border-amber-200 p-4 lg:p-6 shadow-sm">
          <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <!-- Consent Checkbox and Text -->
            <label class="flex items-start gap-3 flex-1 cursor-pointer">
              <input 
                type="checkbox" 
                id="consentCheckbox"
                class="checkbox checkbox-warning checkbox-lg mt-1 flex-shrink-0" 
                onchange="toggleConsentCheckbox(this.checked)"
                aria-label="I accept the privacy policy and terms"
              >
              <div class="consent-text">
                <span class="font-semibold text-amber-900">I accept this policy.</span> By selecting <strong>Agree & Continue</strong>, you confirm the information you provide is true and you consent to the collection, use, and storage of your personal data by <strong>Soliera Hotel</strong>, for the purposes described above and in our <a href="#" onclick="event.preventDefault(); scrollToTop();">Privacy Policy</a> and <a href="#" onclick="event.preventDefault(); scrollToTop();">Guest Terms</a>.
              </div>
            </label>

            <!-- Action Buttons -->
            <div class="flex gap-3 flex-shrink-0 md:flex-row flex-col">
              @if(!empty($mustAcceptPolicies) && $mustAcceptPolicies)
                {{-- Don't show cancel if required --}}
              @else
                <button 
                  onclick="closeLegalConsentModal()" 
                  class="btn-cancel-ghost w-full md:w-auto"
                  aria-label="Cancel and close modal"
                >
                  Cancel
                </button>
              @endif
              <button 
                id="continueBtn" 
                onclick="saveConsents()" 
                class="btn-agree-continue w-full md:w-auto justify-center" 
                disabled
                aria-label="Agree and continue"
              >
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>Agree & Continue</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </dialog>
</div>

<script>
  let legalConsentState = {
    policies: [],
    consentChecked: false
  };

  function initLegalConsent(forceOpen = false) {
    // Double check if we're on a valid legal route
    const currentPath = window.location.pathname;
    const excludedRoutes = ['/dashboard', '/legal/enhanced-dashboard', '/legal/cases'];
    const isExcluded = excludedRoutes.some(route => currentPath === route || currentPath.startsWith(route + '/'));
    
    if (isExcluded) {
      const container = document.getElementById('legalConsentContainer');
      if (container) {
        container.style.display = 'none';
      }
      return;
    }
    
    fetch('/policies/latest', {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(r => r.json())
    .then(data => {
      legalConsentState.policies = data.policies || [];
      renderPolicies();
      updateContinueButton();
      
      const mustAccept = @json($mustAcceptPolicies ?? false);
      if (mustAccept) {
        // Only open if policies are required and we're on a valid route
        setTimeout(() => {
          const container = document.getElementById('legalConsentContainer');
          if (container && container.style.display !== 'none') {
            openLegalConsentModal();
          }
        }, 300);
      } else if (forceOpen) {
        setTimeout(() => {
          const container = document.getElementById('legalConsentContainer');
          if (container && container.style.display !== 'none') {
            openLegalConsentModal();
          }
        }, 300);
      }
    })
    .catch(error => {
      console.error('Error loading policies:', error);
      document.getElementById('policiesContainer').innerHTML = 
        '<div class="flex flex-col items-center justify-center min-h-[300px]"><i data-lucide="alert-circle" class="w-16 h-16 text-red-500 mb-4"></i><p class="font-medium text-red-600">Error loading policies. Please refresh the page.</p></div>';
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
    });
  }

  function renderPolicies() {
    const container = document.getElementById('policiesContainer');
    if (legalConsentState.policies.length === 0) {
      container.innerHTML = '<div class="flex flex-col items-center justify-center min-h-[300px]"><i data-lucide="file-x" class="w-16 h-16 text-gray-400 mb-4"></i><p class="font-medium text-gray-500">No active policies found.</p></div>';
      if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
      }
      return;
    }

    const lastUpdated = new Date().toLocaleDateString('en-US', { 
      month: 'long', 
      day: 'numeric', 
      year: 'numeric' 
    });

    container.innerHTML = `
      <div class="policy-content prose prose-sm md:prose max-w-none">
        ${legalConsentState.policies.map((p, index) => `
          <div class="mb-6 pb-6 ${index < legalConsentState.policies.length - 1 ? 'border-b border-gray-200' : ''}">
            <h4>${index + 1}. ${escapeHtml(p.title)}</h4>
            <div class="mt-3">
              ${p.content}
            </div>
          </div>
        `).join('')}
        
        <div class="mt-6 pt-4 border-t-2 border-gray-300">
          <p class="text-xs text-gray-500 font-medium">Last Updated: ${lastUpdated}</p>
        </div>
      </div>
    `;
    
    if (window.lucide && window.lucide.createIcons) {
      window.lucide.createIcons();
    }
  }

  function toggleConsentCheckbox(checked) {
    legalConsentState.consentChecked = checked;
    updateContinueButton();
  }

  function scrollToTop() {
    const container = document.getElementById('policiesContainer');
    if (container) {
      container.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }

  function updateContinueButton() {
    const btn = document.getElementById('continueBtn');
    if (btn) {
      btn.disabled = !legalConsentState.consentChecked;
    }
  }


  function openLegalConsentModal() {
    // Double check we're on a valid route before opening
    const currentPath = window.location.pathname;
    const excludedRoutes = ['/dashboard', '/legal/enhanced-dashboard', '/legal/cases'];
    const isExcluded = excludedRoutes.some(route => currentPath === route || currentPath.startsWith(route + '/'));
    
    if (isExcluded) {
      return; // Don't open on excluded routes
    }
    
    const modal = document.getElementById('legalConsentModal');
    const container = document.getElementById('legalConsentContainer');
    
    if (!modal || !container) return;
    
    // Make sure container is visible
    if (container.style.display === 'none') {
      return; // Don't open if container is hidden
    }
    
    // Prevent backdrop close if policies are required
    const mustAccept = @json($mustAcceptPolicies ?? false);
    
    modal.showModal();
    document.body.classList.add('modal-open');
    
    // Prevent closing if required
    if (mustAccept) {
      modal.addEventListener('cancel', function preventClose(e) {
        e.preventDefault();
      }, { once: false });
    }
    
    const firstFocusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (firstFocusable) {
      setTimeout(() => firstFocusable.focus(), 100);
    }
  }

  function closeLegalConsentModal() {
    const mustAccept = @json($mustAcceptPolicies ?? false);
    if (mustAccept && !legalConsentState.consentChecked) {
      return;
    }
    
    const modal = document.getElementById('legalConsentModal');
    if (modal) {
      modal.close();
      document.body.classList.remove('modal-open');
    }
  }

  function saveConsents() {
    if (!legalConsentState.consentChecked) return;

    const payload = {
      policies: legalConsentState.policies.map(p => ({ id: p.id, version: p.version }))
    };

    const btn = document.getElementById('continueBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> <span>Saving...</span>';

    fetch('/policies/consent', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
      if (data.ok) {
        const mustAccept = @json($mustAcceptPolicies ?? false);
        if (mustAccept) {
          window.location.reload();
        } else {
          closeLegalConsentModal();
          if (window.showToast) {
            showToast('Policies accepted successfully!', 'success');
          }
        }
      } else {
        throw new Error(data.message || 'Failed to save consent');
      }
    })
    .catch(error => {
      console.error('Error saving consent:', error);
      btn.disabled = false;
      btn.innerHTML = originalText;
      if (window.showToast) {
        showToast('Error saving consent: ' + error.message, 'error');
      } else {
        alert('Error saving consent: ' + error.message);
      }
    });
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // Keyboard handlers
  document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('legalConsentModal');
    if (!modal || !modal.open) return;

    // ESC to close
    if (e.key === 'Escape') {
      const mustAccept = @json($mustAcceptPolicies ?? false);
      if (!mustAccept) {
        closeLegalConsentModal();
      }
    }

    // Enter/Return on focused Agree button submits
    if ((e.key === 'Enter' || e.key === 'Return') && document.activeElement?.id === 'continueBtn') {
      if (!document.getElementById('continueBtn').disabled) {
        e.preventDefault();
        saveConsents();
      }
    }

    // Focus trap
    if (e.key === 'Tab') {
      const focusableElements = modal.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
      );
      const firstElement = focusableElements[0];
      const lastElement = focusableElements[focusableElements.length - 1];

      if (e.shiftKey && document.activeElement === firstElement) {
        e.preventDefault();
        lastElement.focus();
      } else if (!e.shiftKey && document.activeElement === lastElement) {
        e.preventDefault();
        firstElement.focus();
      }
    }
  });

  // Initialize
  document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    
    // Define routes where modal should NOT appear
    const excludedRoutes = [
      '/dashboard',
      '/legal/enhanced-dashboard',
      '/legal/cases',
      '/superadmin/analytics'
    ];
    
    // Check if current path is excluded
    const isExcluded = excludedRoutes.some(route => currentPath === route || currentPath.startsWith(route + '/'));
    
    // Only show on legal management routes (not dashboard, not enhanced-dashboard, not cases)
    const isLegalManagementRoute = currentPath.startsWith('/legal') && 
                                    !currentPath.startsWith('/legal/enhanced-dashboard') &&
                                    !currentPath.startsWith('/legal/cases') &&
                                    currentPath !== '/dashboard';
    
    const container = document.getElementById('legalConsentContainer');
    
    if (isLegalManagementRoute && !isExcluded) {
      // Show container only on valid legal management routes
      if (container) {
        container.style.display = 'block';
      }
      
      // Only initialize if policies are required
      const mustAccept = @json($mustAcceptPolicies ?? false);
      console.log('Legal Consent Modal Debug:', {
        currentPath: currentPath,
        isLegalManagementRoute: isLegalManagementRoute,
        isExcluded: isExcluded,
        mustAccept: mustAccept,
        containerExists: !!container
      });
      
      if (mustAccept) {
        // Small delay to prevent flash
        setTimeout(() => {
          initLegalConsent(false);
        }, 100);
      }
    } else {
      // Hide container completely on non-legal routes or excluded routes
      if (container) {
        container.style.display = 'none';
      }
    }
  });
</script>
