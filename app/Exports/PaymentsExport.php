<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    private $rowNumber = 0;

    public function collection()
    {
        return Payment::with('invoice', 'creator')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'Payment Number',
            'Ref Invoice',
            'Amount',
            'Method',
            'Ref Number',
            'Paid At',
            'Created By',
        ];
    }

    public function map($payment): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $payment->payment_number,
            $payment->invoice->invoice_number ?? 'N/A',
            '$ ' . number_format($payment->amount, 2),
            strtoupper($payment->payment_method),
            $payment->reference_number ?? 'N/A',
            $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : 'N/A',
            $payment->creator->name ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('D')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

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
