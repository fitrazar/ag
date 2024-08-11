@extends('layouts.guest')

@section('title', 'Home Page')

@section('content')
    <!-- Navbar -->
    <nav class="navbar bg-new">
        <div class="container">
            {{-- <span id="tanggal" class="fs-6"></span><span class="badge text-bg-primary" id="jam"></span> --}}
            {{-- <div class="d-flex">
                <a target="_blank" href="#">
                    <span class="icon-soci facebook">
                        <i class="fab fa-facebook"></i>
                    </span>
                </a>
                <a target="_blank" href="#">
                    <span class="icon-soci instagram">
                        <i class="fab fa-instagram"></i>
                    </span>
                </a>
                <a target="_blank" href="#">
                    <span class="icon-soci youtube">
                        <i class="fab fa-whatsapp"></i>
                    </span>
                </a>
                <a target="_blank" href="#">
                    <span class="icon-soci telegram">
                        <i class="fab fa-telegram-plane"></i>
                    </span>
                </a>
            </div> --}}
        </div>
    </nav>
    <nav class="navbar navbar-dark sticky-top navbar-expand-lg bg-new">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                {{-- <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="text-navbar nav-link active" aria-current="page" href="/"><i
                                class="fa-solid fa-house"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="text-navbar nav-link" href="#">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="text-navbar nav-link" href="#">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="text-navbar nav-link" href="#">Kontak</a>
                    </li>
                </ul> --}}
                <div class="d-flex">
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                </div>

            </div>
        </div>
    </nav>

    <!-- Header Title -->
    <div class="header-title">
        <h1>SMAN 2 PURWAKARTA</h1>
        <p>Generasi Kreatif, Inovatif dan Tangguh</p>
    </div>

    <!-- Carousel -->
    <div id="carouselExample" class="carousel slide">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('assets/img/img-1.jpg') }}" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('assets/img/img-2.jpg') }}" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('assets/img/img-1.jpg') }}" class="d-block w-100" alt="...">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>


    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <h6>About</h6>
                    <p class="text-justify">Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                        Iusto beatae exercitationem eaque repellat minima voluptates, odit et aspernatur
                        harum pariatur. Quae dignissimos dicta sed assumenda aspernatur praesentium explicabo
                        cum necessitatibus.</p>
                </div>

                <div class="col-xs-6 col-md-3">
                    <h6>Categories</h6>
                    <ul class="footer-links">
                        <li><a href="#">C</a></li>
                        <li><a href="#">UI Design</a></li>
                        <li><a href="#">PHP</a></li>
                        <li><a href="#">Java</a></li>
                        <li><a href="#">Android</a></li>
                        <li><a href="#">Templates</a></li>
                    </ul>
                </div>

                <div class="col-xs-6 col-md-3">
                    <h6>Quick Links</h6>
                    <ul class="footer-links">
                        <li><a href="/pages/about">About Us</a></li>
                        <li><a href="/pages/contact">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <hr>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-sm-6 col-xs-12">
                    <p class="copyright-text">Copyright &copy; 2024 All Rights Reserved
                        <a href="#"></a>.
                    </p>
                </div>

                <div class="col-md-4 col-sm-6 col-xs-12">
                    <ul class="social-icons">
                        <li><a class="facebook" href="#"><i class="fab fa-facebook"></i></a></li>
                        <li><a class="instagram" href="#"><i class="fab fa-instagram"></i></a></li>
                        <li><a class="whatsapp" href="#"><i class="fab fa-whatsapp"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
@endsection
@section('script')
    <script>
        var date = new Date();
        var tahun = date.getFullYear();
        var bulan = date.getMonth();
        var tanggal = date.getDate();
        var hari = date.getDay();
        var jam = date.getHours();
        var menit = date.getMinutes();
        var detik = date.getSeconds();
        switch (hari) {
            case 0:
                hari = "Minggu";
                break;
            case 1:
                hari = "Senin";
                break;
            case 2:
                hari = "Selasa";
                break;
            case 3:
                hari = "Rabu";
                break;
            case 4:
                hari = "Kamis";
                break;
            case 5:
                hari = "Jum'at";
                break;
            case 6:
                hari = "Sabtu";
                break;
        }
        switch (bulan) {
            case 0:
                bulan = "Jan";
                break;
            case 1:
                bulan = "Feb";
                break;
            case 2:
                bulan = "Mar";
                break;
            case 3:
                bulan = "Apr";
                break;
            case 4:
                bulan = "Mei";
                break;
            case 5:
                bulan = "Jun";
                break;
            case 6:
                bulan = "Jul";
                break;
            case 7:
                bulan = "Agu";
                break;
            case 8:
                bulan = "Sep";
                break;
            case 9:
                bulan = "Okt";
                break;
            case 10:
                bulan = "Nov";
                break;
            case 11:
                bulan = "Des";
                break;
        }
        var tampilTanggal = "Tanggal: " + hari + ", " + tanggal + " " + bulan + " " + tahun;
        var tampilWaktu = "Jam: " + jam + ":" + menit + ":" + detik;
        document.getElementById("tanggal").innerHTML = hari + ", " + tanggal + " " + bulan + " " + tahun;
        // document.getElementById("jam").innerHTML = jam + ":" + menit + ":" + detik;
    </script>
    <script>
        window.onload = function() {
            jam();
        }

        function jam() {
            var e = document.getElementById('jam'),
                d = new Date(),
                h, m, s;
            h = d.getHours();
            m = set(d.getMinutes());
            s = set(d.getSeconds());

            e.innerHTML = h + ':' + m + ':' + s;

            setTimeout('jam()', 1000);
        }

        function set(e) {
            e = e < 10 ? '0' + e : e;
            return e;
        }
    </script>
@endsection
