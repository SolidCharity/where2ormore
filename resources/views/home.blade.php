@extends('layouts.app')

@if (session('alert'))
    <div class="alert alert-success">
        {{ session('alert') }}
    </div>
@endif

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">@lang('messages.overview_visitors')</div>

                <div class="card-body">

@foreach ($services as $service)
   <div class="row border">
     <div class="container">
     <div class="row">
         <div class="col-md-8">
            {{$service->description}}
         </div>
     </div>

    @foreach ($participants as $participant)
         <div class="row">
              <div class='col-md-2'></div>
    @php
         if ($participant->service_id == $service->id) {
             echo "<div class='col-md-4'>".$participant->name . "</div><div class='col-md-4'>". ($participant->count_adults + $participant->count_children)."</div>";
         }
    @endphp
         </div>
    @endforeach

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
            </div>
            <div class="card">
                <div class="card-header">@lang('messages.settings')</div>

                <div class="card-body">
                   <!-- 
                   TODO: add or remove services
                   TODO: max number of visitors
                   TODO: reset button to clear all participants
                   -->
                   <div class="row">
                   <div class="col-8">
                   <form action="/submitServiceNames" method="post">
@csrf
@foreach ($services as $service)
                       <input type="hidden" name="service[service{{$service->id}}][id]" value="{{$service->id}}"/>
                       @lang('messages.service') {{$loop->index+1}}: <input type="text" name="service[service{{$service->id}}][name]" value="{{$service->description}}" style="width:80%"/>
                       <br/>
@endforeach
                       <input type="submit" value="@lang('messages.submit')"/>
                   </form>
                   </div>
                   <div class="col-2">
@foreach ($services as $service)

                   <form action="/delService" method="post">
                  @csrf
                         <input type="hidden" name="id" value="{{$service->id}}"/>
                  <button class="btn btn-danger" type="submit">@lang('messages.delete')</button><br/>
                       </form>
@endforeach
                   </div>
                   </div>

		   <form action="/addService" method="post">
@csrf
                       @lang('messages.addservice'): <input type="text" name="description" style="width:60%"/>
                       <input type="submit" value="@lang('messages.add')"/>
                   </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
