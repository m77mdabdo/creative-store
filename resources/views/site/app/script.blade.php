<script src="{{ asset('cozastore/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('cozastore/vendor/animsition/js/animsition.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('cozastore/vendor/bootstrap/js/popper.js') }}"></script>
<script src="{{ asset('cozastore/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('cozastore/vendor/select2/select2.min.js') }}"></script>
<script>
    $(".js-select2").each(function () {
        $(this).select2({
            minimumResultsForSearch: 20,
            dropdownParent: $(this).next('.dropDownSelect2')
        });
    })
</script>
<!--===============================================================================================-->
<script src="{{ asset('cozastore/vendor/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('cozastore/vendor/daterangepicker/daterangepicker.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('cozastore/vendor/slick/slick.min.js') }}"></script>
<script src="{{ asset('cozastore/js/slick-custom.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('cozastore/vendor/parallax100/parallax100.js') }}"></script>
<script>
    $('.parallax100').parallax100();
</script>
<!--===============================================================================================-->
<script src="{{ asset('cozastore/vendor/MagnificPopup/jquery.magnific-popup.min.js') }}"></script>
<script>
    $('.gallery-lb').each(function () { // the containers for all your galleries
        $(this).magnificPopup({
            delegate: 'a', // the selector for gallery item
            type: 'image',
            gallery: {
                enabled: true
            },
            mainClass: 'mfp-fade'
        });
    });
</script>
<!--===============================================================================================-->
<script src="{{ asset('cozastore/vendor/isotope/isotope.pkgd.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('cozastore/vendor/sweetalert/sweetalert.min.js') }}"></script>
<script>
    $('.js-addwish-b2').on('click', function (e) {
        e.preventDefault();
    });

    $('.js-addwish-b2').each(function () {
        var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
        $(this).on('click', function () {
            swal(nameProduct, "is added to wishlist !", "success");

            $(this).addClass('js-addedwish-b2');
            $(this).off('click');
        });
    });

    $('.js-addwish-detail').each(function () {
        var nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();

        $(this).on('click', function () {
            swal(nameProduct, "is added to wishlist !", "success");

            $(this).addClass('js-addedwish-detail');
            $(this).off('click');
        });
    });

    /*---------------------------------------------*/

    $('.js-addcart-detail').each(function () {
        var nameProduct = $(this).parent().parent().parent().parent().find('.js-name-detail').html();
        $(this).on('click', function () {
            swal(nameProduct, "is added to cart !", "success");
        });
    });
</script>
<!--===============================================================================================-->
<script src="{{ asset('cozastore/vendor/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script>
    $('.js-pscroll').each(function () {
        $(this).css('position', 'relative');
        $(this).css('overflow', 'hidden');
        var ps = new PerfectScrollbar(this, {
            wheelSpeed: 1,
            scrollingThreshold: 1000,
            wheelPropagation: false,
        });

        $(window).on('resize', function () {
            ps.update();
        })
    });
</script>
<!--===============================================================================================-->
<script src="{{ asset('cozastore/js/main.js') }}"></script>

{{-- ===================== VARIANT MAP SCRIPT (ONE TIME ONLY) ===================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const sizeSelect   = document.getElementById('sizeSelect');
    const colorSelect  = document.getElementById('colorSelect');
    const variantInput = document.getElementById('variantInput');
    const errorEl      = document.getElementById('variantError');
    const form         = document.getElementById('addToCartForm');

    const variantMap = @json($variantMap ?? []);

    // لو مش صفحة منتج نخرج
    if (!variantInput) return;

    function buildKey(sizeId, colorId) {

        // Size + Color
        if (sizeSelect && colorSelect) {
            if (!sizeId || !colorId) return '';
            return sizeId + '-' + colorId;
        }

        // Size فقط
        if (sizeSelect) {
            if (!sizeId) return '';
            return sizeId;
        }

        // Color فقط
        if (colorSelect) {
            if (!colorId) return '';
            return colorId;
        }

        return '';
    }

    function updateVariant() {

        const sizeId  = sizeSelect ? sizeSelect.value : '';
        const colorId = colorSelect ? colorSelect.value : '';

        const key = buildKey(sizeId, colorId);

        if (!key) {
            variantInput.value = '';
            if (errorEl) errorEl.style.display = 'none';
            return;
        }

        if (variantMap.hasOwnProperty(key)) {
            variantInput.value = variantMap[key];
            if (errorEl) errorEl.style.display = 'none';
        } else {
            variantInput.value = '';
            if (errorEl) {
                errorEl.style.display = 'block';
                errorEl.textContent = 'This combination is not available.';
            }
        }
    }

    if (sizeSelect)  sizeSelect.addEventListener('change', updateVariant);
    if (colorSelect) colorSelect.addEventListener('change', updateVariant);

    if (form) {
        form.addEventListener('submit', function (e) {

            const sizeId  = sizeSelect ? sizeSelect.value : '';
            const colorId = colorSelect ? colorSelect.value : '';
            const key     = buildKey(sizeId, colorId);

            // لو فيه اختيارات لازم تكون كاملة
            if ((sizeSelect || colorSelect) && (!key || !variantMap[key])) {
                e.preventDefault();

                if (errorEl) {
                    errorEl.style.display = 'block';
                    errorEl.textContent = 'Please select required options.';
                }
            }
        });
    }

    updateVariant();
});
</script>

</body>
</html>
