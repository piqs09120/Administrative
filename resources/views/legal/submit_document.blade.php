<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Submit Legal Document - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
</head>
<body class="bg-base-100">
  <div class="flex h-screen overflow-hidden">
    @include('partials.sidebarr')
    <div class="flex flex-col flex-1 overflow-hidden">
      @include('partials.navbar')
      <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
        <div class="max-w-3xl mx-auto bg-white rounded-xl shadow p-6">
          <h1 class="text-2xl font-bold mb-4">Submit Legal Document</h1>
          <form action="{{ route('legal.documents.store_submission') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="label"><span class="label-text">Title *</span></label>
                <input type="text" name="title" class="input input-bordered w-full" required>
              </div>
              <div>
                <label class="label"><span class="label-text">Department *</span></label>
                <select name="department" class="select select-bordered w-full" required>
                  <option value="">Select department</option>
                  <option>Human Resources</option>
                  <option>Information Technology</option>
                  <option>Finance</option>
                  <option>Operations</option>
                  <option>Marketing</option>
                  <option>Legal</option>
                  <option>Other</option>
                </select>
              </div>
              <div>
                <label class="label"><span class="label-text">Document Type *</span></label>
                <select name="document_type" class="select select-bordered w-full" required>
                  <option value="">Select type</option>
                  <option value="contract">Contract</option>
                  <option value="policy">Policy</option>
                  <option value="license">License</option>
                  <option value="notice">Notice</option>
                  <option value="agreement">Agreement</option>
                </select>
              </div>
              <div>
                <label class="label"><span class="label-text">Responsible Officer</span></label>
                <input type="text" name="responsible_officer" class="input input-bordered w-full">
              </div>
              <div>
                <label class="label"><span class="label-text">Date</span></label>
                <input type="date" name="date" class="input input-bordered w-full">
              </div>
            </div>
            <div>
              <label class="label"><span class="label-text">Attach File *</span></label>
              <input type="file" name="file" class="file-input file-input-bordered w-full" required>
            </div>
            <div class="flex justify-end gap-2">
              <a href="{{ route('legal.legal_documents') }}" class="btn btn-ghost">Cancel</a>
              <button class="btn btn-primary" name="action" value="draft">Save as Draft</button>
            </div>
          </form>
        </div>

        @if(session('ai_analysis'))
          @php $ai = session('ai_analysis'); @endphp
          <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-4xl" data-theme="light">
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-2">
                  <i data-lucide="brain" class="w-5 h-5 text-purple-600"></i>
                  AI Document Analysis
                </h3>
                <button class="btn btn-sm btn-circle btn-ghost" onclick="this.closest('.modal').classList.remove('modal-open')">✕</button>
              </div>

              <div class="space-y-4">
                <!-- Document Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                      <p class="text-gray-500 mb-1">Title:</p>
                      <p class="font-medium">{{ $ai['document']['title'] ?? '—' }}</p>
                    </div>
                    <div>
                      <p class="text-gray-500 mb-1">Status:</p>
                      <p class="font-medium">{{ $ai['document']['status'] ?? '—' }}</p>
                    </div>
                    <div>
                      <p class="text-gray-500 mb-1">Type:</p>
                      <p class="font-medium">{{ $ai['document']['type'] ?? '—' }}</p>
                    </div>
                    <div>
                      <p class="text-gray-500 mb-1">Upload Date:</p>
                      <p class="font-medium">{{ $ai['document']['uploaded_at'] ?? '—' }}</p>
                    </div>
                  </div>
                </div>

                <!-- AI Classification -->
                <div class="rounded-lg p-4 flex items-center gap-3" style="background:#eef2ff">
                  <i data-lucide="wrench" class="w-5 h-5 text-blue-600"></i>
                  <div class="flex-1">
                    <div class="text-xs text-blue-600 mb-1">AI Classification</div>
                    <div class="flex items-center justify-between">
                      <div class="font-bold text-lg">{{ $ai['classification']['label'] ?? 'General Document' }}</div>
                      <div class="text-xs text-blue-600">AI Confidence: {{ $ai['classification']['confidence'] ?? 'High (90%)' }}</div>
                    </div>
                  </div>
                </div>

                <!-- AI Summary -->
                <div class="rounded-lg p-4 flex items-start gap-3" style="background:#ecfdf5">
                  <i data-lucide="lightbulb" class="w-5 h-5 text-green-600 mt-0.5"></i>
                  <div class="flex-1">
                    <div class="text-xs text-green-700 mb-1">AI Summary</div>
                    <div class="text-sm text-gray-700">{{ $ai['summary'] ?? 'This appears to be a general document. Content preview: human resources policy dsbs...' }}</div>
                  </div>
                </div>

                <!-- Legal Implications -->
                <div class="rounded-lg p-4 flex items-start gap-3" style="background:#fff7ed">
                  <i data-lucide="triangle-alert" class="w-5 h-5 text-orange-600 mt-0.5"></i>
                  <div class="flex-1">
                    <div class="text-xs text-orange-700 mb-1">Legal Implications</div>
                    <div class="text-sm text-gray-700">{{ $ai['implications'] ?? 'Limited legal implications identified' }}</div>
                  </div>
                </div>

                <!-- Analysis Details -->
                <div class="rounded-lg p-4 bg-gray-50">
                  <div class="text-xs text-gray-500 mb-3">Analysis Details</div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                      <div class="text-gray-500 mb-1">Compliance:</div>
                      <div class="font-medium">{{ $ai['details']['compliance'] ?? 'review_required' }}</div>
                    </div>
                    <div>
                      <div class="text-gray-500 mb-1">Legal Risk:</div>
                      <div class="font-medium">{{ $ai['details']['risk'] ?? 'Low' }}</div>
                    </div>
                    <div class="md:col-span-2">
                      <div class="text-gray-500 mb-1">Tags:</div>
                      <div class="font-medium">{{ is_array($ai['details']['tags'] ?? null) ? implode(', ', $ai['details']['tags']) : ($ai['details']['tags'] ?? 'general, fallback_analysis, policy, document, analysis') }}</div>
                    </div>
                  </div>
                  <div class="mt-3 text-sm">
                    <span class="text-gray-500">Review Required:</span>
                    <span class="font-semibold">{{ ($ai['details']['review_required'] ?? false) ? 'Yes' : 'No' }}</span>
                  </div>
                </div>
              </div>

              <div class="modal-action">
                <button class="btn btn-primary" onclick="this.closest('.modal').classList.remove('modal-open')">CLOSE</button>
              </div>
            </div>
          </div>
        @endif
      </main>
    </div>
  </div>
</body>
</html>


