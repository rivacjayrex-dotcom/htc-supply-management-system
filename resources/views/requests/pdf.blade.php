<!DOCTYPE html>
<html>
<head>
    <title>Supply Request Form</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .school-name { font-size: 18px; font-bold: bold; color: #144521; }
        .doc-title { font-size: 14px; text-decoration: underline; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .footer { margin-top: 50px; }
        .sig-box { width: 30%; display: inline-block; text-align: center; margin-right: 3%; }
        .sig-line { border-top: 1px solid black; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">HOLY TRINITY COLLEGE OF GENERAL SANTOS CITY</div>
        <div>Supply Management Office (SMO)</div>
        <div class="doc-title">REQUISITION AND ISSUE SLIP</div>
    </div>

    <p><strong>Request ID:</strong> #{{ $request->id }}</p>
    <p><strong>Date:</strong> {{ $request->created_at->format('M d, Y') }}</p>
    <p><strong>Requestor:</strong> {{ $request->user->name }} (ID: {{ $request->user->school_id }})</p>

    <table>
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>Item Name & Specifications</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>{{ $request->item_name }}</strong><br>{{ $request->specifications }}</td>
                <td>{{ $request->quantity }}</td>
                <td>{{ $request->unit }}</td>
                <td>₱{{ number_format($request->unit_price, 2) }}</td>
                <td>₱{{ number_format($request->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p><strong>APPROVAL WORKFLOW:</strong></p>

        <div class="sig-box">
            <div class="sig-line"></div>
            <div>Dept. Head / Adviser</div>
            <small style="color: green;">{{ $request->status != 'pending' ? 'DIGITALLY SIGNED' : '' }}</small>
        </div>

        <div class="sig-box">
            <div class="sig-line"></div>
            <div>VP for Finance/Admin</div>
            <small style="color: green;">{{ str_contains($request->status, 'approved_vp') ? 'DIGITALLY SIGNED' : '' }}</small>
        </div>

        <div class="sig-box">
            <div class="sig-line"></div>
            <div>School President</div>
            <small style="color: green;">{{ $request->status == 'approved_president' || $request->status == 'released' ? 'DIGITALLY SIGNED' : '' }}</small>
        </div>
    </div>
</body>
</html>
