<?php

namespace App\Http\Livewire;

use App\Models\Coupon;
use App\Models\DeliveryAddress;
use App\Models\Option;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderLivewire extends BaseLivewireComponent
{

    //
    public $model = Order::class;

    //
    public $orderId;
    public $deliveryBoys;
    public $deliveryBoyId;
    public $status;
    public $paymentStatus;
    public $note;

    //
    public $orderStatus;
    public $orderPaymentStatus;


    //
    public $showSummary = false;
    public $vendorID;
    public $productIDs;
    public $paymentMethods;
    public $newOrderProducts;
    public $newProductOptions;
    public $newProductSelectedOptions;
    public $newOrderProductsQtys;
    public $couponCode;
    public $isPickup;
    public $tip;
    public $userId;
    public $deliveryAddressId;
    public $paymentMethodId;
    public $coupon;
    public $newOrder;


    public function render()
    {

        $this->deliveryBoys = User::role('driver')->get();
        $this->paymentMethods = PaymentMethod::active()->get();

        //if vendor has any personal delivery boy, use that list instead
        if (!empty(Auth::user()->vendor_id)) {
            $personalDrivers = User::role('driver')->where('vendor_id', Auth::user()->vendor_id)->get();
            if (count($personalDrivers) > 0) {
                $this->deliveryBoys = $personalDrivers;
            } else {
                $this->deliveryBoys = User::role('driver')->whereNull('vendor_id')->get();
            }
        }

        $this->orderStatus = $this->orderStatus();
        $this->orderPaymentStatus = $this->orderPaymentStatus();
        return view('livewire.orders');
    }

    public function showDetailsModal($id)
    {
        $this->selectedModel = $this->model::find($id);
        $this->orderId = $id;
        $this->showDetails = true;
    }

    // Updating model
    public function initiateEdit($id)
    {
        $this->selectedModel = $this->model::find($id);
        $this->deliveryBoyId = $this->selectedModel->driver_id;
        $this->status = $this->selectedModel->status;
        $this->paymentStatus = $this->selectedModel->payment_status;
        $this->note = $this->selectedModel->note;
        $this->emit('showEditModal');
    }


    public function update()
    {

        try {

            DB::beginTransaction();
            $this->selectedModel->driver_id = $this->deliveryBoyId;
            $this->selectedModel->payment_status = $this->paymentStatus;
            $this->selectedModel->note = $this->note;
            $this->selectedModel->save();
            $this->selectedModel->setStatus($this->status);
            DB::commit();

            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert("Order updated successfully!");
            $this->emit('refreshTable');
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? "Order update failed!");
        }
    }



    //reivew payment
    public function reviewPayment($id)
    {
        //
        $this->selectedModel = $this->model::find($id);
        $this->emit('showAssignModal');
    }

    public function approvePayment()
    {
        //
        try {

            DB::beginTransaction();
            $this->selectedModel->payment_status = "successful";
            $this->selectedModel->save();
            //
            $this->selectedModel->payment->status = "successful";
            $this->selectedModel->payment->save();
            DB::commit();

            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert("Order updated successfully!");
            $this->emit('refreshTable');
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? "Order update failed!");
        }
    }








    // New order
    public function showCreateModal()
    {
        $customers = User::active()->get();
        $vendors = Vendor::active()->mine()->get();
        // $this->showSelect2("#vendorSelect2", $this->productIDS, "productsChange");
        $this->showSelect2("#vendorSelect2", [], "vendorChange",  $vendors);
        $this->showSelect2("#productsSelect2", [], "productsChange", []);
        $this->showSelect2("#customerSelect2", [], "customerChange", $customers);
        $this->showCreate = true;
    }

    public function vendorChange($data)
    {
        $this->vendorID = $data;
        $this->productIDs = [];
        $products = Product::active()->where('vendor_id', $this->vendorID)->get();
        $this->updateProductsSelect($products);
    }

    public function updateProductsSelect($products)
    {
        $this->showSelect2("#productsSelect2", $this->productIDs, "productsChange", $products);
    }

    public function productsChange($data)
    {
        $this->productIDs = $data;
        $this->newOrderProducts = Product::whereIn('id', $this->productIDs)->get();
    }

    public function customerChange($data)
    {
        $this->userId = $data;
        $deliveryAddresses = DeliveryAddress::where('user_id', $this->userId)->get();
        $this->showSelect2("#deliveryAddressesSelect2", [], "deliveryAddressesChange", $deliveryAddresses);
    }
    public function deliveryAddressesChange($data)
    {
        $this->deliveryAddressId = $data;
    }

    public function removeModel($id)
    {
        //
        $this->newOrderProducts = $this->newOrderProducts->reject(function ($element) use ($id) {
            return $element->id == $id;
        });

        //
        $this->productIDs = $this->newOrderProducts->pluck('id');
        $this->newOrderProductsQtys[$id] = null;
        $this->updateProductsSelect(null);
    }

    public function updatedIsPickupChange()
    {
        $this->customerChange($this->userId);
    }

    public function applyDiscount()
    {

        $this->coupon = Coupon::with('vendors', 'products')->active()->where('code', $this->couponCode)->first();
        if (empty($this->coupon)) {
            $this->addError('couponCode', __('Invalid Coupon Code'));
        } else {
            $this->resetValidation('couponCode');
        }
    }

    public function showOrderSummary()
    {
        //
        if (empty($this->vendorID)) {
            $this->showErrorAlert(__(("Please check Vendor")));
            return;
        } else if (empty($this->productIDs)) {
            $this->showErrorAlert(__(("Please check at least one product")));
            return;
        } else if (empty($this->userId)) {
            $this->showErrorAlert(__(("Please check customer")));
            return;
        } else if (!$this->isPickup && empty($this->deliveryAddressId)) {
            $this->showErrorAlert(__(("Please select delivery address")));
            return;
        } else if (!$this->isPickup) {
            //disctance between vendor and delivery address
            $vendor = Vendor::find($this->vendorID);
            //default delivery address
            $deliveryAddress = DeliveryAddress::distance($vendor->latitude, $vendor->longitude)->find($this->deliveryAddressId);
            if ($deliveryAddress->distance > $vendor->delivery_range) {
                $this->showErrorAlert(__(("Delivery address is out of vendor delivery range")));
                return;
            }

            //
            $deliveryFee = $vendor->base_delivery_fee;
            $deliveryFee += $vendor->charge_per_km ? ($vendor->delivery_fee * $deliveryAddress->distance) : $vendor->delivery_fee;
        }


        //
        $this->validate([
            'newOrderProductsQtys.*' => 'required|numeric|min:1',
        ], [
            'newOrderProductsQtys.*' => 'Qty is required',
        ]);


        //
        $this->newOrder = $this->getOrderData();
        $this->showSummary = true;
    }

    public function saveNewOrder()
    {

        //
        try {
            DB::beginTransaction();
            $this->newOrder = $this->getOrderData();
            $this->newOrder->save();
            $this->newOrder->setStatus("pending");

            foreach ($this->newOrderProducts as $newOrderProduct) {
                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $this->newOrder->id;
                $orderProduct->quantity = ($this->newOrderProductsQtys[$newOrderProduct->id] ?? 1);
                $orderProduct->price = ($newOrderProduct->discount_price <= 0) ? $newOrderProduct->price : $newOrderProduct->discount_price;
                $orderProduct->product_id = $newOrderProduct->id;

                //flatten options
                $productOptionsString = "";
                if (!empty($this->newProductSelectedOptions) && !empty($this->newProductSelectedOptions[$newOrderProduct->id])) {
                    $productOptions = $this->newProductSelectedOptions[$newOrderProduct->id];
                    foreach ($productOptions as $key => $productOption) {
                        $productOptionsString .= $productOption->name;
                        if ($key < (count($productOptions) - 1)) {
                            $productOptionsString .= ", ";
                        }
                    }
                }
                //
                $orderProduct->options = $productOptionsString;
                $orderProduct->save();

                //reduce product qty
                $product = $orderProduct->product;
                if (!empty($product->available_qty)) {
                    $product->available_qty = $product->available_qty - $orderProduct->quantity;
                    $product->save();
                }
            }
            DB::commit();
            $this->showSuccessAlert("New Order successfully!");

            $this->showSummary = false;
            $this->showCreate = false;
            $this->emit('refreshTable');
        } catch (\Exception $ex) {
            DB::rollback();
            $this->showErrorAlert($ex->getMessage() ?? "New Order failed!");
        }
    }


    //get order
    public function getOrderData()
    {

        $deliveryFee = 0;
        $order = new Order();
        $order->vendor_id = $this->vendorID;
        $order->user_id = $this->userId;
        $order->delivery_address_id = $this->deliveryAddressId;
        if (empty($this->paymentMethodId)) {
            $order->payment_method_id = $this->paymentMethods->first()->id;
        } else {
            $order->payment_method_id = $this->paymentMethodId;
        }

        //cash payment
        if ($order->payment_method->slug == "cash") {
            $order->payment_status = "successful";
        }
        $order->tip = $this->tip;
        $order->note = $this->note;
        $order->created_at = Carbon::now();
        $order->updated_at = Carbon::now();

        //
        foreach ($this->newOrderProducts as $key => $newOrderProduct) {
            if ($newOrderProduct->discount_price > 0) {
                $productPrice = $newOrderProduct->discount_price;
            } else {
                $productPrice = $newOrderProduct->price;
            }
            $order->sub_total += $productPrice * ($this->newOrderProductsQtys[$newOrderProduct->id] ?? 1);
        }

        foreach ($this->newProductOptions ?? [] as $key => $newProductOptionObject) {

            $optionIdsArray = [];
            foreach ($newProductOptionObject as $newProductOptionObjectValues) {

                //
                if (gettype($newProductOptionObjectValues) == 'array') {
                    foreach ($newProductOptionObjectValues as $key3 => $newProductOptionObjectValue) {
                        if ($newProductOptionObjectValue) {
                            array_push($optionIdsArray, $key3);
                        }
                    }
                } else {
                    array_push($optionIdsArray, $newProductOptionObjectValues);
                }
            }

            $selectedProductOptions = Option::whereIn('id', $optionIdsArray)->get();
            $this->newProductSelectedOptions[$key] = $selectedProductOptions;

            //pricing
            foreach ($selectedProductOptions as $selectedProductOption) {
                $order->sub_total += $selectedProductOption->price;
            }
        }


        //
        if (!empty($this->coupon)) {
            //
            $couponVendors = $this->coupon->vendors;
            $couponVendorsIds = $this->coupon->vendors->pluck('id')->toArray();
            $couponProducts = $this->coupon->products;
            $couponProductsIds = $this->coupon->products->pluck('id')->toArray();

            //apply discount directly to total order
            if (count($couponVendors) == 0 && count($couponProducts) == 0) {

                if ($this->coupon->percentage) {
                    $order->discount = $order->sub_total * ($this->coupon->discount / 100);
                } else {
                    $order->discount = $this->coupon->discount;
                }
            } else if (count($couponProducts) > 0) {
                //go through selected products
                foreach ($this->newOrderProducts as $key => $newOrderProduct) {
                    if ($newOrderProduct->discount_price > 0) {
                        $productPrice = $newOrderProduct->discount_price;
                    } else {
                        $productPrice = $newOrderProduct->price;
                    }
                    //if the current product in loop is in the products coupon can be applied on
                    if (in_array($newOrderProduct->id, $couponProductsIds)) {
                        if ($this->coupon->percentage) {
                            $order->discount += $productPrice * ($this->coupon->discount / 100);
                        } else {
                            $order->discount += $productPrice * $this->coupon->discount;
                        }
                    }
                }
            } else if (count($couponVendors) > 0) {
                //check if vendor is part of listed vendors coupon can be applied
                if (in_array($this->newOrder->vendor_id, $couponVendorsIds)) {
                    if ($this->coupon->percentage) {
                        $order->discount = $order->sub_total * ($this->coupon->discount / 100);
                    } else {
                        $order->discount = $order->sub_total * $this->coupon->discount;
                    }
                }
            } else {
                $order->discount = 0;
            }
        } else {
            $order->discount = 0;
        }
        $order->sub_total = number_format($order->sub_total, 2, '.', '');
        $order->delivery_fee = number_format($deliveryFee, 2, '.', '');
        $order->discount = number_format($order->discount, 2, '.', '');
        $order->tip = number_format($order->tip, 2, '.', '');
        $order->tax = number_format($order->sub_total * ($order->vendor->tax / 100), 2, '.', '');
        $order->total = $order->sub_total - $order->discount + $order->tax + $order->tip + $order->delivery_fee;
        return $order;
    }
}
