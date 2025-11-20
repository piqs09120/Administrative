@extends('layouts.app')

@section('content')
<div class="p-6">
  <div class="mb-4 flex items-center justify-between">
    <h1 class="text-2xl font-bold">Policy: {{ $policy->title }}</h1>
    <div class="flex gap-2">
      <a href="{{ route('legal.policies.edit', $policy->id) }}" class="btn btn-sm btn-primary">Edit</a>
      <form method="post" action="{{ route('legal.policies.publish', $policy->id) }}">
        @csrf
        <button type="submit" class="btn btn-sm">Publish</button>
      </form>
      <form method="post" action="{{ route('legal.policies.archive', $policy->id) }}">
        @csrf
        <button type="submit" class="btn btn-sm btn-ghost">Archive</button>
      </form>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card bg-white shadow">
      <div class="card-body">
        <div class="flex items-center gap-3 mb-2">
          <span class="badge {{ $policy->status_color }}">{{ ucfirst($policy->status) }}</span>
          <span class="text-sm">Version {{ $policy->version }}</span>
          <span class="text-sm">Effective: {{ \Carbon\Carbon::parse($policy->effective_date)->format('M d, Y') }}</span>
        </div>
        <p class="text-gray-700 mb-4">{{ $policy->description }}</p>
        <div class="prose max-w-none">{!! nl2br(e($policy->content)) !!}</div>
      </div>
    </div>

    <div class="card bg-white shadow">
      <div class="card-body">
        <h3 class="font-semibold mb-2">Versions</h3>
        <ul class="space-y-2 max-h-64 overflow-y-auto">
          @forelse($policy->versions as $ver)
            <li class="text-sm flex items-center justify-between">
              <span>v{{ $ver->version }} • {{ $ver->created_at->format('M d, Y') }}</span>
              @if($ver->change_notes)
                <span class="text-gray-500">{{ $ver->change_notes }}</span>
              @endif
            </li>
          @empty
            <li class="text-sm text-gray-500">No versions yet</li>
          @endforelse
        </ul>

        <hr class="my-4" />
        <h3 class="font-semibold mb-2">Acknowledgements</h3>
        <form method="post" action="{{ route('legal.policies.ack.create', $policy->id) }}" class="space-y-2">
          @csrf
          <input type="text" name="role" placeholder="Target role (optional)" class="input input-bordered w-full" />
          <input type="date" name="required_by" class="input input-bordered w-full" />
          <button class="btn btn-sm btn-primary" type="submit">Require Acknowledgement</button>
        </form>
        <ul class="mt-3 space-y-2">
          @foreach($policy->acknowledgements as $ack)
            <li class="flex items-center justify-between text-sm">
              <span>{{ $ack->user_id ? ('User #'.$ack->user_id) : ($ack->role ?: 'All') }} — Required by: {{ $ack->required_by ? \Carbon\Carbon::parse($ack->required_by)->format('M d, Y') : 'N/A' }} — {{ $ack->acknowledged_at ? 'Acknowledged' : 'Pending' }}</span>
              <form method="post" action="{{ route('legal.policies.ack.delete', [$policy->id, $ack->id]) }}">
                @csrf
                @method('DELETE')
                <button class="btn btn-xs btn-ghost" type="submit">Remove</button>
              </form>
            </li>
          @endforeach
        </ul>
        <form method="post" action="{{ route('legal.policies.ack.acknowledge', $policy->id) }}" class="mt-3">
          @csrf
          <button type="submit" class="btn btn-sm">Acknowledge</button>
        </form>

        <hr class="my-4" />
        <h3 class="font-semibold mb-2">Link to Case</h3>
        <form method="post" action="{{ route('legal.policies.link_case', [$policy->id, 0]) }}" onsubmit="event.preventDefault(); this.action=this.action.replace('/0', '/' + this.case_id.value); this.submit();">
          @csrf
          <input type="number" name="case_id" class="input input-bordered w-full mb-2" placeholder="Enter Case ID" />
          <button class="btn btn-sm" type="submit">Link Case</button>
        </form>

        <h3 class="font-semibold mt-4 mb-2">Link to Violation</h3>
        <form method="post" action="{{ route('legal.policies.link_violation', [$policy->id, 0]) }}" onsubmit="event.preventDefault(); this.action=this.action.replace('/0', '/' + this.violation_id.value); this.submit();">
          @csrf
          <input type="number" name="violation_id" class="input input-bordered w-full mb-2" placeholder="Enter Violation ID" />
          <button class="btn btn-sm" type="submit">Link Violation</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection


