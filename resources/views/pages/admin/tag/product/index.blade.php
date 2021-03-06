@extends('layouts.admin.index')
@section('title', 'Sản phẩm được gắn thẻ')
@section('content')
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.tag.index') }}">Thẻ</a></li>
                <li class="active">{{ $tag->name }}</li>
                <li><a href="{{ route('admin.tag.product.index', ['id' => $tag->id]) }}">Sản phẩm được gắn thẻ</a></li>
            </ol>
            <div class="table-agile-info">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Sản phẩm được gắn thẻ {{ $tag->name }}
                    </div>
                    <div class="row w3-res-tb">
                        <div class="col-sm-5 m-b-xs">
                            <span class="btn-group">
                                <a href="{{ route('admin.tag.product.index', ['id' => $tag->id]) }}"
                                    class="btn btn-sm btn-default"><i class="fa fa-refresh"></i> Làm mới</a>
                                <a href="{{ route('admin.tag.product.create', ['id' => $tag->id]) }}"
                                    class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Thêm mới</a>
                                <a href="{{ route('admin.tag.product.recycle', ['id' => $tag->id]) }}"
                                    class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Thùng rác</a>
                            </span>
                        </div>
                        <div class="col-sm-2">
                        </div>
                        <div class="col-sm-5">
                            <form enctype="multipart/form-data" method="GET"
                                action="{{ route('admin.tag.product.index', ['id' => $tag->id]) }}">
                                <div class="input-group">
                                    <input type="text" class="input-sm form-control" value="{{ $search }}"
                                        name="search" placeholder="Search">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-default">Tìm kiếm</button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                    <form method="GET"
                        action="{{ route('admin.tag.product.bulk_action', ['id' => $tag->id]) }}"
                        enctype="multipart/form-data">
                        <div class="row w3-res-tb">
                            <div class="col-sm-5 m-b-xs">
                                <select id="bulk_action" name="bulk_action"
                                    class="input-sm form-control w-sm inline v-middle">
                                    <option>Thao tác đa mục tiêu</option>
                                    <option value="0">Tắt</option>
                                    <option value="1">Bật</option>
                                    <option value="2">Loại bỏ</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-default">Áp dụng</button>
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
                        @if (session('errors'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach (session('errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-striped b-t b-light">
                                <thead>
                                    <tr>
                                        <th style="width:20px;">
                                            <label class="i-checks m-b-none">
                                                <input type="checkbox"><i></i>
                                            </label>
                                        </th>
                                        <th>
                                            <div class="dropdown">
                                                <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">ID
                                                    <span class="caret"></span></button>
                                                <ul class="dropdown-menu">
                                                    <li @if ($sort_id == 0) class="active" @endif><a
                                                            href="{{ route('admin.tag.product.index', ['id' => $tag->id, 'sort_id' => 0, 'status' => $status, 'view' => $view]) }}">Inc</a>
                                                    </li>

                                                    <li @if ($sort_id == 1) class="active" @endif><a
                                                            href="{{ route('admin.tag.product.index', ['id' => $tag->id, 'sort_id' => 1, 'status' => $status, 'view' => $view]) }}">Desc</a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </th>
                                        <th>Sản phẩm được gắn thẻ</th>
                                        <th>Hình ảnh</th>
                                        <th>
                                            <div class="dropdown">
                                                <button class="btn dropdown-toggle" type="button"
                                                    data-toggle="dropdown">Trạng thái
                                                    <span class="caret"></span></button>
                                                <ul class="dropdown-menu">
                                                    <li @if ($status == null) class="active" @endif><a
                                                            href="{{ route('admin.tag.product.index', ['id' => $tag->id, 'view' => $view]) }}">Tất
                                                            cả</a>
                                                    </li>
                                                    <li @if ($status == 1) class="active" @endif><a
                                                            href="{{ route('admin.tag.product.index', ['id' => $tag->id, 'sort_id' => $sort_id, 'status' => '1', 'view' => $view]) }}">Hoạt
                                                            động</a>
                                                    </li>

                                                    <li @if ($status == 0 && $status != null) class="active" @endif><a
                                                            href="{{ route('admin.tag.product.index', ['id' => $tag->id, 'sort_id' => $sort_id, 'status' => '0', 'view' => $view]) }}">Ngừng
                                                            hoạt động</a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </th>
                                        <th colspan="2">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tagProducts as $tagProduct)
                                        <tr>
                                            <td>
                                                <label class="i-checks m-b-none">
                                                    <input type="checkbox" value="{{ $tagProduct->id }}"
                                                        name="tagProduct_id_list[]"><i></i>
                                                </label>
                                            </td>
                                            <td>
                                                {{ $tagProduct->id }}
                                            </td>
                                            <td><span class="text-ellipsis">
                                                    {{ $tagProduct->product->name }}
                                                </span>
                                            </td>
                                            <td>
                                                <img style="width: 70px; height : auto"
                                                    src="{{ asset('/storage/images/products/' . $tagProduct->product->image) }}"
                                                    alt="{{ $tagProduct->product->name }}">
                                            </td>
                                            <td>
                                                @if ($tagProduct->verified == 1)
                                                    <div class="alert alert-success" role="alert">
                                                        <strong>Hoạt động</strong>
                                                    </div>
                                                @else
                                                    <div class="alert alert-danger" role="alert">
                                                        <strong>Ngừng hoạt động</strong>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($tagProduct->verified == 0)
                                                    <a onclick="return confirm('Are you sure?')"
                                                        href="{{ route('admin.tag.product.verify', ['id' => $tag->id, 'product_id' => $tagProduct->id, 'verified' => 1]) }}"
                                                        class="btn btn-success" title="Activate">
                                                        <span class="glyphicon glyphicon-check"></span>
                                                    </a>
                                                @else
                                                    <a onclick="return confirm('Are you sure?')"
                                                        href="{{ route('admin.tag.product.verify', ['id' => $tag->id, 'product_id' => $tagProduct->id, 'verified' => 0]) }}"
                                                        class="btn btn-warning" title="Deactivate">
                                                        <span class="glyphicon glyphicon-remove"></span>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                <a class="btn btn-info"
                                                    href="{{ route('admin.tag.product.edit', ['id' => $tag->id, 'product_id' => $tagProduct->id]) }}">
                                                    <span class="glyphicon glyphicon-edit"></span>
                                                </a>
                                            </td>
                                            <td>
                                                <a onclick="return confirm('Are you sure?')"
                                                    href="{{ route('admin.tag.product.delete', ['id' => $tag->id, 'product_id' => $tagProduct->id]) }}"
                                                    class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <footer class="panel-footer">
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">Hiển thị :
                                            {{ $view }} của {{ $tagProducts_count }} Hãng
                                            <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li @if ($view == 10) class="active" @endif><a
                                                    href="{{ route('admin.tag.product.index', ['id' => $tag->id, 'sort_id' => $sort_id, 'status' => $status, 'view' => 10]) }}">10</a>
                                            </li>
                                            <li @if ($view == 15) class="active" @endif><a
                                                    href="{{ route('admin.tag.product.index', ['id' => $tag->id, 'sort_id' => $sort_id, 'status' => $status, 'view' => 15]) }}">15</a>
                                            </li>
                                            <li @if ($view == 20) class="active" @endif><a
                                                    href="{{ route('admin.tag.product.index', ['id' => $tag->id, 'sort_id' => $sort_id, 'status' => $status, 'view' => 20]) }}">20</a>
                                            </li>
                                            <li @if ($view == 30) class="active" @endif><a
                                                    href="{{ route('admin.tag.product.index', ['id' => $tag->id, 'sort_id' => $sort_id, 'status' => $status, 'view' => 30]) }}">30</a>
                                            </li>
                                            <li @if ($view == 40) class="active" @endif><a
                                                    href="{{ route('admin.tag.product.index', ['id' => $tag->id, 'sort_id' => $sort_id, 'status' => $status, 'view' => 40]) }}">40</a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                                <div class="col-sm-7 text-right text-center-xs">
                                    <ul class="pagination pagination-sm m-t-none m-b-none">
                                        {!! $tagProducts->links() !!}
                                    </ul>
                                </div>
                            </div>
                        </footer>
                    </form>
                </div>
            </div>
        </section>
        <!-- footer -->
        @include('components.admin.footer')
        <!-- / footer -->
    </section>

    <!--main content end-->
@endsection
