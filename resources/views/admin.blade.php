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
     <table>
    @foreach ($participants as $participant)
    @php
         if ($participant->service_id == $service->id) {
    @endphp
         <tr>
<form method="post" action="{{ route('participants.update', $participant->id) }}">
            @method('PATCH')
            @csrf
           <td>&nbsp;</td>
           <td><input type="text" name="name" value="{{$participant->name}}" length="20"></td>
           <td><input type="number" name="count_adults" value="{{$participant->count_adults}}" max="9" length="1" style="width:60px"></td>
           <td><button type="submit" class="btn btn-primary" title="@lang('messages.save')">
<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
  <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
</svg>
           </button>
           </td>
</form>
           <td>
<form method="post" action="{{ route('participants.destroy', $participant->id) }}">
            @method('DELETE')
            @csrf
                         <button class="btn btn-danger" type="submit"  onclick="return confirm('@lang('messages.confirm_delete')')" title="@lang('messages.delete')">
<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"></path>
  <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"></path>
</svg>
                         </button>
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
<form method="post" action="{{ route('updateCollectContactDetails') }}">
            @method('PATCH')
            @csrf
                       <td style="width:70%" colspan="2">
                <input type="checkbox" name="collect_contact_details" id="collect_contact_details" value="1" {{$collect_contact_details_checked}}>
                <label for="collect_contact_details">@lang('messages.collect_contact_details')</label>
                       </td>
                       <td>
                           <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                       </td>
</form>
                   </tr>
                   <tr>
<form method="post" action="{{ route('updateChurchName') }}">
            @method('PATCH')
            @csrf
                       <td style="width:10%">
                           @lang('messages.churchname'):
                       </td>
                       <td style="width:70%">
                           <input type="text" name="churchname" value="{{$churchname}}" style="width:100%; min-width:50px"/>
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
                           <input type="text" name="description" value="{{$service->description}}" style="width:100%; min-width:50px"/>
                       </td>
                       <td style="width:10%"><input type="number" name="max_visitors" value="{{$service->max_visitors}}" max="999" length="3" style="width:90px"/></td>
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
                            <input type="text" name="description" style="width:100%; min-width:50px"/>
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
