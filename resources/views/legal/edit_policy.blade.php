@extends('layouts.app')

@section('content')
<div class="p-6 max-w-5xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Edit Policy</h1>
  <form method="post" action="{{ route('legal.policies.update', $policy->id) }}" class="space-y-4">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="label">Title</label>
        <input type="text" name="title" class="input input-bordered w-full" value="{{ old('title', $policy->title) }}" required />
      </div>
      <div>
        <label class="label">Category</label>
        <input type="text" name="category" class="input input-bordered w-full" value="{{ old('category', $policy->category) }}" required />
      </div>
      <div>
        <label class="label">Department</label>
        <input type="text" name="department" class="input input-bordered w-full" value="{{ old('department', $policy->department) }}" />
      </div>
      <div>
        <label class="label">Effective Date</label>
        <input type="date" name="effective_date" class="input input-bordered w-full" value="{{ old('effective_date', $policy->effective_date?->format('Y-m-d')) }}" required />
      </div>
      <div>
        <label class="label">Review Date</label>
        <input type="date" name="review_date" class="input input-bordered w-full" value="{{ old('review_date', $policy->review_date?->format('Y-m-d')) }}" />
      </div>
    </div>

    <div>
      <label class="label">Description</label>
      <textarea name="description" class="textarea textarea-bordered w-full" rows="3" required>{{ old('description', $policy->description) }}</textarea>
    </div>

    <div>
      <label class="label">Content</label>
      <textarea name="content" class="textarea textarea-bordered w-full" rows="10" required>{{ old('content', $policy->content) }}</textarea>
    </div>

    <div>
      <label class="label">Change Notes (for versioning)</label>
      <input type="text" name="change_notes" class="input input-bordered w-full" />
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('legal.policies.show', $policy->id) }}" class="btn btn-ghost">Cancel</a>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
  </form>
</div>
@endsection


