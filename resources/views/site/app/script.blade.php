{{-- RTL flag for JS --}}
<script>
    window.isRTL = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};
</script>

<script src="{{ asset('cozastore/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
{{-- ... باقي الـ scripts زي ما هي --}}




<script src="{{ asset('cozastore/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
<script src="{{ asset('cozastore/vendor/animsition/js/animsition.min.js') }}"></script>
<script src="{{ asset('cozastore/vendor/bootstrap/js/popper.js') }}"></script>
<script src="{{ asset('cozastore/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('cozastore/vendor/select2/select2.min.js') }}"></script>
<script src="{{ asset('cozastore/vendor/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('cozastore/vendor/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('cozastore/vendor/slick/slick.min.js') }}"></script>
<script src="{{ asset('cozastore/js/slick-custom.js') }}"></script>
<script src="{{ asset('cozastore/vendor/parallax100/parallax100.js') }}"></script>
<script src="{{ asset('cozastore/vendor/MagnificPopup/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('cozastore/vendor/isotope/isotope.pkgd.min.js') }}"></script>
<script src="{{ asset('cozastore/vendor/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('cozastore/vendor/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('cozastore/js/main.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const i18n = {
        wishlistAdded: @json(__('site.wishlist_added')),
        cartAdded: @json(__('site.cart_added')),
        combinationNotAvailable: @json(__('site.combination_not_available')),
        pleaseSelectRequiredOptions: @json(__('site.please_select_required_options')),
    };

    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.js-select2').each(function () {
            $(this).select2({
                minimumResultsForSearch: 20,
                dropdownParent: $(this).next('.dropDownSelect2')
            });
        });
    }

    if (typeof $ !== 'undefined' && typeof $.fn.parallax100 !== 'undefined' && $('.parallax100').length) {
        $('.parallax100').parallax100();
    }

    if (typeof $ !== 'undefined' && $.fn.magnificPopup) {
        $('.gallery-lb').each(function () {
            $(this).magnificPopup({
                delegate: 'a',
                type: 'image',
                gallery: { enabled: true },
                mainClass: 'mfp-fade'
            });
        });
    }

    if (typeof PerfectScrollbar !== 'undefined') {
        $('.js-pscroll').each(function () {
            $(this).css('position', 'relative');
            $(this).css('overflow', 'hidden');

            const ps = new PerfectScrollbar(this, {
                wheelSpeed: 1,
                scrollingThreshold: 1000,
                wheelPropagation: false,
            });

            $(window).on('resize', function () {
                ps.update();
            });
        });
    }

    $('.js-addwish-b2').on('click', function (e) {
        e.preventDefault();
    });

    $('.js-addwish-b2').each(function () {
        const nameProduct = $(this).parent().parent().find('.js-name-b2').html();

        $(this).on('click', function () {
            swal(nameProduct, i18n.wishlistAdded, 'success');
            $(this).addClass('js-addedwish-b2');
            $(this).off('click');
        });
    });

    $('.js-addwish-detail').each(function () {
        const nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();

        $(this).on('click', function () {
            swal(nameProduct, i18n.wishlistAdded, 'success');
            $(this).addClass('js-addedwish-detail');
            $(this).off('click');
        });
    });

    $('.js-addcart-detail').each(function () {
        const nameProduct = $(this).parent().parent().parent().parent().find('.js-name-detail').html();

        $(this).on('click', function () {
            swal(nameProduct, i18n.cartAdded, 'success');
        });
    });

    const sizeSelect   = document.getElementById('sizeSelect');
    const colorSelect  = document.getElementById('colorSelect');
    const variantInput = document.getElementById('variantInput');
    const errorEl      = document.getElementById('variantError');
    const form         = document.getElementById('addToCartForm');
    const variantMap   = @json($variantMap ?? []);

    if (variantInput) {
        function buildKey(sizeId, colorId) {
            if (sizeSelect && colorSelect) {
                if (!sizeId || !colorId) return '';
                return sizeId + '-' + colorId;
            }

            if (sizeSelect) {
                if (!sizeId) return '';
                return sizeId;
            }

            if (colorSelect) {
                if (!colorId) return '';
                return colorId;
            }

            return '';
        }

        function hideVariantError() {
            if (!errorEl) return;
            errorEl.style.display = 'none';
            errorEl.textContent = '';
        }

        function showVariantError(message) {
            if (!errorEl) return;
            errorEl.style.display = 'block';
            errorEl.textContent = message;
        }

        function updateVariant() {
            const sizeId  = sizeSelect ? sizeSelect.value : '';
            const colorId = colorSelect ? colorSelect.value : '';
            const key = buildKey(sizeId, colorId);

            if (!key) {
                variantInput.value = '';
                hideVariantError();
                return;
            }

            if (Object.prototype.hasOwnProperty.call(variantMap, key)) {
                variantInput.value = variantMap[key];
                hideVariantError();
            } else {
                variantInput.value = '';
                showVariantError(i18n.combinationNotAvailable);
            }
        }

        if (sizeSelect) {
            sizeSelect.addEventListener('change', updateVariant);
        }

        if (colorSelect) {
            colorSelect.addEventListener('change', updateVariant);
        }

        if (form) {
            form.addEventListener('submit', function (e) {
                const sizeId  = sizeSelect ? sizeSelect.value : '';
                const colorId = colorSelect ? colorSelect.value : '';
                const key     = buildKey(sizeId, colorId);

                if ((sizeSelect || colorSelect) && (!key || !variantMap[key])) {
                    e.preventDefault();
                    showVariantError(i18n.pleaseSelectRequiredOptions);
                }
            });
        }

        updateVariant();
    }
});
</script>

@stack('scripts')
