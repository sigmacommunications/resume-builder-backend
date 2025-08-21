@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Add Template</h5>
    <div class="card-body">
      <form method="post" action="{{route('template.store')}}">
        {{csrf_field()}}
        <div class="form-group">
          <label for="category" class="col-form-label">Category <span class="text-danger">*</span></label>
          {{-- <input id="category" type="text" name="name" placeholder="Enter category"  value="{{old('reading')}}" class="form-control"> --}}
          <select name="category_id" class="form-control">
            <option>::select category::</option>
            @foreach($categories as $row)
            <option value={{ $row->id }}>{{ $row->name }}</option>
            @endforeach
          </select>
          @error('category')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group">
          <label for="heading" class="col-form-label">Heading <span class="text-danger">*</span></label>
          <input id="heading" type="text" name="heading" placeholder="Enter Heading"  value="{{old('reading')}}" class="form-control">
          @error('heading')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group">
          <label for="type" class="col-form-label">type <span class="text-danger">*</span></label>
          <input id="type" type="text" name="type" placeholder="Enter type"  value="{{old('reading')}}" class="form-control">
          @error('type')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group">
          <label for="key" class="col-form-label">key <span class="text-danger">*</span></label>
          <input id="key" type="text" name="key" placeholder="Enter key"  value="{{old('reading')}}" class="form-control">
          @error('key')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
       
        <div class="form-group">
          <label for="inputPhoto" class="col-form-label">Photo <span class="text-danger">*</span></label>
          <div class="input-group">
              <span class="input-group-btn">
                  <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                  <i class="fa fa-picture-o"></i> Choose
                  </a>
              </span>
          <input id="thumbnail" class="form-control" type="text" name="image" value="{{old('image')}}">
        </div>
        <div id="holder" style="margin-top:15px;max-height:100px;"></div>
          @error('photo')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="comment">Description</label>
          <textarea name="description" id="" cols="20" rows="10" class="form-control"></textarea>
        </div>

        <div class="form-group">
          <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-control">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
          </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
          <button type="reset" class="btn btn-warning">Reset</button>
           <button class="btn btn-success" type="submit">Submit</button>
        </div>
      </form>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script>
    $('#lfm').filemanager('image');

    $(document).ready(function() {
      $('#summary').summernote({
        placeholder: "Write short description.....",
          tabsize: 2,
          height: 120
      });
    });
</script>

<script>
  $('#is_parent').change(function(){
    var is_checked=$('#is_parent').prop('checked');
    // alert(is_checked);
    if(is_checked){
      $('#parent_cat_div').addClass('d-none');
      $('#parent_cat_div').val('');
    }
    else{
      $('#parent_cat_div').removeClass('d-none');
    }
  })
</script>
@endpush