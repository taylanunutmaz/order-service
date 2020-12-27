<?php

namespace App\Http\Controllers\Api\Customer;

use App\Events\OrderCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Customer\StoreOrderRequest;
use App\Http\Resources\Customer as CustomerResource;
use App\Http\Resources\CustomerOrders;
use App\Http\Resources\Order as OrderResource;
use App\Models\Customer;
use App\Notifications\SendEmailWhenOrderCreatedNotification;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Customer $customer)
    {
        return new CustomerOrders($customer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request, Customer $customer)
    {
        $order = $customer->orders()->create($request->only(
            'total_price',
            'name',
            'description'
        ));

        $customer->notify(new SendEmailWhenOrderCreatedNotification($order));
        event(new OrderCreated($order, $customer));

        return new OrderResource($order);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
