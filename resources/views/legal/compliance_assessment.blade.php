<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Compliance Assessment - Soliera</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  @vite(['resources/css/soliera.css'])
  
  <style>
    :root {
      --color-regal-navy: #1e3a8a;
      --color-charcoal-ink: #1f2937;
      --color-snow-mist: #f3f4f6;
      --color-white: #ffffff;
      --color-modern-teal: #0d9488;
      --color-golden-ember: #d97706;
      --color-danger-red: #dc2626;
    }
    
    .compliance-item {
      transition: all 0.2s ease;
    }
    
    .compliance-item:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .risk-indicator {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 8px;
    }
    
    .risk-low { background-color: #10b981; }
    .risk-medium { background-color: #f59e0b; }
    .risk-high { background-color: #ef4444; }
    .risk-critical { background-color: #dc2626; }
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
        <!-- Back button and title -->
        <div class="flex items-center mb-6">
          <a href="{{ route('legal.cases.review', $case->id) }}" class="btn btn-ghost btn-sm mr-4" title="Back to Case Review">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
          </a>
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Compliance Assessment</h1>
            <p class="text-gray-600">Evaluate legal compliance and regulatory requirements for case: {{ $case->case_title }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Assessment Area -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Compliance Overview -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h2 class="card-title text-xl mb-6 flex items-center">
                  <i data-lucide="shield-check" class="w-5 h-5 text-green-500 mr-2"></i>
                  Compliance Overview
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-3xl font-bold text-green-600 mb-2">85%</div>
                    <div class="text-sm text-green-700">Overall Compliance</div>
                  </div>
                  <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-3xl font-bold text-yellow-600 mb-2">3</div>
                    <div class="text-sm text-yellow-700">Issues Found</div>
                  </div>
                  <div class="text-center p-4 bg-red-50 rounded-lg">
                    <div class="text-3xl font-bold text-red-600 mb-2">1</div>
                    <div class="text-sm text-red-700">Critical Issues</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Regulatory Compliance Checklist -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h2 class="card-title text-xl mb-6 flex items-center">
                  <i data-lucide="list-checks" class="w-5 h-5 text-blue-500 mr-2"></i>
                  Regulatory Compliance Checklist
                </h2>
                
                <div class="space-y-4">
                  <!-- Labor Law Compliance -->
                  <div class="compliance-item card bg-base-100 shadow-sm border">
                    <div class="card-body p-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <span class="risk-indicator risk-medium"></span>
                          <div>
                            <h4 class="font-semibold">Labor Law Compliance</h4>
                            <p class="text-sm text-gray-600">Employment standards, working hours, benefits</p>
                          </div>
                        </div>
                        <div class="flex items-center gap-2">
                          <span class="badge badge-warning">Needs Review</span>
                          <button class="btn btn-xs btn-outline">Assess</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Health & Safety Compliance -->
                  <div class="compliance-item card bg-base-100 shadow-sm border">
                    <div class="card-body p-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <span class="risk-indicator risk-low"></span>
                          <div>
                            <h4 class="font-semibold">Health & Safety Compliance</h4>
                            <p class="text-sm text-gray-600">Workplace safety, emergency procedures, training</p>
                          </div>
                        </div>
                        <div class="flex items-center gap-2">
                          <span class="badge badge-success">Compliant</span>
                          <button class="btn btn-xs btn-outline">View</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Data Protection Compliance -->
                  <div class="compliance-item card bg-base-100 shadow-sm border">
                    <div class="card-body p-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <span class="risk-indicator risk-high"></span>
                          <div>
                            <h4 class="font-semibold">Data Protection Compliance</h4>
                            <p class="text-sm text-gray-600">GDPR, data privacy, customer information</p>
                          </div>
                        </div>
                        <div class="flex items-center gap-2">
                          <span class="badge badge-error">Non-Compliant</span>
                          <button class="btn btn-xs btn-outline">Fix</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Financial Compliance -->
                  <div class="compliance-item card bg-base-100 shadow-sm border">
                    <div class="card-body p-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <span class="risk-indicator risk-low"></span>
                          <div>
                            <h4 class="font-semibold">Financial Compliance</h4>
                            <p class="text-sm text-gray-600">Tax obligations, accounting standards, reporting</p>
                          </div>
                        </div>
                        <div class="flex items-center gap-2">
                          <span class="badge badge-success">Compliant</span>
                          <button class="btn btn-xs btn-outline">View</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Industry-Specific Regulations -->
                  <div class="compliance-item card bg-base-100 shadow-sm border">
                    <div class="card-body p-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                          <span class="risk-indicator risk-medium"></span>
                          <div>
                            <h4 class="font-semibold">Hospitality Industry Regulations</h4>
                            <p class="text-sm text-gray-600">Food safety, liquor licensing, guest services</p>
                          </div>
                        </div>
                        <div class="flex items-center gap-2">
                          <span class="badge badge-warning">Partial</span>
                          <button class="btn btn-xs btn-outline">Review</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Risk Assessment -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h2 class="card-title text-xl mb-6 flex items-center">
                  <i data-lucide="alert-triangle" class="w-5 h-5 text-orange-500 mr-2"></i>
                  Risk Assessment
                </h2>
                
                <div class="space-y-4">
                  <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                      <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                      <h4 class="font-semibold text-red-800">High Risk: Data Breach Potential</h4>
                    </div>
                    <p class="text-sm text-red-700 mb-3">The incident may involve unauthorized access to customer data, requiring immediate notification to authorities and affected parties.</p>
                    <div class="flex gap-2">
                      <button class="btn btn-xs btn-error">Mitigate Risk</button>
                      <button class="btn btn-xs btn-outline">View Details</button>
                    </div>
                  </div>

                  <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                      <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600"></i>
                      <h4 class="font-semibold text-yellow-800">Medium Risk: Labor Law Violation</h4>
                    </div>
                    <p class="text-sm text-yellow-700 mb-3">Potential violation of overtime regulations may require corrective action and back pay calculations.</p>
                    <div class="flex gap-2">
                      <button class="btn btn-xs btn-warning">Address Issue</button>
                      <button class="btn btn-xs btn-outline">View Details</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar - Actions & Recommendations -->
          <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h2 class="card-title text-xl mb-6 flex items-center">
                  <i data-lucide="zap" class="w-5 h-5 text-purple-500 mr-2"></i>
                  Quick Actions
                </h2>
                
                <div class="space-y-3">
                  <button onclick="openComplianceFixModal()" class="btn btn-outline w-full justify-start">
                    <i data-lucide="wrench" class="w-4 h-4 mr-2"></i>
                    Fix Compliance Issues
                  </button>
                  
                  <button onclick="openRiskMitigationModal()" class="btn btn-outline w-full justify-start">
                    <i data-lucide="shield" class="w-4 h-4 mr-2"></i>
                    Mitigate Risks
                  </button>
                  
                  <button onclick="openRegulatoryConsultationModal()" class="btn btn-outline w-full justify-start">
                    <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                    Consult Regulators
                  </button>
                  
                  <button onclick="openComplianceReportModal()" class="btn btn-outline w-full justify-start">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                    Generate Report
                  </button>
                </div>
              </div>
            </div>

            <!-- Recommendations -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h2 class="card-title text-xl mb-6 flex items-center">
                  <i data-lucide="lightbulb" class="w-5 h-5 text-yellow-500 mr-2"></i>
                  Recommendations
                </h2>
                
                <div class="space-y-3">
                  <div class="p-3 bg-blue-50 rounded-lg">
                    <h4 class="font-semibold text-blue-800 text-sm">Immediate Actions</h4>
                    <ul class="text-xs text-blue-700 mt-1 space-y-1">
                      <li>• Notify data protection authority within 72 hours</li>
                      <li>• Secure all affected systems immediately</li>
                      <li>• Prepare breach notification letters</li>
                    </ul>
                  </div>
                  
                  <div class="p-3 bg-green-50 rounded-lg">
                    <h4 class="font-semibold text-green-800 text-sm">Short-term (1-4 weeks)</h4>
                    <ul class="text-xs text-green-700 mt-1 space-y-1">
                      <li>• Conduct internal investigation</li>
                      <li>• Update data protection policies</li>
                      <li>• Train staff on compliance</li>
                    </ul>
                  </div>
                  
                  <div class="p-3 bg-purple-50 rounded-lg">
                    <h4 class="font-semibold text-purple-800 text-sm">Long-term (1-3 months)</h4>
                    <ul class="text-xs text-purple-700 mt-1 space-y-1">
                      <li>• Implement compliance monitoring</li>
                      <li>• Regular compliance audits</li>
                      <li>• Update legal documentation</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- Compliance Score -->
            <div class="card bg-white shadow-xl">
              <div class="card-body">
                <h2 class="card-title text-xl mb-6 flex items-center">
                  <i data-lucide="trending-up" class="w-5 h-5 text-indigo-500 mr-2"></i>
                  Compliance Score
                </h2>
                
                <div class="text-center">
                  <div class="radial-progress text-primary" style="--value:85; --size:8rem; --thickness:8px;">
                    <span class="text-2xl font-bold">85%</span>
                  </div>
                  <p class="text-sm text-gray-600 mt-2">Overall Compliance Rating</p>
                </div>
                
                <div class="mt-4 space-y-2">
                  <div class="flex justify-between text-sm">
                    <span>Labor Law</span>
                    <span class="font-semibold">75%</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span>Health & Safety</span>
                    <span class="font-semibold">95%</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span>Data Protection</span>
                    <span class="font-semibold">60%</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span>Financial</span>
                    <span class="font-semibold">90%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Compliance Fix Modal -->
  <div id="complianceFixModal" class="modal">
    <div class="modal-box w-11/12 max-w-4xl">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Fix Compliance Issues</h2>
        <button onclick="closeComplianceFixModal()" class="btn btn-sm btn-circle btn-ghost">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <form id="complianceFixForm">
        @csrf
        <div class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Issues to Fix</label>
            <div class="space-y-2">
              <label class="flex items-center gap-3 p-3 border rounded-lg">
                <input type="checkbox" class="checkbox" checked>
                <div>
                  <div class="font-semibold">Data Protection Compliance</div>
                  <div class="text-sm text-gray-600">Implement proper data handling procedures</div>
                </div>
              </label>
              <label class="flex items-center gap-3 p-3 border rounded-lg">
                <input type="checkbox" class="checkbox">
                <div>
                  <div class="font-semibold">Labor Law Compliance</div>
                  <div class="text-sm text-gray-600">Review and update employment policies</div>
                </div>
              </label>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Action Plan</label>
            <textarea class="textarea textarea-bordered w-full" rows="4" placeholder="Describe the specific actions to be taken..."></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Expected Completion Date</label>
            <input type="date" class="input input-bordered w-full">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
            <select class="select select-bordered w-full">
              <option value="">Select team member</option>
              <option value="legal_team">Legal Team</option>
              <option value="hr_team">HR Team</option>
              <option value="it_team">IT Team</option>
              <option value="management">Management</option>
            </select>
          </div>
        </div>
        
        <div class="modal-action">
          <button type="button" onclick="closeComplianceFixModal()" class="btn btn-ghost">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Action Plan</button>
        </div>
      </form>
    </div>
  </div>

  @include('partials.soliera_js')
  
  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Modal functions
    function openComplianceFixModal() {
      document.getElementById('complianceFixModal').classList.add('modal-open');
    }
    
    function closeComplianceFixModal() {
      document.getElementById('complianceFixModal').classList.remove('modal-open');
    }
    
    function openRiskMitigationModal() {
      alert('Risk Mitigation modal - To be implemented');
    }
    
    function openRegulatoryConsultationModal() {
      alert('Regulatory Consultation modal - To be implemented');
    }
    
    function openComplianceReportModal() {
      alert('Compliance Report modal - To be implemented');
    }
  </script>
</body>
</html>
