@extends('master')

@section('content')
<section class="section">
    @include('admin.layout.breadcrumbs', [
        'title' => __('Edit Brand'),
        'headerData' => __('Brand') ,
        'url' => 'brands' ,
    ])

    <div class="section-body">
        <div class="row">
            <div class="col-lg-8"><h2 class="section-title"> {{__('Edit Brand')}}</h2></div>
        </div>

        <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                    <form method="post" action="{{url('brands/'.$brand->id)}}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>{{__('Name')}}</label>
                            <input type="text" name="name" placeholder="{{__('Name')}}" value="{{ old('name', $brand->name) }}" class="form-control @error('name')? is-invalid @enderror" maxlength="20">
                            @error('name')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>{{__('URL')}}</label>
                            <input name="url" type="url" placeholder="{{__('URL')}}" value="{{ old('url', $brand->url) }}" class="form-control @error('url')? is-invalid @enderror" maxlength="255">
                            @error('url')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>{{__('Logo Image')}}</label>
                            <input type="file" name="logo_image" placeholder="{{__('Logo Image')}}" value="{{ old('logo_image', $brand->logo_image) }}" class="form-control @error('logo_image')? is-invalid @enderror" accept="image/*">
                            @error('logo_image')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                            <div class="mt-2 d-flex justify-content-center align-items-center border rounded-md" id="image-preview-container" style="display: none; height: 160px; width: 160px;">
                                <img id="logo-image-preview" src="#" alt="{{ __('Logo Image') }}" class="img-fluid">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{__('status')}}</label>
                            <select name="status" class="form-control select2">
                                <option value="1" {{ old('status', $brand->status) == '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="0" {{ old('status', $brand->status) == '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary demo-button">{{__('Submit')}}</button>
                        </div>
                    </form>
                </div>
              </div>
            </div>
          </div>
        </div>
    </section>
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('logo-image-preview');
                output.src = reader.result;
                document.getElementById('image-preview-container').style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
