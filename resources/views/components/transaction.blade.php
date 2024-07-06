@php
  $negative = ($transaction->amount->valueInBaseUnits<0);
  $formattedValue = sprintf("%.2f", abs($transaction->amount->valueInBaseUnits)/100.0);
@endphp

<div class="flex flex-row justify-between border-b border-gray-400 pt-2 hover:bg-gray-100">
  <div class="w-48" title="{{ $transaction->createdAt }}">
    <span>{{ $transaction->date }}</span>
    <span class="text-xs text-gray-500">{{ $transaction->time }}</span>
  </div>
  <div class="flex-grow px-4" title="{{ $transaction->rawText }}">{{ $transaction->description }}</div>
  <div class="text-sm self-end font-bold {{ $negative?'text-red-700':'text-green-700' }}">
    {{ $negative?'-':'' }} ${{ $formattedValue }}
  </div>
  <!-- @json($transaction) -->
</div>