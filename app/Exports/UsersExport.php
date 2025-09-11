<?php

namespace App\Exports;

use Dompdf\Dompdf;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UsersExport implements FromView, ShouldAutoSize
{
    protected $users;
    protected $dateRange;
    protected $startDate;
    protected $endDate;

    public function __construct($users, $dateRange, $startDate, $endDate)
    {
        $this->users = $users;
        $this->dateRange = $dateRange;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        return view('pdf.rekap_absen', [
            'users' => $this->users,
            'dateRange' => $this->dateRange,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }


}