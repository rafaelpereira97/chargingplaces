<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
          integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
          crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.css"/>


    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
            integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
            crossorigin=""></script>

    <script src="{{asset('L.KML.js')}}"></script>
    <script src="https://unpkg.com/leaflet-kmz@latest/dist/leaflet-kmz.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.js"></script>

    <title>Pontos de Abastecimento Elétrico</title>
    <style>
        #mapid { height: 900px; }
    </style>
</head>
<body>
<!-- Image and text -->
<nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="#">
        <img src="{{asset('images/WebsiteIcon.png')}}" width="30" height="30" class="d-inline-block align-top" alt="">
        Abastecimento e Estacionamento para Veiculos Eléctricos
    </a>
    <a class="btn btn-success" href="{{url('/admin/login')}}" style="float: right">
        Área Admin
    </a>
</nav>

    <div class="container-fluid">
        <div class="row">
<div style="  height:900px;
  overflow-y: scroll;" class="col-md-2">
    <br>
    <h5 align="center">Postos perto de Si</h5>
    <br>

    <ul id="nearbylist" class="list-group">
    </ul>
</div>
<div class="col-md-10">
<div id="mapid"></div>
</div>
    </div>
</div>
<script
    src="https://code.jquery.com/jquery-3.4.1.js"
    integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script>
    getNearbyMarkers();
    function getNearbyMarkers(){
        navigator.geolocation.getCurrentPosition(function(position){
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;

        $.ajax({    //create an ajax request to display.php
            type: "POST",
            url: "{{route('nearplaces')}}",
            dataType: "json",
            contentType: 'application/x-www-form-urlencoded',
            data:{
                _token: "{{ csrf_token() }}",
                lat: lat,
                lng: lng,
            },
            success: function(response){
                var data = JSON.parse(response);
                $.each(data, function(k, v) {
                    var lat = data[k].point;
                    // Atençao martelada
                    lat = lat.replace("POINT(","");
                    lat = lat.replace(")","");
                    lat = lat.split(" ");
                    $("#nearbylist").append('<li class="list-group-item">'+data[k].name+' <a id="fly" data-lat="'+lat[0]+'" data-lng="'+lat[1]+'" href="#"> <img title="Zoom no Ponto" style="width: 20px" class="img img-responsive" src="'+window.location+'images/maplocation.png"> </a> <span style="float: right;">KM:'+data[k].distance+ '</span></li>');
                });
            }
        });
        });
    }

    navigator.geolocation.getCurrentPosition(function(location) {
        var latlng = new L.LatLng(location.coords.latitude, location.coords.longitude);
        var mymap = L.map('mapid').setView(latlng, 16);
    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
        maxZoom: 18,
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
            '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
            'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        id: 'mapbox/streets-v11'
    }).addTo(mymap);

        $(document).on('click','#fly',function() {
            flyToLatLng( $(this).attr("data-lng"), $(this).attr("data-lat"))
        });

        function flyToLatLng(lat, lng) {
            mymap.flyTo([lat, lng], 18);
        };

        var personIcon = L.icon({
            iconUrl: '{{asset('mycar.png')}}',
            shadowUrl: null,

            iconSize:     [90, 95], // size of the icon
            shadowSize:   [50, 64], // size of the shadow
            iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
            shadowAnchor: [4, 62],  // the same for the shadow
            popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
        });
        var mylocation = L.marker(latlng,{icon: personIcon}).bindTooltip("Localização Atual",
            {
                permanent: true,
                direction: 'right'
            }
        ).addTo(mymap);

        var MyIcon = L.icon({
            iconUrl: '{{asset('chargerpoint.png')}}',
            iconSize:     [50, 95], // size of the icon
            shadowSize:   [50, 64], // size of the shadow
            iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
            shadowAnchor: [4, 62],  // the same for the shadow
            popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
        });
        group = new L.FeatureGroup();
        @foreach($points as $point)
            @php
                $point = json_encode(new Grimzy\LaravelMysqlSpatial\Types\Point($point->location->getLat(), $point->location->getLng()));
            @endphp

        group.addLayer(L.geoJSON({!! $point !!},{
                pointToLayer: function(feature,latlng){
                    return L.marker(latlng,{icon: MyIcon});
                }
            }));
        @endforeach


        //Abrir Google maps para navegar até um posto
        group.on("click", function (event) {
            var clickedMarker = event.layer;
            window.location.href = 'https://maps.google.com/?q='+clickedMarker._latlng.lat+','+clickedMarker._latlng.lng;
        });

        var baseMaps = {

            };

        var overlayMaps = {
                "Pontos de Abastecimento Elétrico": group
        };

        L.control.layers(baseMaps, overlayMaps).addTo(mymap);


    mymap.on('click', OpenNewChargePointModal);

        function OpenNewChargePointModal(e){
        $('#modal').modal('show');
        $("#latitude").val(e.latlng.lat);
        $("#longitude").val(e.latlng.lng);
    }


        $('#formAddPoint').on('submit', function (e) {
            e.preventDefault();

            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    $('#modal').modal('toggle');
                    var lat = form.serializeArray()[2].value;
                    var lng = form.serializeArray()[3].value;
                    group.addLayer(L.marker([lat, lng], {icon: MyIcon}));
                    $('#nearbylist').empty();
                    getNearbyMarkers();
                }
            });



        });

    });
</script>

<div id="modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Ponto de Carregamento Automóvel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddPoint" method="POST" action="{{route('addplace')}}">
                @csrf
            <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Descrição</label>
                        <input name="name" type="text" class="form-control" id="name" placeholder="Introduza uma Descrição">
                    </div>
                    <div class="form-group">
                        <label for="position">Latitude</label>
                        <input name="latitude" type="text" class="form-control" id="latitude" placeholder="Latitude / Longitude">

                        <div class="form-group">
                            <label for="position">Longitude</label>
                            <input name="longitude" type="text" class="form-control" id="longitude" placeholder="Latitude / Longitude">
                        </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->

</body>
</html>
