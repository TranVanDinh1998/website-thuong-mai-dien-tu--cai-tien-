<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Collection;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Address;
use App\Models\Ward;
use App\Models\District;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function __construct(Order $order, OrderDetail $orderDetail, Product $product)
    {
        $this->product = $product;
        $this->order = $order;
        $this->middleware('auth:admin');
        $this->orderDetail = $orderDetail;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orders = $this->order->notDone()
            ->status($request)->sortId($request)->active()
            ->sortTotal($request)->sortDate($request)->sortPaid($request);
        $orders_count = $orders->count();
        $view = $request->has('view') ? $request->view : 10;
        $orders = $orders->paginate($view);

        // filter
        $sort_id = $request->sort_id;
        $status = $request->status;
        $sort_total = $request->sort_total;
        $sort_date = $request->sort_date;
        $sort_paid = $request->sort_paid;
        // user
        $user = Auth::guard('admin')->user();
        return view('pages.admin.order.index', [
            // order
            'orders' => $orders,
            'orders_count' => $orders_count,
            'view' => $view,

            // filter
            'sort_id' => $sort_id,
            'sort_total' => $sort_total,
            'sort_date' => $sort_date,
            'status' => $status,
            'sort_paid' => $sort_paid,
            // current user
            'current_user' => $user,

        ]);
    }

    /**
     * Verify an item.
     *
     * @return \Illuminate\Http\Response
     */
    public function verify($id, $verified)
    {
        //
        $order = $this->order->find($id);
        $verify = $order->update([
            'verified' => $verified,
        ]);
        if ($verified == 0) {
            foreach ($order->orderDetails as $detail) {
                $product = null;
                $product = $this->product->find($detail->product_id);
                $product = $product->update(['remaining' => $product->remaining + $detail->quantity]);
            }
            return back()->with('success', 'H??a ????n #' . $id . ' ???? ???????c b???t .');
        } else {
            foreach ($order->orderDetails as $detail) {
                $product = null;
                $product = $this->product->find($detail->product_id);
                $product = $product->update(['remaining' => $product->remaining - $detail->quantity]);
            }
            return back()->with('success', 'H??a ????n #' . $id . ' ???? ???????c t???t.');
        }
    }

    public function comfirm($id, $confirmed)
    {
        //
        $order = $this->order->find($id);
        if ($confirmed == 0) {
            $confirmation = $order->update([
                'delivered_at' => null,
                'done' => $confirmed,
            ]);
            return back()->withSuccess('H??a ????n #' . $id . ' ch??a ???????c x??c nh???n v?? ho??n t???t');
        } else {
            if (!$order->done)
                return back()->withError('H??a ????n  #' . $id . ' ch??a ???????c thanh to??n, kh??ng th??? x??c nh???n');
            else
                $confirmation = $order->update([
                    'delivered_at' => date("Y-m-d H:i:s"),
                    'done' => $confirmed,
                ]);
            return back()->withSuccess('H??a ????n  #' . $id . ' ???? ???????c x??c nh???n v?? ho??n t???t');
        }
    }

    public function pay($id, $paid)
    {
        //
        $order = $this->order->find($id);
        $confirmation = $order->update([
            'paid' => $paid,
        ]);
        if ($paid == 0)
            return back()->withSuccess('H??a ????n #' . $id . ' ch??a ???????c thanh to??n');
        else
            return back()->withSuccess('H??a ????n  #' . $id . ' ???? ???????c thanh to??n');
    }

    public function history(Request $request)
    {
        $orders =  $this->order->done()
            ->status($request)->sortId($request)
            ->sortTotal($request)->sortDate($request)->sortPaid($request);
        $orders_count = $orders->count();
        $view = $request->has('view') ? $request->view : 10;

        // filter
        $sort_id = $request->sort_id;
        $status = $request->status;
        $sort_total = $request->sort_total;
        $sort_date = $request->sort_date;
        $sort_paid = $request->sort_paid;
        // user
        $user = Auth::guard('admin')->user();
        return view('pages.admin.order.history', [
            // order
            'orders' => $orders,
            'orders_count' => $orders_count,
            'view' => $view,

            // filter
            'sort_id' => $sort_id,
            'sort_total' => $sort_total,
            'sort_date' => $sort_date,
            'status' => $status,
            'sort_paid' => $sort_paid,
            'current_user' => $user,

        ]);
    }

    public function cancel(Request $request)
    {
        $orders = $this->order
            ->status($request)->sortId($request)->inactive()
            ->sortTotal($request)->sortDate($request)->sortPaid($request);
        $orders_count = $orders->count();
        $view = $request->has('view') ? $request->view : 10;
        $orders = $orders->paginate($view);

        // filter
        $sort_id = $request->sort_id;
        $status = $request->status;
        $sort_total = $request->sort_total;
        $sort_date = $request->sort_date;
        $sort_paid = $request->sort_paid;
        // user
        $user = Auth::guard('admin')->user();
        return view('pages.admin.order.cancel', [
            // order
            'orders' => $orders,
            'orders_count' => $orders_count,
            'view' => $view,

            // filter
            'sort_id' => $sort_id,
            'sort_total' => $sort_total,
            'sort_date' => $sort_date,
            'status' => $status,
            'sort_paid' => $sort_paid,
            'current_user' => $user,

        ]);
    }

    public function edit($id)
    {
        $order = $this->order->find($id);
        // user
        $user = Auth::guard('admin')->user();
        return view('pages.admin.order.detail', [
            // order
            'order' => $order,
            'current_user' => $user,
        ]);
    }

    public function update(OrderRequest $request, $id)
    {
        $order = $this->order->find($id);
        $result = $order->update(['status' => $request->status, 'paid' => $request->payment]);
        return $result ? back()->withSuccess('????n h??ng #' . $id . ' ???? ???????c c???p nh???t') : back()->withError('X???y ra l???i trong qu?? tr??nh c???p nh???t ????n h??ng #' . $id);
    }

    public function delete($id)
    {
        $order = $this->order->find($id);
        if ($order->done == 0) {
            return back()->with('error', '????n h??ng #' . $order->id . ' ch??a ???????c ho??n thi???n.');
        } else {
            if ($order->paid == 1) {
                return back()->with('error', '????n h??ng #' . $order->id . ' ???? ???????c thanh to??n.');
            } else {
                $result = $order->delete();
                return $result ? back()->withSuccess('????n h??ng #' . $id . ' ???? ???????c x??a') : back()->withError('L???i x???y ra khi x??a ????n hang #' . $id);
            }
        }
    }
    public function recycle(Request $request)
    {
        $orders = $this->order->onlyTrashed()
            ->status($request)->sortId($request)
            ->sortTotal($request)->sortDate($request)->sortPaid($request);
        $orders_count = $orders->count();
        $view = $request->has('view') ? $request->view : 10;
        $orders = $orders->paginate($view);
        // filter
        $sort_id = $request->sort_id;
        $status = $request->status;
        $sort_total = $request->sort_total;
        $sort_date = $request->sort_date;
        $sort_paid = $request->sort_paid;
        // user
        $user = Auth::guard('admin')->user();
        return view('pages.admin.order.recycle', [
            // order
            'orders' => $orders,
            'order_count' => $orders_count,
            'view' => $view,

            // filter
            'sort_id' => $sort_id,
            'sort_total' => $sort_total,
            'sort_date' => $sort_date,
            'status' => $status,
            'sort_paid' => $sort_paid,
            // current user
            'current_user' => $user,

        ]);
    }
    public function restore($id)
    {
        $result = $this->order->onlyTrashed()->find($id)->restore();
        return back()->with('success', '????n h??ng #' . $id . ' ???? ???????c kh??i ph???c.');
    }

    public function destroy($id)
    {
        $order = $this->order->find($id);
        if ($order->done == 0) {
            return back()->with('error', '????n h??ng #' . $order->id . ' ch??a ???????c ho??n thi???n.');
        } else {
            if ($order->paid == 1) {
                return back()->with('error', '????n h??ng #' . $order->id . ' ???? ???????c thanh to??n.');
            } else {
                $result = $order->forceDelete();
                return $result ? back()->withSuccess('????n h??ng #' . $id . ' ???? ???????c x??a v??nh vi???n') : back()->withError('L???i x???y ra khi x??a v??nh vi???n ????n hang #' . $id);
            }
        }
    }

    public function bulk_action(Request $request)
    {
        if ($request->has('bulk_action')) {
            if ($request->has('order_id_list')) {
                $message = null;
                $errors = null;
                switch ($request->bulk_action) {
                    case 0: // deactivate
                        $message = '????n h??ng ';
                        foreach ($request->order_id_list as $order_id) {
                            $order = $this->order->find($order_id);
                            $verify = $order->update([
                                'verified' => 0,
                            ]);
                            if ($verify) {
                                $message .= ' #' . $order->id . ', ';
                            } else {
                                $errors[] = 'L???i x???y ra khi t???t ????n h??ng #' . $order->id . '.';
                            }
                        }
                        $message .= '???? ???????c t???t.';
                        break;
                    case 1: // activate
                        $message = '????n h??ng ';
                        foreach ($request->order_id_list as $order_id) {
                            $order = $this->order->find($order_id);
                            $verify = $order->update([
                                'verified' => 1,
                            ]);
                            if ($verify) {
                                $message .= ' #' . $order->id . ', ';
                            } else {
                                $errors[] = 'L???i x???y ra khi b???t ????n h??ng #' . $order->id . '.';
                            }
                        }
                        $message .= '???? ???????c b???t.';
                        break;
                    case 2: // undone
                        $message = '????n h??ng ';
                        foreach ($request->order_id_list as $order_id) {
                            $order = $this->order->find($order_id);
                            $verify = $order->update([
                                'done' => 0,
                            ]);
                            if ($verify) {
                                $message .= ' #' . $order->id . ', ';
                            } else {
                                $errors[] = 'L???i x???y ra khi ????nh d???u ????n h??ng #' . $order->id . ' l?? ch??a ho??n thi???n.';
                            }
                        }
                        $message .= '???? ???????c ????nh d???u l?? ch??a ho??n thi???n.';
                        break;
                    case 3: // done
                        $message = '????n h??ng ';
                        foreach ($request->order_id_list as $order_id) {
                            $order = $this->order->find($order_id);
                            $verify = $order->update([
                                'done' => 1,
                            ]);
                            if ($verify) {
                                $message .= ' #' . $order->id . ', ';
                            } else {
                                $errors[] = 'L???i x???y ra khi ho??n thi???n ????n h??ng #' . $order->id . '.';
                            }
                        }
                        $message .= '???? ???????c ????nh d???u l?? ho??n thi???n.';
                        break;
                    case 4: // paid
                        $message = '????n h??ng ';
                        foreach ($request->order_id_list as $order_id) {
                            $order = $this->order->find($order_id);
                            $verify = $order->update([
                                'paid' => 1,
                            ]);
                            if ($verify) {
                                $message .= ' #' . $order->id . ', ';
                            } else {
                                $errors[] = 'L???i x???y ra khi ????nh d???u ????n h??ng #' . $order->id . ' l?? ???? thanh to??n.';
                            }
                        }
                        $message .= '???? ???????c thanh to??n.';
                        break;
                    case 5: // un paid
                        $message = '????n h??ng ';
                        foreach ($request->order_id_list as $order_id) {
                            $order = $this->order->find($order_id);
                            $verify = $order->update([
                                'paid' => 0,
                            ]);
                            if ($verify) {
                                $message .= ' #' . $order->id . ', ';
                            } else {
                                $errors[] = 'L???i x???y ra khi ????nh d???u ????n h??ng #' . $order->id . ' l?? ch??a thanh to??n.';
                            }
                        }
                        $message .= '???? ???????c ????nh d???u l?? ch??a thanh to??n.';
                        break;
                    case 6: // remove
                        $message = '????n h??ng';
                        foreach ($request->order_id_list as $order_id) {
                            $order = null;
                            $order = $this->order->find($order_id);
                            $result = $order->delete();
                            if ($result) {
                                $message .= ' #' . $order->id . ', ';
                            } else {
                                $errors[] = 'L???i x???y ra khi lo???i b??? ????n h??ng #' . $order->id . '.';
                            }
                        }
                        $message .= '???? ???????c lo???i b???.';
                        break;
                    case 7: // restore
                        $message = '????n h??ng';
                        foreach ($request->order_id_list as $order_id) {
                            $order = null;
                            $order = $this->order->onlyTrashed()->find($order_id);
                            $result = $order->restore();
                            if ($result) {
                                $message .= ' #' . $order->id . ', ';
                            } else {
                                $errors[] = 'L???i x???y ra khi kh??i ph???c ????n h??ng #' . $order->id . '.';
                            }
                        }
                        $message .= '???? ???????c kh??i ph???c.';
                        break;
                    case 8: // delete
                        $message = '????n h??ng';
                        foreach ($request->order_id_list as $order_id) {
                            $order = null;
                            $order = $this->order->onlyTrashed()->find($order_id);
                            $result = $order->forceDelete();
                            if ($result) {
                                $message .= ' #' . $order->id . ', ';
                            } else {
                                $errors[] = 'L???i x???y ra khi x??a v??nh vi???n ????n h??ng #' . $order->id . '.';
                            }
                        }
                        $message .= '???? ???????c x??a v??nh vi???n.';
                        break;
                }
                if ($errors != null) {
                    return back()->withSuccess($message)->withErrors($errors);
                } else {
                    return back()->withSuccess($message);
                }
            } else {
                return back()->withError('H??y ch???n ??t nh???t 1 ????n h??ng ????? th???c hi???n thao t??c!');
            }
        } else {
            return back()->withError('H??y ch???n 1 thao t??c c??? th???!');
        }
    }
}
