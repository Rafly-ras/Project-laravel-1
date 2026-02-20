<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    private $rowNumber = 0;

    public function collection()
    {
        return Invoice::with('salesOrder')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'Invoice Number',
            'Ref Sales Order',
            'Customer Name',
            'Total Amount',
            'Paid Amount',
            'Remaining Balance',
            'Status',
            'Due Date',
            'Issued At',
        ];
    }

    public function map($invoice): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $invoice->invoice_number,
            $invoice->salesOrder->sales_number ?? 'N/A',
            $invoice->salesOrder->customer_name ?? 'N/A',
            '$ ' . number_format($invoice->total_amount, 2),
            '$ ' . number_format($invoice->paid_amount, 2),
            '$ ' . number_format($invoice->remaining_balance, 2),
            strtoupper($invoice->status),
            $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'N/A',
            $invoice->issued_at ? $invoice->issued_at->format('Y-m-d H:i') : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('F')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('G')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ]
            ],
        ];
    }
}
