@extends('layouts.app')

@section('title', 'AI Analyses')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">AI Analyses</h1>
                <div class="flex space-x-4">
                    <button onclick="refreshAnalyses()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                    <a href="{{ route('legal.legal_documents') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-file-alt mr-2"></i>Documents
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="analysis_type" class="block text-sm font-medium text-gray-700">Analysis Type</label>
                        <select name="analysis_type" id="analysis_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <option value="">All Types</option>
                            <option value="document_classification" {{ request('analysis_type') == 'document_classification' ? 'selected' : '' }}>Document Classification</option>
                            <option value="complaint_analysis" {{ request('analysis_type') == 'complaint_analysis' ? 'selected' : '' }}>Complaint Analysis</option>
                            <option value="violation_analysis" {{ request('analysis_type') == 'violation_analysis' ? 'selected' : '' }}>Violation Analysis</option>
                            <option value="compliance_check" {{ request('analysis_type') == 'compliance_check' ? 'selected' : '' }}>Compliance Check</option>
                        </select>
                    </div>
                    <div>
                        <label for="risk_level" class="block text-sm font-medium text-gray-700">Risk Level</label>
                        <select name="risk_level" id="risk_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <option value="">All Risk Levels</option>
                            <option value="low" {{ request('risk_level') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('risk_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('risk_level') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ request('risk_level') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                    <div>
                        <label for="compliance_status" class="block text-sm font-medium text-gray-700">Compliance Status</label>
                        <select name="compliance_status" id="compliance_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <option value="">All Status</option>
                            <option value="compliant" {{ request('compliance_status') == 'compliant' ? 'selected' : '' }}>Compliant</option>
                            <option value="non_compliant" {{ request('compliance_status') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant</option>
                            <option value="needs_review" {{ request('compliance_status') == 'needs_review' ? 'selected' : '' }}>Needs Review</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- AI Analyses Table -->
        <x-table-card :title="'AI Analyses'" :pagination="($analyses->count() > 0) ? $analyses->links() : null">
          <table class="table w-full">
            <thead class="bg-gray-100">
              <tr>
                <th class="text-left py-4 px-4 font-semibold text-gray-700">Analysis Type</th>
                <th class="text-left py-4 px-4 font-semibold text-gray-700">Entity</th>
                <th class="text-left py-4 px-4 font-semibold text-gray-700">Document Type</th>
                <th class="text-left py-4 px-4 font-semibold text-gray-700">Risk Level</th>
                <th class="text-left py-4 px-4 font-semibold text-gray-700">Compliance</th>
                <th class="text-left py-4 px-4 font-semibold text-gray-700">Confidence</th>
                <th class="text-left py-4 px-4 font-semibold text-gray-700">Date</th>
                <th class="text-left py-4 px-4 font-semibold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($analyses as $analysis)
              <tr>
                <td class="py-4 px-4">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">{{ ucfirst(str_replace('_', ' ', $analysis->analysis_type)) }}</span>
                </td>
                <td class="py-4 px-4">
                  @if($analysis->document)
                    <div>
                      <div class="font-medium">{{ $analysis->document->title }}</div>
                      <div class="text-gray-500 text-sm">Document</div>
                    </div>
                  @elseif($analysis->complaint)
                    <div>
                      <div class="font-medium">{{ $analysis->complaint->case_id }}</div>
                      <div class="text-gray-500 text-sm">Complaint</div>
                    </div>
                  @elseif($analysis->violationReport)
                    <div>
                      <div class="font-medium">{{ $analysis->violationReport->report_id }}</div>
                      <div class="text-gray-500 text-sm">Violation Report</div>
                    </div>
                  @else
                    <span class="text-gray-400">N/A</span>
                  @endif
                </td>
                <td class="py-4 px-4 text-sm text-gray-500">{{ $analysis->document_type ?? 'N/A' }}</td>
                <td class="py-4 px-4"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $analysis->risk_level_color }}">{{ ucfirst($analysis->risk_level) }}</span></td>
                <td class="py-4 px-4"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $analysis->compliance_status_color }}">{{ ucfirst(str_replace('_', ' ', $analysis->compliance_status)) }}</span></td>
                <td class="py-4 px-4 text-sm text-gray-500">
                  <div class="flex items-center">
                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                      <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $analysis->confidence }}%"></div>
                    </div>
                    <span>{{ $analysis->confidence }}%</span>
                  </div>
                </td>
                <td class="py-4 px-4 text-sm text-gray-500">{{ $analysis->created_at->format('M d, Y H:i') }}</td>
                <td class="py-4 px-4 text-sm font-medium">
                  <a href="{{ route('legal.ai_analyses.show', $analysis->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                  @if($analysis->requiresImmediateAttention())
                    <span class="text-red-600 font-medium">⚠️ Urgent</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="py-10 text-center">
                  <i class="fas fa-robot text-4xl text-gray-400 mb-4"></i>
                  <h3 class="text-lg font-medium text-gray-900 mb-2">No AI analyses found</h3>
                  <p class="text-gray-500 mb-4">No analyses match your current filters.</p>
                  <a href="{{ route('legal.legal_documents') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-file-alt mr-2"></i>Upload Documents
                  </a>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </x-table-card>
    </div>
</div>

<script>
function refreshAnalyses() {
    location.reload();
}
</script>
@endsection
