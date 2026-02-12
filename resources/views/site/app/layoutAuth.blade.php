@include('site.app.head')
<body class="animsition">

	<!-- Header -->


    @include('site.app.header')
	<!-- Cart -->


    {{-- @include('site.app.cart') --}}


	<!-- Slider -->

    {{-- @include('site.app.slider') --}}


    @yield('body')
	<!-- Banner -->


    {{-- @include('site.app.banner') --}}

	<!-- Product -->


    {{-- @include('site.app.product') --}}

	<!-- Footer -->

    @include('site.app.footer')

<!--===============================================================================================-->

@include('site.app.script')
