<?php

namespace App\Http\Controllers;

use App\Models\WareHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class StockSummaryController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $warehouses = WareHouse::orderBy('warehouse_name')->get();

        if ($request->ajax()) {
            $priceExpression = "COALESCE((SELECT COALESCE(pd.price, pd.selling_price, 0)
                FROM product_details pd
                WHERE pd.product_id = products.id
                ORDER BY pd.isprimary DESC, pd.id DESC LIMIT 1), 0)";

            $baseQuery = DB::table('products')
                ->crossJoin('ware_houses')
                ->leftJoin('warehouse_stocks', function ($join) {
                    $join->on('warehouse_stocks.product_id', '=', 'products.id')
                        ->on('warehouse_stocks.warehouse_id', '=', 'ware_houses.id');
                })
                ->leftJoin('unit_measures', 'unit_measures.id', '=', 'products.unit_id')
                ->where('products.active', 'Y')
                ->when($request->filled('search_text'), function ($query) use ($request) {
                    $query->where('products.product_name', 'like', '%' . $request->search_text . '%');
                })
                ->when($request->filled('warehouse_id'), fn ($query) => $query->where('ware_houses.id', $request->warehouse_id));

            $totalStockValue = (clone $baseQuery)
                ->selectRaw("COALESCE(SUM(COALESCE(warehouse_stocks.quantity, 0) * {$priceExpression}), 0) AS total_value")
                ->value('total_value');

            $query = $baseQuery->select([
                'products.id as product_id',
                'products.product_name',
                'ware_houses.id as warehouse_id',
                'ware_houses.warehouse_name',
                'unit_measures.unit_code',
                'unit_measures.unit_name',
            ])->selectRaw('COALESCE(warehouse_stocks.quantity, 0) AS available_quantity')
                ->selectRaw("{$priceExpression} AS unit_price")
                ->selectRaw("(COALESCE(warehouse_stocks.quantity, 0) * {$priceExpression}) AS stock_value");

            $response = datatables()->query($query)
                ->editColumn('available_quantity', function ($row) {
                    $quantity = rtrim(rtrim(number_format($row->available_quantity, 3, '.', ''), '0'), '.');
                    $unit = $row->unit_code ?: $row->unit_name;
                    return trim($quantity . ' ' . $unit);
                })
                ->editColumn('stock_value', fn ($row) => '₹' . number_format($row->stock_value, 2))
                ->make(true);

            $payload = $response->getData(true);
            $payload['total_stock_value'] = round((float) $totalStockValue, 2);
            return response()->json($payload);
        }

        return view('inventory.stock_summary', compact('warehouses'));
    }
}
