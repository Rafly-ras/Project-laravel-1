<?php

namespace App\Exports;

use App\Models\RequestOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RequestOrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    private $rowNumber = 0;

    public function collection()
    {
        return RequestOrder::with('creator', 'approver')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'Request Number',
            'Customer Name',
            'Customer Email',
            'Status',
            'Total Amount',
            'Created By',
            'Approved By',
            'Approved At',
            'Created At',
        ];
    }

    public function map($order): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $order->request_number,
            $order->customer_name,
            $order->customer_email,
            strtoupper($order->status),
            '$ ' . number_format($order->total_amount, 2),
            $order->creator->name ?? 'N/A',
            $order->approver->name ?? 'N/A',
            $order->approved_at ? $order->approved_at->format('Y-m-d H:i') : 'N/A',
            $order->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('F')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

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
