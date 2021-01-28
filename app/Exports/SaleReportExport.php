<?php

namespace App\Exports;

use App\Sale;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView as FormView;

class SaleReportExport implements FormView
{
    private $dateStart;
    private $dateEnd;
    private $sales;
    private $totalSale;

    public function __construct($dateStartp, $dateEndp)
    {
        $dateStart = date("Y-m-d H:i:s", strtotime($dateStartp));
        $dateEnd = date("Y-m-d H:i:s", strtotime($dateEndp));

        $sales = Sale::whereBetween('updated_at', [$dateStart, $dateEnd])->where('sale_status', 'paid')->get();
        $totalSale = $sales->sum('total_price');

        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->sales = $sales;
        $this->totalSale = $totalSale;
    }

    public function view(): View{
        return view('export.salereport', [
            'sales' => $this->sales,
            'totalSale' => $this->totalSale,
            'dateStart' => $this->dateStart,
            'dateEnd' => $this->dateEnd
        ]);
    }
}
