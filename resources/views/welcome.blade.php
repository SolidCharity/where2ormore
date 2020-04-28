<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Gottesdienst Planer</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>

@if (session('alert'))
    <div class="alert alert-success">
        {{ session('alert') }}
    </div>
@endif

<form action="/submitParticipant" method="post">
  @csrf
  <fieldset>
   Mein Name: <input type="text" id="name" name="name" required value="{{old('name')}}"><br/>
    Es reicht der Vorname oder Nachname, aber ihr solltet von der Gemeindeleitung eindeutig an diesem Namen erkannt werden! Also z.B. Familie Schmidt, wenn es sonst keine Familie Schmidt in der Gemeinde gibt, oder MaxM wenn es sonst keinen Max mit Anfangsbuchstaben M im Nachnamen gibt.
  </fieldset>
  <fieldset>
    <p>Bitte Gottesdienst wählen:</><br/>
@foreach ($services as $service)
    <input type="radio" id="service-{{ $service->id }}" name="service_id" value="{{$service->id}}" required>
    <label for="service-{{ $service->id }}">  
@php
       echo date('H:i', strtotime($service->starting_at));
@endphp 
       Uhr: {{$service->description}}</label> Momentan: {{$service->count_adults}} große Kinder und Erwachsene und {{$service->count_children}} kleine Kinder
    <br/>
@endforeach
  </fieldset>

  <fieldset>
    <input type="number" id="quantityAdults" name="count_adults" min="1" max="9" value="{{old('count_adults', 1)}}" required>
    <label for="quantityAdults">Anzahl Erwachsene und Kinder ab 5. Klasse</label>
    <br/>
    <input type="number" id="quantityChildren" name="count_children" min="0" max="9" value="{{old('count_children', 0)}}">
    <label for="quantityChildren">Anzahl Kindergartenkinder und Grundschulkinder</label>
  </fieldset>

  <input type="submit">
</form>

    </body>
</html>
