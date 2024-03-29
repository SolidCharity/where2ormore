@extends('layouts.report')

@section('content')

<style>
html { font-size: 100% }
body {font-size: 1.25em; line-height: 1.25em }
div.serviceTODO {
    page-break-after: always;
}
</style>

<div class="container">
@foreach ($services as $service)
   <div class="row border">
     <div class="container service">
     <div class="row">
         <div class="col-md-12">
            <p style="text-align: center; width: 100%">
            {{$service->description}}
            </p>
         </div>
     </div>
     <table style="width: 100%; table-layout: fixed;">

         <tr>
@if (!$option_for_single_registration)
           <th style="width: 5%"></th>
@endif
@if ($collect_contact_details)
           <th>Name</td>
           <th>Adresse</th>
           <th>Telefon</th>
@else
           <th style="width: 30%">@lang('messages.name')</th>
@endif
@if ($display_2g)
            <th style="width: 5%">@lang('messages.2GStatus')</td>
@elseif ($display_3g)
            <th style="width: 5%">@lang('messages.3GStatus')</td>
@endif
@if ($display_3g_status)
           <th style="width: 5%">@lang('messages.approved_3g')</th>
@endif
@if ($display_signatures)
           <th style="width: 20%">@lang('messages.signature')</th>
@endif
@if (!$collect_contact_details)
           <th style="width: 20%">@lang('messages.phone_or_email')</th>
@endif
         </tr>

    @foreach ($participants as $participant)
    @php
         if ($participant->service_id == $service->id) {
    @endphp
         <tr>
@if (!$option_for_single_registration)
           <td style="width: 5%">{{$participant->count_adults}}</td>
@endif
@if ($collect_contact_details)
           <td>{{$participant->name}}</td>
           <td>{{$participant->address}}</td>
           <td>{{$participant->phone}}</td>
@else
@if ($participant->report_details)
           <td style="width: 30%">{{$participant->name}}</td>
@else
           <td style="width: 30%">Anonymous</td>
@endif
@endif
@if ($display_2g || $display_3g)
           <td style="width: 5%">{{$participant->have_all_2g_msg}}{{$participant->have_all_3g_msg}}</td>
@endif
@if ($display_3g_status)
           <td style="width: 5%; border: 2px solid black"></td>
@endif
@if ($display_signatures)
           <td style="width: 20%"><br/>___________________________</td>
           <td style="width: 20%"><br/>___________________________</td>
@endif
         </tr>
    @php
         }
    @endphp
    @endforeach
     </table>

     <div class="row">
         <div class="col-md-2"></div>
         <div class="col-md-8">
@if ($display_2g)
             @lang('messages.currently_visitors_with2g',
               ['value' => $service->count_adults + $service->count_children,
               'max' => $service->max_visitors,
               'have_2g' => $service->have_2g,
               'have_no_2g' => $service->have_no_2g])
@endif
@if ($display_3g)
             @lang('messages.currently_visitors_with3g',
               ['value' => $service->count_adults + $service->count_children,
               'max' => $service->max_visitors,
               'have_3g' => $service->have_3g,
               'have_no_3g' => $service->have_no_3g])
@endif
@if (!$display_2g && !$display_3g)
             @lang('messages.currently_visitors',
               ['value' => $service->count_adults + $service->count_children,
               'max' => $service->max_visitors])
@endif
         </div>
      </div>

     <div class="row">
         <div class="col-md-2"></div>
         <div class="col-md-8">
            <br/><br/>
            {{$tenant->text_for_report_welcome_person}}: ___________________________
         </div>
      </div>

     <div class="row">
        <div class="col-md-12">
            <p style="text-align: center; width: 100%">
            {{$tenant->text_for_report_destroy_list}}
            </p>
         </div>
      </div>

     <div class="row">
         <div class="col-md-12">
            <p style="text-align: center; width: 100%">
            {{$tenant->text_for_report_church_details}}
            </p>
         </div>
      </div>

   </div>
   </div>

@endforeach

</div>
@endsection
