<?php

namespace App\Exports;

use App\Models\Borrowing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class BorrowingsReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;
    
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    
    public function collection()
    {
        return Borrowing::with(['user', 'item.category', 'approvedBy'])
            ->whereBetween('borrow_date', [$this->startDate, $this->endDate])
            ->get();
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'Tanggal Pinjam',
            'Tanggal Kembali',
            'Tanggal Aktual',
            'Peminjam',
            'Email',
            'Alat',
            'Kode',
            'Kategori',
            'Status',
            'Catatan',
            'Disetujui Oleh',
            'Dibuat',
        ];
    }
    
    public function map($borrowing): array
    {
        return [
            $borrowing->id,
            $borrowing->borrow_date,
            $borrowing->return_date,
            $borrowing->actual_return_date ?? '-',
            $borrowing->user->name ?? '-',
            $borrowing->user->email ?? '-',
            $borrowing->item->name ?? '-',
            $borrowing->item->code ?? '-',
            $borrowing->item->category->name ?? '-',
            ucfirst($borrowing->status),
            $borrowing->notes ?? '-',
            $borrowing->approvedBy->name ?? '-',
            $borrowing->created_at->format('Y-m-d H:i'),
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
    
    public function title(): string
    {
        return 'Laporan Peminjaman';
    }
}