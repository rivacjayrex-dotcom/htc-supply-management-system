<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>HTC Institutional Procurement Report</title>
    <style>
        @page { margin: 25px; }
        body { font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif; font-size: 9px; color: #1e293b; line-height: 1.2; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px; }
        th { background-color: #f1f5f9; font-weight: bold; text-transform: uppercase; font-size: 8px; color: #334155; }
        .header-table td { border: none; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-muted { color: #64748b; }
        .badge { padding: 2px 5px; font-size: 7px; text-transform: uppercase; font-weight: bold; border-radius: 3px; }
        .footer-table td { border: none; padding-top: 25px; }
        .signature-line { border-top: 1px solid #000; width: 80%; margin: 30px auto 3px auto; }
    </style>
</head>
<body>

    <!-- INSTITUTIONAL HEADER -->
    <table class="header-table">
        <tr>
            <td width="15%" class="text-center">
                <img src="{{ public_path('images/school_seal.png') }}" width="55" onerror="this.src='{{ public_path('images/android-chrome-512x5122.png') }}'">
            </td>
            <td width="70%" class="text-center">
                <div style="font-size: 13px; font-weight: bold; color: #185b3b;">HOLY TRINITY COLLEGE OF GENERAL SANTOS CITY</div>
                <div style="font-size: 10px; font-weight: bold; margin-top: 2px;">SUPPLY MANAGEMENT OFFICE (SMO)</div>
                <div style="font-size: 11px; font-weight: bold; margin-top: 5px; text-transform: uppercase;">
                    OFFICIAL INSTITUTIONAL {{ strtoupper(str_replace('_', ' ', $reportType)) }} AUDIT REPORT
                </div>
                <div style="font-size: 8px; color: #64748b; margin-top: 2px;">
                    Generated on: {{ $generatedDate }} • Prepared by: {{ $generatedBy }}
                </div>
            </td>
            <td width="15%" class="text-right">
                <div style="border: 1px solid #000; padding: 4px; text-align: center; font-size: 8px;">
                    <strong>CONTROL REF</strong><br>
                    HTC-SMO-{{ date('Ymd') }}
                </div>
            </td>
        </tr>
    </table>

    <hr style="border: none; border-top: 1px solid #185b3b; margin-top: 10px;">

    <!-- DATA PRESENTATION -->
    @if($reportType === 'requisitions')
        <table>
            <thead>
                <tr>
                    <th width="5%">Req #</th>
                    <th width="10%">Date</th>
                    <th width="15%">Requestor</th>
                    <th width="8%">Dept</th>
                    <th width="8%">Tier</th>
                    <th width="34%">Item Breakdown & Specifications</th>
                    <th width="10%">Status</th>
                    <th width="10%" class="text-right">Total (₱)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $r)
                <tr>
                    <td class="text-center">#{{ str_pad($r->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $r->created_at->format('m/d/Y') }}</td>
                    <td><strong>{{ $r->user->name }}</strong></td>
                    <td class="text-center">{{ $r->user->department }}</td>
                    <td class="text-center">{{ strtoupper($r->request_type) }}</td>
                    <td>
                        @foreach($r->items as $item)
                            <div>• {{ $item->item_name }} ({{ $item->quantity }} {{ $item->unit }})
                                @if(in_array('specs', $selectedColumns) && $item->specifications)
                                    <small class="text-muted">- {{ $item->specifications }}</small>
                                @endif
                            </div>
                        @endforeach
                        @if(in_array('remarks', $selectedColumns) && $r->remarks)
                            <div class="text-muted" style="font-style: italic; font-size: 7px; margin-top: 2px;">"{{ $r->remarks }}"</div>
                        @endif
                    </td>
                    <td class="text-center">{{ strtoupper(str_replace('_', ' ', $r->status)) }}</td>
                    <td class="text-right">₱{{ number_format($r->grand_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="7" class="text-right">AGGREGATE GRAND TOTAL:</td>
                    <td class="text-right">₱{{ number_format($totalValue, 2) }}</td>
                </tr>
            </tfoot>
        </table>

    @elseif($reportType === 'inventory')
        <table>
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="25%">Item Name & Brand</th>
                    <th width="18%">Category</th>
                    <th width="22%">Specifications</th>
                    <th width="10%" class="text-center">Current Stock</th>
                    <th width="10%" class="text-right">Unit Price</th>
                    <th width="10%" class="text-right">Asset Valuation</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $item->item_name }}</strong><br><small class="text-muted">{{ $item->brand }}</small></td>
                    <td>{{ $item->category }}</td>
                    <td><small>{{ $item->specifications }}</small></td>
                    <td class="text-center">{{ $item->quantity }} {{ $item->unit }}</td>
                    <td class="text-right">₱{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">₱{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="6" class="text-right">TOTAL INVENTORY ASSET VALUE:</td>
                    <td class="text-right">₱{{ number_format($totalValue, 2) }}</td>
                </tr>
            </tfoot>
        </table>

    @elseif($reportType === 'dept_spending')
        <table>
            <thead>
                <tr>
                    <th width="10%">No.</th>
                    <th width="35%">College Department</th>
                    <th width="20%" class="text-center">Total Completed Requisitions</th>
                    <th width="15%" class="text-right">Average Order Value</th>
                    <th width="20%" class="text-right">Total Expenditure</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $idx => $dept)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $dept->department }}</strong></td>
                    <td class="text-center">{{ $dept->total_requests }}</td>
                    <td class="text-right">₱{{ number_format($dept->avg_cost, 2) }}</td>
                    <td class="text-right">₱{{ number_format($dept->total_spent, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td colspan="4" class="text-right">TOTAL INSTITUTIONAL DISBURSEMENTS:</td>
                    <td class="text-right">₱{{ number_format($totalValue, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- SIGN-OFF VERIFICATION BLOCK -->
    <table class="footer-table">
        <tr>
            <td width="33%" class="text-center">
                Prepared by:
                <div class="signature-line"></div>
                <strong>{{ Auth::user()->name }}</strong><br>
                <small class="text-muted">SMO In-Charge</small>
            </td>
            <td width="33%" class="text-center">
                Reviewed by:
                <div class="signature-line"></div>
                <strong>MARYLONE A. CANLAS</strong><br>
                <small class="text-muted">VP for Finance</small>
            </td>
            <td width="33%" class="text-center">
                Noted by:
                <div class="signature-line"></div>
                <strong>DR. REY T. ALBANO</strong><br>
                <small class="text-muted">School President</small>
            </td>
        </tr>
    </table>

</body>
</html>
