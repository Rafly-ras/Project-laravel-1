<?php

namespace App\Exports;

use App\Models\RequestOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterO2CExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private $rowNumber = 0;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Load all request orders with their full relationship chain
        return RequestOrder::with([
            'salesOrder.invoice.payments',
            'creator',
            'approver'
        ])->latest()->get();
    }

    public function headings(): array
    {
        return [
            // Row 1: Group Markers
            [
                'NO.',
                'REQUEST ORDER',
                '',
                '',
                '',
                '',
                'SALES ORDER',
                '',
                '',
                '',
                'INVOICE',
                '',
                '',
                '',
                'PAYMENT SUMMARY',
                '',
                '',
                ''
            ],
            // Row 2: Detailed Headers
            [
                'No.',
                'RO Number',
                'Customer',
                'RO Status',
                'RO Amount',
                'Requested Date',
                
                'SO Number',
                'SO Status',
                'SO Amount',
                'Confirmed Date',
                
                'Invoice Number',
                'Invoice Status',
                'Due Date',
                'Total Billed',
                
                'Total Paid',
                'Remaining Balance',
                'Last Payment Date',
                'Last Payment Method'
            ]
        ];
    }

    public function map($ro): array
    {
        $this->rowNumber++;
        $so = $ro->salesOrder;
        $inv = $so ? $so->invoice : null;
        $payments = $inv ? $inv->payments : collect();
        $lastPayment = $payments->sortByDesc('paid_at')->first();

        return [
            $this->rowNumber,
            // RO Info
            $ro->request_number,
            $ro->customer_name,
            strtoupper($ro->status),
            number_format($ro->total_amount, 2),
            $ro->created_at->format('Y-m-d'),

            // SO Info
            $so ? $so->sales_number : 'N/A',
            $so ? strtoupper($so->status) : 'N/A',
            $so ? number_format($so->total_amount, 2) : '0.00',
            $so && $so->confirmed_at ? $so->confirmed_at->format('Y-m-d') : 'N/A',

            // Invoice Info
            $inv ? $inv->invoice_number : 'N/A',
            $inv ? strtoupper($inv->status) : 'N/A',
            $inv && $inv->due_date ? $inv->due_date->format('Y-m-d') : 'N/A',
            $inv ? number_format($inv->total_amount, 2) : '0.00',

            // Payments Summary
            $inv ? number_format($inv->paid_amount, 2) : '0.00',
            $inv ? number_format($inv->remaining_balance, 2) : '0.00',
            $lastPayment ? $lastPayment->paid_at->format('Y-m-d') : 'N/A',
            $lastPayment ? strtoupper($lastPayment->payment_method) : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge No. vertically
        $sheet->mergeCells('A1:A2');
        
        // Merge cells for group markers (shifted by 1)
        $sheet->mergeCells('B1:F1'); // Request Order
        $sheet->mergeCells('G1:J1'); // Sales Order
        $sheet->mergeCells('K1:N1'); // Invoice
        $sheet->mergeCells('O1:R1'); // Payment

        // Numerical columns alignment (Right) - shifted by 1
        $rightAligned = ['A', 'E', 'I', 'N', 'O', 'P'];
        foreach ($rightAligned as $col) {
            $sheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        return [
            // Style for Marker Row (Indigo)
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '312E81']
                ]
            ],
            // Style for Detail Headers (Lighter Indigo)
            2 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ]
            ],
        ];
    }
}
