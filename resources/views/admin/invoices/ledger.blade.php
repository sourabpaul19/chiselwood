@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Ledger – Invoice {{ $invoice->invoice_no }}</h4>
        <small>
            Client: <strong>{{ $invoice->client->company_name }}</strong><br>
            Invoice Amount: ₹{{ number_format($invoice->grand_total, 2) }}
        </small>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Particulars</th>
                    <th class="text-end">Debit</th>
                    <th class="text-end">Credit</th>
                    <th class="text-end">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                        <td>{{ $row['particulars'] }}</td>
                        <td class="text-end">
                            {{ $row['debit'] ? number_format($row['debit'], 2) : '-' }}
                        </td>
                        <td class="text-end">
                            {{ $row['credit'] ? number_format($row['credit'], 2) : '-' }}
                        </td>
                        <td class="text-end">
                            {{ number_format($row['balance'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            <strong>Status:</strong>
            @if($entries->last()['balance'] == 0)
                <span class="badge bg-success">Settled</span>
            @else
                <span class="badge bg-warning">Due</span>
            @endif
        </div>
    </div>
</div>
@endsection
