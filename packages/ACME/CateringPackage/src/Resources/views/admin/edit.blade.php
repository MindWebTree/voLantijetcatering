@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.settings.cateringpackages.add-title') }}
@stop

@section('content')
    <div class="content">
        
  
        <form
            method="POST"
            @submit.prevent="onSubmit"
            enctype="multipart/form-data"
            action="{{ route('admin.cateringpackage.update',$airport->id) }}">
            
            <div class="page-header">

                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="window.location = '{{ route('admin.cateringpackage.index') }}'"></i>

                        {{ __('admin::app.settings.cateringpackages.edit-title') }}
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('admin::app.settings.cateringpackages.save-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">

                <div class="form-container">
                    @csrf()

                    {!! view_render_event('bagisto.admin.settings.slider.create.before') !!}

                     <div class="control-group" :class="[errors.has('name') ? 'has-error' : '']"> 
                        <label for="sort_order">{{ __('admin::app.settings.cateringpackages.name') }}</label>
                        <input type="text" class="control" id="sort_order" name="name" v-validate="'required'" value="{{$airport->name}}"/>
                        <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name') }}</span>

                    </div>

                      <input type="hidden" value="{{$airport->state}}" id="hidden_state">

                    <div class="control-group" :class="[errors.has('address') ? 'has-error' : '']">
                        <label for="content">{{ __('admin::app.settings.cateringpackages.address') }}</label>

                        <textarea v-validate="'required'" id="" class="control" id="address" name="address" rows="5">{{$airport->address}}</textarea>

                        <span class="control-error" v-if="errors.has('address')">@{{ errors.first('address') }}</span>
                    </div>


                     <div class="control-group" :class="[errors.has('zipcode') ? 'has-error' : '']"> 
                        <label for="sort_order">{{ __('admin::app.settings.cateringpackages.zipcode') }}</label>
                        <input v-validate="'required'" type="text" class="control" id="zipcode" name="zipcode" value="{{$airport->zipcode}}"/>
                        <span class="control-error" v-if="errors.has('zipcode')">@{{ errors.first('zipcode') }}</span>
                    </div>



                    <div class="control-group multi-select" :class="[errors.has('country') ? 'has-error' : '']">
                        <label for="locale">{{ __('admin::app.settings.cateringpackages.country') }}</label>

                        <select v-validate="'required'" class="control" id="country" name="country"  value="" v-validate="'required'" >
                             @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{$country->id == $airport->country ? 'selected' : '' }}>
                                   {{ $country->name }} 
                                </option>
                            @endforeach      
                               
                        </select> 
                        <span class="control-error" v-if="errors.has('country')">@{{ errors.first('country') }}</span>                     
                    </div>



                     <div class="control-group multi-select" :class="[errors.has('state') ? 'has-error' : '']">
                        <label for="locale">{{ __('admin::app.settings.cateringpackages.state') }}</label>

                        <select class="control" id="state" name="state" data-vv-as="&quot;{{ __('admin::app.datagrid.locale') }}&quot;" value="" v-validate="'required'" >
                                 {{-- @foreach($states as $state)
                                <option value="{{ $state->id }}" {{$state->id == $airport->state ? 'selected' : '' }}>
                                   {{ $state->default_name }}
                                </option>
                            @endforeach --}}
                        </select>
                        <span class="control-error" v-if="errors.has('state')">@{{ errors.first('state') }}</span> 
                    </div>


                   
                    <div class="control-group" :class="[errors.has('latitude') ? 'has-error' : '']"> 
                        <label for="sort_order">{{ __('admin::app.settings.cateringpackages.latitude') }}</label>
                        <input type="text" class="control" id="sort_order" name="latitude" value="{{$airport->latitude}}"  v-validate="'required'"/>
                         <span class="control-error" v-if="errors.has('latitude')">@{{ errors.first('latitude') }}</span> 
                    </div>


                    <div class="control-group" :class="[errors.has('longitude') ? 'has-error' : '']"> 
                        <label for="sort_order">{{ __('admin::app.settings.cateringpackages.longitude') }}</label>
                        <input type="text" class="control" id="sort_order" name="longitude" value="{{$airport->longitude}}"  v-validate="'required'" />
                        <span class="control-error" v-if="errors.has('longitude')">@{{ errors.first('longitude') }}</span> 
                    </div>


                    <div class="control-group" :class="[errors.has('display_order') ? 'has-error' : '']"> 
                        <label for="sort_order">{{ __('admin::app.settings.cateringpackages.display_order') }}</label>
                        <input type="text" class="control" id="sort_order" name="display_order" value="{{$airport->display_order}}" v-validate="'required'" />

                        <span class="control-error" v-if="errors.has('display_order')">@{{ errors.first('display_order') }}</span>
                    </div>

                    {{-- <div class="control-group" :class="[errors.has('active') ? 'has-error' : '']"> 
                        <label for="sort_order">{{ __('admin::app.settings.cateringpackages.active') }}</label>
                        <input type="text" class="control" id="sort_order" name="active" value="{{$airport->active}}" v-validate="'required'"/>
                        <span class="control-error" v-if="errors.has('active')">@{{ errors.first('active') }}</span>
                    </div> --}}
                    
                    {{-- Sandeep's Active Checkbox Code --}}
                    <div class="control-group" :class="[errors.has('active') ? 'has-error' : '']">
                        <div class="active_airport_button">
                            <label for="active">{{ __('admin::app.settings.cateringpackages.active') }}</label>
                            <input type="checkbox" class="control active_airport_checkbox" id="active" name="active" 
                                 {{ $airport->active == 1 ? 'checked' : '' }} />
                        </div>
                        <span class="control-error" v-if="errors.has('active')">@{{ errors.first('active') }}</span>
                    </div>
                  
                    @php $channels = core()->getAllChannels() @endphp
    
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    @include('admin::layouts.tinymce')

    <script>
        $(document).ready(function () {

            tinyMCEHelper.initTinyMCE({
                selector: 'textarea#tiny',
                height: 200,
                width: "100%",
                plugins: 'image imagetools media wordcount save fullscreen code table lists link hr',
                toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor link hr | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code | table',
                image_advtab: true,
                templates: [
                    { title: 'Test template 1', content: 'Test 1' },
                    { title: 'Test template 2', content: 'Test 2' }
                ],
            });


            // added by umesh 15-06-2023 

             var selectedCountryId = $("#country").val();
             var selectedStateId =  $("#hidden_state").val();

             var data = JSON.parse({!! json_encode($states) !!});

                   $.each(data, function(key, value) {
                   
                        if(value.country_id==selectedCountryId)
                        {
                            $("#state").append('<option value="'+ value.id +'">'+ value.default_name +'</option>');
                           
                             if(selectedStateId==value.id)
                             {
                                console.log(value.default_name);                                
                                $("#state").val(value.id).prop("selected", true);
                             }

                        }
 
                    });
            

              $("#country").change(function() {

                   $("#state").empty();

                   var countryId = $(this).val();
               
                   var data = JSON.parse({!! json_encode($states) !!});

                   $.each(data, function(key, value) {
                   
                        if(value.country_id==countryId)
                        {
                            $("#state").append('<option value="'+ value.id +'">'+ value.default_name +'</option>');
                        }

                    });

                });

              // end added by umesh   
        });
    </script>
@endpush
