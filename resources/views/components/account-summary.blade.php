<div class="bg-orange-100 border-t-4 border-orange-500 rounded-b text-orange-900 px-4 py-3 m-4 shadow-md">
  <div class="flex">
    <div class="px-2 text-2xl">
      <i data-feather="{{ $account->icon }}"></i>
    </div>
    <div>
      <p class="font-bold">{{ $account->name }}</p>
      <p class="text-sm">{{ $account->balanceFormatted }}</p>
    </div>
  </div>
</div>
