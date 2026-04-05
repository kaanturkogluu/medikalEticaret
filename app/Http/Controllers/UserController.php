<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /** Dashboard */
    public function dashboard()
    {
        $user = Auth::user();
        $orders = Order::where('customer_email', $user->email)
            ->latest()
            ->take(5)
            ->get();
        $orderCount  = Order::where('customer_email', $user->email)->count();
        $addressCount = UserAddress::where('user_id', $user->id)->count();

        return view('user.dashboard', compact('user', 'orders', 'orderCount', 'addressCount'));
    }

    /** All Orders */
    public function orders(Request $request)
    {
        $user  = Auth::user();
        $query = Order::where('customer_email', $user->email)->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('order_status', $request->status);
        }

        $orders = $query->paginate(10);
        return view('user.orders', compact('orders'));
    }

    /** Order Detail */
    public function orderShow(Order $order)
    {
        if ($order->customer_email !== Auth::user()->email) {
            abort(403);
        }
        $order->load('items');
        return view('user.order-detail', compact('order'));
    }

    /** Addresses */
    public function addresses()
    {
        $addresses = UserAddress::where('user_id', Auth::id())->get();
        return view('user.addresses', compact('addresses'));
    }

    public function addressStore(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:100',
            'full_name' => 'required|string|max:255',
            'phone'     => 'required|string|max:20',
            'city'      => 'required|string|max:100',
            'district'  => 'required|string|max:100',
            'address'   => 'required|string',
        ]);

        $data = $request->only(['title', 'full_name', 'phone', 'city', 'district', 'address', 'zip_code']);
        $data['user_id'] = Auth::id();

        if ($request->boolean('is_default')) {
            UserAddress::where('user_id', Auth::id())->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        UserAddress::create($data);

        return back()->with('success', 'Adres başarıyla eklendi.');
    }

    public function addressDestroy(UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);
        $address->delete();
        return back()->with('success', 'Adres silindi.');
    }

    /** Profile */
    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update(['name' => $request->name]);

        return back()->with('success', 'Profil bilgileriniz güncellendi.');
    }

    /** Password */
    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password'  => 'required',
            'password'          => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Mevcut şifrenizi girin.',
            'password.min'              => 'Yeni şifre en az 8 karakter olmalıdır.',
            'password.confirmed'        => 'Şifre tekrarı eşleşmiyor.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mevcut şifreniz hatalı.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Şifreniz başarıyla güncellendi.');
    }
}
