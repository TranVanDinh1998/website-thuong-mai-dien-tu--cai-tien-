<ul id="products-list" class="products-list">
    @foreach ($products as $product)
        <li class="item odd">
            <div class="col-item">
                <div class="product_image">
                    <div class="images-container"> <a class="product-image" title="{{ $product->name }}"
                            href="{{ route('product_details', ['id' => $product->id]) }}"> <img
                                src="{{ asset('storage/images/products/' . $product->image) }}" class="img-responsive"
                                alt="a" /> </a>
                        <div class="qv-button-container"> <a class="qv-e-button btn-quickview-1"
                                onclick="return quick_view({{ $product->id }});"><span><span>Xem nhanh</span></span></a> </div>
                    </div>
                </div>
                <div class="product-shop">
                    <h2 class="product-name"><a title="{{ $product->name }}"
                            href="product-detail.html">{{ $product->name }}</a>
                    </h2>
                    <div class="price-box">
                        @if ($product->discount != null && $product->discount != 0)
                            <p class="old-price">
                                <span class="price-label"></span>
                                <span id="old-price-212" class="price">
                                    {{ $product->price }} d
                                </span>
                            </p>
                            <p class="special-price">
                                <span class="price-label"></span>
                                <span id="product-price-212" class="price">
                                    {{ $product->price - ($product->price * $product->discount) / 100 }} d
                                </span>
                            </p>
                        @else
                            <p class="regular-price">
                                <span class="price-label"></span>
                                <span id="product-price-212" class="price"> {{ $product->price }} d
                                </span>
                            </p>
                        @endif

                    </div>
                    <div class="ratings">
                        <div class="rating-box">
                            <div style="width:{{ $product->rating * 20 }}%" class="rating"></div>
                        </div>
                        <p class="rating-links"> <a href="#">1 ????nh gi??</a> <span class="separator">|</span> <a
                                href="#review-form">Th??m ????nh gi?? c???a b???n</a> </p>
                    </div>
                    <div class="price-block">
                        <div class="price-box">
                            <p>Category :
                                @foreach ($categories as $category)
                                    @if ($category->id == $product->category_id)
                                        <a href="{{ URL::to('filter/' . $category->id) }}">
                                            {{ $category->name }} </a>
                                    @endif
                                @endforeach
                            </p>
                            <p>Producer :
                                @foreach ($producers as $producer)
                                    @if ($producer->id == $product->producer_id)
                                        {{ $producer->name }}
                                    @endif
                                @endforeach
                            </p>
                            <p>Sold : {{ $product->quantity - $product->remaining }}</p>
                            <p>Remaining : {{ $product->remaining }}</p>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="button" onclick="add_to_cart({{ $product->id }});" title="Th??m v??o gi??? h??ng"
                            class="button btn-cart"><span>Th??m v??o gi??? h??ng</span></button>
                        <span class="add-to-links">
                            @if (Auth::user())
                                <a onclick="add_to_wish_list({{ $product->id }});" title="Th??m v??o danh s??ch ??u th??ch"
                                    class="link-wishlist">
                                    <span>??u th??ch</span>
                                </a>
                            @else
                                <a onclick="alert('You need to login to add this product to the wish list!');"
                                    title="Add to Wishlist" class="link-wishlist">
                                    <span>??u th??ch</span></a>
                                @endif <a title="Th??m v??o danh s??ch so s??nh" class="button link-compare"
                                    onclick="add_to_compare({{ $product->id }})"><span>So s??nh</span></a>
                        </span>
                    </div>
                </div>
            </div>
        </li>
    @endforeach
</ul>
