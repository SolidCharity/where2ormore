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
    @foreach ($participants as $participant)
    @php
         if ($participant->service_id == $service->id) {
    @endphp
         <tr>
           <td style="width: 5%"></td>
           <td style="width: 5%">{{$participant->count_adults}}</td>
@if ($display_2g)
           <td style="width: 5%">{{$participant->have_all_2g_msg}}</td>
@endif
@if ($collect_contact_details)
           <td>{{$participant->name}}</td>
           <td>{{$participant->address}}</td>
           <td>{{$participant->phone}}</td>
@else
@if ($participant->report_details)
           <td style="width: 60%">{{$participant->name}}</td>
@else
           <td style="width: 60%">Anonymous</td>
@endif
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
             @lang('messages.currently_visitors_with2g',
               ['value' => $service->count_adults + $service->count_children,
               'max' => $service->max_visitors,
               'have_2g' => $service->have_2g,
               'have_no_2g' => $service->have_no_2g])
         </div>
     </div>
   </div>
   </div>

@endforeach

</div>
@endsection
