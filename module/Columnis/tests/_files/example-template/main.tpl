{include file="../header.tpl"}
{include file="inc/slider.tpl"}
{include file="inc/head.tpl"}

<section id="presentacion" class="area wrapper centerText">
    {$modulos.secciones[2].contenido}
    <a href="{$modulos.secciones[2].vinculo}" class="boton">Conoc&eacute; m&aacute;s sobre Solcre</a>
</section> <!-- .wrapper -->

<section id="servicios" class="area dark separador centerText">
    <div class="wrapper">
        {$modulos.secciones[3].contenido}
        <div class="columnas-3"><!--
            {foreach $modulos.secciones[3].subSecciones as $SubSeccion}
                --><div class="columna servicio {$SubSeccion.keywords_ar[0]}">
                    <h3><span class="icon-servicios-{$SubSeccion.keywords_ar[0]}"></span>{$SubSeccion.titulo}</h3>
                    <ul>
                        {foreach $SubSeccion.subSecciones as $SubSubSeccion}
                            <li><span class="icon-servicios-list-{$SubSubSeccion.keywords_ar[0]}"></span>{$SubSubSeccion.titulo}</li>
                        {/foreach}
                    </ul>
                </div><!--
            {/foreach}
        --></div> <!-- .columnas-3 -->
        <a href="{$modulos.secciones[3].vinculo}" class="boton">Conoc&eacute; nuestras &aacute;reas de expertise</a>
    </div> <!-- .wrapper -->
</section>

<section id="mundo" class="area centerText" style="background-image:url({$sitio.images_path}galeria/{$modulos.secciones[4].foto.src})">
    <div class="wrapper">
        {$modulos.secciones[4].contenido}
    </div> <!-- .wrapper -->
</section>

<section id="proyectos" class="area centerText">
    <h2 class="notVisible">Proyectos destacados</h2>
    <div id="portfolioSlider" class="owl-carousel">
        
        {foreach $modulos.portfolio.proyectos as $proyecto}
        <div class="proyecto">
            <a href="{$proyecto.ampliar}">
                <span class="logo" style="background-image:url({$sitio.images_path}galeria/{$proyecto.cliente.logo});"></span>
                <span class="foto" style="background-image:url({$sitio.images_path}galeria/{$proyecto.album.fotos[0].src});"></span>
                <span class="texto">
                    <span>{$proyecto.descripcion} <strong>{$proyecto.nombre}</strong></span>
                </span>
            </a>
        </div> <!-- .proyecto -->
        {/foreach}

    </div> <!-- .owl-carousel -->
    <a href="{$modulos.secciones[4].vinculo}" class="boton">Ver nuestro portfolio</a>
</section> <!-- portfolio -->
<script>
    $(document).ready(function() {
        $("#portfolioSlider").owlCarousel({
            center: false,
            items: 1.3,
            loop: true,
            margin: 2,
            merge: true,
            responsive: {
                768: {
                    items: 3.2
                },
                980: {
                    items: 4.2
                }
            },
            dots: false,
            autoplay: true,
            autoplayTimeout: 4000,
            mobileBoost: true
        });
    });
</script>

{*
<section id="productos" class="separador centerText">
    <div class="wrapper">
        {$modulos.secciones[5].contenido}
    </div> <!-- .wrapper -->
    <div class="columnas-2"><!--
        {foreach $modulos.secciones[5].subSecciones as $SubSeccion}
            --><div class="producto columna area clearFix" style="background-image:url({$sitio.images_path}galeria/{$SubSeccion.foto.src});">
                <div class="semiWrapper">
                    {$SubSeccion.contenido}
                    <a href="{$SubSeccion.vinculo}" class="boton">{$SubSeccion.descripcion}</a>
                </div> <!-- .semiWrapper -->
            </div><!--
        {/foreach}
    --></div>
</section>

<section id="ultimosProyectos">
    <div class="wrapper">
        <div class="cleanclear">
            <span class="icono icon-proyectos"></span>
            <h2>&Uacute;ltimos proyectos</h2>
        </div>
        <ul>
            {for $index=0 to 4}
                {$noticia = $modulos.noticias.categorias_agregadas[0].noticias[$index]}
                {if !empty($noticia)}
                    <li class="proyecto">
                        <h3>{$noticia.titulo}</h3>
                        <p>{$noticia.descripcion}</p>
                    </li> <!-- .proyecto -->
                {/if}
            {/for}
        </ul>
    </div> <!-- .wrapper -->
</section> <!-- #proyectos -->

<script>
    $('#ultimosProyectos ul').bxSlider({
        auto: true,
        speed: 1000,
        pause: 6000,
        controls: true,
        nextText: '',
        prevText: '',
        autoStart: true,
        pager: false,
        adaptiveHeight: true
    });
</script>

<div id="noticiaTwitter" class="columnas-2 separador">
    <section id="ultimaNoticia" class="columna">
        <div class="semiWrapper">
            {$ultimaNoticia = $modulos.noticias.categorias_agregadas[1].noticias[0]}
            <h2>&Uacute;ltima noticia</h2>
            <h3>{if !empty($ultimaNoticia.ampliar)}<a href="{$ultimaNoticia.ampliar}">{/if}{$ultimaNoticia.titulo}{if !empty($ultimaNoticia.ampliar)}</a>{/if}</h3>
            <p>{$ultimaNoticia.descripcion}</p>
            <a href="/novedades-7" class="boton">Todas las novedades</a>
        </div>
    </section><div id="twitter" class="columna">
        <div class="semiWrapper">
            <span class="icono icon-twitter"></span>
            <span class="user">@solcre</span>
            <span class="fecha">28 de mar.</span>
            <p class="tweet">¡Nueva presentación de la Empresa presente en la Revista Fama!... Mire el articlo en <a href="#">http://fb.me/2IbCIiOJg</a></p>
            <div class="actions">
                <a href="#" class="responder"><span class="icon-tw-answer"></span>Responder</a>
                <a href="#" class="retweet"><span class="icon-tw-retweet"></span>Retwittear</a>
                <a href="#" class="fav"><span class="icon-star"></span>Favorito</a>
                <a href="#" class="more"><span class="icon-tw-more"></span>M&aacute;s</a>
            </div> <!-- .actions -->
        </div> <!-- .semiWrapper -->
    </div> <!-- .semiWrapper -->
</div> <!-- .columnas-2 -->

<section id="equipo" class="area centerText callToAction" style="background-image:url({$sitio.images_path}galeria/{$modulos.secciones[6].foto.src});">
    <div class="wrapper">
        {$modulos.secciones[6].contenido}
        <a href="{$modulos.secciones[6].vinculo}" class="boton">{$modulos.secciones[6].descripcion}</a>
    </div> <!-- .wrapper -->
</section>
*}
{include file="../footer.tpl"}