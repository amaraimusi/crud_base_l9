@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('メールアドレスを確認してください') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('メールアドレスに送信されました。届いたメールに記載されている確認URLにアクセスしてください。') }}
                        </div>
                    @endif

                    {{ __('続ける前に、メールに記載されている確認URLを確認してください。') }}
                    {{ __('メールが届かない場合') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('再送信') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
