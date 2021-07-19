<?php

namespace App\Observers;

use App\Models\Order;
use App\Mail\OrderUpdateMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class OrderObserver
{


    public function creating(Order $model)
    {
        // logger("Pending Order", [$model]);
        $model->code = Str::random(10);
        $model->verification_code = Str::random(5);
        if (empty($model->user_id)) {
            $model->user_id = Auth::id();
        }
    }

    public function created(Order $model)
    {
        //sending notifications base on status change of the order
        $model->sendOrderStatusChangeNotification($model, true);
        $this->sendOrderUpdateMail($model);
        $this->statusTracking($model);
        $model->notifyDeliveryBoys();
    }


    public function updated(Order $model)
    {
        //sending notifications base on status change of the order
        $model->refresh();
        $model->sendOrderStatusChangeNotification($model);
        $this->sendOrderUpdateMail($model);
        $this->statusTracking($model);
        $model->updateEarning();
        $model->refundUser();
        $model->notifyDeliveryBoys();
        $model->clearFirestore();
    }

    //
    public function sendOrderUpdateMail($model)
    {
        //only delivered
        if (in_array($model->status, ['delivered'])) {
            //send mail
            try {
                \Mail::to($model->user->email)
                    ->cc([$model->vendor->email])
                    ->send(new OrderUpdateMail($model));
            } catch (\Exception $ex) {
                // logger("Mail Error", [$ex]);
                logger("Mail Error");
            }
        }
    }


    //
    public function statusTracking(Order $order)
    {

        // logger("Working", ["Nice"]);

        // $latestOrderStatus = $order->latestStatus($order->status);
        // if (empty($latestOrderStatus)) {
        //     $order->setStatus($order->status);
        //     logger("Recent statues", [
        //         $order->statuses
        //     ]);
        // }else{
        //     logger("Old Statues", [
        //         $order->statuses
        //     ]);
        // }
    }
}
