<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>HTC Requisition Slip #{{ $request->id }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; font-size: 12px; }
        .header-table { width: 100%; border-bottom: 2px solid #185b3b; padding-bottom: 10px; margin-bottom: 20px; }
        .school-name { font-size: 18px; font-weight: bold; color: #185b3b; }
        .doc-type { background: #185b3b; color: white; padding: 5px 15px; display: inline-block; font-weight: bold; margin-top: 10px; }

        .info-section { width: 100%; margin-bottom: 20px; }
        .info-box { width: 48%; display: inline-block; vertical-align: top; }

        table.items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.items-table th { background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; text-align: left; text-transform: uppercase; font-size: 10px; }
        table.items-table td { border: 1px solid #dee2e6; padding: 10px; }

        .signature-section { width: 100%; margin-top: 40px; }
        .sig-box { width: 30%; display: inline-block; text-align: center; font-size: 10px; }
        .sig-line { border-top: 1px solid #000; width: 80%; margin: 40px auto 5px auto; }
        .digital-stamp { color: #185b3b; font-weight: bold; font-style: italic; font-size: 9px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="70">
                <!-- If you have the logo in your public/images folder, use this path -->
                <img src="{{ public_path('images/android-chrome-512x5122.png') }}" width="60">
            </td>
            <td>
                <div class="school-name">HOLY TRINITY COLLEGE OF GENERAL SANTOS CITY</div>
                <div>Supply Management Office (SMO)</div>
                <div class="doc-type">REQUISITION AND ISSUE SLIP (RIS)</div>
            </td>
            <td align="right" valign="top">
                <strong>No: {{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}</strong><br>
                Date: {{ $request->created_at->format('M d, Y') }}
            </td>
        </tr>
    </table>

    <div class="info-section">
        <div class="info-box">
            <strong>REQUESTOR DETAILS:</strong><br>
            Name: {{ $request->user->name }}<br>
            Department: {{ $request->user->department ?? 'General' }}<br>
            School ID: {{ $request->user->school_id }}
        </div>
        <div class="info-box" align="right">
            <strong>REQUISITION TIER:</strong><br>
            <span style="text-transform: uppercase;">{{ $request->request_type }}</span><br>
            Status: {{ strtoupper($request->status) }}
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item Description & Specifications</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th align="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($request->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->item_name }}</strong><br>
                    <small>{{ $item->specifications }}</small>
                </td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->unit }}</td>
                <td align="right">₱{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" align="right"><strong>GRAND TOTAL</strong></td>
                <td align="right" style="color: #185b3b;"><strong>₱{{ number_format($request->grand_total, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-section">
        <div class="sig-box">
            <div class="sig-line"></div>
            <div>Dept. Head / Adviser</div>
            <div class="digital-stamp">{{ in_array($request->status, ['approved_dept', 'approved_vp', 'approved_provost', 'approved_president', 'released']) ? '✓ DIGITALLY SIGNED' : '' }}</div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div>{{ $request->request_type == 'minor' ? 'VP for Finance' : 'VP for Admin' }}</div>
            <div class="digital-stamp">{{ in_array($request->status, ['approved_vp', 'approved_provost', 'approved_president', 'released']) ? '✓ DIGITALLY SIGNED' : '' }}</div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div>{{ $request->request_type == 'minor' ? 'SMO Receipt' : 'School President' }}</div>
            <div class="digital-stamp">{{ in_array($request->status, ['approved_president', 'released']) ? '✓ DIGITALLY SIGNED' : '' }}</div>
        </div>
    </div>

    <div style="margin-top: 30px; border: 1px dashed #ccc; padding: 10px; font-size: 10px; text-align: center;">
        This is a system-generated document from the HTC Web-Based Integrated Supply Management System.
    </div>
</body>
</html>
