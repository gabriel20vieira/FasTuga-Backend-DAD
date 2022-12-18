<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Types\UserType;
use Illuminate\Support\Carbon;
use App\Models\Types\ProductType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Types\OrderStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Builder;

class StatisticsController extends Controller
{

    protected int $minutes = 30;

    /**
     * Statistics endpoint
     *
     * @return Response
     */
    public function statistics()
    {
        Gate::authorize('access-statistics');

        switch (auth('api')->user('api')->type) {
            case UserType::MANAGER->value:
                return $this->manager();
            case UserType::CUSTOMER->value:
                return $this->customer();
            case UserType::DELIVERY->value:
                return $this->delivery();
            case UserType::CHEF->value:
                return $this->chef();
        }

        return [
            'message' => 'No statistics found for this user.'
        ];
    }

    private function chef()
    {
        return [
            'items_cooked' => auth('api')->user()->prepared->count(),
            'most_cooked' =>  OrderItem::selectRaw('products.name, products.type, products.photo_url, COUNT(*) AS times_cooked')
                ->leftJoin('products', 'products.id', 'order_items.product_id')
                ->where('order_items.preparation_by', '=', auth('api')->user()->id)
                ->groupBy('products.type')
                ->get()
        ];
    }

    /**
     * Chef statistics
     *
     * @return array
     */
    private function delivery()
    {
        return [
            'orders_delivered' => auth('api')->user()->delivered->count(),
            'orders_last_hour' => auth('api')->user()->delivered()->whereBetween('updated_at', [Carbon::now()->subHour(), Carbon::now()])->get()->count()
        ];
    }

    /**
     * Statistics for customer
     *
     * @return array
     */
    private function customer()
    {
        $total_of_orders = auth('api')->user()->orders->count();
        $mean_paid_per_order = round(auth('api')->user()->orders->sum('total_paid') / $total_of_orders, 2);
        $most_chosen_product_per_type = $this->ordersByType(function ($builder) {
            /** @var Builder $builder */
            return $builder->where('orders.customer_id', '=', auth('api')->user()->customer->id);
        });
        $most_points_on_order = auth('api')->user()->orders->max('points_gained');
        $biggest_discount = auth('api')->user()->orders->max('total_paid_with_points');

        return [
            'total_of_orders' => $total_of_orders,
            'mean_of_paid_per_order' => $mean_paid_per_order,
            'most_chosen_product_per_type' => $most_chosen_product_per_type,
            'most_points_on_order' => $most_points_on_order,
            'biggest_discount' => $biggest_discount
        ];
    }

    /**
     * Manager collection of staatistics
     *
     * @return array
     */
    private function manager()
    {
        return
            [
                'all' => [
                    'total_of_new_customers' => $this->totalOfCustomers(),
                    'total_of_orders_by_type' => $this->ordersByType(),
                    'employee_with_most_deliveries' => $this->employeeWithMostDeliveries(),
                    'chef_with_most_orders' => $this->chefWithMostOrders(),
                    'best_selling_products_by_type' => $this->bestSellingProductsByType(),
                    'average_paid_value_per_order' => $this->averagePaidValuePerOrder(),
                    'order_with_highest_paid_value' => $this->orderWithHighestPaidValue(),
                    'new_users_by_month' => $this->newUsersByMonth(),
                ],
                'monthly' => [
                    'average_paid_value_per_order' => $this->averagePaidValuePerOrder(Carbon::now()->subMonth()),
                    'order_with_highest_paid_value' => $this->orderWithHighestPaidValue(Carbon::now()->subMonth()),
                ],
                'weekly' => [
                    'total_of_new_customers' => $this->totalOfCustomers(Carbon::now()->subWeek()),
                    'average_paid_value_per_order' => $this->averagePaidValuePerOrder(Carbon::now()->subWeek()),
                    'order_with_highest_paid_value' => $this->orderWithHighestPaidValue(Carbon::now()->subWeek()),
                ],
                'daily' => [
                    'total_earnings' => $this->totalEarnings(Carbon::now()->subDay()),
                    'total_of_new_customers' => $this->totalOfCustomers(Carbon::now()->subDay()),
                    'total_of_orders' => $this->totalOfOrders(Carbon::now()->subDay()),
                    'mean_of_orders_by_' . $this->minutes . '_minutes' => $this->meanOfOrdersAMinute($this->minutes),
                    'mean_of_paid_by_' . $this->minutes . '_minutes' => $this->meanOfPaidAMinute($this->minutes),
                    'average_paid_value_per_order' => $this->averagePaidValuePerOrder(Carbon::now()->subDay()),
                    'order_with_highest_paid_value' => $this->orderWithHighestPaidValue(Carbon::now()->subDay()),
                ]
            ];
    }

    /**
     * Query of products by type and date
     *
     * @param Carbon|null $howOld
     * @return void
     */
    private function ordersByType($extra = null)
    {
        /** @var Builder $builder */
        $builder = Order::selectRaw('COUNT(*) AS delivered, products.type AS type')
            ->rightJoin('order_items', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.type');

        if ($extra) {
            $builder = $extra($builder);
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

    /**
     * Stats of users by month
     *
     * @return \Illuminate\Support\Collection
     */
    private function newUsersByMonth()
    {
        $builder = DB::table('users')
            ->selectRaw(DB::raw('YEAR(created_at) as year,
            sum(case when Month(users.created_at) = 1 then 1 else 0 end) AS Jan,
            sum(case when Month(users.created_at) = 2 then 1 else 0 end) AS Feb,
            sum(case when Month(users.created_at) = 3 then 1 else 0 end) AS Mar,
            sum(case when Month(users.created_at) = 4 then 1 else 0 end) AS Apr,
            sum(case when Month(users.created_at) = 5 then 1 else 0 end) AS May,
            sum(case when Month(users.created_at) = 6 then 1 else 0 end) AS Jun,
            sum(case when Month(users.created_at) = 7 then 1 else 0 end) AS Jul,
            sum(case when Month(users.created_at) = 8 then 1 else 0 end) AS Aug,
            sum(case when Month(users.created_at) = 9 then 1 else 0 end) AS Sep,
            sum(case when Month(users.created_at) = 10 then 1 else 0 end) AS Oct,
            sum(case when Month(users.created_at) = 11 then 1 else 0 end) AS Nov ,
            sum(case when Month(users.created_at) = 12 then 1 else 0 end) AS Dece '))
            ->whereYear('created_at', date('Y'))
            ->groupby('year')
            ->get();
        return $builder;
    }

    /**
     * Best selling products by type
     *
     * @return array
     */
    private function bestSellingProductsByType()
    {
        $builder = array();
        foreach (ProductType::cases() as $type) {
            $builder[] = DB::table('order_items')->selectRaw('products.id,products.name,products.photo_url,products.type,COUNT(order_items.product_id) AS `product_count`')
                ->join('products', 'products.id', '=', 'order_items.product_id')
                ->where('products.type', '=', $type)
                ->groupBy('order_items.product_id')->orderByDesc('product_count')->limit(1)->get();
        }
        return $builder;
    }

    /**
     * Chef with most orders
     *
     * @return \Illuminate\Support\Collection
     */
    private function chefWithMostOrders()
    {
        $builder = DB::table('order_items')->selectRaw('users.name,preparation_by, COUNT(preparation_by) AS `count`')
            ->leftJoin('users', 'users.id', '=', 'order_items.preparation_by')
            ->groupBy('preparation_by')->orderByDesc('count')->limit(5)->get();
        return $builder;
    }

    /**
     * Employee with most deliveries
     *
     * @return \Illuminate\Support\Collection
     */
    private function employeeWithMostDeliveries()
    {
        $builder = Order::selectRaw('users.name,delivered_by, COUNT(delivered_by) AS `count`')->where('status', 'D')
            ->leftJoin('users', 'users.id', '=', 'orders.delivered_by')
            ->groupBy('delivered_by')->orderByDesc('count')->limit(5)->get();
        return $builder;
    }

    /**
     * Statsistic of average pain per Order
     *
     * @param Carbon|null $howOld
     * @return float
     */
    private function averagePaidValuePerOrder(Carbon $howOld = null)
    {
        if ($howOld) {
            /** @var Builder $builder */
            $builder = Order::where('status', OrderStatus::DELIVERED->value)->whereBetween('created_at', [$howOld, Carbon::now()])->avg('total_paid');
        } else {
            $builder = Order::where('status', OrderStatus::DELIVERED->value)->avg('total_paid');
        }
        return round($builder, 2);
    }

    /**
     * Statistic of highest paid
     *
     * @param Carbon|null $howOld
     * @return float
     */
    private function orderWithHighestPaidValue(Carbon $howOld = null)
    {
        if ($howOld) {
            /** @var Builder $builder */
            $builder = Order::where('status', OrderStatus::DELIVERED->value)->whereBetween('created_at', [$howOld, Carbon::now()])->max('total_paid');
        } else {
            $builder = Order::where('status', OrderStatus::DELIVERED->value)->max('total_paid');
        }
        return $builder;
    }
}
