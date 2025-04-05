<?php

namespace App\Mixins\Cart;

class CartItemInfo
{
    public function getItemInfo($cart)
    {
        if (!empty($cart->webinar_id)) {
            $webinar = $cart->webinar;

            return $this->getCourseInfo($cart, $webinar);
        } elseif (!empty($cart->bundle_id)) {
            $bundle = $cart->bundle;

            return $this->getBundleInfo($cart, $bundle);
        } elseif (!empty($cart->productOrder) and !empty($cart->productOrder->product)) {
            $product = $cart->productOrder->product;

            return $this->getProductInfo($cart, $product);
        } elseif (!empty($cart->reserve_meeting_id)) {
            $creator = $cart->reserveMeeting->meeting->creator;

            return $this->getReserveMeetingInfo($cart, $creator);
        } elseif (!empty($cart->installment_payment_id)) {
            $installmentPayment = $cart->installmentPayment;

            return $this->getInstallmentOrderInfo($cart, $installmentPayment);
        }
    }

    private function getCourseInfo($cart, $webinar)
    {
        $info = [];

        $info['imgPath'] = $webinar->getImage();
        $info['itemUrl'] = $webinar->getUrl();
        $info['title'] = $webinar->title;
        $info['profileUrl'] = $webinar->teacher->getProfileUrl();
        $info['teacherName'] = $webinar->teacher->full_name;
        $info['rate'] = $webinar->getRate();
        $info['price'] = $webinar->price;
        $info['discountPrice'] = $webinar->getDiscount($cart->ticket) ? ($webinar->price - $webinar->getDiscount($cart->ticket)) : null;

        return $info;
    }

    private function getBundleInfo($cart, $bundle)
    {
        $info = [];

        $info['imgPath'] = $bundle->getImage();
        $info['itemUrl'] = $bundle->getUrl();
        $info['title'] = $bundle->title;
        $info['profileUrl'] = $bundle->teacher->getProfileUrl();
        $info['teacherName'] = $bundle->teacher->full_name;
        $info['rate'] = $bundle->getRate();
        $info['price'] = $bundle->price;
        $info['discountPrice'] = $bundle->getDiscount($cart->ticket) ? ($bundle->price - $bundle->getDiscount($cart->ticket)) : null;

        return $info;
    }

    private function getProductInfo($cart, $product)
    {
        $info = [];

        $info['isProduct'] = true;
        $info['imgPath'] = $product->thumbnail;
        $info['itemUrl'] = $product->getUrl();
        $info['title'] = $product->title;
        $info['profileUrl'] = $product->creator->getProfileUrl();
        $info['teacherName'] = $product->creator->full_name;
        $info['rate'] = $product->getRate();
        $info['quantity'] = $cart->productOrder ? $cart->productOrder->quantity : 1;
        $info['price'] = $product->price;
        $info['discountPrice'] = ($product->getPriceWithActiveDiscountPrice() < $product->price) ? $product->getPriceWithActiveDiscountPrice() : null;

        return $info;
    }

    private function getReserveMeetingInfo($cart, $creator)
    {
        $info = [];

        $info['imgPath'] = $creator->getAvatar(150);
        $info['itemUrl'] = null;
        $info['title'] = trans('meeting.reservation_appointment') . ' ' . ((!empty($cart->reserveMeeting->student_count) and $cart->reserveMeeting->student_count > 1) ? '(' . trans('update.reservation_appointment_student_count', ['count' => $cart->reserveMeeting->student_count]) . ')' : '');
        $info['profileUrl'] = $creator->getProfileUrl();
        $info['teacherName'] = $creator->full_name;
        $info['rate'] = $creator->rates();
        $info['price'] = $cart->reserveMeeting->paid_amount;

        return $info;
    }

    private function getSubscribeInfo($cart, $subscribe)
    {
        $info = [];

        $info['imgPath'] = $subscribe->icon;
        $info['itemUrl'] = null;
        $info['title'] = $subscribe->title;
        $info['profileUrl'] = null;
        $info['teacherName'] = null;
        $info['extraHint'] = trans('public.subscribe');
        $info['rate'] = null;
        $info['quantity'] = null;
        $info['price'] = $subscribe->price;
        $info['discountPrice'] = null;

        return $info;
    }

    private function getRegistrationPackageInfo($cart, $registrationPackage)
    {
        $info = [];

        $info['imgPath'] = $registrationPackage->icon;
        $info['itemUrl'] = null;
        $info['title'] = $registrationPackage->title;
        $info['profileUrl'] = null;
        $info['teacherName'] = null;
        $info['extraHint'] = trans('update.registration_package');
        $info['rate'] = null;
        $info['quantity'] = null;
        $info['price'] = $registrationPackage->price;
        $info['discountPrice'] = null;

        return $info;
    }

    private function getInstallmentOrderInfo($cart, $installmentPayment)
    {
        $info = [];

        $installmentOrder = $installmentPayment->installmentOrder;

        if (!empty($installmentOrder)) {

            if (!empty($installmentOrder->webinar_id)) {
                $webinar = $installmentOrder->webinar;

                $info = $this->getCourseInfo($cart, $webinar);
            } elseif (!empty($installmentOrder->bundle_id)) {
                $bundle = $installmentOrder->bundle;

                $info = $this->getBundleInfo($cart, $bundle);
            } elseif (!empty($installmentOrder->product_id)) {
                $product = $installmentOrder->product;

                $info = $this->getProductInfo($cart, $product);
            } elseif (!empty($installmentOrder->subscribe_id)) {
                $subscribe = $installmentOrder->subscribe;

                $info = $this->getSubscribeInfo($cart, $subscribe);
            } elseif (!empty($installmentOrder->registration_package_id)) {
                $registrationPackage = $installmentOrder->registrationPackage;

                $info = $this->getRegistrationPackageInfo($cart, $registrationPackage);
            }

            $info['price'] = $installmentPayment->amount;
            $info['discountPrice'] = 0;
            $info['extraPriceHint'] = ($installmentPayment->type == 'upfront') ? trans('update.installment_upfront') : trans('update.installment');
        }

        return $info;
    }
}
