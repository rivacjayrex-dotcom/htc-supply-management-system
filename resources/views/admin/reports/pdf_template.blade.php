<!DOCTYPE html>
<html>
<head>
    <title>Institutional Procurement Report</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; border-bottom: 2px solid #185b3b; padding-bottom: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-transform: uppercase; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0; color: #185b3b;">HOLY TRINITY COLLEGE OF GENERAL SANTOS CITY</h2>
        <p style="margin:5px 0;">Supply Management Office - Master Procurement Report</p>
        <small>Generated on: {{ now()->format('F d, Y h:i A') }}</small>
    </div>

    <div style="margin-bottom: 15px;">
        <strong>Report Filters:</strong>
        Dept: {{ $filters['dept'] ?? 'All' }} |
        Status: {{ $filters['status'] ?? 'All' }} |
        Period: {{ $filters['date_from'] ?? 'Start' }} to {{ $filters['date_to'] ?? 'Now' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Control #</th>
                <th>Requestor / Department</th>
                <th>Items</th>
                <th align="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requisitions as $req)
            <tr>
                <td>{{ $req->created_at->format('m/d/Y') }}</td>
                <td>{{ str_pad($req->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $req->user->name }}<br><small>{{ $req->user->department }}</small></td>
                <!-- We add '??' to provide a fallback name if the items are missing -->
                <td>
                    {{ $req->items->first()->item_name ?? 'General Procurement' }}
                    @if($req->items->count() > 1)
                        <br><small>(+{{ $req->items->count() - 1 }} other items)</small>
                    @endif
                </td>
                <td align="right">₱{{ number_format($req->grand_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #eee;">
                <td colspan="4" align="right">TOTAL INSTITUTIONAL EXPENDITURE:</td>
                <td align="right">₱{{ number_format($requisitions->sum('grand_total'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Page 1 of 1 - HTC Supply Management System Audit Tool
    </div>
</body>
</html>
