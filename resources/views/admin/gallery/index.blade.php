@extends('admin.layout')
@section('title', 'Gallery')
@section('content')
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.product.index') }}">Product management</a></li>
                <li><a href="#">{{ $product->name }}</a></li>
                <li class="active">Gallery</li>
            </ol>
            <section class="panel">
                <div class="panel-heading">
                    Gallery
                </div>
                <div class="row w3-res-tb">
                    <div class="col-sm-5 m-b-xs">
                        <span class="btn-group">
                            <a 
                            href="{{ route('admin.product.image.index',['id'=>$product->id]) }}" 
                            class="btn btn-sm btn-default"><i
                                    class="fa fa-refresh"></i> Refresh</a>
                            <a 
                            href="{{ route('admin.product.image.add',['id'=>$product->id]) }}" 
                            class="btn btn-sm btn-success"><i
                                    class="fa fa-plus"></i> Add</a>
                            <a 
                            href="{{ route('admin.product.image.recycle',['id'=>$product->id]) }}" 
                            class="btn btn-sm btn-danger"><i
                                    class="fa fa-trash"></i> Recycle</a>
                        </span>
                    </div>
                    <div class="col-sm-2">
                    </div>
                    <div class="col-sm-5">
                    </div>
                </div>
                <form method="GET" action="{{ route('admin.product.image.bulk_action',['id'=>$product->id]) }}" enctype="multipart/form-data">
                <div class="row w3-res-tb">
                    <div class="col-sm-5 m-b-xs">
                        <select name="bulk_action" class="input-sm form-control w-sm inline v-middle">
                            <option>Bulk action</option>
                            <option value="0">Deactivate</option>
                            <option value="1">Activate</option>
                            <option value="2">Remove</option>
                        </select>
                        <button class="btn btn-sm btn-default">Apply</button>
                    </div>
                    <div class="col-sm-4">
                    </div>
                    <div class="col-sm-3">
                    </div>
                </div>
                @if (session('success'))
                    <div id="success_msg" class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div id="error_msg" class="alert alert-danger">
                        {!! session('error') !!}
                    </div>
                @endif
                <!--main content start-->
                <!-- gallery -->
                {{-- <div class="gallery">
                    <div class="gallery-grids">
                        @foreach ($product.images as $product.image)
                            <div class="col-sm-4 gallery-grids-left">
                                <div class="gallery-grid">
                                    <a class="example-image-link"
                                        href="{{ url('uploads/product.images-images/' . $product.image->id . '/' . $product.image->image) }}"
                                        data-lightbox="example-set" data-title="{{ $product.image->image }}">
                                        <img src="{{ url('uploads/product.images-images/' . $product.image->id . '/' . $product.image->image) }}"
                                            alt="" />
                                        <div class="captn">
                                            <h4>Zoom</h4>
                                        </div>
                                    </a>
                                </div>
                                <a onclick="return confirm('Are you sure?')"
                                    href="{{ URL::to('administrator/product.image/' . $product.image->id . '/gallery/remove/' . $product.image->id) }}"
                                    class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- //gallery -->
                <!--main content end-->
                <!-- //gallery -->
                <footer class="panel-footer">
                    <div class="row">
                        <div class="col-sm-5 text-center">
                            <small class="text-muted inline m-t-sm m-b-sm">showing 20-30 of 50 items</small>
                        </div>
                    </div>
                </footer> --}}
                <div class="table-responsive">
                    <table class="table table-striped b-t b-light">
                        <thead>
                            <tr>
                                <th style="width:20px;">
                                    <label class="i-checks m-b-none">
                                        <input name="product_image_id_list[]" type="checkbox"><i></i>
                                    </label>
                                </th>
                                <th>
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">ID
                                            <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li @if ($sort_id == 0)
                                                class="active"
                                                @endif
                                                ><a
                                                    href="{{ route('admin.product.image.index', ['id'=>$product->id,'sort_id' => 0, 'status' => $status, 'view' => $view]) }}">Inc</a>
                                            </li>

                                            <li @if ($sort_id == 1)
                                                class="active"
                                                @endif
                                                ><a
                                                    href="{{ route('admin.product.image.index', ['id'=>$product->id,'sort_id' => 1, 'status' => $status, 'view' => $view]) }}">Desc</a>
                                            </li>

                                        </ul>
                                    </div>
                                </th>
                                <th>Image</th>
                                <th>
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">Status
                                            <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li @if ($status == null)
                                                class="active"
                                                @endif
                                                ><a
                                                    href="{{ route('admin.product.image.index', ['id'=>$product->id,'sort_id' => $sort_id, 'view' => $view]) }}">All</a>
                                            </li>
                                            <li @if ($status == 1)
                                                class="active"
                                                @endif
                                                ><a
                                                    href="{{ route('admin.product.image.index', ['id'=>$product->id,'sort_id' => $sort_id, 'status' => '1', 'view' => $view]) }}">Active</a>
                                            </li>
                                            <li @if ($status == 0 && $status != null)
                                                class="active"
                                                @endif
                                                ><a
                                                    href="{{ route('admin.product.image.index', ['id'=>$product->id,'sort_id' => $sort_id, 'status' => '0', 'view' => $view]) }}">Inactive</a>
                                            </li>

                                        </ul>
                                    </div>
                                </th>
                                <th colspan="4">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-product_images">
                            <div id="display-product_images">
                                @foreach ($product_images as $product_image)
                                    <tr>
                                        <td>
                                            <label class="i-checks m-b-none">
                                                <input type="checkbox" value="{{ $product_image->id }}"
                                                    name="product_image_id_list[]"><i></i>
                                            </label>
                                        </td>
                                        <td>
                                            {{ $product_image->id }}
                                        </td>
                                        <td>
                                            <div class="gallery-grids-left">
                                                <div class="gallery-grid">
                                                    <a class="example-image-link"
                                                        href="{{ url('uploads/products-images/' . $product->id . '/' . $product_image->image) }}"
                                                        data-lightbox="example-set"
                                                        data-title="{{ $product_image->image }}">
                                                        <img style="width: 400px;height:auto;" src="{{ url('uploads/products-images/' . $product->id . '/' . $product_image->image) }}"
                                                            alt="" />
                                                        <div class="captn">
                                                            <h4>Zoom</h4>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($product_image->is_actived == 1)
                                                <div class="alert alert-success" role="alert">
                                                    <strong>Active</strong>
                                                </div>
                                            @else
                                                <div class="alert alert-danger" role="alert">
                                                    <strong>Inactive</strong>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($product_image->is_actived == 0)
                                                <a onclick="return confirm('Are you sure?')"
                                                    href="{{route('admin.product.image.activate',['id'=>$product->id,'image_id'=>$product_image->id])}}"
                                                    class="btn btn-success" title="Activate">
                                                    <span class="glyphicon glyphicon-check"></span>
                                                </a>
                                            @else
                                                <a onclick="return confirm('Are you sure?')"
                                                    href="{{route('admin.product.image.deactivate',['id'=>$product->id,'image_id'=>$product_image->id])}}"
                                                    class="btn btn-warning" title="Deactivate">
                                                    <span class="glyphicon glyphicon-remove"></span>
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-info"
                                                href="{{route('admin.product.image.edit',['id'=>$product->id,'image_id'=>$product_image->id])}}">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>
                                        </td>
                                        <td>
                                            <a onclick="return confirm('Are you sure?')"
                                                href="{{route('admin.product.image.remove',['id'=>$product->id,'image_id'=>$product_image->id])}}"
                                                class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </div>
                        </tbody>
                    </table>
                </div>
                <footer class="panel-footer">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="dropdown">
                                <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">View : {{ $view }}
                                    of {{ $count_image }} item(s)
                                    <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li @if ($view == 10)
                                        class="active"
                                        @endif
                                        ><a
                                            href="{{ route('admin.product.image.index', ['id'=>$product->id,'sort_id' => $sort_id, 'status' => $status, 'view' => 10]) }}">10</a>
                                    </li>
                                    <li @if ($view == 15)
                                        class="active"
                                        @endif><a
                                            href="{{ route('admin.product.image.index', ['id'=>$product->id,'sort_id' => $sort_id, 'status' => $status, 'view' => 15]) }}">15</a>
                                    </li>
                                    <li @if ($view == 20)
                                        class="active"
                                        @endif><a
                                            href="{{ route('admin.product.image.index', ['id'=>$product->id,'sort_id' => $sort_id, 'status' => $status, 'view' => 20]) }}">20</a>
                                    </li>
                                    <li @if ($view == 30)
                                        class="active"
                                        @endif><a
                                            href="{{ route('admin.product.image.index', ['id'=>$product->id,'sort_id' => $sort_id, 'status' => $status, 'view' => 30]) }}">30</a>
                                    </li>
                                    <li @if ($view == 40)
                                        class="active"
                                        @endif><a
                                            href="{{ route('admin.product.image.index', ['id'=>$product->id,'sort_id' => $sort_id, 'status' => $status, 'view' => 40]) }}">40</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-7 text-right text-center-xs">
                            <ul class="pagination pagination-sm m-t-none m-b-none">
                                {!! $product_images->withQueryString()->links() !!}

                            </ul>
                        </div>
                    </div>
                </footer>
                </form>

            </section>
        </section>
        <!-- footer -->
        @include('admin.footer')
        <!-- / footer -->
    </section>

    <!--main content end-->
    <script src="{{ url('admin/js/lightbox-plus-jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#success_msg").fadeOut(10000);
            $("#error_msg").fadeOut(10000);
        });

    </script>
@endsection
