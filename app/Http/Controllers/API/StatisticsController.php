<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class StatisticsController extends Controller
{

    protected int $minutes = 30;

    /**
     * Global app statistics
     *
     * @return void
     */
    public function statistics()
    {
        Gate::authorize('access-statistics');

        return [
            'all' => [
                'total_of_new_customers' => $this->totalOfCustomers(),
                'total_of_orders' => $this->totalOfOrders(),
                'total_of_orders_by_type' => $this->ordersByType(),
            ],
            'monthly' => [
                'total_of_new_customers' => $this->totalOfCustomers(Carbon::now()->subMonth()),
                'total_of_orders' => $this->totalOfOrders(Carbon::now()->subMonth()),
                'total_of_orders_by_type' => $this->ordersByType(Carbon::now()->subMonth()),
            ],
            'weekly' => [
                'total_of_new_customers' => $this->totalOfCustomers(Carbon::now()->subWeek()),
                'total_of_orders' => $this->totalOfOrders(Carbon::now()->subWeek()),
                'total_of_orders_by_type' => $this->ordersByType(Carbon::now()->subWeek()),
            ],
            'daily' => [
                'total_earnings' => $this->totalEarnings(Carbon::now()->subDay()),
                'total_of_new_customers' => $this->totalOfCustomers(Carbon::now()->subDay()),
                'total_of_orders' => $this->totalOfOrders(Carbon::now()->subDay()),
                'total_of_orders_by_type' => $this->ordersByType(Carbon::now()->subDay()),
                'mean_of_orders_by_' . $this->minutes . '_minutes' => $this->meanOfOrdersAMinute($this->minutes),
                'mean_of_paid_by_' . $this->minutes . '_minutes' => $this->meanOfPaidAMinute($this->minutes)
            ]
        ];
    }

    /**
     * Query of products by type and date
     *
     * @param Carbon|null $howOld
     * @return void
     */
    private function ordersByType(Carbon $howOld = null)
    {
        /** @var Builder $builder */
        $builder = Order::selectRaw('COUNT(*) AS delivered, products.id, products.name, products.type')
            ->rightJoin('order_items', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.type');

        if ($howOld != null) {
            /** @var Builder $builder */
            $builder = $builder->whereBetween(
                'orders.created_at',
                [
                    $howOld,
                    Carbon::now()
                ]
            );
        }

        return $builder->get();
    }

    /**
     * Total os customers within a period
     *
     * @param Carbon|null $howOld
     * @return int
     */
    private function totalOfCustomers(Carbon $howOld = null)
    {
        $builder = Customer::query();

        if ($howOld != null) {
            /** @var Builder $builder */
            $builder = $builder->whereBetween(
                'customers.created_at',
                [$howOld, Carbon::now()]
            );
        }

        return $builder->get()->count();
    }

    /**
     * Total of orders within a period
     *
     * @param Carbon|null $howOld
     * @return int
     */
    private function totalOfOrders(Carbon $howOld = null)
    {
        /** @var Builder $builder */
        $builder = Order::query();

        if ($howOld) {
            /** @var Builder $builder */
            $builder = $builder->whereBetween('created_at', [$howOld, Carbon::now()]);
        }

        return $builder->get()->count();
    }

    /**
     * Mean of orders by minute (default a day)
     *
     * @return int
     */
    private function meanOfOrdersAMinute(int $minutes = 1440)
    {
        return round(($this->totalOfOrders(Carbon::now()->subMinutes($minutes)) ?? 0) / $minutes, 2);
    }

    /**
     * Mean of paid by minute (default a day)
     *
     * @param integer $minutes
     * @return int
     */
    private function meanOfPaidAMinute(int $minutes = 1440)
    {
        $items = Order::whereBetween('created_at', [Carbon::now()->subMinutes($minutes), Carbon::now()])->sum('total_paid');
        return round($items / $minutes, 2);
    }

    /**
     * Totoal pais within givern time
     *
     * @param Carbon|null $howOld
     * @return float
     */
    private function totalEarnings(Carbon $howOld = null)
    {
        $builder = Order::whereBetween('created_at', [$howOld, Carbon::now()])->sum('total_paid');
        return $builder;
    }
}
