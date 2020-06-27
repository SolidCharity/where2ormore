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
     <table style="width: 100%">
    @foreach ($participants as $participant)
    @php
         if ($participant->service_id == $service->id) {
    @endphp
         <tr>
<form method="post" action="{{ route('participants.update', $participant->id) }}">
            @method('PATCH')
            @csrf
           <td style="width: 10%"></td>
           <td style="width: 60%"><input type="text" name="name" value="{{$participant->name}}" style="width:100%"></td>
           <td><input type="number" name="count_adults" value="{{$participant->count_adults}}"></td>
           <td><button type="submit" class="btn btn-primary">@lang('messages.save')</button></td>
</form>
           <td>
<form method="post" action="{{ route('participants.destroy', $participant->id) }}">
            @method('DELETE')
            @csrf
                         <button class="btn btn-danger" type="submit"  onclick="return confirm('@lang('messages.confirm_delete')')">@lang('messages.delete')</button>
</form>
           </td>
         </tr>
    @php
         }
    @endphp
    @endforeach
     </table>

     <div class="row">
         <div class="col-md-4"></div>
         <div class="col-md-4">
             @lang('messages.currently_visitors', ['value' => $service->count_adults + $service->count_children, 'max' => $service->max_visitors])
         </div>
     </div>
   </div>

<div class="btn-group">
<form method="post" action="{{ route('dropAllParticipants', $service->id) }}">
            @method('DELETE')
            @csrf
                 <button type="submit" class="btn btn-danger" onclick="return confirm('@lang('messages.confirm_delete')')">@lang('messages.delete_all_participants')</button>
</form>

<a href="/report/{{$service->id}}" target="_blank"><button class="btn btn-primary">@lang('messages.print_report')</button></a>
</div>
   </div>
@endforeach

<br/>
@lang('messages.for_all_services'):
<div class="btn-group">
<form method="post" action="{{ route('dropAllParticipants') }}">
            @method('DELETE')
            @csrf
                 <button type="submit" class="btn btn-danger" onclick="return confirm('@lang('messages.confirm_delete')')">@lang('messages.delete_all_participants')</button>
</form>

<a href="/report" target="_blank"><button class="btn btn-primary">@lang('messages.print_report')</button></a>
</div>
</div>
            </div>
            <div class="card">
                <div class="card-header">@lang('messages.settings')</div>

                <div class="card-body">

                @lang('messages.link_for_visitors'): <a href="{{$link_visitors}}">{{$link_visitors}}</a><br/>

                <table>
                   <tr>
<form method="post" action="{{ route('updateChurchName') }}">
            @method('PATCH')
            @csrf
                       <td style="width:10%">
                           @lang('messages.churchname'):
                       </td>
                       <td style="width:70%">
                           <input type="text" name="churchname" value="{{$churchname}}" style="width:100%"/>
                       </td>
                       <td>
                           <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                       </td>
</form>
                    </tr>
                </table>

                <table>
@foreach ($services as $service)
                   <tr>
<form method="post" action="{{ route('services.update', $service->id) }}">
            @method('PATCH')
            @csrf
                       <td style="width:10%">
                           @lang('messages.service') {{$loop->index+1}}:
                       </td>
                       <td style="width:70%">
                           <input type="text" name="description" value="{{$service->description}}" style="width:100%"/>
                       </td>
                       <td style="width:10%"><input type="number" name="max_visitors" value="{{$service->max_visitors}}" style="width:100%"/></td>
                       <td>
                           <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                       </td>
</form>

<form method="post" action="{{ route('serviceToggleActivation', $service->id) }}">
            @method('PATCH')
            @csrf
                       <td>
@if ($service->registration_open)
                           <button type="submit" class="btn btn-warning">
                               @lang('messages.deactivate_service_registration')
                           </button>
@else
                           <button type="submit" class="btn btn-secondary">
                               @lang('messages.activate_service_registration')
                           </button>
@endif
                       </td>

</form>
<form method="post" action="{{ route('services.destroy', $service->id) }}">
            @method('DELETE')
            @csrf
                       <td>
                           <button type="submit" class="btn btn-danger" onclick="return confirm('@lang('messages.confirm_delete')')">
                               @lang('messages.delete')
                           </button>
                       </td>

</form>
                   </tr>
@endforeach
                   <tr>
		   <form action="{{ route('services.store') }}" method="post">
@csrf
                       <td>
                       @lang('messages.addservice'):
                       </td>
                       <td>
                            <input type="text" name="description" style="width:100%"/>
                       </td>
                       <td>
                          <button type="submit" class="btn btn-primary">@lang('messages.add')</button>
                       </td>
                   </form>
                   </tr>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
