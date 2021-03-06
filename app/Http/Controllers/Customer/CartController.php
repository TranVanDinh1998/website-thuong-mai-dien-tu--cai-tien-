<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Producer;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Tag;
use App\Models\CollectionProduct;
use App\Models\Coupon;
use App\Models\ProductImage;
use App\Models\WishList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(Category $category, Product $product, Collection $collection, Producer $producer, Tag $tag, Coupon $coupon)
    {
        $this->category = $category;
        $this->coupon = $coupon;
        $this->product = $product;
        $this->collection = $collection;
        $this->producer = $producer;
        $this->tag = $tag;
    }
    public function index()
    {

        // tags
        $tags = $this->tag->orderByDesc('view')->limit(10)->get();
        // cart
        $shopping_carts = session()->get('cart');
        $discount_cart = session()->get('discount');
        $count_cart = null;
        if (isset($shopping_carts)) {
            foreach ($shopping_carts as $product_id => $info) {
                $count_cart += 1;
            }
        }
        $total_cart = null;
        if (isset($shopping_carts)) {
            foreach ($shopping_carts as $product_id => $info) {
                if ($info['product_discount'] != null && $info['product_discount'] != 0) {
                    $total_cart +=  ($info['product_price'] - ($info['product_price'] * $info['product_discount'] / 100)) * $info['product_quantity'];
                } else {
                    $total_cart += $info['product_price'] * $info['product_quantity'];
                }
            }
        }

        return view('pages.customer.cart', [

            'tags' => $tags,
            //cart
            'shopping_carts' => $shopping_carts,
            'count_cart' => $count_cart,
            'total_cart' => $total_cart,
            'discount_cart' => $discount_cart,
        ]);
    }

    public function create(Request $request)
    {
        $product = $this->product->find($request->id);
        // add product to shopping cart
        if ($product->remaining == 0 || $product->verified == 0) {
            return response()->json([
                'error' => true,
                'message' => 'S???n ph???m ' . $product->name . ' ???? h???t h??ng!'
            ]);
        } else {
            $cart = session()->get('cart');

            // if cart is empty then this is the first product of the cart
            if (!$cart) {
                $cart = [
                    $request->id => [
                        'product_name' => $product->name,
                        'product_image' => $product->image,
                        'product_quantity' => $request->quantity,
                        'product_price' => $product->price,
                        'product_discount' => $product->discount,
                    ]
                ];
                session()->put('cart', $cart);
                return response()->json([
                    'error' => false,
                    'message' => 'S???n ph???m ' . $product->name . ' ???? ???????c th??m v??o gi??? h??ng.'
                ]);
            }

            // if cart is not empty then check if this product exist before then change the chapter
            if (isset($cart[$request->id])) {
                $cart[$request->id]['product_quantity'] += $request->quantity;
                session()->put('cart', $cart);
                return response()->json([
                    'error' => false,
                    'message' => 'T??ng s??? l?????ng s???n ph???m ' . $product->name . ' c?? trong gi??? h??ng.'
                ]);
            }

            //if product not exist in cart then add product into cart
            $cart[$request->id] = [
                'product_name' => $product->name,
                'product_image' => $product->image,
                'product_quantity' => $request->quantity,
                'product_price' => $product->price,
                'product_discount' => $product->discount,
            ];
            session()->put('cart', $cart);
            return response()->json([
                'error' => false,
                'message' => 'S???n ph???m ' . $product->name . ' ???????c th??m v??o gi??? h??ng.'
            ]);
        }
    }

    public function update_cart(Request $request)
    {
        $data = $request->all();
        $cart = session()->get('cart');

        $shopping_carts = session()->get('cart');
        foreach ($shopping_carts as $product_id => $info) {
            $product = null;
            $product = Product::find($product_id);
            if ($product->remaining > $data['quantity_' . $product_id]) {
                $cart[$product_id]['product_quantity'] = $data['quantity_' . $product_id];
                session()->put('cart', $cart);

                //if applying coupon
                $discount = session()->get('discount');
                if ($discount != null) {
                    $total_cart = null;
                    $shopping_carts = session()->get('cart');
                    if (isset($shopping_carts)) {
                        foreach ($shopping_carts as $product_id => $info) {
                            if ($info['product_discount'] != null && $info['product_discount'] != 0) {
                                $total_cart +=  ($info['product_price'] - ($info['product_price'] * $info['product_discount'] / 100)) * $info['product_quantity'];
                            } else {
                                $total_cart += $info['product_price'] * $info['product_quantity'];
                            }
                        }
                    }
                    $coupon = Coupon::find($discount['coupon_id']);
                    if (!$coupon) {
                        return response()->json([
                            'error' => true,
                            'message' => 'Gi??? h??ng ???? ???????c c???p nh???t, tuy nhi??n m?? gi???m gi?? kh??ng h???p l???, xin h??y ki???m tra l???i.',
                        ]);
                    } else {
                        if ($coupon->remaing <= 0 && date('Y-m-d') > $coupon->expire_date) {
                            session()->forget('discount');
                            return response()->json([
                                'error' => true,
                                'message' => 'Gi??? h??ng ???? ???????c c???p nh???t, tuy nhi??n m?? gi???m gi?? kh??ng c??n hi???u l???c n???a, xin h??y ki???m tra l???i.',
                            ]);
                        } else {
                            if ($total_cart < $coupon->minimum_order_value) {
                                session()->forget('discount');
                                return response()->json([
                                    'error' => true,
                                    'message' => 'Gi??? h??ng ???? ???????c c???p nh???t, tuy nhi??n m?? gi???m gi?? ch??? c?? t??c d???ng ?????i v???i nh???ng ????n h??ng t???i thi???u t??? ' . $discount->minimum_order_value . ' ??',
                                ]);
                            } else {
                                $discount = session()->get('discount');
                                $discount['coupon_id'] = $coupon->id;
                                switch ($coupon->type) {
                                    case 0:
                                        $discount['coupon_discount'] = $coupon->discount;
                                        break;
                                    case 1:
                                        $discount['coupon_discount'] = $total_cart * $coupon->discount / 100;
                                        break;
                                }
                                session()->put('discount', $discount);
                                return response()->json([
                                    'error' => false,
                                    'message' => 'Gi??? h??ng ???? ???????c c???p nh???t',
                                ]);
                            }
                        }
                    }
                }
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Kho h??ng ch??? c??n l???i ' . $product->quantity . ' s???n ph???m ' . $product->name,
                ]);
            }
        }
        return response()->json([
            'error' => false,
            'message' => 'Gi??? h??ng ???? ???????c c???p nh???t',
        ]);
    }

    public function apply_coupon(Request $request)
    {
        $total_cart = null;
        $shopping_carts = session()->get('cart');
        if (isset($shopping_carts)) {
            foreach ($shopping_carts as $product_id => $info) {
                if ($info['product_discount'] != null && $info['product_discount'] != 0) {
                    $total_cart +=  ($info['product_price'] - ($info['product_price'] * $info['product_discount'] / 100)) * $info['product_quantity'];
                } else {
                    $total_cart += $info['product_price'] * $info['product_quantity'];
                }
            }
        }
        $coupon = $this->coupon->active()->where('code', '=', $request->code)->first();
        if (!$coupon) {
            return response()->json([
                'error' => true,
                'message' => 'M?? gi???m gi?? kh??ng h???p l???!',
            ]);
        } else {
            if ($coupon->remaing <= 0 && date('Y-m-d') > $coupon->expired_at) {
                return response()->json([
                    'error' => true,
                    'message' => 'M?? gi???m gi?? kh??ng c??n ho???t ?????ng n???a!',
                ]);
            } else {
                if ($total_cart < $coupon->minimum_order_value) {
                    return response()->json([
                        'error' => true,
                        'message' => 'M?? gi???m gi?? n??y ch??? ??p d???ng ?????i v???i ????n h??ng t??? ' . $coupon->minimum_order_value . ' ??',
                    ]);
                } else {
                    $discount = session()->get('discount');
                    $discount['coupon_id'] = $coupon->id;
                    switch ($coupon->type) {
                        case 0:
                            $discount['coupon_discount'] = $coupon->discount;
                            break;
                        case 1:
                            $discount['coupon_discount'] = $total_cart * $coupon->discount / 100;
                            break;
                    }
                    session()->put('discount', $discount);
                    return response()->json([
                        'error' => false,
                        'message' => 'M?? gi???m gi?? ???? ???????c ??p d???ng.',
                    ]);
                }
            }
        }
    }

    public function remove_coupon()
    {
        if (session()->forget('discount') == false) {
            return response()->json([
                'error' => false,
                'message' => 'The coupon has been removed.',
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Error occoured!',
            ]);
        }
    }

    public function remove_item_from_cart(Request $request)
    {
        $cart = session()->get('cart');
        $product = Product::find($request->id);
        if (isset($cart[$request->id])) {
            unset($cart[$request->id]);
            session()->put('cart', $cart);
            return response()->json([
                'error' => false,
                'message' => 'S???n ph???m ' . $product->name . ' ???? ???????c lo???i b??? kh???i gi??? h??ng.',
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'M?? s???n ph???m kh??ng h???p l???',
            ]);
        }
    }

    public function remove_cart()
    {
        if (session()->forget('cart') == false) {
            session()->forget('discount');
            return response()->json([
                'error' => false,
                'message' => 'To??n b??? gi??? h??ng ???? ???????c x??a.',
            ]);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'L???i ph??t sinh',
            ]);
        }
    }

    public function check()
    {
        // check product
        $shopping_carts = session()->get('cart');
        foreach ($shopping_carts as $product_id => $info) {
            $product = null;
            $product = $this->product->find($product_id);
            if ($product->remaining < $info['product_quantity']) {
                return response()->json([
                    'error' => true,
                    'message' => 'Kho h??ng ch??? c??n l???i ' . $product->quantity . ' s???n ph???m ' . $product->name,
                ]);
            }
        }
        // check coupon
        $total_cart = null;
        if (isset($shopping_carts)) {
            foreach ($shopping_carts as $product_id => $info) {
                if ($info['product_discount'] != null && $info['product_discount'] != 0) {
                    $total_cart +=  ($info['product_price'] - ($info['product_price'] * $info['product_discount'] / 100)) * $info['product_quantity'];
                } else {
                    $total_cart += $info['product_price'] * $info['product_quantity'];
                }
            }
        }
        $discount = session()->get('discount');
        if ($discount != null) {
            $coupon = $this->coupon->find($discount['coupon_id']);
            if ($coupon->remaing <= 0 && date('Y-m-d') > $coupon->expired_at) {
                return response()->json([
                    'error' => true,
                    'message' => 'M?? gi???m gi?? kh??ng c??n s??? d???ng ???????c n???a',
                ]);
            } else {
                if ($total_cart < $coupon->minimum_order_value) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Gi??? h??ng ???? ???????c c???p nh???t, tuy nhi??n m?? gi???m gi?? ch??? c?? t??c d???ng ?????i v???i nh???ng ????n h??ng t???i thi???u t??? ' . $discount->minimum_order_value . ' ??',
                    ]);
                }
            }
        }
        return response()->json([
            'error' => false,
            'message' => 'Process to next step.',
        ]);
    }
}
