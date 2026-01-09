@extends('inventory.layouts.inventory')


@section('title','New Purchase')
@section('heading','New Purchase (Stock-In)')
@section('subtitle','Add supplier, date and multiple products')

@section('content')
<form method="POST" action="{{ route('inventory.purchases.store') }}"
      class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-6">
  @csrf

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
      <label class="text-sm text-slate-600">Supplier</label>
      <select name="supplier_id" class="mt-1 w-full rounded-xl border-slate-200">
        @foreach($suppliers as $s)
          <option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-sm text-slate-600">Purchase Date</label>
      <input type="date" name="purchase_date" value="{{ old('purchase_date', now()->toDateString()) }}"
             class="mt-1 w-full rounded-xl border-slate-200" />
    </div>

    <div>
      <label class="text-sm text-slate-600">Invoice No (optional)</label>
      <input name="invoice_no" value="{{ old('invoice_no') }}"
             class="mt-1 w-full rounded-xl border-slate-200 focus:ring-slate-200" />
    </div>
  </div>

  <div class="border-t border-slate-200 pt-5">
    <div class="flex items-center justify-between">
      <div class="font-semibold">Purchase Items</div>
      <button type="button" id="addRow"
              class="px-3 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-sm">
        + Add Row
      </button>
    </div>

    <div class="mt-3 overflow-x-auto">
      <table class="w-full text-sm" id="itemsTable">
        <thead class="text-slate-500 bg-slate-50">
          <tr>
            <th class="text-left px-3 py-2">Product</th>
            <th class="text-left px-3 py-2">Qty</th>
            <th class="text-left px-3 py-2">Unit Cost</th>
            <th class="text-left px-3 py-2">Expiry Date</th>
            <th class="text-right px-3 py-2">Remove</th>
          </tr>
        </thead>
        <tbody class="divide-y" id="itemsBody">
          <!-- Initial row -->
          <tr>
            <td class="px-3 py-3">
              <select name="items[0][product_id]" class="w-full rounded-xl border-slate-200">
                @foreach($products as $p)
                  <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->unit }})</option>
                @endforeach
              </select>
            </td>
            <td class="px-3 py-3">
              <input name="items[0][qty]" type="number" step="0.001" value="1"
                     class="w-full rounded-xl border-slate-200" />
            </td>
            <td class="px-3 py-3">
              <input name="items[0][unit_cost]" type="number" step="0.01" value="0"
                     class="w-full rounded-xl border-slate-200" />
            </td>
            <td class="px-3 py-3">
              <input name="items[0][expiry_date]" type="date"
                     class="w-full rounded-xl border-slate-200" />
            </td>
            <td class="px-3 py-3 text-right">
              <button type="button" class="removeRow text-red-700 underline">Remove</button>
            </td>
          </tr>
        </tbody>
      </table>
      <p class="text-xs text-slate-500 mt-2">
        Tip: expiry date is optional. Use it for items that expire.
      </p>
    </div>
  </div>

  <div class="flex gap-3">
    <button class="px-5 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800">
      Save Purchase
    </button>
    <a href="{{ route('inventory.purchases.index') }}"
       class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100">
      Cancel
    </a>
  </div>
</form>

<script>
  (function () {
    let index = 1;
    const addRowBtn = document.getElementById('addRow');
    const body = document.getElementById('itemsBody');

    // Build product options once (Blade rendered)
    const productOptions = `{!! collect($products)->map(fn($p)=>"<option value='{$p->id}'>".e($p->name)." ({$p->unit})</option>")->implode('') !!}`;

    function rowHtml(i) {
      return `
      <tr>
        <td class="px-3 py-3">
          <select name="items[${i}][product_id]" class="w-full rounded-xl border-slate-200">
            ${productOptions}
          </select>
        </td>
        <td class="px-3 py-3">
          <input name="items[${i}][qty]" type="number" step="0.001" value="1"
                 class="w-full rounded-xl border-slate-200" />
        </td>
        <td class="px-3 py-3">
          <input name="items[${i}][unit_cost]" type="number" step="0.01" value="0"
                 class="w-full rounded-xl border-slate-200" />
        </td>
        <td class="px-3 py-3">
          <input name="items[${i}][expiry_date]" type="date"
                 class="w-full rounded-xl border-slate-200" />
        </td>
        <td class="px-3 py-3 text-right">
          <button type="button" class="removeRow text-red-700 underline">Remove</button>
        </td>
      </tr>`;
    }

    addRowBtn.addEventListener('click', () => {
      body.insertAdjacentHTML('beforeend', rowHtml(index));
      index++;
    });

    body.addEventListener('click', (e) => {
      if (e.target.classList.contains('removeRow')) {
        const rows = body.querySelectorAll('tr');
        if (rows.length <= 1) return alert('At least one item is required.');
        e.target.closest('tr').remove();
      }
    });
  })();
</script>
@endsection
