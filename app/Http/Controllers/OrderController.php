<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Midtrans\CoreApi;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function notification(Request $req)
    {
        try {
            $notification_body = json_decode($req->getContent(), true);
            $invoice = $notification_body['order_id'];
            $transaction_id = $notification_body['transaction_id'];
            $status_code = $notification_body['status_code'];
            $order = Order::where('invoice', $invoice)->where('transaction_id', $transaction_id)->first();
            if (!$order)
                return ['code' => 0, 'messgae' => 'Terjadi kesalahan | Pembayaran tidak valid'];
            switch ($status_code) {
                case '200':
                    $order->status = "SUCCESS";
                    Notification::sendFcm($order, "Pembayaran berhasil", $order->user_id);
                    break;
                case '201':
                    $order->status = "PENDING";
                    break;
                case '202':
                    $order->status = "CANCEL";
                    break;
            }
            $order->save();
            return response('Ok', 200)->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            return response('Error', 404)->header('Content-Type', 'text/plain');
        }
    }

    public function topup(Request $req)
    {
        try {
            $result = null;
            $payment_method = $req->payment_method;
            $order_id = "CXS" . date('YmdHis');
            $total_amount = $req->total;
            $transaction = array(
                "transaction_details" => [
                    "gross_amount" => $total_amount,
                    "order_id" => $order_id
                ],
                "customer_details" => [
                    "email" => $req->user()->email,
                    "first_name" => $req->user()->name,
                    "last_name" => " ",
                    "phone" => $req->user()->phone ?? " "
                ]
            );
            switch ($payment_method) {
                case 'bank_transfer':
                    $result = self::bankTransfer($order_id, $req->code, $transaction, $req->user()->id,$total_amount);
                    break;
                default:
                    $result = self::ewallet($order_id, $req->code, $transaction,$req->user()->id,$total_amount);
                    break;
            }
            return response($result, 200);
        } catch (\Exception $th) {
            return response(['success' => false, 'message' => 'Terjadi kesalahan'], 401);
        }
    }

    static function bankTransfer($order_id, $code, $transaction_object, $user_id, $total)
    {
        try {
            $transaction = $transaction_object;
            $transaction['payment_type'] = "bank_transfer";
            $transaction['bank_transfer'] = array(
                "bank" => $code
            );
            $charge = CoreApi::charge($transaction);
            if (!$charge) {
                return ['code' => 0, 'messgae' => 'Terjadi kesalahan'];
            }
            $order = Order::create(
                [
                    "user_id"=>$user_id,
                    "invoice"=>$order_id,
                    "transaction_id" => $charge->transaction_id,
                    "status" => "PENDING",
                    "total"=>$total
                ]
            );
            if (!$order)
                return false;
            return ['code' => 1, 'messgae' => 'Success', 'result' => $charge];
        } catch (\Exception $e) {
            return ['code' => 0, 'messgae' => $e->getMessage()];
        }
    }

    static function ewallet($order_id, $code, $transaction_object, $user_id,$total)
    {
        try {
            $transaction = $transaction_object;
            $transaction['payment_type'] = $code;
            $transaction[$code] = array(
                "callback_url" => "https://lamkhil.page.link"
            );
            $charge = CoreApi::charge($transaction);
            if (!$charge) {
                return ['code' => 0, 'messgae' => 'Terjadi kesalahan'];
            }
            $order = Order::create(
                [
                    "user_id"=>$user_id,
                    "invoice"=>$order_id,
                    "transaction_id" => $charge->transaction_id,
                    "status" => "PENDING",
                    "total"=>$total
                ]
            );
            if (!$order)
                return false;
            return ['code' => 1, 'messgae' => 'Success', 'result' => $charge];
        } catch (\Exception $e) {
            return ['code' => 0, 'messgae' => 'Terjadi kesalahan'];
        }
    }
}
