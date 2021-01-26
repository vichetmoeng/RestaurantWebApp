<?php

namespace App\Http\Controllers\Cashier;

use App\Category;
use App\Http\Controllers\Controller;
use App\Menu;
use App\Sale;
use App\SaleDetail;
use App\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{
    // page of cashier
    public function index() {
        $categories = Category::all();
        return view('cashier.index')->with('categories', $categories);
    }

    public function getTable() {
        $tables = Table::all();
        $html = '';
        foreach ($tables as $table) {
            $html .= '<div class="col-md-2 mb-4">';
            $html .= '<button class="btn btn-primary btn-table" data-id="'.$table->id.'" data-name="'.$table->name.'">
                          <img class="img-fluid" src="'.url('/images/table.svg').'"/> <br>';
            if ($table->status == "available") {
                $html .= '<span class="badge badge-success">'.$table->name.'</span>';
            } else {
                $html .= '<span class="badge badge-danger">'.$table->name.'</span>';
            }

            $html .= '</button>';
            $html .= '</div>';
        }

        return $html;
    }

    public function getMenuByCategory($category_id) {
        $menus = Menu::where('category_id', $category_id)->get();
        $html = '';
        foreach ($menus as $menu) {
            $html .= '
                <div class="col-md-3 text-center">
                    <a class="btn btn-outline-secondary btn-menu" data-id="'.$menu->id.'">
                        <img class="img-fluid" src="'.url('/menu_images/'.$menu->image).'"><br>
                        '.$menu->name.'<br>
                        $'.number_format($menu->price).'
                    </a>
                </div>
            ';
        }

        return $html;
    }

    public function orderFood(Request $request) {
        $menu = Menu::find($request->menu_id);
        $tableId = $request->table_id;
        $tableName = $request->table_name;

        $sale = Sale::where('table_id', $tableId)->where('sale_status', 'unpaid')->first();

        // if no sale for the selected table then create a new sale record
        if (!$sale) {
            $user = Auth::user();
            $sale = new Sale();
            $sale->table_id = $tableId;
            $sale->table_name = $tableName;
            $sale->user_id = $user->id;
            $sale->user_name = $user->name;
            $sale->save();
            $saleId = $sale->id;
            // update table status
            $table = Table::find($tableId);
            $table->status = "unavailable";
            $table->save();
        } else {
            $saleId = $sale->id;
        }

        //add ordered menu to sale_detail table
        $saleDetail = new SaleDetail();
        $saleDetail->sale_id = $saleId;
        $saleDetail->menu_id = $menu->id;
        $saleDetail->menu_name = $menu->name;
        $saleDetail->menu_price = $menu->price;
        $saleDetail->quantity = $request->quantity;
        $saleDetail->save();

        // update total price in sale table
        $sale->total_price = $sale->total_price + ($request->quantity * $menu->price);
        $sale->save();

        return $html = $this->getSaleDetails($saleId);
    }

    public function getSaleDetailsByTable($table_id) {
        $sale = Sale::where('table_id', $table_id)->where('sale_status', 'unpaid')->first();

        $html = '';

        if ($sale) {
            $saleId = $sale->id;
            $html .= $this->getSaleDetails($saleId);
        } else {
            $html .= "Not Found Any Sale Details for the selected table!";
        }

        return $html;
    }

    private function getSaleDetails($saleId) {
        // list all sale_details
        $html = '<p>Sale ID: '.$saleId.'</p>';
        $saleDetails = SaleDetail::where('sale_id', $saleId)->get();
        $html .= '<div class="table-responsive-md" style="overflow-y: scroll; height: 400px; border: 1px solid #343A40;">
                    <table class="table table-striped table-dark">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Menu</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Price</th>
                                <th scope="col">Total</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>';
        $showBtnPayment = true;
        foreach ($saleDetails as $saleDetail) {

            $html .= '
                                    <tr>
                                        <td>'.$saleDetail->menu_id.'</td>
                                        <td>'.$saleDetail->menu_name.'</td>
                                        <td>'.$saleDetail->quantity.'</td>
                                        <td>'.$saleDetail->menu_price.'</td>
                                        <td>'.$saleDetail->menu_price * $saleDetail->quantity.'</td>';
            if ($saleDetail->status == "noConfirm") {
                $showBtnPayment = false;
                $html .= '<td><a data-id="'.$saleDetail->id.'" class="btn btn-danger btn-delete-saledetail"><i class="far fa-trash-alt"></i> </a> </td>';
            } else {
                $html .= '<td><i class="fas fa-check-circle"></i> </td>';
            }

                                    $html .= '</tr>';
        }

        $html .='</tbody>
                    </table>
                  </div>';

        $sale = Sale::find($saleId);
        $html .= '<hr>';
        $html .= '<h3>Total Amount: $'.number_format($sale->total_price).'</h3>';

        if ($showBtnPayment) {
            $html .= '<button data-id="'.$saleId.'" class="btn btn-success btn-block btn-payment">Payment</button>';
        } else {
            $html .= '<button data-id="'.$saleId.'" class="btn btn-warning btn-block btn-confirm-order">Confirm Order</button>';
        }


        return $html;
    }

    public function confirmOrderStatus(Request $request) {
        $saleId = $request->sale_id;
        $saleDetails = SaleDetail::where('sale_id', $saleId)->update(['status' => 'confirm']);
        $html = $this->getSaleDetails($saleId);

        return $html;
    }

    public function deleteSaleDetail(Request $request) {
        $saleDetailID = $request->saleDetail_id;
        $saleDetail = SaleDetail::find($saleDetailID);

        $saleId = $saleDetail->sale_id;
        $menuPrice = ($saleDetail->menu_price * $saleDetail->quantity);
        $saleDetail->delete();

        // update total price
        $sale = Sale::find($saleId);
        $sale->total_price = $sale->total_price - $menuPrice;
        $sale->save();

        // check if there any saledetail having the sale id
        $saleDetails = SaleDetail::where('sale_id', $saleId)->first();
        if ($saleDetails) {
            $html = $this->getSaleDetails($saleId);
        } else {
            $html = "NOt Found Any Sale Details for the Selected Table";
        }

        return $html;

    }

}
