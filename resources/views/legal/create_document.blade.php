<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Create Legal Document - Soliera</title>
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
        <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Create Legal Document</h1>
            <a href="{{ route('legal.legal_documents') }}" class="btn btn-ghost btn-sm">
              <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
              Back
            </a>
          </div>

          <form action="{{ route('legal.documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="label"><span class="label-text">Title *</span></label>
                <input type="text" name="title" required class="input input-bordered w-full" placeholder="e.g. Guest Agreement Template">
              </div>
              <div>
                <label class="label"><span class="label-text">Department *</span></label>
                <select name="department" required class="select select-bordered w-full">
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
                <select name="document_type" required class="select select-bordered w-full">
                  <option value="">Select type</option>
                  <option value="contract">Contract</option>
                  <option value="policy">Policy</option>
                  <option value="license">License</option>
                  <option value="notice">Notice</option>
                  <option value="agreement">Agreement</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div>
                <label class="label"><span class="label-text">Purpose</span></label>
                <input type="text" name="purpose" class="input input-bordered w-full" placeholder="Brief purpose">
              </div>
              <div class="md:col-span-2">
                <label class="label"><span class="label-text">Parties</span></label>
                <input type="text" name="parties" class="input input-bordered w-full" placeholder="List parties involved">
              </div>
              <div>
                <label class="label"><span class="label-text">Amount</span></label>
                <input type="number" step="0.01" name="amount" class="input input-bordered w-full" placeholder="0.00">
              </div>
              <div>
                <label class="label"><span class="label-text">Effective Date</span></label>
                <input type="date" name="effective_date" class="input input-bordered w-full">
              </div>
              <div>
                <label class="label"><span class="label-text">End Date</span></label>
                <input type="date" name="end_date" class="input input-bordered w-full">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="label"><span class="label-text">Create Mode</span></label>
                <select id="createMode" class="select select-bordered w-full">
                  <option value="custom">Custom Draft</option>
                  <option value="template">From Template</option>
                </select>
              </div>
              <div id="templatePicker" class="hidden">
                <label class="label"><span class="label-text">Template</span></label>
                <select id="templateKey" class="select select-bordered w-full">
                  <option value="">Select a template</option>
                  @if(isset($templates))
                    @foreach($templates as $tpl)
                      <option value="{{ $tpl['key'] }}" data-title="{{ $tpl['title'] }}" data-content='@json($tpl["content"])'>{{ $tpl['name'] }}</option>
                    @endforeach
                  @endif
                </select>
              </div>
            </div>

            <!-- Template-driven fields (shown when mode=template) -->
            <div id="templateFields" class="grid grid-cols-1 md:grid-cols-2 gap-4 hidden">
              <div>
                <label class="label"><span class="label-text">First Party *</span></label>
                <input type="text" id="first_party" class="input input-bordered w-full" placeholder="Company/Individual name">
              </div>
              <div>
                <label class="label"><span class="label-text">Second Party *</span></label>
                <input type="text" id="second_party" class="input input-bordered w-full" placeholder="Company/Individual name">
              </div>
              <div class="md:col-span-2">
                <label class="label"><span class="label-text">Primary Terms & Conditions</span></label>
                <textarea id="primary_terms" class="textarea textarea-bordered w-full h-28" placeholder="Main obligations, scope, limits..."></textarea>
              </div>
            </div>

            <!-- Content field removed per request -->

            <div>
              <label class="label"><span class="label-text">Attach File (Optional)</span></label>
              <input type="file" name="file" class="file-input file-input-bordered w-full" />
              <p class="text-xs text-gray-500 mt-1">Accepted: pdf, doc, docx, txt (max 20 MB)</p>
            </div>

            <div class="flex items-center justify-end gap-2">
              <button name="action" value="draft" class="btn btn-primary">Save as Draft</button>
            </div>
          </form>
        </div>
      </main>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide && window.lucide.createIcons) window.lucide.createIcons();

      const createMode = document.getElementById('createMode');
      const templatePicker = document.getElementById('templatePicker');
      const templateKey = document.getElementById('templateKey');
      const titleInput = document.querySelector('input[name="title"]');
      const contentArea = document.getElementById('contentArea');
      const partiesInput = document.querySelector('input[name="parties"]');
      const amountInput = document.querySelector('input[name="amount"]');
      const effInput = document.querySelector('input[name="effective_date"]');
      const firstParty = document.getElementById('first_party');
      const secondParty = document.getElementById('second_party');
      const primaryTerms = document.getElementById('primary_terms');
      const templateFields = document.getElementById('templateFields');

      function toggleTemplateUI() {
        if (!createMode) return;
        const isTpl = createMode.value === 'template';
        templatePicker.classList.toggle('hidden', !isTpl);
        templateFields.classList.toggle('hidden', !isTpl);
      }
      toggleTemplateUI();
      if (createMode) createMode.addEventListener('change', toggleTemplateUI);

      function generateFromTemplate() {
        const opt = templateKey?.selectedOptions ? templateKey.selectedOptions[0] : null;
        if (!opt || !opt.dataset.content) return;
        if (opt.dataset.title && !titleInput.value) titleInput.value = opt.dataset.title;
        const tpl = opt.dataset.content;
        const a = (firstParty?.value || partiesInput.value.split(' vs ')[0] || partiesInput.value.split(',')[0] || '').trim();
        const b = (secondParty?.value || partiesInput.value.split(' vs ')[1] || partiesInput.value.split(',')[1] || '').trim();
        const map = {
          '[[PARTY_A]]': a,
          '[[PARTY_B]]': b,
          '[[EFFECTIVE_DATE]]': effInput.value || new Date().toISOString().slice(0,10),
          '[[AMOUNT]]': amountInput.value || '0.00',
          '[[TERMS]]': (primaryTerms?.value || '')
        };
        let content = tpl;
        Object.keys(map).forEach(k => content = content.replaceAll(k, map[k]));
        // Put generated text into both primary terms and content (as requested)
        if (primaryTerms) primaryTerms.value = content;
        if (contentArea) contentArea.value = content;
      }

      if (templateKey) {
        templateKey.addEventListener('change', generateFromTemplate);
      }
      // Regenerate when key inputs change to keep content synced
      [firstParty, secondParty, effInput, amountInput].forEach(el => {
        if (el) el.addEventListener('input', () => {
          if (createMode?.value === 'template') generateFromTemplate();
        });
      });
      // When user edits primary terms manually, mirror to content
      if (primaryTerms) primaryTerms.addEventListener('input', () => { if (contentArea) contentArea.value = primaryTerms.value; });

      // Preselect mode/template from URL params (for Create tab template links)
      try {
        const params = new URLSearchParams(window.location.search);
        const mode = params.get('mode');
        const tplKey = params.get('template');
        if (mode === 'template') {
          if (createMode) {
            createMode.value = 'template';
          }
          toggleTemplateUI();
          if (tplKey && templateKey) {
            const option = Array.from(templateKey.options).find(o => o.value === tplKey);
            if (option) {
              templateKey.value = tplKey;
              // trigger to fill content
              const event = new Event('change');
              templateKey.dispatchEvent(event);
            }
          }
        }
      } catch (e) {
        console.warn('URL param preselect failed:', e);
      }
    });
  </script>
</body>
</html>


