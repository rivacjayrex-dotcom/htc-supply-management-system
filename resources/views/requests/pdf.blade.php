<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>HTC Request Form</title>
    <style>
        @page { margin: 20px; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; line-height: 1.2; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; }

        .no-border { border: none !important; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .header-container td { border: none; vertical-align: middle; }
        .school-name { font-size: 13px; font-weight: bold; }
        .form-title { font-size: 11px; font-weight: bold; margin-top: 5px; }
        .control-box { border: 2px solid #000; padding: 5px; width: 150px; text-align: center; }

        .items-table th { background: #f0f0f0; font-size: 9px; }
        .items-table td { height: 18px; }

        .sig-label { font-size: 9px; vertical-align: top; width: 80px; border-right: none; }
        .sig-value { border-left: none; }
        .digital-stamp { color: #185b3b; font-size: 8px; font-weight: bold; font-style: italic; }

        .footer-note { font-size: 9px; position: fixed; bottom: 10px; right: 20px; }
        .bg-light { background-color: #f8f9fa; }
    </style>
</head>
<body>

    <!-- HEADER SECTION -->
    <table class="header-container">
        <tr>
            <td width="15%"><img src="{{ public_path('images/android-chrome-512x5122.png') }}" width="65"></td>
            <td width="60%" class="text-center">
                <div class="school-name">HOLY TRINITY COLLEGE OF GENERAL SANTOS CITY</div>
                <div>Supply Management Office</div>
                <div class="form-title">
                    SUPPLY AND EQUIPMENT {{ $request->request_type == 'major' ? 'REQUEST' : 'MINOR REQUEST' }} FORM
                </div>
                <div style="font-size: 8px; font-style: italic;">
                    (For Request of {{ $request->request_type == 'major' ? 'MORE THAN P1,000' : 'NOT MORE THAN P1,000' }})
                </div>
            </td>
            <td width="25%">
                <div class="control-box" style="float: right;">
                    <div style="font-size: 8px;">Control No.</div>
                    <div style="font-size: 16px; font-weight: bold; letter-spacing: 2px;">{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div style="border-top: 1px solid #000; margin-top: 3px; font-size: 8px;">Date: {{ $request->created_at->format('m/d/Y') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- REQUESTOR INFO SECTION -->
    <table style="margin-top: 10px;">
        <tr>
            <td class="sig-label">Signature:</td>
            <td class="sig-value" width="35%"><span class="digital-stamp">✓ Verified Electronically</span></td>
            <td class="sig-label">Signature:</td>
            <td class="sig-value"><span class="digital-stamp">{{ in_array($request->status, ['approved_dept', 'approved_vp', 'approved_provost', 'approved_president', 'released']) ? '✓ Signed Digitally' : '' }}</span></td>
        </tr>
        <tr>
            <td class="sig-label">Requested by:</td>
            <td class="sig-value" class="fw-bold">{{ $request->user->name }}</td>
            <td class="sig-label">Department Head:</td>
            <td class="sig-value">&nbsp;</td>
        </tr>
        <tr>
            <td class="sig-label">Department:</td>
            <td class="sig-value">{{ $request->user->department ?? 'General' }}</td>
            <td class="sig-label">Date:</td>
            <td class="sig-value">{{ $request->created_at->format('m/d/Y') }}</td>
        </tr>
    </table>

    <table style="margin-top: -1px;">
        <tr>
            <td class="sig-label" style="height: 30px;">Purpose:</td>
            <td class="sig-value">{{ $request->remarks ?? 'Office/Departmental Use' }}</td>
            <td width="25%" class="text-center">
                <div class="small">Request:</div>
                <div style="font-size: 8px;">[ X ] Supply &nbsp;&nbsp; [ ] Monetary</div>
            </td>
        </tr>
    </table>

    <!-- ITEMS TABLE -->
    <table class="items-table" style="margin-top: 10px;">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="15%">Item</th>
                <th width="35%">Specifications</th>
                <th width="10%">Qty</th>
                <th width="10%">Unit</th>
                <th width="12%">Unit Price</th>
                <th width="13%">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($request->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->item_name }}</td>
                <td><small>{{ $item->specifications }}</small></td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-center">{{ $item->unit }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach

            <!-- FIXED ROW FILLING LOGIC -->
            @php
                $maxRows = ($request->request_type == 'major' ? 9 : 14);
                $remaining = $maxRows - $request->items->count();
            @endphp

            @for($i = 0; $i < $remaining; $i++)
            <tr>
                <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
            </tr>
            @endfor

            <tr class="fw-bold">
                <td colspan="6" class="text-right">Total:</td>
                <td class="text-right">₱{{ number_format($request->grand_total, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- SIGNATURE BOXES -->
    @if($request->request_type == 'major')
        <table style="margin-top: 10px;">
            <tr>
                <td width="50%" class="text-center uppercase fw-bold bg-light" style="padding: 2px; font-size: 8px;">Processed by:</td>
                <td width="50%" class="text-center uppercase fw-bold bg-light" style="padding: 2px; font-size: 8px;">Recommending Approval:</td>
            </tr>
            <tr>
                <td>
                    <div style="height: 30px;"><span class="digital-stamp">{{ $request->status == 'released' ? '✓ RELEASED' : '' }}</span></div>
                    <div class="fw-bold">GERALDINE T. GULANE</div>
                    <div style="font-size: 8px;">SMO In-charge</div>
                </td>
                <td>
                    <div style="height: 30px;"><span class="digital-stamp">{{ in_array($request->status, ['approved_provost', 'approved_president', 'released']) ? '✓ APPROVED' : '' }}</span></div>
                    <div class="fw-bold">ATTY. JOSEMAR T. ALBANO</div>
                    <div style="font-size: 8px;">Provost</div>
                </td>
            </tr>
            <tr>
                <td class="text-center uppercase fw-bold bg-light" style="padding: 2px; font-size: 8px;">Noted By:</td>
                <td class="text-center uppercase fw-bold bg-light" style="padding: 2px; font-size: 8px;">Approved by:</td>
            </tr>
            <tr>
                <td>
                    <div style="height: 30px;"><span class="digital-stamp">{{ in_array($request->status, ['approved_vp', 'approved_provost', 'approved_president', 'released']) ? '✓ NOTED' : '' }}</span></div>
                    <div class="fw-bold">DR. RUBY L. TAMAYO</div>
                    <div style="font-size: 8px;">VP Admin</div>
                </td>
                <td>
                    <div style="height: 30px;"><span class="digital-stamp">{{ in_array($request->status, ['approved_president', 'released']) ? '✓ SIGNED' : '' }}</span></div>
                    <div class="fw-bold">DR. REY T. ALBANO</div>
                    <div style="font-size: 8px;">President</div>
                </td>
            </tr>
        </table>
    @else
        <table style="margin-top: 10px;">
            <tr>
                <td width="50%" class="text-center uppercase fw-bold bg-light" style="padding: 2px; font-size: 8px;">Processed by:</td>
                <td width="50%" class="text-center uppercase fw-bold bg-light" style="padding: 2px; font-size: 8px;">Approved by:</td>
            </tr>
            <tr>
                <td>
                    <div style="height: 30px;"><span class="digital-stamp">{{ $request->status == 'released' ? '✓ RELEASED' : '' }}</span></div>
                    <div class="fw-bold">VEVERLY EMBALSADO</div>
                    <div style="font-size: 8px;">SMO In-charge</div>
                </td>
                <td>
                    <div style="height: 30px;"><span class="digital-stamp">{{ in_array($request->status, ['approved_vp', 'released']) ? '✓ SIGNED' : '' }}</span></div>
                    <div class="fw-bold">MARYLONE A. CANLAS</div>
                    <div style="font-size: 8px;">VP for Finance</div>
                </td>
            </tr>
        </table>
    @endif

    <div class="footer-note fw-bold">
        Document ID: {{ $request->request_type == 'major' ? 'QR-HTSM-040' : 'QR-HTSM-041' }}
    </div>

</body>
</html>
