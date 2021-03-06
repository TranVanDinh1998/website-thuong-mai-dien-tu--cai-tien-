<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\RemoveImage;
use App\Http\Helpers\UploadImage;
use App\Http\Requests\GeneralRequest;
use App\Http\Requests\ImageRequest;
use App\Models\Collection;
use App\Models\CollectionProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class CollectionProductController extends Controller
{
    // contructor
    public function __construct(Collection $collection, CollectionProduct $collectionProduct, Product $product)
    {
        $this->product = $product;
        $this->middleware('auth:admin');
        $this->collection = $collection;
        $this->collectionProduct = $collectionProduct;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id, Request $request)
    {
        // parameter
        $sort_id = $request->sort_id;
        $status = $request->status;
        // search
        $search = $request->search;
        // view
        $view = $request->has('view') ? $request->view : 10;
        // data
        $collection = $this->collection->find($id);
        $collectionProducts = $collection->collectionProducts()->withoutTrashed();
        $collectionProducts_count = $collectionProducts->count();
        $collectionProducts = $collectionProducts->paginate($view);
        return view('pages.admin.collection.product.index', [
            'collectionProducts' => $collectionProducts,
            'collection' => $collection,
            // parameter
            'sort_id' => $sort_id,
            'search' => $search,
            'status' => $status,
            'search' => $search,
            'view' => $view,
            'collectionProducts_count' => $collectionProducts_count,
        ]);
    }

    /**
     * Verify an item.
     *
     * @return \Illuminate\Http\Response
     */
    public function verify($id, $product_id, $verified)
    {
        //
        $verify = $this->collectionProduct->find($product_id)->update([
            'verified' => $verified,
        ]);
        if ($verified == 0)
            return back()->with('success', 'S???n ph???m #' . $product_id . ' ???? ???????c t???t .');
        else
            return back()->with('success', 'S???n ph???m #' . $product_id . ' ???? ???????c b???t.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //
        $collection = $this->collection->find($id);
        $products = $this->product->all();
        return view('pages.admin.collection.product.create', [
            'products' => $products,
            'collection' => $collection,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id, Request $request)
    {
        //
        $result = $this->collectionProduct->create([
            'collection_id' => $id,
            'product_id' => $request->product_id,
        ]);
        return $result ? back()->with('success', 'S???n ph???m m???i ???????c th??m v??o b??? s??u t???p th??nh c??ng.') : back()->with('error', 'L???i x???y ra trong qu?? tr??nh th??m S???n ph???m v??o b??? s??u t???p');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $product_id)
    {
        //
        $collection = $this->collection->find($id);
        $products = $this->product->all();
        $collectionProduct = $this->collectionProduct->find($product_id);
        return view('pages.admin.collection.product.edit', [
            'collection' => $collection,
            'collectionProduct' => $collectionProduct,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $product_id, UploadImage $uploadImage)
    {
        //
        $collectionProduct = $this->collectionProduct->find($product_id);
        $result = $collectionProduct->update([
            'collection_id' => $id,
            'product_id' => $request->product_id,
        ]);
        return $result ? back()->with('success', 'S???n ph???m #' . $product_id . ' ???? ???????c c???p nh???t trong b??? s??u t???p.') : back()->with('error', 'L???i x???y ra khi c???p nh???t S???n ph???m #' . $product_id);
    }

    /**
     * Softdelete the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id, $product_id)
    {
        $collectionProduct = $this->collectionProduct->find($product_id);
        $result = $collectionProduct->delete();
        return $result ? back()->withSuccess('S???n ph???m #' . $product_id . ' ???? b??? lo???i b???.') : back()->withError('X???y ra l???i khi lo???i b??? S???n ph???m #' . $product_id);
    }


    /**
     * Display a listing of the softdeleted resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function recycle($id, Request $request)
    {
        // parameter
        $sort_id = $request->sort_id;
        $status = $request->status;
        // search
        $search = $request->search;
        // view
        $view = $request->has('view') ? $request->view : 10;
        // data
        $collection = $this->collection->find($id);
        $collectionProducts = $collection->collectionProducts()->onlyTrashed();
        $collectionProducts_count = $collectionProducts->count();
        $collectionProducts = $collectionProducts->paginate($view);
        return view('pages.admin.collection.product.recycle', [
            'collectionProducts' => $collectionProducts,
            'collection' => $collection,
            // parameter
            'sort_id' => $sort_id,
            'search' => $search,
            'status' => $status,
            'search' => $search,
            'view' => $view,
            'collectionProducts_count' => $collectionProducts_count,
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id, $product_id)
    {
        $result = $this->collectionProduct->onlyTrashed()->find($product_id)->restore();
        return $result ? back()->withSuccess('S???n ph???m #' . $product_id . ' ???? ???????c ph???c h???i.') : back()->withError('L???i x???y ra trong qu?? tr??nh kh??i ph???c S???n ph???m #' . $product_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $product_id)
    {
        //
        $collectionProduct = $this->collectionProduct->onlyTrashed()->find($product_id);
        $result = $collectionProduct->forceDelete();
        return $result ? back()->with('success', 'S???n ph???m #' . $product_id . ' ???? ???????c x??a v??nh vi???n.') : back()->withError('L???i x???y trong qu?? tr??nh x??a v??nh vi???n S???n ph???m #' . $product_id);
    }

    public function bulk_action($id, Request $request)
    {
        if ($request->has('bulk_action')) {
            if ($request->has('collectionProduct_id_list')) {
                $message = null;
                $errors = null;
                switch ($request->bulk_action) {
                    case 0: // deactivate
                        $message = 'S???n ph???m ';
                        foreach ($request->collectionProduct_id_list as $collectionProduct_id) {
                            $collectionProduct = $this->collectionProduct->find($collectionProduct_id);
                            $verify = $collectionProduct->update([
                                'verified' => 0,
                            ]);
                            if ($verify) {
                                $message .= ' #' . $collectionProduct->id . ', ';
                            } else {
                                $errors[] = 'L???i x???y ra khi t???t S???n ph???m #' . $collectionProduct->id . '.';
                            }
                        }
                        $message .= '???? ???????c t???t.';
                        break;
                    case 1: // activate
                        $message = 'S???n ph???m ';
                        foreach ($request->collectionProduct_id_list as $collectionProduct_id) {
                            $collectionProduct = $this->collectionProduct->find($collectionProduct_id);
                            $verify = $collectionProduct->update([
                                'verified' => 1,
                            ]);
                            if ($verify) {
                                $message .= ' #' . $collectionProduct->id . ', ';
                            } else {
                                $errors[] = 'L???i x???y ra khi b???t S???n ph???m #' . $collectionProduct->id . '.';
                            }
                        }
                        $message .= '???? ???????c b???t.';
                        break;
                    case 2: // remove
                        $message = 'S???n ph???m';
                        foreach ($request->collectionProduct_id_list as $collectionProduct_id) {
                            $collectionProduct = null;
                            $collectionProduct = $this->collectionProduct->find($collectionProduct_id);
                            $result = $collectionProduct->delete();
                            if ($result) {
                                $message .= ' #' . $collectionProduct->id . ', ';
                            } else {
                                $errors[] = 'L???i x???y ra khi lo???i b??? S???n ph???m #' . $collectionProduct->id . '.';
                            }
                        }
                        $message .= '???? ???????c lo???i b???.';
                        break;
                    case 3: // restore
                        $message = 'S???n ph???m';
                        foreach ($request->collectionProduct_id_list as $collectionProduct_id) {
                            $collectionProduct = null;
                            $collectionProduct = $this->collectionProduct->onlyTrashed()->find($collectionProduct_id);
                            $result = $collectionProduct->restore();
                            if ($result) {
                                $message .= ' #' . $collectionProduct->id . ', ';
                            } else {
                                $errors[] = 'L???i x???y ra khi kh??i ph???c S???n ph???m #' . $collectionProduct->id . '.';
                            }
                        }
                        $message .= '???? ???????c kh??i ph???c.';
                        break;
                    case 4: // delete
                        $message = 'S???n ph???m';
                        foreach ($request->collectionProduct_id_list as $collectionProduct_id) {
                            $collectionProduct = null;
                            $collectionProduct = $this->collectionProduct->onlyTrashed()->find($collectionProduct_id);
                            $result = $collectionProduct->forceDelete();
                            if ($result) {
                                $message .= ' #' . $collectionProduct->id . ', ';
                            } else {
                                $errors[] = 'L???i x???y ra khi x??a v??nh vi???n S???n ph???m #' . $collectionProduct->id . '.';
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
                return back()->withError('H??y ch???n ??t nh???t 1 S???n ph???m ????? th???c hi???n thao t??c!');
            }
        } else {
            return back()->withError('H??y ch???n 1 thao t??c c??? th???!');
        }
    }
}
