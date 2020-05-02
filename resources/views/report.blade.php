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
           <td style="width: 10%"></td>
           <td style="width: 60%">{{$participant->name}}</td>
           <td>{{$participant->count_adults}}</td>
         </tr>
    @php
         }
    @endphp
    @endforeach
     </table>

     <div class="row">
         <div class="col-md-4"></div>
         <div class="col-md-4">
             @lang('messages.currently_visitors', ['value' => $service->count_adults + $service->count_children])
         </div>
     </div>
   </div>
   </div>

@endforeach

</div>
@endsection
