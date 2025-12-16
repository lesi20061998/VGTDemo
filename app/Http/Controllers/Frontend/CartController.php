<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
        
        return view('frontend.cart.index', compact('cart', 'total'));
    }
    
    public function add(Request $request)
    {
        $cart = session('cart', []);
        $productId = $request->slug;
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'name' => $request->name,
                'slug' => $request->slug,
                'price' => $request->price,
                'image' => $request->image,
                'quantity' => 1
            ];
        }
        
        session(['cart' => $cart]);
        return back()->with('success', 'Đã thêm vào giỏ hàng!');
    }
    
    public function update(Request $request, $slug)
    {
        $cart = session('cart', []);
        if (isset($cart[$slug])) {
            $cart[$slug]['quantity'] = $request->quantity;
            session(['cart' => $cart]);
        }
        return back();
    }
    
    public function remove($slug)
    {
        $cart = session('cart', []);
        unset($cart[$slug]);
        session(['cart' => $cart]);
        return back()->with('success', 'Đã xóa khỏi giỏ hàng!');
    }
    
    public function checkout()
    {
        $cart = session('cart', []);
        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
        
        return view('frontend.cart.checkout', compact('cart', 'total'));
    }
    
    public function processCheckout(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'payment_method' => 'required'
        ]);
        
        $cart = session('cart', []);
        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
        
        // Tạo đơn hàng
        $orderId = 'ORD' . time();
        
        // Xử lý payment
        if ($request->payment_method === 'vnpay') {
            return $this->vnpayPayment($orderId, $total, $request);
        } elseif ($request->payment_method === 'momo') {
            return $this->momoPayment($orderId, $total, $request);
        }
        
        // COD
        session()->forget('cart');
        return redirect()->route('project.order.success', request()->route('projectCode'))->with('orderId', $orderId);
    }
    
    private function vnpayPayment($orderId, $amount, $request)
    {
        $vnp_TmnCode = env('VNP_TMN_CODE', 'DEMO');
        $vnp_HashSecret = env('VNP_HASH_SECRET', 'DEMO');
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = url('/' . request()->route('projectCode') . '/payment/vnpay/return');
        
        $vnp_TxnRef = $orderId;
        $vnp_OrderInfo = 'Thanh toán đơn hàng ' . $orderId;
        $vnp_Amount = $amount * 100;
        $vnp_IpAddr = $request->ip();
        
        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];
        
        ksort($inputData);
        $query = "";
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnp_Url = $vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', ltrim($hashdata, '&'), $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        
        return redirect($vnp_Url);
    }
    
    private function momoPayment($orderId, $amount, $request)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = env('MOMO_PARTNER_CODE', 'DEMO');
        $accessKey = env('MOMO_ACCESS_KEY', 'DEMO');
        $secretKey = env('MOMO_SECRET_KEY', 'DEMO');
        $orderInfo = "Thanh toán đơn hàng " . $orderId;
        $returnUrl = url('/' . request()->route('projectCode') . '/payment/momo/return');
        $notifyurl = url('/' . request()->route('projectCode') . '/payment/momo/notify');
        $requestId = time() . "";
        $requestType = "captureWallet";
        $extraData = "";
        
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $notifyurl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $returnUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        
        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $returnUrl,
            'ipnUrl' => $notifyurl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];
        
        return view('frontend.cart.momo-redirect', compact('endpoint', 'data'));
    }
}

