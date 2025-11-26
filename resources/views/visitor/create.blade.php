<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Visitors - Review & Actions</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
  <style>
    .floating-input-container {
      position: relative;
      padding-top: 0;
      padding-bottom: 8px;
      margin-top: 24px;
    }
    .floating-input {
      width: 100%;
      outline: none;
      font-size: 1.125rem;
      letter-spacing: 0.1em;
      color: #1f2937;
      background: transparent;
      border: 0;
      border-bottom: 2px solid #d1d5db;
      padding-bottom: 8px;
      padding-top: 0;
      transition: border-color 0.3s ease;
      cursor: text;
    }
    .floating-input:focus {
      border-color: #E0761C;
    }
    .floating-input[readonly] {
      cursor: pointer;
    }
    .floating-input[readonly]:focus {
      border-color: #E0761C;
    }
    .floating-label {
      position: absolute;
      left: 0;
      font-size: 1.125rem;
      color: #9ca3af;
      pointer-events: none;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      top: 0;
      line-height: 1.5;
      z-index: 1;
    }
    .floating-label.active {
      top: -24px !important;
      font-size: 0.875rem !important;
      color: #E0761C !important;
    }
    .static-input-container {
      position: relative;
      margin-bottom: 16px;
    }
    .static-label {
      display: block;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #9ca3af;
      margin-bottom: 4px;
      transition: color 0.3s ease;
    }
    .static-input {
      width: 100%;
      outline: none;
      font-size: 0.875rem;
      color: #1f2937;
      background: transparent;
      border: 0;
      border-bottom: 2px solid #d1d5db;
      padding-bottom: 4px;
      padding-top: 0;
      transition: border-color 0.3s ease;
      cursor: pointer;
    }
    .static-input:focus {
      border-color: #E0761C;
    }
    .static-input:focus ~ .static-label,
    .static-input:focus + .static-label {
      color: #E0761C;
    }
    .static-input-container:has(.static-input:focus) .static-label {
      color: #E0761C;
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
      <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
        <!-- Toast notifications in bottom right corner -->
        @if(session('success'))
          <div class="toast toast-bottom toast-end z-50">
            <div class="alert alert-success shadow-lg">
              <i data-lucide="check-circle" class="w-5 h-5"></i>
              <span>{{ session('success') }}</span>
            </div>
          </div>
        @endif

        @if(session('error'))
          <div class="toast toast-bottom toast-end z-50">
            <div class="alert alert-error shadow-lg">
              <i data-lucide="alert-circle" class="w-5 h-5"></i>
              <span>{{ session('error') }}</span>
            </div>
          </div>
        @endif

        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-3">
            <h1 class="text-2xl font-semibold text-gray-800">New Visitors (Pending Review)</h1>
          </div>
        </div>

        <div class="border-b border-gray-200 mb-6"></div>

        <x-table-card :title="'New Visitors (Pending Review)'" :pagination="method_exists($pendingVisitors, 'links') ? $pendingVisitors->links() : null">
          <table class="table table-zebra w-full text-sm">
                <thead>
                  <tr class="text-gray-600">
                    <th>Name</th>
                    <th>Email</th>
                    <th>Purpose</th>
                    <th>Department</th>
                    <th>Registered At</th>
                    <th class="text-right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($pendingVisitors as $visitor)
                    <tr class="hover:bg-gray-50 transition-all duration-200">
                      <td class="font-medium">{{ $visitor->name }}</td>
                      <td>{{ $visitor->email ?? 'N/A' }}</td>
                      <td>{{ \Illuminate\Support\Str::limit($visitor->purpose ?? 'N/A', 40) }}</td>
                      <td>{{ $visitor->department ?? ($visitor->facility->name ?? 'N/A') }}</td>
                      <td>{{ $visitor->created_at?->format('M d, Y h:i A') }}</td>
                      <td>
                        <div class="flex justify-end gap-3">
                          <button type="button" class="btn-view-visitor p-2 rounded-lg transition-all duration-200 cursor-pointer hover:scale-110" data-visitor-id="{{ $visitor->id }}" title="View" style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25);" onmouseover="this.style.background='#E6940F'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'" onmouseout="this.style.background='#F7A923'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                            <i data-lucide="eye" class="w-4 h-4" style="fill: none;"></i>
                          </button>
                          <a href="/test-id-verification?visitor_id={{ $visitor->id }}" class="p-2 rounded-lg transition-all duration-200 cursor-pointer hover:scale-110" title="ID Verification" style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25);" onmouseover="this.style.background='#E6940F'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'" onmouseout="this.style.background='#F7A923'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                            <i data-lucide="shield-check" class="w-4 h-4" style="fill: none;"></i>
                          </a>
                          <form action="{{ route('visitor.approve', $visitor->id) }}" method="POST" class="approve-form">
                            @csrf
                            <input type="hidden" name="access_code" value="">
                            <input type="hidden" name="profile_photo" value="">
                             <button 
                               type="button" 
                               class="btn-approve-visitor p-2 rounded-lg transition-all duration-200 cursor-pointer hover:scale-110" 
                               title="Approve" 
                               data-visitor-name="{{ e($visitor->name) }}"
                               data-visitor-email="{{ e($visitor->email ?? '') }}"
                               data-visitor-host="{{ e($visitor->host_employee ?? ($visitor->facility->name ?? '')) }}"
                               data-visitor-phone="{{ e($visitor->contact ?? '') }}"
                               style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25);"
                               onmouseover="this.style.background='#E6940F'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'"
                               onmouseout="this.style.background='#F7A923'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                              <i data-lucide="check" class="w-4 h-4" style="fill: none;"></i>
                            </button>
                          </form>
                          <form action="{{ route('visitor.decline', $visitor->id) }}" method="POST" onsubmit="return confirm('Decline this visitor?');">
                            @csrf
                            <button type="submit" class="p-2 rounded-lg transition-all duration-200 cursor-pointer hover:scale-110" title="Decline" style="background: #F7A923; color: #1f2937; box-shadow: 0 2px 8px rgba(247, 169, 35, 0.25);" onmouseover="this.style.background='#E6940F'; this.style.boxShadow='0 4px 12px rgba(247, 169, 35, 0.35)'" onmouseout="this.style.background='#F7A923'; this.style.boxShadow='0 2px 8px rgba(247, 169, 35, 0.25)'">
                              <i data-lucide="x" class="w-4 h-4" style="fill: none;"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center text-gray-500 py-6">No pending visitors.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
        </x-table-card>
      </main>
    </div>
  </div>

  @include('partials.soliera_js')

  <script>
    const baseDetailsUrl = "{{ url('/visitor') }}";
    let pendingApproveForm = null;
    let pendingInviteData = null;
    let pendingInviteCode = '';
    let activeCameraStream = null;
    let pendingPhotoData = '';

    function setupDarkMode() {
      const toggle = document.getElementById('darkModeToggle');
      const sunIcon = document.getElementById('sunIcon');
      const moonIcon = document.getElementById('moonIcon');
      function updateIcons() {
        if(document.documentElement.classList.contains('dark')) {
          sunIcon && sunIcon.classList.remove('hidden');
          moonIcon && moonIcon.classList.add('hidden');
        } else {
          sunIcon && sunIcon.classList.add('hidden');
          moonIcon && moonIcon.classList.remove('hidden');
        }
      }
      const isDarkMode = localStorage.getItem('darkMode') === 'true';
      if (isDarkMode) {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
        document.body.classList.remove('dark');
      }
      updateIcons();
      if (toggle) {
        toggle.addEventListener('click', function() {
          if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            document.body.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
          } else {
            document.documentElement.classList.add('dark');
            document.body.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
          }
          updateIcons();
        });
      }
    }

    function updateDateTime() {
      const now = new Date();
      const dateElement = document.getElementById('currentDate');
      const timeElement = document.getElementById('currentTime');
      const dateOptions = { weekday: 'short', month: 'short', day: 'numeric' };
      const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
      if (dateElement) dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
      if (timeElement) timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize floating label for pass code input
      const inviteCodeInput = document.getElementById('inviteCodeInput');
      if (inviteCodeInput) {
        setupFloatingLabel(inviteCodeInput);
      }
      setupDarkMode();
      updateDateTime();
      setInterval(updateDateTime, 1000);

      // Auto-hide toast notifications after 5 seconds
      setTimeout(() => {
        document.querySelectorAll('.toast').forEach(toast => {
          toast.style.opacity = '0';
          toast.style.transition = 'opacity 0.5s ease-out';
          setTimeout(() => toast.remove(), 500);
        });
      }, 5000);

      // Attach click handlers for view buttons
      document.querySelectorAll('.btn-view-visitor').forEach(btn => {
        btn.addEventListener('click', async function() {
          const id = this.getAttribute('data-visitor-id');
          try {
            const res = await fetch(`${baseDetailsUrl}/${id}/details`, {
              headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('Failed to load visitor');
            const visitor = await res.json();
            openVisitorPreviewModal(visitor);
          } catch (e) {
            console.error(e);
            alert('Unable to load visitor details.');
          }
        });
      });

      // Approve button flow -> open pass code modal
      document.querySelectorAll('.btn-approve-visitor').forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          const form = this.closest('form');
          if (!form) return;
          const data = {
            name: this.dataset.visitorName || '—',
            email: this.dataset.visitorEmail || '—',
            host: this.dataset.visitorHost || '—',
            phone: this.dataset.visitorPhone || '—',
          };
          openInviteCodeModal(form, data);
        });
      });
    });

    function openVisitorPreviewModal(visitor) {
      const modal = document.getElementById('visitorPreviewModal');
      if (!modal) return;
      // Fill contents
      modal.querySelector('[data-field="name"]').textContent = visitor.name ?? '-';
      modal.querySelector('[data-field="email"]').textContent = visitor.email ?? '-';
      modal.querySelector('[data-field="contact"]').textContent = visitor.contact ?? '-';
      modal.querySelector('[data-field="purpose"]').textContent = visitor.purpose ?? '-';
      modal.querySelector('[data-field="department"]').textContent = visitor.department ?? '-';
      modal.querySelector('[data-field="host"]').textContent = visitor.host_employee ?? '-';
      modal.querySelector('[data-field="id_type"]').textContent = visitor.id_type ?? '-';
      modal.querySelector('[data-field="time_in"]').textContent = visitor.time_in ? new Date(visitor.time_in).toLocaleString() : 'Not checked in';
      modal.querySelector('[data-field="time_out"]').textContent = visitor.time_out ? new Date(visitor.time_out).toLocaleString() : 'IN';
      modal.querySelector('[data-field="registered_at"]').textContent = visitor.registered_at ? new Date(visitor.registered_at).toLocaleString() : '-';
      modal.querySelector('[data-field="pass_id"]').textContent = visitor.pass_id || 'N/A';
      modal.querySelector('[data-field="access_code"]').textContent = visitor.access_code || '—';
      const qrImage = modal.querySelector('[data-field="qr_code"]');
      if (qrImage) {
        qrImage.src = visitor.qr_code || '';
        qrImage.alt = visitor.pass_id ? `QR code for ${visitor.pass_id}` : 'Visitor QR code';
        qrImage.classList.toggle('hidden', !visitor.qr_code);
      }

      // Open
      modal.classList.add('modal-open');
      document.body.classList.add('modal-open');
    }

    function closeVisitorPreviewModal() {
      const modal = document.getElementById('visitorPreviewModal');
      if (!modal) return;
      modal.classList.remove('modal-open');
      document.body.classList.remove('modal-open');
    }

    function openInviteCodeModal(form, data) {
      pendingApproveForm = form;
      pendingInviteData = data;
      pendingInviteCode = '';
      const modal = document.getElementById('inviteCodeModal');
      const input = document.getElementById('inviteCodeInput');
      if (!modal || !input) return;
      input.value = '';
      input.placeholder = ' ';
      populateInviteDetails(data);
      showInviteStep('code');
      modal.classList.add('modal-open');
      document.body.classList.add('modal-open');
      
      // Reset floating label state
      const label = input.nextElementSibling;
      if (label && label.classList.contains('floating-label')) {
        label.classList.remove('active');
      }
      
      // Setup floating label handlers after a short delay to ensure DOM is ready
      setTimeout(() => {
        const currentInput = document.getElementById('inviteCodeInput');
        if (currentInput) {
          setupFloatingLabel(currentInput);
          setTimeout(() => currentInput.focus(), 50);
        }
      }, 50);
    }
    
    function setupFloatingLabel(input) {
      if (!input) return;
      const label = input.nextElementSibling;
      if (!label || !label.classList.contains('floating-label')) {
        return;
      }
      
      // Remove existing flag if any
      if (input.dataset.floatingLabelInit) {
        return; // Already initialized
      }
      input.dataset.floatingLabelInit = 'true';
      
      function updateLabel() {
        const isFocused = document.activeElement === input;
        const hasValue = input.value.trim().length > 0 && input.value.trim() !== '—';
        
        // Only activate label when focused OR when it has value AND is not readonly
        // For readonly fields, only activate on focus
        if (input.readOnly) {
          // For readonly fields, only activate on focus
          if (isFocused) {
            label.classList.add('active');
            input.style.borderColor = '#E0761C';
          } else {
            label.classList.remove('active');
            input.style.borderColor = '#d1d5db';
          }
        } else {
          // For editable fields, activate on focus or if has value
          if (isFocused || hasValue) {
            label.classList.add('active');
          } else {
            label.classList.remove('active');
          }
        }
      }
      
      // Add event listeners for all fields (including readonly)
      input.addEventListener('focus', updateLabel);
      input.addEventListener('blur', updateLabel);
      if (!input.readOnly) {
        input.addEventListener('input', updateLabel);
        input.addEventListener('keyup', updateLabel);
      }
      
      // Initial state - labels should be inside (not active) unless focused
      label.classList.remove('active');
      if (!input.readOnly) {
        input.style.borderColor = '#d1d5db';
      }
    }

    function closeInviteCodeModal(reset = true) {
      const modal = document.getElementById('inviteCodeModal');
      if (modal) modal.classList.remove('modal-open');
      document.body.classList.remove('modal-open');
      
      // Reset floating label
      const input = document.getElementById('inviteCodeInput');
      const label = input?.nextElementSibling;
      if (input && label && label.classList.contains('floating-label')) {
        input.value = '';
        label.classList.remove('active');
        if (input.dataset.floatingLabelInit) {
          delete input.dataset.floatingLabelInit;
        }
      }
      
      if (reset) {
        pendingApproveForm = null;
        pendingInviteData = null;
        pendingInviteCode = '';
      }
    }

    function populateInviteDetails(data) {
      const nameField = document.getElementById('inviteNameField');
      const emailField = document.getElementById('inviteEmailField');
      const hostField = document.getElementById('inviteHostField');
      const phoneField = document.getElementById('invitePhoneField');
      
      // Set values
      if (nameField) {
        nameField.value = data?.name || '—';
        setupStaticLabel(nameField);
      }
      if (emailField) {
        emailField.value = data?.email || '—';
        setupStaticLabel(emailField);
      }
      if (hostField) {
        hostField.value = data?.host || '—';
        setupStaticLabel(hostField);
      }
      if (phoneField) {
        phoneField.value = data?.phone || '—';
        setupStaticLabel(phoneField);
      }
    }
    
    function setupStaticLabel(input) {
      if (!input) return;
      const container = input.closest('.static-input-container');
      const label = container?.querySelector('.static-label');
      if (!label) return;
      
      function updateLabel() {
        const isFocused = document.activeElement === input;
        if (isFocused) {
          label.style.color = '#E0761C';
          input.style.borderColor = '#E0761C';
        } else {
          label.style.color = '#9ca3af';
          input.style.borderColor = '#d1d5db';
        }
      }
      
      input.addEventListener('focus', updateLabel);
      input.addEventListener('blur', updateLabel);
      
      // Initial state
      updateLabel();
    }

    function showInviteStep(step) {
      document.querySelectorAll('.invite-step').forEach(section => {
        section.classList.add('hidden');
      });
      const target = document.querySelector(`.invite-step[data-step="${step}"]`);
      if (target) {
        target.classList.remove('hidden');
        // Setup static labels for details step
        if (step === 'details') {
          setTimeout(() => {
            const nameField = document.getElementById('inviteNameField');
            const emailField = document.getElementById('inviteEmailField');
            const hostField = document.getElementById('inviteHostField');
            const phoneField = document.getElementById('invitePhoneField');
            if (nameField) setupStaticLabel(nameField);
            if (emailField) setupStaticLabel(emailField);
            if (hostField) setupStaticLabel(hostField);
            if (phoneField) setupStaticLabel(phoneField);
          }, 50);
        }
      }
    }

    function goToInviteDetails() {
      if (!pendingApproveForm) return;
      const input = document.getElementById('inviteCodeInput');
      if (!input) return;
      const code = input.value.trim();
      if (!code) {
        input.focus();
        return;
      }
      pendingInviteCode = code;
      showInviteStep('details');
    }

    function submitInviteDetails() {
      if (!pendingApproveForm || !pendingInviteCode) return;
      const hiddenInput = pendingApproveForm.querySelector('input[name="access_code"]');
      if (hiddenInput) hiddenInput.value = pendingInviteCode;
      // Keep pendingInviteData/Code so we can show them in the summary
      closeInviteCodeModal(false);
      openCameraCaptureModal();
    }

    function openCameraCaptureModal() {
      const modal = document.getElementById('cameraCaptureModal');
      const video = document.getElementById('cameraVideo');
      const previewImage = document.getElementById('cameraPreviewImage');
      const liveFooter = document.getElementById('cameraFooterLive');
      const previewFooter = document.getElementById('cameraFooterPreview');
      if (!modal || !video) return;

      // Reset state: show live camera, hide preview + preview footer
      video.classList.remove('hidden');
      if (previewImage) {
        previewImage.classList.add('hidden');
        previewImage.src = '';
      }
      if (liveFooter) liveFooter.classList.remove('hidden');
      if (previewFooter) previewFooter.classList.add('hidden');

      modal.classList.remove('hidden');
      document.body.classList.add('modal-open');

      navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
          activeCameraStream = stream;
          video.srcObject = stream;
          video.play();
        })
        .catch(err => {
          console.error('Camera error:', err);
          alert('Unable to access camera. Please allow camera access or upload manually later.');
          finalizeApprovalWithoutPhoto();
        });
    }

    function closeCameraCaptureModal() {
      const modal = document.getElementById('cameraCaptureModal');
      if (modal) modal.classList.add('hidden');
      document.body.classList.remove('modal-open');
      if (activeCameraStream) {
        activeCameraStream.getTracks().forEach(track => track.stop());
        activeCameraStream = null;
      }
    }

    function capturePhoto() {
      const video = document.getElementById('cameraVideo');
      const canvas = document.getElementById('cameraCanvas');
      const previewImage = document.getElementById('cameraPreviewImage');
      const liveFooter = document.getElementById('cameraFooterLive');
      const previewFooter = document.getElementById('cameraFooterPreview');
      if (!video || !canvas) {
        finalizeApprovalWithoutPhoto();
        return;
      }
      canvas.width = video.videoWidth || 640;
      canvas.height = video.videoHeight || 480;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
      const dataUrl = canvas.toDataURL('image/jpeg', 0.9);

      const hiddenPhoto = pendingApproveForm?.querySelector('input[name="profile_photo"]');
      if (hiddenPhoto) hiddenPhoto.value = dataUrl;
      pendingPhotoData = dataUrl;

      // Show captured preview like Vizitor: freeze frame, show Retake/Next
      if (previewImage) {
        previewImage.src = dataUrl;
        previewImage.classList.remove('hidden');
      }
      video.classList.add('hidden');
      if (liveFooter) liveFooter.classList.add('hidden');
      if (previewFooter) previewFooter.classList.remove('hidden');
    }

    function retakePhoto() {
      const video = document.getElementById('cameraVideo');
      const previewImage = document.getElementById('cameraPreviewImage');
      const liveFooter = document.getElementById('cameraFooterLive');
      const previewFooter = document.getElementById('cameraFooterPreview');
      const hiddenPhoto = pendingApproveForm?.querySelector('input[name="profile_photo"]');

      if (hiddenPhoto) hiddenPhoto.value = '';
      pendingPhotoData = '';
      if (previewImage) {
        previewImage.src = '';
        previewImage.classList.add('hidden');
      }
      if (video) video.classList.remove('hidden');
      if (liveFooter) liveFooter.classList.remove('hidden');
      if (previewFooter) previewFooter.classList.add('hidden');
    }

    function finalizeApprovalWithoutPhoto() {
      const hiddenPhoto = pendingApproveForm?.querySelector('input[name="profile_photo"]');
      if (hiddenPhoto) hiddenPhoto.value = '';
      finalizeApproval();
    }

    function finalizeApproval() {
      // After capturing / skipping, show summary modal like Vizitor before final submit
      closeCameraCaptureModal();
      openInviteSummaryModal();
    }

    function openInviteSummaryModal() {
      const modal = document.getElementById('inviteSummaryModal');
      if (!modal || !pendingInviteData) {
        // Fallback: if something is wrong, just submit
        if (pendingApproveForm) pendingApproveForm.submit();
        return;
      }

      const img = document.getElementById('inviteSummaryPhoto');
      const nameEl = document.getElementById('inviteSummaryName');
      const emailEl = document.getElementById('inviteSummaryEmail');
      const phoneEl = document.getElementById('inviteSummaryPhone');
      const checkInEl = document.getElementById('inviteSummaryCheckIn');

      if (img) {
        if (pendingPhotoData) {
          img.src = pendingPhotoData;
          img.classList.remove('bg-gray-300');
        } else {
          img.src = '';
          img.classList.add('bg-gray-300');
        }
      }
      if (nameEl) nameEl.textContent = pendingInviteData.name || '—';
      if (emailEl) emailEl.textContent = pendingInviteData.email || '—';
      if (phoneEl) phoneEl.textContent = pendingInviteData.phone || '—';
      if (checkInEl) {
        const now = new Date();
        checkInEl.textContent = now.toLocaleString();
      }

      modal.classList.add('modal-open');
      document.body.classList.add('modal-open');
    }

    function closeInviteSummaryModal() {
      const modal = document.getElementById('inviteSummaryModal');
      if (modal) modal.classList.remove('modal-open');
      document.body.classList.remove('modal-open');
    }

    function skipInviteSummary() {
      closeInviteSummaryModal();
      if (!pendingApproveForm) return;
      pendingApproveForm.submit();
    }

    function nextInviteSummary() {
      closeInviteSummaryModal();
      if (!pendingApproveForm) return;
      pendingApproveForm.submit();
    }
  </script>

  <!-- Visitor Preview Modal -->
  <div id="visitorPreviewModal" class="modal" onclick="closeVisitorPreviewModal()">
    <div class="modal-box max-w-3xl" onclick="event.stopPropagation()">
      <div class="modal-header flex items-center justify-between mb-4">
        <h3 class="text-xl font-semibold"><i data-lucide="user" class="w-5 h-5 mr-2 inline"></i>Visitor Preview</h3>
        <button class="btn btn-ghost btn-sm" onclick="closeVisitorPreviewModal()"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div><span class="font-semibold">Name:</span> <span data-field="name">-</span></div>
        <div><span class="font-semibold">Email:</span> <span data-field="email">-</span></div>
        <div><span class="font-semibold">Contact:</span> <span data-field="contact">-</span></div>
        <div><span class="font-semibold">Purpose:</span> <span data-field="purpose">-</span></div>
        <div><span class="font-semibold">Department:</span> <span data-field="department">-</span></div>
        <div><span class="font-semibold">Host:</span> <span data-field="host">-</span></div>
        <div><span class="font-semibold">ID Type:</span> <span data-field="id_type">-</span></div>
        <div><span class="font-semibold">Time In:</span> <span data-field="time_in">-</span></div>
        <div><span class="font-semibold">Time Out:</span> <span data-field="time_out">-</span></div>
        <div><span class="font-semibold">Registered At:</span> <span data-field="registered_at">-</span></div>
      </div>
      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="rounded-xl border border-gray-200 p-4 bg-slate-50">
          <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Visitor Code</p>
          <p class="text-lg font-semibold tracking-widest text-gray-900" data-field="pass_id">PASS-000000</p>
          <p class="text-xs text-gray-400 mt-2">Manual Entry Code</p>
          <p class="text-base font-semibold text-gray-800" data-field="access_code">—</p>
        </div>
        <div class="flex items-center gap-4">
          <img data-field="qr_code" alt="Visitor QR code" class="w-32 h-32 rounded-xl border border-gray-200 object-cover bg-white" src="">
          <p class="text-xs text-gray-500 leading-relaxed">Scan the QR code to automatically pull up this visitor’s record. The numeric code remains for manual entry.</p>
        </div>
      </div>
      <div class="modal-footer mt-6 flex justify-end gap-2">
        <button class="btn btn-ghost" onclick="closeVisitorPreviewModal()">Close</button>
        <a class="btn btn-primary" href="#" onclick="event.preventDefault(); closeVisitorPreviewModal();">OK</a>
      </div>
    </div>
  </div>

  <!-- Pass Code Modal -->
  <div id="inviteCodeModal" class="modal" onclick="closeInviteCodeModal()">
    <div class="modal-box w-96" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-semibold text-[#E0761C]">Pass Code</h3>
        <button class="text-gray-400 hover:text-gray-600" onclick="closeInviteCodeModal()">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <div class="invite-step" data-step="code">
        <div class="floating-input-container">
          <input 
            type="text" 
            id="inviteCodeInput" 
            maxlength="6" 
            class="floating-input" 
            placeholder=" "
          />
          <label 
            for="inviteCodeInput" 
            class="floating-label"
          >
            Enter Pass Code
          </label>
        </div>
        <div class="modal-action mt-6">
          <button type="button" class="btn btn-ghost" onclick="closeInviteCodeModal()">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="goToInviteDetails()">Next</button>
        </div>
      </div>

      <div class="invite-step hidden space-y-4" data-step="details">
        <div class="static-input-container">
          <label for="inviteNameField" class="static-label">Name</label>
          <input id="inviteNameField" type="text" class="static-input" readonly>
        </div>
        <div class="static-input-container">
          <label for="inviteEmailField" class="static-label">Email</label>
          <input id="inviteEmailField" type="text" class="static-input" readonly>
        </div>
        <div class="static-input-container">
          <label for="inviteHostField" class="static-label">Host</label>
          <input id="inviteHostField" type="text" class="static-input" readonly>
        </div>
        <div class="static-input-container">
          <label for="invitePhoneField" class="static-label">Phone Number</label>
          <input id="invitePhoneField" type="text" class="static-input" readonly>
        </div>
        <div class="modal-action mt-4">
          <button type="button" class="btn btn-ghost" onclick="showInviteStep('code')">Back</button>
          <button type="button" class="btn btn-primary" onclick="submitInviteDetails()">Next</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Invite Summary Modal (after capture, before final approve) -->
  <div id="inviteSummaryModal" class="modal" onclick="closeInviteSummaryModal()">
    <div class="modal-box max-w-xl" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-semibold text-[#E0761C]">Pass Code</h3>
        <button class="text-gray-400 hover:text-gray-600" onclick="closeInviteSummaryModal()">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      <div class="border-b border-gray-200 mb-4"></div>

      <div class="flex items-start gap-10 mb-6">
        <div class="w-24 h-24 rounded-full overflow-hidden border border-gray-300 flex items-center justify-center bg-gray-300 mt-1">
          <img id="inviteSummaryPhoto" src="" alt="Visitor photo" class="w-full h-full object-cover">
        </div>
        <div class="text-sm space-y-1 flex-1 ml-4">
          <div><span class="font-semibold">Name :</span> <span id="inviteSummaryName">—</span></div>
          <div><span class="font-semibold">Email :</span> <span id="inviteSummaryEmail">—</span></div>
          <div><span class="font-semibold">Phonenumber :</span> <span id="inviteSummaryPhone">—</span></div>
          <div><span class="font-semibold">CheckIn :</span> <span id="inviteSummaryCheckIn">—</span></div>
        </div>
      </div>

      <div class="flex items-center justify-between text-xs text-gray-500 mb-4 mt-2">
        <div class="flex flex-col items-start w-40">
          <div class="w-full border-t border-gray-400 mb-1"></div>
          <span class="font-semibold text-[11px]">Sign of the Visitor</span>
        </div>
        <div class="flex flex-col items-end w-40">
          <div class="w-full border-t border-gray-400 mb-1"></div>
          <span class="font-semibold text-[11px] text-right">Sign of the Person Seen</span>
        </div>
      </div>

      <div class="border-t border-gray-200 mt-2 mb-3"></div>

      <div class="modal-action mt-0 flex justify-center items-center gap-3">
        <button type="button" class="btn btn-ghost btn-sm" onclick="skipInviteSummary()">Skip</button>
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">Print</button>
      </div>
    </div>
  </div>

  <!-- Camera Capture Modal -->
  <div id="cameraCaptureModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80">
    <div class="bg-gray-900 text-white rounded-3xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden flex flex-col">
      <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
        <h3 class="text-lg font-semibold tracking-wide">Capture Visitor Photo</h3>
        <button class="text-white/70 hover:text-white" onclick="finalizeApprovalWithoutPhoto()">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      <div class="flex-1 flex flex-col items-center justify-center px-6 py-6 gap-6">
        <video id="cameraVideo" class="w-full max-w-2xl rounded-2xl border-4 border-white/10 bg-black" autoplay playsinline></video>
        <img id="cameraPreviewImage" class="w-full max-w-2xl rounded-2xl border-4 border-white/10 bg-black object-cover hidden" alt="Captured visitor photo preview">
        <canvas id="cameraCanvas" class="hidden"></canvas>
        <p class="text-sm text-white/70 text-center">Align the visitor within the frame and tap capture. This photo will appear on their badge and logs.</p>
      </div>
      <!-- Live camera footer -->
      <div id="cameraFooterLive" class="flex items-center justify-center gap-4 px-6 py-5 bg-black/30">
        <button type="button" class="btn btn-ghost text-white" onclick="finalizeApprovalWithoutPhoto()">Skip</button>
        <button type="button" class="btn" style="background: linear-gradient(135deg, #F7A923 0%, #E6940F 100%); color: #1f2937;" onclick="capturePhoto()">Capture</button>
      </div>
      <!-- Preview footer (after capture) -->
      <div id="cameraFooterPreview" class="hidden flex items-center justify-center gap-4 px-6 py-5 bg-black/30">
        <button type="button" class="btn btn-ghost text-white" onclick="retakePhoto()">Retake</button>
        <button type="button" class="btn" style="background: linear-gradient(135deg, #F7A923 0%, #E6940F 100%); color: #1f2937;" onclick="finalizeApproval()">Next</button>
      </div>
    </div>
  </div>
</body>
</html>
