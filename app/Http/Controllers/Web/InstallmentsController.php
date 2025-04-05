<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\traits\RegionsDataByUser;
use App\Models\Bundle;
use App\Models\Cart;
use App\Models\Installment;
use App\Models\InstallmentOrder;
use App\Models\InstallmentOrderAttachment;
use App\Models\InstallmentOrderPayment;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\RegistrationPackage;
use App\Models\SelectedInstallment;
use App\Models\SelectedInstallmentStep;
use App\Models\Subscribe;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InstallmentsController extends Controller
{
    use RegionsDataByUser;

    public function index(Request $request, $installmentId)
    {
        $user = auth()->user();
        $itemId = $request->get('item');
        $itemType = $request->get('item_type');

        if (empty($user) or !$user->enable_installments) {
            $toastData = [
                'title' => trans('public.request_failed'),
                'msg' => trans('update.you_cannot_use_installment_plans'),
                'status' => 'error'
            ];
            return back()->with(['toast' => $toastData]);
        }

        if (!empty($itemId) and !empty($itemType) and getInstallmentsSettings('status')) {

            $item = $this->getItem($itemId, $itemType, $user);

            if (!empty($item)) {
                $installment = Installment::query()->where('id', $installmentId)
                    ->where('enable', true)
                    ->withCount([
                        'steps'
                    ])
                    ->first();

                if (!empty($installment)) {

                    if (!$installment->hasCapacity()) {
                        $toastData = [
                            'title' => trans('public.request_failed'),
                            'msg' => trans('update.installment_not_capacity'),
                            'status' => 'error'
                        ];
                        return back()->with(['toast' => $toastData]);
                    }

                    $itemPrice = $item->getPrice();

                    $hasPhysicalProduct = false;
                    if ($itemType == 'product') {
                        $quantity = $request->get('quantity', 1);
                        $itemPrice = $itemPrice * $quantity;
                        $hasPhysicalProduct = ($item->type == Product::$physical);
                    }

                    $data = [
                        'pageTitle' => trans('update.verify_your_installments'),
                        'installment' => $installment,
                        'itemPrice' => $itemPrice,
                        'itemType' => $itemType,
                        'item' => $item,
                        'hasPhysicalProduct' => $hasPhysicalProduct,
                    ];

                    if ($hasPhysicalProduct) {
                        $data = array_merge($data, $this->getLocationsData($user));
                    }

                    return view('web.default.installment.verify', $data);
                }
            }
        }

        abort(404);
    }

    private function getItem($itemId, $itemType, $user)
    {
        if ($itemType == 'course') {
            $course = Webinar::where('id', $itemId)
                ->where('status', 'active')
                ->first();

            $hasBought = $course->checkUserHasBought($user);
            $canSale = ($course->canSale() and !$hasBought);

            if ($canSale and !empty($course->price)) {
                return $course;
            }
        } else if ($itemType == 'bundles') {
            $bundle = Bundle::where('id', $itemId)
                ->where('status', 'active')
                ->first();

            $hasBought = $bundle->checkUserHasBought($user);
            $canSale = ($bundle->canSale() and !$hasBought);

            if ($canSale and !empty($bundle->price)) {
                return $bundle;
            }
        } elseif ($itemType == 'product') {
            $product = Product::where('status', Product::$active)
                ->where('id', $itemId)
                ->first();

            $hasBought = $product->checkUserHasBought($user);

            if (!$hasBought and !empty($product->price)) {
                return $product;
            }
        } elseif ($itemType == 'registration_package') {
            $package = RegistrationPackage::where('id', $itemId)
                ->where('status', 'active')
                ->first();

            return $package;
        } elseif ($itemType == 'subscribe') {
            return Subscribe::where('id', $itemId)->first();
        }

        return null;
    }

    private function getColumnByItemType($itemType)
    {
        if ($itemType == 'course') {
            return 'webinar_id';
        } elseif ($itemType == 'product') {
            return 'product_id';
        } elseif ($itemType == 'bundles') {
            return 'bundle_id';
        } elseif ($itemType == 'subscribe') {
            return 'subscribe_id';
        } elseif ($itemType == 'registration_package') {
            return 'registration_package_id';
        }
    }

    public function store(Request $request, $installmentId)
    {
        $user = auth()->user();
        $itemId = $request->get('item');
        $itemType = $request->get('item_type');

        if (empty($user) or !$user->enable_installments) {
            $toastData = [
                'title' => trans('public.request_failed'),
                'msg' => trans('update.you_cannot_use_installment_plans'),
                'status' => 'error'
            ];
            return back()->with(['toast' => $toastData]);
        }


        $installment = Installment::query()->where('id', $installmentId)
            ->where('enable', true)
            ->withCount([
                'steps'
            ])
            ->first();

        if (!empty($installment)) {
            if (!$installment->hasCapacity()) {
                $toastData = [
                    'title' => trans('public.request_failed'),
                    'msg' => trans('update.installment_not_capacity'),
                    'status' => 'error'
                ];
                return back()->with(['toast' => $toastData]);
            }


            $this->validate($request, [
                'item' => 'required',
                'item_type' => 'required',
            ]);

            $data = $request->all();
            $attachments = (!empty($data['attachments']) and count($data['attachments'])) ? array_map('array_filter', $data['attachments']) : [];
            $attachments = !empty($attachments) ? array_filter($attachments) : [];

            if ($installment->request_uploads) {
                if (count($attachments) < 1) {
                    return redirect()->back()->withErrors([
                        'attachments' => trans('validation.required', ['attribute' => 'attachments'])
                    ]);
                }
            }

            if (!empty($installment->capacity)) {
                $openOrdersCount = InstallmentOrder::query()->where('installment_id', $installment->id)
                    ->where('status', 'open')
                    ->count();

                if ($openOrdersCount >= $installment->capacity) {
                    $toastData = [
                        'title' => trans('public.request_failed'),
                        'msg' => trans('update.installment_not_capacity'),
                        'status' => 'error'
                    ];

                    return back()->with(['toast' => $toastData]);
                }
            }

            $item = $this->getItem($itemId, $itemType, $user);

            if (!empty($item)) {

                $productOrder = null;

                if ($itemType == 'product') {
                    $hasPhysicalProduct = ($item->type == Product::$physical);

                    $this->validate($request, [
                        'country_id' => Rule::requiredIf($hasPhysicalProduct),
                        'province_id' => Rule::requiredIf($hasPhysicalProduct),
                        'city_id' => Rule::requiredIf($hasPhysicalProduct),
                        'district_id' => Rule::requiredIf($hasPhysicalProduct),
                        'address' => Rule::requiredIf($hasPhysicalProduct),
                    ]);

                    /* Product Order */
                    $productOrder = $this->handleProductOrder($request, $user, $item);
                }

                $columnName = $this->getColumnByItemType($itemType);

                $status = 'paying';

                if (empty($installment->upfront)) {
                    $status = 'open';

                    if ($installment->needToVerify()) {
                        $status = 'pending_verification';
                    }
                }

                $itemPrice = $item->getPrice();

                $order = InstallmentOrder::query()->updateOrCreate([
                    'installment_id' => $installment->id,
                    'user_id' => $user->id,
                    $columnName => $itemId,
                    'product_order_id' => !empty($productOrder) ? $productOrder->id : null,
                    'item_price' => $itemPrice,
                    'status' => $status,
                ], [
                    'created_at' => time(),
                ]);

                /* Attachments */
                $this->handleAttachments($attachments, $order);

                /* Store Installment Data */
                $this->handleSelectedInstallment($user, $order, $installment);

                /* Update Product Order */
                if (!empty($productOrder)) {
                    $productOrder->update([
                        'installment_order_id' => $order->id
                    ]);
                }

                $notifyOptions = [
                    '[u.name]' => $order->user->full_name,
                    '[installment_title]' => $installment->main_title,
                    '[time.date]' => dateTimeFormat(time(), 'j M Y - H:i'),
                    '[amount]' => handlePrice($itemPrice),
                ];

                sendNotification("instalment_request_submitted", $notifyOptions, $order->user_id);
                sendNotification("instalment_request_submitted_for_admin", $notifyOptions, 1);


                /* Payment and Cart */
                if (!empty($installment->upfront)) {
                    $installmentPayment = InstallmentOrderPayment::query()->updateOrCreate([
                        'installment_order_id' => $order->id,
                        'sale_id' => null,
                        'type' => 'upfront',
                        'selected_installment_step_id' => null,
                        'amount' => $installment->getUpfront($order->getItemPrice()),
                        'status' => 'paying',
                    ], [
                        'created_at' => time(),
                    ]);

                    Cart::updateOrCreate([
                        'creator_id' => $user->id,
                        'installment_payment_id' => $installmentPayment->id,
                    ], [
                        'created_at' => time()
                    ]);

                    return redirect('/cart');
                } else {

                    if ($installment->needToVerify()) {
                        sendNotification("installment_verification_request_sent", $notifyOptions, $order->user_id);
                        sendNotification("admin_installment_verification_request_sent", $notifyOptions, 1); // Admin

                        return redirect('/installments/request_submitted');
                    } else {
                        sendNotification("approve_installment_verification_request", $notifyOptions, $order->user_id);

                        return $this->handleOpenOrder($item, $productOrder);
                    }
                }
            }
        }

        abort(404);
    }

    private function handleOpenOrder($item, $productOrder)
    {
        if (!empty($productOrder)) {
            $productOrderStatus = ProductOrder::$waitingDelivery;

            if ($item->isVirtual()) {
                $productOrderStatus = ProductOrder::$success;
            }

            $productOrder->update([
                'status' => $productOrderStatus
            ]);
        }

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.your_installment_purchase_has_been_successfully_completed'),
            'status' => 'success'
        ];

        return redirect('/panel/financial/installments')->with(['toast' => $toastData]);
    }

    private function handleProductOrder(Request $request, $user, $product)
    {
        $data = $request->all();

        $specifications = $data['specifications'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        $order = ProductOrder::query()->create([
            'product_id' => $product->id,
            'seller_id' => $product->creator_id,
            'buyer_id' => $user->id,
            'sale_id' => null,
            'installment_order_id' => null,
            'status' => 'pending',
            'specifications' => $specifications ? json_encode($specifications) : null,
            'quantity' => $quantity,
            'discount_id' => null,
            'message_to_seller' => $data['message_to_seller'],
            'created_at' => time()
        ]);

        if ($product->type == Product::$physical) {
            $user->update([
                'country_id' => $data['country_id'] ?? $user->country_id,
                'province_id' => $data['province_id'] ?? $user->province_id,
                'city_id' => $data['city_id'] ?? $user->city_id,
                'district_id' => $data['district_id'] ?? $user->district_id,
                'address' => $data['address'] ?? $user->address,
            ]);
        }

        return $order;
    }

    private function handleAttachments($attachments, $order)
    {
        InstallmentOrderAttachment::query()->where('installment_order_id', $order->id)->delete();

        if (!empty($attachments)) {
            $attachmentsInsert = [];

            foreach ($attachments as $attachment) {
                if (!empty($attachment['title']) and !empty($attachment['file'])) {
                    $attachmentsInsert[] = [
                        'installment_order_id' => $order->id,
                        'title' => $attachment['title'],
                        'file' => $attachment['file'],
                    ];
                }
            }

            if (!empty($attachmentsInsert)) {
                InstallmentOrderAttachment::query()->insert($attachmentsInsert);
            }
        }
    }

    private function handleSelectedInstallment($user, $order, $installment)
    {
        $selected = SelectedInstallment::query()->updateOrCreate([
            'user_id' => $user->id,
            'installment_id' => $installment->id,
            'installment_order_id' => $order->id,
        ], [
            'start_date' => $installment->start_date,
            'end_date' => $installment->end_date,
            'upfront' => $installment->upfront,
            'upfront_type' => $installment->upfront_type,
            'created_at' => time(),
        ]);

        SelectedInstallmentStep::query()->where('selected_installment_id', $selected->id)->delete();

        $insert = [];

        foreach ($installment->steps as $step) {
            $insert[] = [
                'selected_installment_id' => $selected->id,
                'installment_step_id' => $step->id,
                'deadline' => $step->deadline,
                'amount' => $step->amount,
                'amount_type' => $step->amount_type,
            ];
        }

        if (!empty($insert)) {
            SelectedInstallmentStep::query()->insert($insert);
        }
    }

    public function requestSubmitted()
    {
        $data = [
            'pageTitle' => trans('update.installment_request_submitted'),
        ];

        return view('web.default.installment.request_submitted', $data);
    }

    public function requestRejected()
    {
        $data = [
            'pageTitle' => trans('update.installment_request_rejected'),
        ];

        return view('web.default.installment.request_rejected', $data);
    }
}
