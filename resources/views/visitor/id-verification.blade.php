<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ID Verification - Soliera</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@4.1.1/dist/tesseract.min.js"></script>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <i data-lucide="shield-check" class="h-8 w-8 text-blue-600 mr-3"></i>
                        <h1 class="text-2xl font-bold text-gray-900">ID Verification</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('visitor.index') }}" class="btn btn-outline">
                            <i data-lucide="arrow-left" class="h-4 w-4 mr-2"></i>
                            Back to Visitors
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Pending Verification -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Pending ID Verification</h2>
                    <p class="text-sm text-gray-600">Review and verify visitor ID documents</p>
                </div>
                <div class="p-6">
                    @if($pendingVerification->count() > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($pendingVerification as $visitor)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $visitor->name }}</h3>
                                            <p class="text-sm text-gray-600">{{ $visitor->email }}</p>
                                            <p class="text-sm text-gray-500">{{ $visitor->id_type }} - {{ $visitor->id_number }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    </div>

                                    <!-- ID Document Preview -->
                                    @if($visitor->id_document_path)
                                        <div class="mb-4">
                                            <img src="{{ Storage::url($visitor->id_document_path) }}" 
                                                 alt="ID Document" 
                                                 class="w-full h-32 object-cover rounded border cursor-pointer"
                                                 onclick="openImageModal('{{ Storage::url($visitor->id_document_path) }}')">
                                        </div>
                                    @endif

                                    <!-- Action Buttons -->
                                    <div class="flex space-x-2">
                                        <button onclick="openVerificationModal({{ $visitor->id }}, '{{ $visitor->name }}')" 
                                                type="button"
                                                class="flex-1 bg-green-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-green-700 transition-colors cursor-pointer"
                                                style="pointer-events: auto;">
                                            <i data-lucide="check" class="h-4 w-4 inline mr-1"></i>
                                            Verify
                                        </button>
                                        <button onclick="openRejectionModal({{ $visitor->id }}, '{{ $visitor->name }}')" 
                                                type="button"
                                                class="flex-1 bg-red-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-red-700 transition-colors cursor-pointer"
                                                style="pointer-events: auto;">
                                            <i data-lucide="x" class="h-4 w-4 inline mr-1"></i>
                                            Reject
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i data-lucide="check-circle" class="h-12 w-12 text-green-500 mx-auto mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">All caught up!</h3>
                            <p class="text-gray-600">No pending ID verifications at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recently Verified -->
            @if($verified->count() > 0)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Recently Verified</h2>
                        <p class="text-sm text-gray-600">Latest ID verifications</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($verified as $visitor)
                                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i data-lucide="check-circle" class="h-5 w-5 text-green-600 mr-3"></i>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $visitor->name }}</p>
                                            <p class="text-sm text-gray-600">
                                                Verified by {{ $visitor->verifier->name ?? 'System' }} 
                                                {{ $visitor->id_verified_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst($visitor->id_verification_method) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="modal" onclick="if(event.target === this) closeImageModal()">
        <div class="modal-box max-w-4xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">ID Document</h3>
                <button onclick="closeImageModal()" class="btn btn-sm btn-circle btn-ghost">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
            <img id="modalImage" src="" alt="ID Document" class="w-full h-auto rounded">
        </div>
    </div>

    <!-- Verification Modal -->
    <div id="verificationModal" class="modal" onclick="if(event.target === this) closeVerificationModal()">
        <div class="modal-box">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Verify ID Document</h3>
                <button onclick="closeVerificationModal()" class="btn btn-sm btn-circle btn-ghost">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
            <form id="verificationForm" action="{{ route('visitor.verify_id', 0) }}" method="POST">
                @csrf
                <input type="hidden" id="visitor_id" name="visitor_id">
                
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Verification Method</span>
                    </label>
                    <select name="verification_method" id="verification_method" class="select select-bordered" required>
                        <option value="">Select Method</option>
                        <option value="upload">Document Upload Review</option>
                        <option value="scan">Physical ID Scan</option>
                        <option value="manual">Manual Verification</option>
                    </select>
                </div>

                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Notes (Optional)</span>
                    </label>
                    <textarea name="verification_notes" id="verification_notes" 
                              class="textarea textarea-bordered" 
                              placeholder="Add verification notes..."></textarea>
                </div>

                <!-- Scanner Section (hidden by default) -->
                <div id="scannerSection" class="hidden mb-4">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                        <div id="scannerContainer" class="relative">
                            <video id="scannerVideo" class="w-full h-64 bg-gray-100 rounded"></video>
                            <canvas id="scannerCanvas" class="hidden"></canvas>
                        </div>
                        <div class="mt-4 text-center">
                            <button type="button" id="startScanner" class="btn btn-primary">
                                <i data-lucide="camera" class="h-4 w-4 mr-2"></i>
                                Start Scanner
                            </button>
                            <button type="button" id="stopScanner" class="btn btn-secondary ml-2">
                                <i data-lucide="square" class="h-4 w-4 mr-2"></i>
                                Stop Scanner
                            </button>
                        </div>
                    </div>
                </div>

                <div class="modal-action">
                    <button type="button" onclick="closeVerificationModal()" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="check" class="h-4 w-4 mr-2"></i>
                        Verify ID
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal" class="modal" onclick="if(event.target === this) closeRejectionModal()">
        <div class="modal-box">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Reject ID Verification</h3>
                <button onclick="closeRejectionModal()" class="btn btn-sm btn-circle btn-ghost">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
            <form id="rejectionForm" action="{{ route('visitor.reject_id', 0) }}" method="POST">
                @csrf
                <input type="hidden" id="reject_visitor_id" name="visitor_id">
                
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Rejection Reason *</span>
                    </label>
                    <textarea name="rejection_reason" id="rejection_reason" 
                              class="textarea textarea-bordered" 
                              placeholder="Please specify why the ID verification is being rejected..."
                              required></textarea>
                </div>

                <div class="modal-action">
                    <button type="button" onclick="closeRejectionModal()" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-error">
                        <i data-lucide="x" class="h-4 w-4 mr-2"></i>
                        Reject ID
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let scanner = null;
        let isScanning = false;

        // Image Modal Functions
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            const modal = document.getElementById('imageModal');
            modal.classList.add('modal-open');
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.remove('modal-open');
        }

        // Verification Modal Functions
        function openVerificationModal(visitorId, visitorName) {
            console.log('Opening verification modal for visitor:', visitorId);
            document.getElementById('visitor_id').value = visitorId;
            document.getElementById('verificationForm').action = "{{ route('visitor.verify_id', '') }}/" + visitorId;
            
            // Reset form but keep visitor_id
            const verificationMethod = document.getElementById('verification_method');
            const verificationNotes = document.getElementById('verification_notes');
            if (verificationMethod) verificationMethod.value = '';
            if (verificationNotes) verificationNotes.value = '';
            
            const modal = document.getElementById('verificationModal');
            modal.classList.add('modal-open');
        }

        function closeVerificationModal() {
            const modal = document.getElementById('verificationModal');
            modal.classList.remove('modal-open');
            stopScanner();
        }

        // Rejection Modal Functions
        function openRejectionModal(visitorId, visitorName) {
            console.log('Opening rejection modal for visitor:', visitorId);
            document.getElementById('reject_visitor_id').value = visitorId;
            document.getElementById('rejectionForm').action = "{{ route('visitor.reject_id', '') }}/" + visitorId;
            
            // Reset form but keep visitor_id
            const rejectionReason = document.getElementById('rejection_reason');
            if (rejectionReason) rejectionReason.value = '';
            
            const modal = document.getElementById('rejectionModal');
            modal.classList.add('modal-open');
        }

        function closeRejectionModal() {
            const modal = document.getElementById('rejectionModal');
            modal.classList.remove('modal-open');
        }

        // Scanner Functions
        function startScanner() {
            if (isScanning) return;
            
            const video = document.getElementById('scannerVideo');
            const canvas = document.getElementById('scannerCanvas');
            
            navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } 
            })
            .then(stream => {
                video.srcObject = stream;
                isScanning = true;
                
                // Initialize Quagga for barcode scanning
                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: video,
                        constraints: {
                            width: 640,
                            height: 480,
                            facingMode: "environment"
                        },
                    },
                    decoder: {
                        readers: [
                            "code_128_reader",
                            "ean_reader",
                            "ean_8_reader",
                            "code_39_reader",
                            "code_39_vin_reader",
                            "codabar_reader",
                            "upc_reader",
                            "upc_e_reader",
                            "i2of5_reader"
                        ]
                    },
                    locate: true,
                    locator: {
                        patchSize: "medium",
                        halfSample: true
                    }
                }, function(err) {
                    if (err) {
                        console.error('Quagga initialization error:', err);
                        return;
                    }
                    Quagga.start();
                });

                // Handle barcode detection
                Quagga.onDetected(function(result) {
                    const code = result.codeResult.code;
                    console.log('Barcode detected:', code);
                    
                    // Extract data from barcode
                    const scannedData = {
                        barcode: code,
                        timestamp: new Date().toISOString(),
                        method: 'barcode_scan'
                    };
                    
                    document.getElementById('verification_notes').value = 
                        `Barcode scanned: ${code}\n` + document.getElementById('verification_notes').value;
                    
                    // Stop scanner after successful scan
                    stopScanner();
                });
            })
            .catch(err => {
                console.error('Camera access error:', err);
                alert('Unable to access camera. Please check permissions.');
            });
        }

        function stopScanner() {
            if (!isScanning) return;
            
            const video = document.getElementById('scannerVideo');
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
                video.srcObject = null;
            }
            
            if (Quagga) {
                Quagga.stop();
            }
            
            isScanning = false;
        }

        // Event Listeners
        document.getElementById('verification_method').addEventListener('change', function() {
            const scannerSection = document.getElementById('scannerSection');
            if (this.value === 'scan') {
                scannerSection.classList.remove('hidden');
            } else {
                scannerSection.classList.add('hidden');
                stopScanner();
            }
        });

        document.getElementById('startScanner').addEventListener('click', startScanner);
        document.getElementById('stopScanner').addEventListener('click', stopScanner);

        // Form Submissions
        document.getElementById('verificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const visitorId = formData.get('visitor_id');
            
            fetch(`/visitor/${visitorId}/verify-id`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    verification_method: formData.get('verification_method'),
                    verification_notes: formData.get('verification_notes'),
                    scanned_data: null // Can be enhanced to include scanned data
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while verifying the ID.');
            });
        });

        document.getElementById('rejectionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const visitorId = formData.get('visitor_id');
            
            fetch(`/visitor/${visitorId}/reject-id`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    rejection_reason: formData.get('rejection_reason')
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while rejecting the ID.');
            });
        });

        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            
            // Debug: Log when page is ready
            console.log('ID Verification page loaded');
            console.log('Verify buttons:', document.querySelectorAll('button[onclick*="openVerificationModal"]').length);
            console.log('Reject buttons:', document.querySelectorAll('button[onclick*="openRejectionModal"]').length);
        });
        
        // Reinitialize icons after any dynamic content changes
        function reinitializeIcons() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    </script>
</body>
</html>
