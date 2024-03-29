<table class="min-w-full">
  <thead>
    <tr>
      <th>Time</th>
      <th>Status</th>
      <th>Description</th>
      <th>Amount</th>
    </tr>
  </thead>
  <tbody>
@foreach($transactions as $i => $transaction)


    <tr class="transaction {{ ($i%2)?'bg-gray-100':''}}">

      <td title="{{ $transaction->createdAt }}" class="shrink">
        <span>{{ $transaction->date }}</span>
        <span class="text-xs text-gray-500">{{ $transaction->time }}</span>
      </td>

      <td class="shrink">{{ $transaction->status }}</td>

      <td class="expand" title="{{ $transaction->rawText }}">{{ $transaction->description }}</td>

      <td class="shrink text-sm text-right font-bold {{ $transaction->debitCredit }}">{{ $transaction->amountFormatted }}</td>

    </tr>
@endforeach
  </tbody>
</table>
<div >

</div>
