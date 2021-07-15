<footer>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
                <p>Copyright &copy; 2020 Expert-Annonce-Immo.fr</p>
                <nav>
                    <ul>
                        <li><a href="https://expert-annonce-auto.com/qui-sommes-nous.php">Mentions légales | Qui sommes-nous ?</a></li> /
                        <li><a href="https://expert-annonce-auto.com/donnees-personnelles.php">Données personnelles</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<?php wp_footer(); ?>
<a id="btn-top" href="#"></a>

<script>
    $(document).ready(function() {
        $(".owl").owlCarousel({
            items : 4,
            loop:true,
            autoplay: true,
            nav: true,
            dots: false,
            responsiveClass:true,
            responsive:{
                0:{
                    items:1,
                    nav:true
                },
                600:{
                    items:2,
                    nav:false
                },
                1000:{
                    items:3,
                    nav:true,
                    loop:false
                },
                1400:{
                    items:4,
                    nav:true,
                    loop:false
                }
            }
        });

        $(".owl-one").owlCarousel({
            items : 1,
            margin:10,
            autoHeight:true,
            loop:true,
            nav: true,
            dots: false
        });
    });
</script>
</body>
</html>