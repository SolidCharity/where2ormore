@extends('layouts.report')

@section('content')
<div class="container">
@foreach ($services as $service)
   <div class="row border">
     <div class="container">
     <div class="row">
         <div class="col-md-8">
            {{$service->description}}
         </div>
     </div>
     <table style="width: 100%">

@if ($display_3g_signatures)
         <tr>
@if ($display_2g)
            <th style="width: 5%">2G Status</td>
@endif
@if ($collect_contact_details)
           <th>Name</td>
           <th>Adresse</th>
           <th>Telefon</th>
@else
           <th style="width: 20%">Name</th>
@endif
           <th style="width: 5%">Geimpft</th>
           <th style="width: 5%">Genesen</th>
           <th style="width: 5%">Getestet</th>
           <th style="width: 20%">Unterschrift</th>
           <th style="width: 20%">Telefon oder E-Mail</th>
         </tr>
@endif

    @foreach ($participants as $participant)
    @php
         if ($participant->service_id == $service->id) {
    @endphp
         <tr>
@if (!$display_3g_signatures)
           <td style="width: 5%"></td>
           <td style="width: 5%">{{$participant->count_adults}}</td>
@endif
@if ($display_2g)
           <td style="width: 5%">{{$participant->have_all_2g_msg}}</td>
@endif
@if ($collect_contact_details)
           <td>{{$participant->name}}</td>
           <td>{{$participant->address}}</td>
           <td>{{$participant->phone}}</td>
@else
@if ($participant->report_details)
           <td style="width: 20%">{{$participant->name}}</td>
@else
           <td style="width: 20%">Anonymous</td>
@endif
@endif
@if ($display_3g_signatures)
           <td style="width: 5%; border: 2px solid black"></td>
           <td style="width: 5%; border: 2px solid black"></td>
           <td style="width: 5%; border: 2px solid black"></td>
           <td style="width: 20%; height: 4em;"><br/>___________________________________</td>
           <td style="width: 20%; height: 4em;"><br/>___________________________________</td>
@endif
         </tr>
    @php
         }
    @endphp
    @endforeach
     </table>

     <div class="row">
         <div class="col-md-4"></div>
         <div class="col-md-4">
@if ($display_2g)
             @lang('messages.currently_visitors_with2g',
               ['value' => $service->count_adults + $service->count_children,
               'max' => $service->max_visitors,
               'have_2g' => $service->have_2g,
               'have_no_2g' => $service->have_no_2g])
@else
             @lang('messages.currently_visitors',
               ['value' => $service->count_adults + $service->count_children,
               'max' => $service->max_visitors])
@endif
         </div>
      </div>

     <div class="row">
         <div class="col-md-4"></div>
         <div class="col-md-8">
            <br/><br/>
            Eingangskontrolle durch: ___________________________
         </div>
      </div>

     <div class="row">
         <div class="col-md-4"></div>
         <div class="col-md-8">
            <br/><br/>
            Diese Liste wird nach 4 Wochen vernichtet
         </div>
      </div>

     <div class="row">
         <div class="col-md-12">
            <p style="text-align: center; width: 100%">
            TODO $tenant->ChurchDetailsForReport
            </p>
         </div>
      </div>

   </div>
   </div>

@endforeach

</div>
@endsection
