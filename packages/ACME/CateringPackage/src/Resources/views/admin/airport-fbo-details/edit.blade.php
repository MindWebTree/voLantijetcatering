@extends('admin::layouts.content')

@section('page_title')
    {{-- {{ __('admin::app.settings.cateringpackages.add-title') }} --}}
    Edit Airport Fbo Detail
@stop

@section('content')
    <div class="content">

        <form method="POST" @submit.prevent="onSubmit" enctype="multipart/form-data"
            action="{{ route('admin.cateringpackage.fbo-details.update', ['id' => $airportfbo->id, 'airport_id' => $airportfbo->airport_id]) }}">

            <div class="page-header">

                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link"
                            onclick="window.location = '{{ route('admin.cateringpackage.airport-fbo-details.index', ['id' => $airportfbo->airport_id]) }}'"></i>

                        {{-- {{ __('admin::app.settings.cateringpackages.edit-title') }} --}}
                        Edit Airport Fbo
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{-- {{ __('admin::app.settings.cateringpackages.save-btn-title') }} --}}
                        Save Airport Fbo
                    </button>
                </div>
            </div>

            <div class="page-content">
                <div class="form-container">
                    @csrf()

                    {!! view_render_event('bagisto.admin.settings.slider.create.before') !!}

                    <div class="control-group" :class="[errors.has('name') ? 'has-error' : '']">
                        {{-- <label for="sort_order">{{ __('admin::app.settings.cateringpackages.name') }}</label> --}}
                        <label for="sort_order">Fbo Name</label>
                        <input type="text" class="control" id="sort_order" name="name" v-validate="'required'"
                            value="{{ $airportfbo->name }}" />
                        <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name') }}</span>

                    </div>

                    {{-- <div class="control-group" :class="[errors.has('email') ? 'has-error' : '']"> --}}
                        {{-- <label for="sort_order">{{ __('admin::app.settings.cateringpackages.name') }}</label> --}}
                        {{-- <label for="sort_order">Email</label>
                        <input type="email" class="control" id="sort_order" name="email" v-validate="'required'"
                            value="{{ $airportfbo->email }}" />

                        <span class="control-error" v-if="errors.has('email')">@{{ errors.first('email') }}</span>

                    </div> --}}

                    {{-- <div class="control-group" :class="[errors.has('phone') ? 'has-error' : '']"> --}}
                        {{-- <label for="sort_order">{{ __('admin::app.settings.cateringpackages.name') }}</label> --}}
                        {{-- <label for="sort_order">Phone</label>
                        <input type="number" class="control" id="sort_order" name="phone" v-validate="'required'"
                            value="{{ $airportfbo->phone }}" />
                        <span class="control-error" v-if="errors.has('phone')">@{{ errors.first('phone') }}</span>

                    </div> --}}


                    <input type="hidden" value="{{ $airportfbo->state }}" id="hidden_state">

                    <div class="control-group" :class="[errors.has('address') ? 'has-error' : '']">
                        <label for="content">{{ __('admin::app.settings.cateringpackages.address') }}</label>

                        <textarea v-validate="'required'" id="" class="control" id="address" name="address" rows="5">{{ $airportfbo->address }}</textarea>

                        <span class="control-error" v-if="errors.has('address')">@{{ errors.first('address') }}</span>
                    </div> 

{{-- 
                    <div class="control-group" :class="[errors.has('zipcode') ? 'has-error' : '']">
                        <label for="sort_order">{{ __('admin::app.settings.cateringpackages.zipcode') }}</label>
                        <input v-validate="'required'" type="text" class="control" id="zipcode" name="zipcode"
                            value="{{ $airportfbo->zipcode }}" />
                        <span class="control-error" v-if="errors.has('zipcode')">@{{ errors.first('zipcode') }}</span>
                    </div> --}}


{{-- 
                    <div class="control-group multi-select" :class="[errors.has('country') ? 'has-error' : '']">
                        <label for="locale">{{ __('admin::app.settings.cateringpackages.country') }}</label>

                        <select v-validate="'required'" class="control" id="country" name="country" value=""
                            v-validate="'required'">
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}"
                                    {{ $country->id == $airportfbo->country ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach

                        </select>
                        <span class="control-error" v-if="errors.has('country')">@{{ errors.first('country') }}</span>
                    </div> --}}



                    {{-- <div class="control-group multi-select" :class="[errors.has('state') ? 'has-error' : '']">
                        <label for="locale">{{ __('admin::app.settings.cateringpackages.state') }}</label>

                        <select class="control" id="state" name="state"
                            data-vv-as="&quot;{{ __('admin::app.datagrid.locale') }}&quot;" value=""
                            v-validate="'required'"> --}}
                            {{-- @foreach ($states as $state)
                                <option value="{{ $state->id }}" {{$state->id == $airportfbo->state ? 'selected' : '' }}>
                                   {{ $state->default_name }}
                                </option>
                            @endforeach --}}
                        {{-- </select>
                        <span class="control-error" v-if="errors.has('state')">@{{ errors.first('state') }}</span>
                    </div> --}}


                    <div class="control-group" :class="[errors.has('notes') ? 'has-error' : '']">
                        <label for="content">Notes (Optional)</label>

                        <textarea class="control" id="notes" name="notes" rows="5">{{ $airportfbo->notes }}</textarea>

                        <span class="control-error" v-if="errors.has('notes')">@{{ errors.first('notes') }}</span>
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
        $(document).ready(function() {

            tinyMCEHelper.initTinyMCE({
                selector: 'textarea#tiny',
                height: 200,
                width: "100%",
                plugins: 'image imagetools media wordcount save fullscreen code table lists link hr',
                toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor link hr | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code | table',
                image_advtab: true,
                templates: [{
                        title: 'Test template 1',
                        content: 'Test 1'
                    },
                    {
                        title: 'Test template 2',
                        content: 'Test 2'
                    }
                ],
            });


            // added by umesh 15-06-2023 

            var selectedCountryId = $("#country").val();
            var selectedStateId = $("#hidden_state").val();

            var data = JSON.parse({!! json_encode($states) !!});

            $.each(data, function(key, value) {

                if (value.country_id == selectedCountryId) {
                    $("#state").append('<option value="' + value.id + '">' + value.default_name +
                        '</option>');

                    if (selectedStateId == value.id) {
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

                    if (value.country_id == countryId) {
                        $("#state").append('<option value="' + value.id + '">' + value
                            .default_name + '</option>');
                    }

                });

            });

            // end added by umesh   
        });
    </script>
@endpush
