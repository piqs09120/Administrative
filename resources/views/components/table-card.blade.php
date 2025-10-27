<div class="overflow-x-auto mt-5 rounded-xl border border-gray-100 shadow-lg">
  @isset($title)
  <div class="bg-blue-900 text-white px-6 py-4 rounded-t-xl">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold">{{ $title }}</h2>
      @isset($headerAction)
      {{ $headerAction }}
      @endisset
    </div>
  </div>
  @endisset

  <div class="w-full">
    {{ $slot }}
  </div>

  @isset($pagination)
  <div class="mt-4 px-6 py-3 bg-gray-50 flex justify-end rounded-b-xl">
    {{ $pagination }}
  </div>
  @endisset
</div>





