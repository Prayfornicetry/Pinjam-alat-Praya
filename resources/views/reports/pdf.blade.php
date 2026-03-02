<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        .info { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4e73df; color: white; }
        .stats { margin-bottom: 20px; }
        .stat-box { display: inline-block; width: 30%; padding: 10px; margin: 5px; 
                    text-align: center; border: 1px solid #ddd; border-radius: 5px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PEMINJAMAN ALAT</h2>
        <p>Sistem E-Lending</p>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>
    
    <div class="stats">
        <div class="stat-box">
            <strong>Total: {{ $borrowings->count() }}</strong>
        </div>
        <div class="stat-box">
            <strong>Disetujui: {{ $borrowings->where('status', 'approved')->count() }}</strong>
        </div>
        <div class="stat-box">
            <strong>Dikembalikan: {{ $borrowings->where('status', 'returned')->count() }}</strong>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($borrowings as $borrowing)
            <tr>
                <td>{{ $borrowing->id }}</td>
                <td>{{ $borrowing->user->name ?? '-' }}</td>
                <td>{{ $borrowing->item->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}</td>
                <td>{{ ucfirst($borrowing->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>
</html>