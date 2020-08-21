@if (session('message') || session('html_message')|| (isset($errors) && $errors->all()))
    <div class="alert {{ (session('result'))? 'alert-info' : 'alert-danger' }}">
        @if (session('message'))
            {{session('message')}}
        @elseif(session('html_message'))
            {!! session('html_message') !!}
        @endif 
        
        
        @foreach ($errors->all() as $error)
        <font color='red'><strong> {{ $error }}</strong></font><br>
        @endforeach
    </div>
@endif



