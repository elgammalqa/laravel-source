<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class APiController extends Controller
{
    //payload [HEADER => access_token], [BODY => [order_id, new_order_status, payment_reference_id, payment_gateways_logs]]
    public function changeOrderStatus(Request $request){
        if($request->header('Modifier-Token') != '3bnsKJsemrBQi0YZZRixu6hY126xVqL0v0F'){
            return response()->json(['success' => false, 'msg' => 'Access token is invalid.']);
        }

        $order = DB::table('order')->where('id', $request->order_id)->first();
        if(!$order){
            return response()->json(['success' => false, 'msg' => 'Not found this order.']);
        }
        if(!in_array($order->status, ['pending-checkout'])){
            return response()->json(['success' => false, 'msg' => 'Order new status invalid.']);
        }

        if(!in_array($request->new_order_status, ['new'])){
            return response()->json(['success' => false, 'msg' => 'Order new status invalid.']);
        }

        DB::table('order')->where('id', $request->order_id)->update(['status' => $request->new_order_status, 'payment_reference_id' => $request->payment_reference_id, 'payment_gateways_logs' => $request->payment_gateways_logs, 'update_by_laravel' => 1]);
        $orderStatusHistory = DB::table('order_status_history')->insert(['order_id' => $order->id, 'status' => $request->new_order_status, 'action_done_by_id' => $order->user_id]);

        return response()->json(['success' => true, 'msg' => 'Order status updated.']);
    }
}